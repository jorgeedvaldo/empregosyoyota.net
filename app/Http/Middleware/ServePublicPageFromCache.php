<?php

namespace App\Http\Middleware;

use App\Services\DatabaseCircuitBreaker;
use App\Services\PublicPageCache;
use Closure;
use Illuminate\Http\Request;

/**
 * Serve as paginas publicas a partir da copia guardada e, quando nao ha
 * copia, guarda a que o controlador acabou de produzir.
 *
 * Fica no fim do grupo "web" de proposito: assim ve a resposta antes de a
 * sessao lhe colar o cookie, e a copia guardada nunca leva cookies de
 * ninguem. O cookie da sessao de cada visitante continua a ser posto
 * normalmente pelo middleware de fora, tambem sobre as respostas que saem
 * daqui.
 */
class ServePublicPageFromCache
{
    /** @var \App\Services\PublicPageCache */
    protected $pages;

    /** @var \App\Services\DatabaseCircuitBreaker */
    protected $breaker;

    public function __construct(PublicPageCache $pages, DatabaseCircuitBreaker $breaker)
    {
        $this->pages = $pages;
        $this->breaker = $breaker;
    }

    public function handle(Request $request, Closure $next)
    {
        if (! $this->pages->shouldCache($request)) {
            return $next($request);
        }

        if ($copy = $this->pages->fresh($request)) {
            // prepare() trata do que a resposta guardada nao sabe sobre
            // este pedido — um HEAD, por exemplo, nao leva corpo.
            return $this->pages->toResponse($copy, 'hit')->prepare($request);
        }

        // MySQL em baixo: serve-se a ultima copia valida sem sequer tentar
        // a ligacao. Sem copia, o pedido segue — ha paginas que nao
        // precisam da base de dados e continuam a funcionar na mesma; se
        // precisarem, e o Handler que responde.
        if ($this->breaker->isOpen() && ($copy = $this->pages->emergency($request))) {
            return $this->pages->toResponse($copy, 'emergency')->prepare($request);
        }

        $response = $next($request);

        $this->pages->put($request, $response);

        return $response;
    }
}
