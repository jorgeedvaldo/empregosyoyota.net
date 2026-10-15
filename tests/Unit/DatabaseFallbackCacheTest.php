<?php

namespace Tests\Unit;

use App\Exceptions\DatabaseUnavailableException;
use App\Services\DatabaseCircuitBreaker;
use App\Services\DatabaseFallbackCache;
use Illuminate\Cache\ArrayStore;
use Illuminate\Cache\Repository;
use Tests\Concerns\SimulatesMysqlFailures;
use Tests\TestCase;

/**
 * O coracao da coisa: duas copias de cada valor, e a de emergencia a
 * sobreviver a tudo o que faz sentido sobreviver.
 */
class DatabaseFallbackCacheTest extends TestCase
{
    use SimulatesMysqlFailures;

    /** @var \Illuminate\Cache\Repository */
    protected $store;

    /** @var \App\Services\DatabaseCircuitBreaker */
    protected $breaker;

    /** @var \App\Services\DatabaseFallbackCache */
    protected $cache;

    protected function setUp(): void
    {
        parent::setUp();

        $this->store = new Repository(new ArrayStore);
        $this->breaker = new DatabaseCircuitBreaker($this->store, [
            'enabled' => true,
            'failures' => 3,
            'window' => 60,
            'cooldown' => 30,
            'probe_interval' => 5,
        ]);
        $this->cache = new DatabaseFallbackCache($this->store, $this->breaker);
    }

    /** @test */
    public function com_o_mysql_bom_consulta_e_guarda_as_duas_copias(): void
    {
        $valor = $this->cache->remember('vagas', 60, fn () => ['vaga A']);

        $this->assertSame(['vaga A'], $valor);
        $this->assertTrue($this->cache->hasFresh('vagas'));
        $this->assertTrue($this->cache->hasEmergency('vagas'));
    }

    /** @test */
    public function com_a_cache_cheia_nao_volta_a_base_de_dados(): void
    {
        $this->cache->remember('vagas', 60, fn () => ['vaga A']);

        $consultas = 0;
        $valor = $this->cache->remember('vagas', 60, function () use (&$consultas) {
            $consultas++;

            return ['vaga B'];
        });

        $this->assertSame(0, $consultas, 'A segunda leitura nao pode tocar na base de dados.');
        $this->assertSame(['vaga A'], $valor);
    }

    /** @test */
    public function o_erro_1226_devolve_a_copia_de_emergencia(): void
    {
        $this->cache->remember('vagas', 60, fn () => ['vaga A']);

        // A copia normal expira; a de emergencia fica.
        $this->store->forget(DatabaseFallbackCache::PREFIX_FRESH . 'vagas');

        $valor = $this->cache->remember('vagas', 60, function () {
            throw $this->maxUserConnectionsException();
        });

        $this->assertSame(['vaga A'], $valor);
    }

    /** @test */
    public function sem_copia_nenhuma_assume_a_indisponibilidade(): void
    {
        $this->expectException(DatabaseUnavailableException::class);

        $this->cache->remember('nunca-visto', 60, function () {
            throw $this->maxUserConnectionsException();
        });
    }

    /** @test */
    public function invalidar_o_conteudo_nao_deita_fora_a_rede_de_seguranca(): void
    {
        $this->cache->remember('vagas', 60, fn () => ['vaga A']);

        // E o que acontece ao publicar uma vaga nova.
        $this->cache->forget('vagas');

        $this->assertFalse($this->cache->hasFresh('vagas'));
        $this->assertTrue($this->cache->hasEmergency('vagas'), 'A copia de emergencia tem de sobreviver a invalidacao.');
    }

    /** @test */
    public function um_erro_de_sql_nosso_continua_a_rebentar(): void
    {
        $this->expectException(\Illuminate\Database\QueryException::class);

        $this->cache->remember('vagas', 60, function () {
            throw $this->syntaxErrorException();
        });
    }

    /** @test */
    public function as_falhas_acabam_por_abrir_o_disjuntor(): void
    {
        $this->cache->remember('vagas', 60, fn () => ['vaga A']);
        $this->store->forget(DatabaseFallbackCache::PREFIX_FRESH . 'vagas');

        for ($i = 0; $i < 3; $i++) {
            $this->cache->remember('vagas', 60, function () {
                throw $this->maxUserConnectionsException();
            });
            $this->store->forget(DatabaseFallbackCache::PREFIX_FRESH . 'vagas');
        }

        $this->assertTrue($this->breaker->isOpen());
    }

    /** @test */
    public function com_o_disjuntor_aberto_nem_se_tenta_a_ligacao(): void
    {
        $this->cache->remember('vagas', 60, fn () => ['vaga A']);
        $this->store->forget(DatabaseFallbackCache::PREFIX_FRESH . 'vagas');

        $this->breaker->recordFailure();
        $this->breaker->recordFailure();
        $this->breaker->recordFailure();

        // A primeira leitura ainda e a sonda de recuperacao: falha, e o
        // circuito volta a fechar-se a novas tentativas.
        $this->cache->remember('vagas', 60, function () {
            throw $this->maxUserConnectionsException();
        });

        $tentativas = 0;
        $valor = $this->cache->remember('vagas', 60, function () use (&$tentativas) {
            $tentativas++;

            return ['vaga C'];
        });

        $this->assertSame(0, $tentativas, 'Com o disjuntor aberto nao se abre ligacao nenhuma.');
        $this->assertSame(['vaga A'], $valor);
    }

    /** @test */
    public function quando_o_mysql_recupera_volta_tudo_ao_normal_sozinho(): void
    {
        $this->cache->remember('vagas', 60, fn () => ['vaga A']);
        $this->store->forget(DatabaseFallbackCache::PREFIX_FRESH . 'vagas');

        // Falha: serve a copia antiga.
        $this->assertSame(['vaga A'], $this->cache->remember('vagas', 60, function () {
            throw $this->maxUserConnectionsException();
        }));

        // Recuperacao: nova consulta, cache actualizada, disjuntor fechado.
        $valor = $this->cache->remember('vagas', 60, fn () => ['vaga A', 'vaga B']);

        $this->assertSame(['vaga A', 'vaga B'], $valor);
        $this->assertSame(['vaga A', 'vaga B'], $this->cache->emergency('vagas'));
        $this->assertFalse($this->breaker->isOpen());
        $this->assertSame(0, $this->breaker->failures());
    }

    /** @test */
    public function guardar_null_nao_e_confundido_com_nao_ter_nada(): void
    {
        $consultas = 0;

        $primeiro = $this->cache->remember('vazio', 60, function () use (&$consultas) {
            $consultas++;

            return null;
        });

        $segundo = $this->cache->remember('vazio', 60, function () use (&$consultas) {
            $consultas++;

            return 'outra coisa';
        });

        $this->assertNull($primeiro);
        $this->assertNull($segundo);
        $this->assertSame(1, $consultas);
    }
}
