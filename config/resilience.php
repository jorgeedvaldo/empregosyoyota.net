<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Onde vivem as copias de seguranca
    |--------------------------------------------------------------------------
    |
    | Um store de cache proprio, separado do store por omissao, por duas
    | razoes: nunca depende do MySQL (e sempre ficheiros) e um
    | "php artisan cache:clear" nao apaga as copias de emergencia, que sao
    | precisamente aquilo que mantem o site de pe durante uma falha.
    |
    */

    'store' => env('RESILIENCE_CACHE_STORE', 'resilience'),

    /*
    |--------------------------------------------------------------------------
    | Cache de dados (DatabaseFallbackCache)
    |--------------------------------------------------------------------------
    |
    | "ttl" e a vida da copia normal — enquanto for valida nao se vai a base
    | de dados. "emergency_ttl" e a vida da ultima copia valida conhecida,
    | usada apenas quando o MySQL nao responde. Cada chamada pode indicar o
    | seu proprio ttl; estes valores sao o que se usa quando nao indica.
    |
    */

    'data' => [
        'ttl' => (int) env('RESILIENCE_DATA_TTL', 1440),            // 24 minutos
        'forever_ttl' => (int) env('RESILIENCE_FOREVER_TTL', 2592000), // 30 dias
        'emergency_ttl' => (int) env('RESILIENCE_EMERGENCY_TTL', 604800), // 7 dias
    ],

    /*
    |--------------------------------------------------------------------------
    | Disjuntor (circuit breaker)
    |--------------------------------------------------------------------------
    |
    | Ao fim de "failures" falhas de ligacao dentro de "window" segundos, o
    | circuito abre durante "cooldown" segundos: durante esse tempo nenhum
    | pedido tenta ligar-se ao MySQL (e portanto nao consome ligacoes das 30
    | disponiveis), excepto uma sonda a cada "probe_interval" segundos, que
    | serve para perceber se a base de dados ja recuperou.
    |
    | "block_queries" trava qualquer consulta enquanto o circuito estiver
    | aberto — incluindo as que nao passam pela cache. Nunca se aplica a
    | comandos de consola, para o artisan continuar utilizavel durante uma
    | falha (migrate, tinker, etc.).
    |
    */

    'breaker' => [
        'enabled' => (bool) env('RESILIENCE_BREAKER', true),
        'failures' => (int) env('RESILIENCE_BREAKER_FAILURES', 3),
        'window' => (int) env('RESILIENCE_BREAKER_WINDOW', 60),
        'cooldown' => (int) env('RESILIENCE_BREAKER_COOLDOWN', 30),
        'probe_interval' => (int) env('RESILIENCE_BREAKER_PROBE', 5),
        'block_queries' => (bool) env('RESILIENCE_BLOCK_QUERIES', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache de HTML das paginas publicas
    |--------------------------------------------------------------------------
    |
    | Guarda a resposta ja renderizada das paginas publicas de leitura. Com
    | uma copia valida, o pedido nem chega ao controlador — e portanto nao
    | abre ligacao ao MySQL.
    |
    | A chave e construida a partir do caminho e apenas dos parametros
    | indicados em "query" (os restantes, como utm_source, sao ignorados de
    | proposito para nao multiplicar copias do mesmo conteudo). Paginas cujo
    | conteudo dependa de outros parametros — a pesquisa, por exemplo —
    | pertencem a lista "except".
    |
    */

    'page_cache' => [
        'enabled' => (bool) env('RESILIENCE_PAGE_CACHE', true),
        'ttl' => (int) env('RESILIENCE_PAGE_TTL', 600),                   // 10 minutos
        'emergency_ttl' => (int) env('RESILIENCE_PAGE_EMERGENCY_TTL', 604800), // 7 dias
        'max_bytes' => (int) env('RESILIENCE_PAGE_MAX_BYTES', 1048576),   // 1 MB por pagina

        // Parametros que fazem parte da identidade da pagina.
        'query' => ['page'],

        // Nunca sao guardadas: painel, API, ferramentas interactivas e a
        // pesquisa (resultados variam com cada termo escrito).
        'except' => [
            'admin',
            'admin/*',
            'api/*',
            'livewire/*',
            'filament/*',
            'linkstorage',
            'pesquisar',
            'dashboard',
            'en/dashboard',
        ],

        'content_types' => ['text/html', 'application/xml', 'text/xml'],

        // Cabecalho X-LiteSpeed-Cache-Control nas paginas publicas. So faz
        // algum efeito se o modulo LSCache estiver ligado na conta; desligado
        // por omissao para nao se prometer aquilo que o alojamento pode nao ter.
        'litespeed_header' => (bool) env('RESILIENCE_LITESPEED_HEADER', false),
        'litespeed_ttl' => (int) env('RESILIENCE_LITESPEED_TTL', 600),
    ],

    /*
    |--------------------------------------------------------------------------
    | Pagina de indisponibilidade
    |--------------------------------------------------------------------------
    |
    | Ultimo recurso: nao ha copia nenhuma da pagina pedida e o MySQL nao
    | responde. Ficheiro estatico em public/, servido com 503 e Retry-After.
    |
    */

    'offline_page' => 'database-unavailable.html',
    'retry_after' => (int) env('RESILIENCE_RETRY_AFTER', 120),

    /*
    |--------------------------------------------------------------------------
    | Em desenvolvimento, mostrar o erro verdadeiro
    |--------------------------------------------------------------------------
    |
    | Com APP_DEBUG=true o erro original do MySQL aparece tal e qual, em vez
    | da pagina de indisponibilidade — caso contrario era impossivel perceber
    | o que se passou. Em producao (APP_DEBUG=false) nada disto se aplica.
    |
    */

    'debug_bypass' => (bool) env('RESILIENCE_DEBUG_BYPASS', true),

    /*
    |--------------------------------------------------------------------------
    | Registo no log
    |--------------------------------------------------------------------------
    |
    | Numa falha prolongada cada pedido daria uma linha de log. Grava-se no
    | maximo uma linha por chave a cada "log_interval" segundos.
    |
    */

    'log_interval' => (int) env('RESILIENCE_LOG_INTERVAL', 60),

];
