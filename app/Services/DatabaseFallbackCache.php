<?php

namespace App\Services;

use App\Exceptions\DatabaseUnavailableException;
use App\Support\DatabaseAvailability;
use Closure;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Cache de dados com copia de emergencia.
 *
 * Cada valor e guardado duas vezes:
 *
 *   - a copia normal, de vida curta, e a que serve o dia-a-dia e expira
 *     para o conteudo nao envelhecer;
 *   - a copia de emergencia, de vida longa, que so e lida quando o MySQL
 *     nao responde. Nao expira com a normal nem e apagada quando o
 *     conteudo muda, porque o seu trabalho e exactamente esse: ser a ultima
 *     coisa de pe quando nao ha base de dados.
 *
 * Com o MySQL vivo e a copia normal expirada, consulta-se e reescrevem-se
 * as duas. Quando o MySQL volta, isto acontece sozinho no primeiro pedido
 * que apanhar a copia normal expirada — nao ha nada para reiniciar a mao.
 */
class DatabaseFallbackCache
{
    const PREFIX_FRESH = 'data:';
    const PREFIX_EMERGENCY = 'emergency:';

    /** @var \Illuminate\Contracts\Cache\Repository */
    protected $store;

    /** @var \App\Services\DatabaseCircuitBreaker */
    protected $breaker;

    public function __construct(Repository $store, DatabaseCircuitBreaker $breaker)
    {
        $this->store = $store;
        $this->breaker = $breaker;
    }

    /**
     * @param  string   $key           Chave logica (ex.: "latest_jobs_50").
     * @param  int      $ttl           Vida da copia normal, em segundos.
     * @param  \Closure $callback      Como obter os dados na base de dados.
     * @param  int|null $emergencyTtl  Vida da copia de emergencia.
     * @return mixed
     *
     * @throws \App\Exceptions\DatabaseUnavailableException
     */
    public function remember(string $key, int $ttl, Closure $callback, int $emergencyTtl = null)
    {
        $fresh = $this->read(self::PREFIX_FRESH . $key);

        if ($fresh['hit']) {
            return $fresh['value'];
        }

        // Disjuntor aberto: nem se tenta a ligacao, vai-se directo a
        // ultima copia valida.
        if (! $this->breaker->allowsAttempt()) {
            return $this->emergencyOrFail($key, DatabaseUnavailableException::circuitOpen($this->breaker->retryAfter()));
        }

        try {
            $value = $callback();
        } catch (Throwable $e) {
            if (! DatabaseAvailability::isUnavailable($e)) {
                throw $e;
            }

            $this->breaker->recordFailure();
            $this->report($key, $e);

            return $this->emergencyOrFail($key, DatabaseUnavailableException::from($e, $this->retryAfter()));
        }

        $this->breaker->recordSuccess();
        $this->put($key, $value, $ttl, $emergencyTtl);

        return $value;
    }

    /**
     * Para valores que so mudam quando alguem os muda — e que nesse momento
     * chamam forget(). Nao e um "para sempre" literal: ao fim de 30 dias
     * vai-se confirmar a base de dados, para uma entrada esquecida nao
     * ficar errada indefinidamente.
     */
    public function rememberForever(string $key, Closure $callback, int $emergencyTtl = null)
    {
        return $this->remember($key, $this->foreverTtl(), $callback, $emergencyTtl);
    }

    /**
     * Guarda as duas copias.
     */
    public function put(string $key, $value, int $ttl, int $emergencyTtl = null): void
    {
        $payload = ['value' => $value];

        $this->store->put(self::PREFIX_FRESH . $key, $payload, max(1, $ttl));
        $this->store->put(
            self::PREFIX_EMERGENCY . $key,
            $payload,
            max(1, $emergencyTtl === null ? $this->emergencyTtl() : $emergencyTtl)
        );
    }

    /**
     * Invalida a copia normal — o conteudo mudou e deve ser lido outra vez.
     *
     * A copia de emergencia fica. E este o ponto em que esta cache difere
     * da cache normal do Laravel: apagar a emergencia ao editar uma vaga
     * deixava o site sem rede de seguranca precisamente no momento mais
     * movimentado.
     */
    public function forget(string $key): void
    {
        $this->store->forget(self::PREFIX_FRESH . $key);
    }

    /**
     * Apaga tambem a copia de emergencia (limpeza, testes).
     */
    public function purge(string $key): void
    {
        $this->store->forget(self::PREFIX_FRESH . $key);
        $this->store->forget(self::PREFIX_EMERGENCY . $key);
    }

    public function hasFresh(string $key): bool
    {
        return $this->read(self::PREFIX_FRESH . $key)['hit'];
    }

    public function hasEmergency(string $key): bool
    {
        return $this->read(self::PREFIX_EMERGENCY . $key)['hit'];
    }

    public function emergency(string $key)
    {
        return $this->read(self::PREFIX_EMERGENCY . $key)['value'];
    }

    protected function emergencyOrFail(string $key, DatabaseUnavailableException $e)
    {
        $copy = $this->read(self::PREFIX_EMERGENCY . $key);

        if ($copy['hit']) {
            return $copy['value'];
        }

        throw $e;
    }

    /**
     * O valor guardado vai dentro de um array para se distinguir "guardei
     * null" de "nao ha nada guardado".
     *
     * @return array{hit: bool, value: mixed}
     */
    protected function read(string $storeKey): array
    {
        $payload = $this->store->get($storeKey);

        if (is_array($payload) && array_key_exists('value', $payload)) {
            return ['hit' => true, 'value' => $payload['value']];
        }

        return ['hit' => false, 'value' => null];
    }

    /**
     * Uma linha de log por chave a cada "log_interval" segundos. Numa falha
     * de uma hora, cada pedido daria a sua linha e o log so servia para
     * encher o disco da conta.
     */
    protected function report(string $key, Throwable $e): void
    {
        $interval = (int) config('resilience.log_interval', 60);

        if ($interval > 0 && ! $this->store->add('log:' . $key, 1, $interval)) {
            return;
        }

        Log::warning('[resiliencia] MySQL indisponivel; a usar copia de emergencia.', [
            'chave' => $key,
            'erro' => $e->getMessage(),
        ]);
    }

    protected function emergencyTtl(): int
    {
        return (int) config('resilience.data.emergency_ttl', 604800);
    }

    protected function foreverTtl(): int
    {
        return (int) config('resilience.data.forever_ttl', 2592000);
    }

    protected function retryAfter(): int
    {
        return (int) config('resilience.retry_after', 120);
    }
}
