<?php

namespace Tests\Feature;

use App\Services\DatabaseCircuitBreaker;
use App\Services\PublicPageCache;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

/**
 * A cache de HTML das paginas publicas: com uma copia valida, o pedido nem
 * chega ao controlador — e portanto nao gasta nenhuma das 30 ligacoes.
 */
class PublicPageCacheTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config(['app.debug' => false]);
    }

    /** @test */
    public function a_segunda_visita_e_servida_da_copia_guardada(): void
    {
        $execucoes = 0;

        Route::middleware('web')->get('/_teste/vagas', function () use (&$execucoes) {
            $execucoes++;

            return response('<html><body>vaga numero ' . $execucoes . '</body></html>');
        });

        $primeira = $this->get('/_teste/vagas');
        $primeira->assertOk();
        $primeira->assertSee('vaga numero 1');
        $this->assertNull($primeira->headers->get(PublicPageCache::HEADER));

        $segunda = $this->get('/_teste/vagas');
        $segunda->assertOk();
        $segunda->assertSee('vaga numero 1');
        $this->assertSame('hit', $segunda->headers->get(PublicPageCache::HEADER));
        $this->assertSame(1, $execucoes, 'O controlador nao pode voltar a correr.');
    }

    /** @test */
    public function publicar_conteudo_invalida_as_copias_normais(): void
    {
        $execucoes = 0;

        Route::middleware('web')->get('/_teste/vagas', function () use (&$execucoes) {
            $execucoes++;

            return response('<html><body>vaga numero ' . $execucoes . '</body></html>');
        });

        $this->get('/_teste/vagas');

        // E o que o model faz no saved(): a versao muda, as copias normais
        // deixam de servir.
        app(PublicPageCache::class)->bumpVersion();

        $this->get('/_teste/vagas')->assertSee('vaga numero 2');
        $this->assertSame(2, $execucoes);
    }

    /** @test */
    public function a_paginacao_tem_copias_proprias(): void
    {
        Route::middleware('web')->get('/_teste/vagas', function () {
            return response('<html><body>pagina ' . request('page', 1) . '</body></html>');
        });

        $this->get('/_teste/vagas?page=1')->assertSee('pagina 1');
        $this->get('/_teste/vagas?page=2')->assertSee('pagina 2');
        $this->get('/_teste/vagas?page=2')->assertSee('pagina 2');
    }

    /** @test */
    public function os_parametros_de_campanha_nao_multiplicam_copias(): void
    {
        $execucoes = 0;

        Route::middleware('web')->get('/_teste/vagas', function () use (&$execucoes) {
            $execucoes++;

            return response('<html><body>vagas</body></html>');
        });

        $this->get('/_teste/vagas');
        $this->get('/_teste/vagas?utm_source=facebook');

        $this->assertSame(1, $execucoes, 'O utm_source nao muda o conteudo da pagina.');
    }

    /** @test */
    public function os_pedidos_que_escrevem_nunca_sao_guardados(): void
    {
        $execucoes = 0;

        Route::middleware('web')->post('/_teste/candidatura', function () use (&$execucoes) {
            $execucoes++;

            return response('<html><body>recebido</body></html>');
        })->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

        $this->post('/_teste/candidatura');
        $this->post('/_teste/candidatura');

        $this->assertSame(2, $execucoes);
    }

    /** @test */
    public function o_painel_de_administracao_nunca_e_guardado(): void
    {
        $execucoes = 0;

        Route::middleware('web')->get('/admin/qualquer-coisa', function () use (&$execucoes) {
            $execucoes++;

            return response('<html><body>painel</body></html>');
        });

        $this->get('/admin/qualquer-coisa');
        $this->get('/admin/qualquer-coisa');

        $this->assertSame(2, $execucoes, 'O painel tem dados de quem la esta dentro.');
    }

    /** @test */
    public function a_pesquisa_nao_e_guardada(): void
    {
        $execucoes = 0;

        Route::middleware('web')->get('/pesquisar', function () use (&$execucoes) {
            $execucoes++;

            return response('<html><body>resultados</body></html>');
        });

        $this->get('/pesquisar?query=contabilista');
        $this->get('/pesquisar?query=contabilista');

        $this->assertSame(2, $execucoes, 'Os termos de pesquisa encheriam o disco de copias.');
    }

    /** @test */
    public function uma_sessao_autenticada_nunca_recebe_nem_alimenta_a_cache(): void
    {
        $execucoes = 0;

        Route::middleware('web')->get('/_teste/vagas', function () use (&$execucoes) {
            $execucoes++;

            return response('<html><body>vagas</body></html>');
        });

        // Guarda uma copia a partir de uma visita anonima.
        $this->get('/_teste/vagas');

        // Com marca de login na sessao, o pedido nao e servido da cache
        // partilhada nem a alimenta.
        $this->withSession(['login_web_' . sha1('alguem') => 1])->get('/_teste/vagas');

        $this->assertSame(2, $execucoes);
    }

    /** @test */
    public function paginas_com_token_csrf_nunca_sao_partilhadas(): void
    {
        $execucoes = 0;

        Route::middleware('web')->get('/_teste/formulario', function () use (&$execucoes) {
            $execucoes++;

            return response('<html><head><meta name="csrf-token" content="abc"></head><body>ola</body></html>');
        });

        $this->get('/_teste/formulario');
        $this->get('/_teste/formulario');

        $this->assertSame(2, $execucoes, 'O token de uma sessao nao pode ser distribuido a toda a gente.');
    }

    /** @test */
    public function respostas_de_erro_nao_sao_guardadas(): void
    {
        $execucoes = 0;

        Route::middleware('web')->get('/_teste/em-falta', function () use (&$execucoes) {
            $execucoes++;

            return response('<html><body>nao existe</body></html>', 404);
        });

        $this->get('/_teste/em-falta')->assertNotFound();
        $this->get('/_teste/em-falta')->assertNotFound();

        $this->assertSame(2, $execucoes);
    }

    /** @test */
    public function com_o_disjuntor_aberto_serve_a_copia_de_emergencia(): void
    {
        $execucoes = 0;

        Route::middleware('web')->get('/_teste/vagas', function () use (&$execucoes) {
            $execucoes++;

            return response('<html><body>vagas de ontem</body></html>');
        });

        $this->get('/_teste/vagas');

        // O conteudo mudou (a copia normal deixou de valer) e, a seguir, o
        // MySQL caiu.
        app(PublicPageCache::class)->bumpVersion();

        $breaker = app(DatabaseCircuitBreaker::class);
        for ($i = 0; $i < 3; $i++) {
            $breaker->recordFailure();
        }
        $this->assertTrue($breaker->isOpen());

        $resposta = $this->get('/_teste/vagas');

        $resposta->assertOk();
        $resposta->assertSee('vagas de ontem');
        $this->assertSame('emergency', $resposta->headers->get(PublicPageCache::HEADER));
        $this->assertSame('no-store, private', $resposta->headers->get('Cache-Control'));
        $this->assertSame(1, $execucoes, 'Com o MySQL em baixo nao se chama o controlador.');
    }
}
