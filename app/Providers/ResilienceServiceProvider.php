<?php

namespace App\Providers;

use App\Exceptions\DatabaseUnavailableException;
use App\Services\DatabaseCircuitBreaker;
use App\Services\DatabaseFallbackCache;
use App\Services\DatabaseUnavailableResponder;
use App\Services\PublicPageCache;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Throwable;

/**
 * Liga as pecas da tolerancia a falhas do MySQL.
 *
 * Ver config/resilience.php e docs/resiliencia-mysql.md.
 */
class ResilienceServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(DatabaseCircuitBreaker::class, function () {
            return new DatabaseCircuitBreaker(
                $this->store(),
                (array) config('resilience.breaker', [])
            );
        });

        $this->app->singleton(DatabaseFallbackCache::class, function ($app) {
            return new DatabaseFallbackCache($this->store(), $app->make(DatabaseCircuitBreaker::class));
        });

        $this->app->singleton(PublicPageCache::class, function () {
            return new PublicPageCache($this->store());
        });

        $this->app->singleton(DatabaseUnavailableResponder::class, function ($app) {
            return new DatabaseUnavailableResponder(
                $app->make(PublicPageCache::class),
                $app->make(DatabaseCircuitBreaker::class),
                $this->store()
            );
        });
    }

    public function boot()
    {
        $this->blockQueriesWhileCircuitIsOpen();
    }

    /**
     * Com o disjuntor aberto, qualquer consulta falha de imediato em vez de
     * ir abrir mais uma ligacao ao MySQL — incluindo as que nao passam pela
     * cache (paginacao, pesquisa, painel).
     *
     * Registar o callback nao abre ligacao nenhuma: o PDO do Laravel so e
     * criado na primeira consulta.
     *
     * Nunca se aplica na consola, para o artisan continuar utilizavel
     * durante uma falha (migrate, tinker, os proprios comandos de
     * diagnostico).
     */
    protected function blockQueriesWhileCircuitIsOpen(): void
    {
        if ($this->app->runningInConsole() && ! $this->app->runningUnitTests()) {
            return;
        }

        if (! config('resilience.breaker.block_queries', true)) {
            return;
        }

        try {
            DB::beforeExecuting(function () {
                $breaker = $this->app->make(DatabaseCircuitBreaker::class);

                if (! $breaker->allowsAttempt()) {
                    throw DatabaseUnavailableException::circuitOpen($breaker->retryAfter());
                }
            });
        } catch (Throwable $e) {
            // Versao do Laravel sem beforeExecuting, ou ligacao por
            // configurar: o resto da protecao nao depende disto.
            report($e);
        }
    }

    /**
     * O store dedicado das copias de seguranca. Se ainda nao existir —
     * config em cache de antes desta alteracao, por exemplo — usa-se o
     * store por omissao em vez de rebentar o pedido inteiro.
     */
    protected function store(): Repository
    {
        $name = (string) config('resilience.store', 'resilience');

        try {
            return Cache::store($name);
        } catch (Throwable $e) {
            return Cache::store();
        }
    }
}
