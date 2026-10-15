<?php

namespace App\Services;

use App\Exceptions\DatabaseUnavailableException;
use App\Support\DatabaseAvailability;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * O que responder quando uma excepcao diz que o MySQL nao esta disponivel.
 *
 * Por ordem de preferencia:
 *
 *   1. a copia guardada da propria pagina, servida com 200 — o visitante ve
 *      conteudo verdadeiro, ainda que possa nao ser o mais recente;
 *   2. a pagina estatica de indisponibilidade, com 503 e Retry-After.
 *
 * O ponto 2 vale tambem para formularios e para tudo o que escreve: um 503
 * honesto, nunca um sucesso simulado (ver seccao "Operacoes sensiveis" da
 * documentacao). So trata excepcoes de indisponibilidade — um erro de
 * programacao continua a dar 500, como deve ser.
 */
class DatabaseUnavailableResponder
{
    /** @var \App\Services\PublicPageCache */
    protected $pages;

    /** @var \App\Services\DatabaseCircuitBreaker */
    protected $breaker;

    /** @var \Illuminate\Contracts\Cache\Repository */
    protected $store;

    public function __construct(PublicPageCache $pages, DatabaseCircuitBreaker $breaker, Repository $store)
    {
        $this->pages = $pages;
        $this->breaker = $breaker;
        $this->store = $store;
    }

    public function render(Throwable $e, Request $request): ?Response
    {
        if (! DatabaseAvailability::isUnavailable($e)) {
            return null;
        }

        // Em desenvolvimento mostra-se o erro verdadeiro, senao nao havia
        // forma de o depurar.
        if (config('app.debug') && config('resilience.debug_bypass', true)) {
            return null;
        }

        $this->breaker->recordFailure();
        $this->report($e, $request);

        if ($request->isMethod('GET') || $request->isMethod('HEAD')) {
            if ($this->pages->shouldCache($request) && ($copy = $this->pages->any($request))) {
                return $this->pages->toResponse($copy, 'emergency')->prepare($request);
            }
        }

        $retryAfter = $this->retryAfter($e);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Serviço temporariamente indisponível. Tente novamente dentro de alguns instantes.',
            ], 503)->header('Retry-After', (string) $retryAfter);
        }

        return $this->offlineResponse($retryAfter);
    }

    /**
     * Pagina estatica em public/, lida directamente do disco: nao passa
     * pelo Blade, nao toca na base de dados e funciona mesmo que a
     * aplicacao esteja em mau estado.
     */
    public function offlineResponse(int $retryAfter): Response
    {
        $file = public_path((string) config('resilience.offline_page', 'database-unavailable.html'));

        $content = is_readable($file) ? (string) file_get_contents($file) : $this->minimalPage();

        return new \Illuminate\Http\Response($content, 503, [
            'Content-Type' => 'text/html; charset=UTF-8',
            'Retry-After' => (string) $retryAfter,
            'Cache-Control' => 'no-store, private',
            PublicPageCache::HEADER => 'offline',
        ]);
    }

    protected function retryAfter(Throwable $e): int
    {
        if ($e instanceof DatabaseUnavailableException) {
            return $e->retryAfter();
        }

        return $this->breaker->isOpen()
            ? $this->breaker->retryAfter()
            : (int) config('resilience.retry_after', 120);
    }

    /**
     * Uma linha por minuto, no maximo: durante uma falha longa todos os
     * pedidos passam por aqui.
     */
    protected function report(Throwable $e, Request $request): void
    {
        $interval = (int) config('resilience.log_interval', 60);

        if ($interval > 0 && ! $this->store->add('log:response', 1, $interval)) {
            return;
        }

        Log::warning('[resiliencia] Pedido servido sem base de dados.', [
            'url' => $request->fullUrl(),
            'metodo' => $request->method(),
            'erro' => $e->getMessage(),
        ]);
    }

    /**
     * Se o ficheiro estatico tiver desaparecido, ainda assim nao se mostra
     * um erro tecnico ao visitante.
     */
    protected function minimalPage(): string
    {
        return '<!doctype html><html lang="pt-PT"><head><meta charset="utf-8">'
            . '<meta name="viewport" content="width=device-width, initial-scale=1">'
            . '<title>Serviço temporariamente indisponível</title></head>'
            . '<body style="font-family:system-ui,sans-serif;max-width:40rem;margin:4rem auto;padding:0 1.5rem;color:#000">'
            . '<h1>Serviço temporariamente indisponível</h1>'
            . '<p>Estamos a realizar operações de recuperação do sistema. '
            . 'Por favor, tente novamente dentro de alguns instantes.</p>'
            . '</body></html>';
    }
}
