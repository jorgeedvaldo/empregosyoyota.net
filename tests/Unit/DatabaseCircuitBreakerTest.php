<?php

namespace Tests\Unit;

use App\Services\DatabaseCircuitBreaker;
use Illuminate\Cache\ArrayStore;
use Illuminate\Cache\Repository;
use PHPUnit\Framework\TestCase;

/**
 * O disjuntor e o que impede que um MySQL em baixo seja alimentado por
 * dezenas de tentativas de ligacao a cada segundo.
 */
class DatabaseCircuitBreakerTest extends TestCase
{
    protected function breaker(array $config = []): DatabaseCircuitBreaker
    {
        return new DatabaseCircuitBreaker(
            new Repository(new ArrayStore),
            array_merge([
                'enabled' => true,
                'failures' => 3,
                'window' => 60,
                'cooldown' => 30,
                'probe_interval' => 5,
            ], $config)
        );
    }

    /** @test */
    public function comeca_fechado(): void
    {
        $breaker = $this->breaker();

        $this->assertFalse($breaker->isOpen());
        $this->assertTrue($breaker->allowsAttempt());
    }

    /** @test */
    public function abre_ao_fim_das_falhas_configuradas(): void
    {
        $breaker = $this->breaker(['failures' => 3]);

        $breaker->recordFailure();
        $breaker->recordFailure();
        $this->assertFalse($breaker->isOpen(), 'Duas falhas ainda nao chegam para abrir.');

        $breaker->recordFailure();
        $this->assertTrue($breaker->isOpen());
        $this->assertSame(3, $breaker->failures());
    }

    /** @test */
    public function com_o_circuito_aberto_so_passa_uma_sonda_de_cada_vez(): void
    {
        $breaker = $this->breaker(['failures' => 1, 'probe_interval' => 60]);
        $breaker->recordFailure();

        // Primeiro pedido depois da falha: vai sondar a recuperacao.
        $this->assertTrue($breaker->allowsAttempt());

        // Os seguintes, no mesmo intervalo, nao tentam ligar-se — e isto
        // que evita que 30 pedidos em paralelo esgotem outra vez as
        // ligacoes enquanto o MySQL ainda esta a recuperar.
        $this->assertFalse($breaker->allowsAttempt());
        $this->assertFalse($breaker->allowsAttempt());
    }

    /** @test */
    public function um_sucesso_fecha_o_circuito(): void
    {
        $breaker = $this->breaker(['failures' => 1]);
        $breaker->recordFailure();
        $this->assertTrue($breaker->isOpen());

        $breaker->recordSuccess();

        $this->assertFalse($breaker->isOpen());
        $this->assertSame(0, $breaker->failures());
        $this->assertTrue($breaker->allowsAttempt());
    }

    /** @test */
    public function uma_sonda_falhada_volta_a_adiar_a_tentativa_seguinte(): void
    {
        $breaker = $this->breaker(['failures' => 1, 'cooldown' => 30]);

        $breaker->recordFailure();
        $primeiraReabertura = $breaker->openUntil();

        // A sonda passa, falha, e o circuito volta a abrir mais a frente.
        $this->assertTrue($breaker->allowsAttempt());
        sleep(1);
        $breaker->recordFailure();

        $this->assertTrue($breaker->isOpen());
        $this->assertGreaterThan($primeiraReabertura, $breaker->openUntil());
    }

    /** @test */
    public function o_arrefecimento_acaba_sozinho(): void
    {
        $breaker = $this->breaker(['failures' => 1, 'cooldown' => 1]);
        $breaker->recordFailure();

        $this->assertTrue($breaker->isOpen());

        sleep(2);

        $this->assertFalse($breaker->isOpen(), 'Passado o arrefecimento volta a tentar-se sozinho.');
        $this->assertTrue($breaker->allowsAttempt());
    }

    /** @test */
    public function desligado_nunca_trava_nada(): void
    {
        $breaker = $this->breaker(['enabled' => false, 'failures' => 1]);

        $breaker->recordFailure();
        $breaker->recordFailure();

        $this->assertFalse($breaker->isOpen());
        $this->assertTrue($breaker->allowsAttempt());
    }

    /** @test */
    public function pode_ser_reposto_a_mao(): void
    {
        $breaker = $this->breaker(['failures' => 1]);
        $breaker->recordFailure();

        $breaker->reset();

        $this->assertFalse($breaker->isOpen());
        $this->assertSame(0, $breaker->failures());
    }
}
