<?php

namespace App\Services;

use Illuminate\Contracts\Cache\Repository;

/**
 * Disjuntor das ligacoes ao MySQL.
 *
 * Sem isto, um MySQL em baixo e pior do que parece: cada pedido que chega
 * tenta abrir a sua ligacao, espera pelo erro e so depois desiste. Com 30
 * ligacoes no tecto, as tentativas falhadas sao parte do problema, nao
 * apenas o seu sintoma.
 *
 * Ao fim de N falhas o circuito abre e durante o arrefecimento ninguem
 * tenta ligar-se — excepto uma sonda de vez em quando, para dar pela
 * recuperacao. O estado vive na cache de ficheiros, partilhada por todos os
 * processos PHP do alojamento, e nunca no MySQL (seria circular).
 */
class DatabaseCircuitBreaker
{
    const KEY_FAILURES = 'breaker:failures';
    const KEY_OPEN_UNTIL = 'breaker:open_until';
    const KEY_PROBE = 'breaker:probe';

    /** @var \Illuminate\Contracts\Cache\Repository */
    protected $store;

    /** @var array */
    protected $config;

    /**
     * O instante de reabertura lido da cache, guardado em memoria para o
     * resto do pedido.
     *
     * Isto e consultado antes de cada consulta a base de dados; sem o
     * memo, uma pagina com vinte consultas fazia vinte leituras ao disco
     * para confirmar vinte vezes a mesma coisa.
     *
     * @var int|null
     */
    protected $openUntil;

    public function __construct(Repository $store, array $config = [])
    {
        $this->store = $store;
        $this->config = $config;
    }

    /**
     * Pode este pedido tentar falar com o MySQL?
     *
     * Com o circuito aberto devolve true no maximo uma vez por
     * "probe_interval" segundos: e a tentativa controlada de recuperacao,
     * para que dezenas de pedidos em simultaneo nao se atropelem todos a
     * testar a mesma coisa.
     */
    public function allowsAttempt(): bool
    {
        if (! $this->enabled()) {
            return true;
        }

        if (! $this->isOpen()) {
            return true;
        }

        return (bool) $this->store->add(self::KEY_PROBE, 1, $this->setting('probe_interval', 5));
    }

    public function isOpen(): bool
    {
        if (! $this->enabled()) {
            return false;
        }

        return $this->openUntil() > time();
    }

    public function openUntil(): int
    {
        if ($this->openUntil === null) {
            $this->openUntil = (int) $this->store->get(self::KEY_OPEN_UNTIL, 0);
        }

        return $this->openUntil;
    }

    /**
     * Quantos segundos faltam para o circuito voltar a fechar. Serve de
     * Retry-After nas respostas 503.
     */
    public function retryAfter(): int
    {
        return max(1, $this->openUntil() - time());
    }

    public function failures(): int
    {
        return (int) $this->store->get(self::KEY_FAILURES, 0);
    }

    /**
     * Mais uma falha de ligacao. Atingido o limite dentro da janela, o
     * circuito abre. Uma sonda que falhe volta a abri-lo, adiando a
     * proxima tentativa.
     */
    public function recordFailure(): void
    {
        if (! $this->enabled()) {
            return;
        }

        $failures = $this->failures() + 1;

        $this->store->put(self::KEY_FAILURES, $failures, $this->setting('window', 60));

        if ($failures >= $this->setting('failures', 3)) {
            $cooldown = $this->setting('cooldown', 30);
            $this->openUntil = time() + $cooldown;

            $this->store->put(self::KEY_OPEN_UNTIL, $this->openUntil, $cooldown + 60);
        }
    }

    /**
     * O MySQL respondeu: fecha o circuito e limpa a contagem.
     *
     * Le antes de escrever porque isto corre em todos os pedidos que vao a
     * base de dados, e no caminho normal — o unico que interessa ser rapido
     * — nao ha nada para limpar.
     */
    public function recordSuccess(): void
    {
        if (! $this->enabled()) {
            return;
        }

        if ($this->failures() === 0 && $this->openUntil() === 0) {
            return;
        }

        $this->reset();
    }

    /**
     * Repoe o disjuntor a mao (comando resilience:status --reset).
     */
    public function reset(): void
    {
        $this->openUntil = 0;

        $this->store->forget(self::KEY_FAILURES);
        $this->store->forget(self::KEY_OPEN_UNTIL);
        $this->store->forget(self::KEY_PROBE);
    }

    public function enabled(): bool
    {
        return (bool) $this->setting('enabled', true);
    }

    protected function setting(string $key, $default)
    {
        return array_key_exists($key, $this->config) ? $this->config[$key] : $default;
    }
}
