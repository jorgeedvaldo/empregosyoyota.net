<?php

namespace Tests\Feature;

use App\Exceptions\DatabaseUnavailableException;
use App\Services\DatabaseCircuitBreaker;
use App\Services\PublicPageCache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use RuntimeException;
use Tests\Concerns\SimulatesMysqlFailures;
use Tests\TestCase;

/**
 * O criterio de sucesso de tudo isto: com o MySQL no tecto das 30 ligacoes,
 * quem visita o site continua a ver paginas, e nunca um erro 500.
 */
class DatabaseUnavailableResponseTest extends TestCase
{
    use SimulatesMysqlFailures;

    protected function setUp(): void
    {
        parent::setUp();

        // Em producao APP_DEBUG e false; e esse o comportamento em teste.
        config(['app.debug' => false]);

        Log::spy();
    }

    /** @test */
    public function com_copia_guardada_a_pagina_publica_continua_a_abrir(): void
    {
        $falhar = false;

        Route::middleware('web')->get('/_teste/vagas', function () use (&$falhar) {
            if ($falhar) {
                throw $this->maxUserConnectionsException();
            }

            return response('<html><body>ultimas vagas</body></html>');
        });

        // Visita normal: fica guardada.
        $this->get('/_teste/vagas')->assertOk();

        // O conteudo e publicado de novo (a copia normal deixa de valer) e
        // logo a seguir o MySQL esgota as ligacoes.
        app(PublicPageCache::class)->bumpVersion();
        $falhar = true;

        $resposta = $this->get('/_teste/vagas');

        $resposta->assertOk();
        $resposta->assertSee('ultimas vagas');
        $this->assertSame('emergency', $resposta->headers->get(PublicPageCache::HEADER));
        $resposta->assertDontSee('max_user_connections');
        $resposta->assertDontSee('SQLSTATE');
    }

    /** @test */
    public function sem_copia_nenhuma_responde_503_com_a_pagina_de_emergencia(): void
    {
        Route::middleware('web')->get('/_teste/novidade', function () {
            throw $this->maxUserConnectionsException();
        });

        $resposta = $this->get('/_teste/novidade');

        $resposta->assertStatus(503);
        $resposta->assertSee('Serviço temporariamente indisponível', false);
        $this->assertNotEmpty($resposta->headers->get('Retry-After'));
    }

    /** @test */
    public function a_pagina_de_emergencia_nao_revela_nada_de_tecnico(): void
    {
        Route::middleware('web')->get('/_teste/novidade', function () {
            throw $this->maxUserConnectionsException();
        });

        $resposta = $this->get('/_teste/novidade');

        $resposta->assertDontSee('max_user_connections');
        $resposta->assertDontSee('SQLSTATE');
        $resposta->assertDontSee('yqqnyhrgnt_edv');
        $resposta->assertDontSee('select * from');
    }

    /** @test */
    public function a_falha_fica_registada_no_log(): void
    {
        Route::middleware('web')->get('/_teste/novidade', function () {
            throw $this->maxUserConnectionsException();
        });

        $this->get('/_teste/novidade');

        Log::shouldHaveReceived('warning')
            ->withArgs(fn ($mensagem) => str_contains($mensagem, '[resiliencia]'))
            ->atLeast()->once();
    }

    /** @test */
    public function um_formulario_nunca_recebe_um_sucesso_inventado(): void
    {
        $gravou = false;

        Route::middleware('web')->post('/_teste/candidatura', function () use (&$gravou) {
            throw $this->maxUserConnectionsException();
            $gravou = true; // nunca chega aqui
        })->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

        $resposta = $this->post('/_teste/candidatura', ['nome' => 'Maria']);

        $resposta->assertStatus(503);
        $this->assertFalse($gravou);
        $resposta->assertDontSee('recebido');
        $resposta->assertDontSee('SQLSTATE');
    }

    /** @test */
    public function os_pedidos_de_json_recebem_503_e_nao_html(): void
    {
        Route::middleware('web')->get('/_teste/api-interna', function () {
            throw $this->maxUserConnectionsException();
        });

        $resposta = $this->getJson('/_teste/api-interna');

        $resposta->assertStatus(503);
        $resposta->assertJsonStructure(['message']);
        $this->assertNotEmpty($resposta->headers->get('Retry-After'));
        $resposta->assertDontSee('SQLSTATE');
    }

    /** @test */
    public function um_erro_de_programacao_continua_a_ser_um_erro_de_programacao(): void
    {
        Route::middleware('web')->get('/_teste/avariado', function () {
            throw new RuntimeException('isto e um bug a serio');
        });

        $this->get('/_teste/avariado')->assertStatus(500);
    }

    /** @test */
    public function um_erro_de_sql_nosso_nao_passa_por_indisponibilidade(): void
    {
        Route::middleware('web')->get('/_teste/consulta-ma', function () {
            throw $this->syntaxErrorException();
        });

        $this->get('/_teste/consulta-ma')->assertStatus(500);
    }

    /** @test */
    public function as_falhas_consecutivas_abrem_o_disjuntor(): void
    {
        Route::middleware('web')->get('/_teste/vagas', function () {
            throw $this->maxUserConnectionsException();
        });

        config(['resilience.breaker.failures' => 3]);
        app()->forgetInstance(DatabaseCircuitBreaker::class);

        for ($i = 0; $i < 3; $i++) {
            $this->get('/_teste/vagas')->assertStatus(503);
        }

        $this->assertTrue(app(DatabaseCircuitBreaker::class)->isOpen());
    }

    /** @test */
    public function com_o_disjuntor_aberto_as_consultas_nem_tentam_a_ligacao(): void
    {
        Route::middleware('web')->get('/_teste/consulta', function () {
            DB::select('select 1');

            return response('<html><body>ok</body></html>');
        });

        $breaker = app(DatabaseCircuitBreaker::class);
        for ($i = 0; $i < 3; $i++) {
            $breaker->recordFailure();
        }
        $breaker->allowsAttempt(); // consome a sonda de recuperacao

        $this->withoutExceptionHandling();

        try {
            $this->get('/_teste/consulta');
            $this->fail('A consulta devia ter sido travada pelo disjuntor.');
        } catch (DatabaseUnavailableException $e) {
            // Travada antes de abrir ligacao: se tivesse chegado ao MySQL,
            // a excepcao seria uma QueryException do driver.
            $this->assertStringContainsString('disjuntor', $e->getMessage());
        }
    }

    /** @test */
    public function quando_o_mysql_recupera_as_paginas_voltam_a_ser_geradas(): void
    {
        $falhar = true;

        Route::middleware('web')->get('/_teste/vagas', function () use (&$falhar) {
            if ($falhar) {
                throw $this->maxUserConnectionsException();
            }

            return response('<html><body>vagas de hoje</body></html>');
        });

        $this->get('/_teste/vagas')->assertStatus(503);

        // MySQL recuperado. O disjuntor ainda nao chegou a abrir (uma so
        // falha), por isso o pedido seguinte volta a passar.
        $falhar = false;

        $resposta = $this->get('/_teste/vagas');

        $resposta->assertOk();
        $resposta->assertSee('vagas de hoje');
    }
}
