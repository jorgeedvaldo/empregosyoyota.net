<?php

namespace App\Services;

use Illuminate\Contracts\Cache\Repository;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Cache do HTML ja renderizado das paginas publicas de leitura.
 *
 * A cache de dados poupa consultas; esta poupa o pedido inteiro. Com uma
 * copia valida, o visitante e servido sem o controlador correr e, portanto,
 * sem se abrir ligacao nenhuma ao MySQL — que e o recurso escasso aqui.
 *
 * Como na cache de dados, ha duas copias: a normal (curta, invalidada
 * sempre que o conteudo muda) e a de emergencia (longa, usada so quando a
 * base de dados nao responde).
 *
 * O que nunca entra aqui: pedidos que nao sejam GET, o painel, a API, a
 * pesquisa, qualquer pedido de uma sessao autenticada e qualquer resposta
 * que traga cookies ou um token CSRF — uma cache partilhada com coisas
 * dessas dentro deixava de ser publica.
 */
class PublicPageCache
{
    const PREFIX_FRESH = 'page:';
    const PREFIX_EMERGENCY = 'page-emergency:';
    const KEY_VERSION = 'page:version';

    const HEADER = 'X-Resilience-Cache';

    /** @var \Illuminate\Contracts\Cache\Repository */
    protected $store;

    public function __construct(Repository $store)
    {
        $this->store = $store;
    }

    public function enabled(): bool
    {
        return (bool) config('resilience.page_cache.enabled', true);
    }

    /**
     * Versao do conteudo. Entra na chave das copias normais, por isso
     * basta incrementa-la para as invalidar todas de uma vez — ao publicar
     * uma vaga, por exemplo — sem ter de as enumerar.
     *
     * As copias de emergencia nao levam versao: sobrevivem de proposito.
     */
    public function version(): int
    {
        return (int) $this->store->rememberForever(self::KEY_VERSION, function () {
            return 1;
        });
    }

    public function bumpVersion(): void
    {
        $this->store->forever(self::KEY_VERSION, $this->version() + 1);
    }

    /**
     * Esta pagina pode ser guardada e servida a toda a gente?
     */
    public function shouldCache(Request $request): bool
    {
        if (! $this->enabled()) {
            return false;
        }

        if (! $request->isMethod('GET') && ! $request->isMethod('HEAD')) {
            return false;
        }

        if ($request->is(...(array) config('resilience.page_cache.except', []))) {
            return false;
        }

        return ! $this->looksAuthenticated($request);
    }

    /**
     * Sessao com marca de login. Le-se a sessao (ficheiros) em vez de
     * $request->user(), que iria buscar o utilizador a base de dados —
     * exactamente aquilo que se quer evitar.
     */
    protected function looksAuthenticated(Request $request): bool
    {
        if (! $request->hasSession()) {
            return false;
        }

        foreach (array_keys($request->session()->all()) as $key) {
            if (str_starts_with($key, 'login_') || str_starts_with($key, 'password_hash_')) {
                return true;
            }
        }

        return false;
    }

    /**
     * A resposta pode ser guardada?
     */
    public function isCacheable(Response $response): bool
    {
        if ($response->getStatusCode() !== 200) {
            return false;
        }

        if (count($response->headers->getCookies()) > 0) {
            return false;
        }

        $contentType = strtolower((string) $response->headers->get('Content-Type', 'text/html'));
        $allowed = (array) config('resilience.page_cache.content_types', ['text/html']);

        $matches = false;
        foreach ($allowed as $type) {
            if (str_contains($contentType, strtolower($type))) {
                $matches = true;
                break;
            }
        }

        if (! $matches) {
            return false;
        }

        $content = $response->getContent();

        if (! is_string($content) || $content === '') {
            return false;
        }

        if (strlen($content) > (int) config('resilience.page_cache.max_bytes', 1048576)) {
            return false;
        }

        // Rede de seguranca: hoje nenhuma pagina publica tem formularios
        // POST, mas se um dia tiver, o token dessa sessao nao pode ser
        // distribuido a todos os visitantes.
        return ! str_contains($content, 'csrf-token') && ! str_contains($content, 'name="_token"');
    }

    public function fresh(Request $request): ?array
    {
        return $this->read(self::PREFIX_FRESH . 'v' . $this->version() . ':' . $this->hash($request));
    }

    public function emergency(Request $request): ?array
    {
        return $this->read(self::PREFIX_EMERGENCY . $this->hash($request));
    }

    /**
     * A melhor copia disponivel, usada quando ja nao ha base de dados e
     * qualquer conteudo real vale mais do que uma pagina de erro.
     */
    public function any(Request $request): ?array
    {
        return $this->fresh($request) ?: $this->emergency($request);
    }

    public function put(Request $request, Response $response): void
    {
        if (! $this->isCacheable($response)) {
            return;
        }

        $payload = [
            'content' => $response->getContent(),
            'status' => $response->getStatusCode(),
            'content_type' => $response->headers->get('Content-Type', 'text/html; charset=UTF-8'),
            'stored_at' => time(),
        ];

        $hash = $this->hash($request);

        $this->store->put(
            self::PREFIX_FRESH . 'v' . $this->version() . ':' . $hash,
            $payload,
            max(1, (int) config('resilience.page_cache.ttl', 600))
        );

        $this->store->put(
            self::PREFIX_EMERGENCY . $hash,
            $payload,
            max(1, (int) config('resilience.page_cache.emergency_ttl', 604800))
        );
    }

    /**
     * Constroi a resposta a partir da copia guardada.
     *
     * @param  string  $state  "hit" (copia normal) ou "emergency".
     */
    public function toResponse(array $payload, string $state): Response
    {
        $response = new \Illuminate\Http\Response(
            $payload['content'],
            200,
            ['Content-Type' => $payload['content_type'] ?? 'text/html; charset=UTF-8']
        );

        $response->headers->set(self::HEADER, $state);
        $response->headers->set('X-Resilience-Age', (string) max(0, time() - (int) ($payload['stored_at'] ?? time())));

        if ($state === 'emergency') {
            // Conteudo possivelmente desactualizado: serve para o visitante
            // continuar a usar o site, nao para ficar guardado no browser
            // nem em intermediarios.
            $response->headers->set('Cache-Control', 'no-store, private');
        } elseif (config('resilience.page_cache.litespeed_header', false)) {
            $response->headers->set(
                'X-LiteSpeed-Cache-Control',
                'public,max-age=' . (int) config('resilience.page_cache.litespeed_ttl', 600)
            );
        }

        return $response;
    }

    /**
     * Identidade da pagina: metodo, dominio, caminho e so os parametros que
     * mudam o conteudo (config resilience.page_cache.query). Os restantes
     * — utm_source e companhia — sao ignorados de proposito, para nao
     * encherem o disco com copias do mesmo HTML.
     */
    public function hash(Request $request): string
    {
        $allowed = (array) config('resilience.page_cache.query', ['page']);

        $query = [];
        foreach ($allowed as $name) {
            $value = $request->query($name);

            if (is_scalar($value) && $value !== '') {
                $query[$name] = (string) $value;
            }
        }

        ksort($query);

        return sha1($request->getHost() . '|' . $request->getPathInfo() . '|' . http_build_query($query));
    }

    protected function read(string $key): ?array
    {
        $payload = $this->store->get($key);

        return is_array($payload) && isset($payload['content']) ? $payload : null;
    }
}
