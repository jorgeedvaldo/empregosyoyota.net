<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Onde vivem as copias de seguranca de dados (DatabaseFallbackCache)
    |--------------------------------------------------------------------------
    |
    | Um store de cache proprio, separado do store por omissao, por duas
    | razoes: nunca depende do MySQL (e sempre ficheiros) e um
    | "php artisan cache:clear" nao apaga as copias de emergencia, que sao
    | precisamente aquilo que mantem o site de pe durante uma falha.
    |
    | So guarda um numero FIXO e pequeno de chaves — as listas "ultimas 50",
    | as categorias, as contagens, o feed, as definicoes — nunca uma por
    | vaga, artigo ou URL visitado. Essas vivem no snapshot unico (ver
    | abaixo), nao aqui.
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
    | Snapshot unico de emergencia (EmergencySnapshotService)
    |--------------------------------------------------------------------------
    |
    | UM ficheiro, sempre com o mesmo nome — nao um por vaga, artigo ou URL.
    | "max_jobs"/"max_articles" limitam o conteudo as entradas mais
    | recentes: um site com 8 anos de conteudo nao faz o ficheiro crescer
    | sem parar, so mantem sempre as ultimas N vagas/artigos disponiveis
    | para servir durante uma falha.
    |
    | Calculo do tamanho esperado (para af inar estes numeros): uma
    | descricao tipica ocupa 2-5 KB; com 300 vagas + 200 artigos o JSON
    | bruto anda pelos poucos MB, e o gzip (texto HTML repetitivo) costuma
    | reduzir isso para algumas centenas de KB — perfeitamente seguro de
    | carregar inteiro para memoria num pedido PHP num alojamento partilhado.
    | Se o conteudo do site for tipicamente maior do que isto, reduza os
    | limites antes de aumentar — nunca o inverso sem medir primeiro.
    |
    */

    'snapshot' => [
        'path' => env('RESILIENCE_SNAPSHOT_PATH', storage_path('app/emergency/public_backup.json.gz')),
        'lock_path' => env('RESILIENCE_SNAPSHOT_LOCK_PATH', storage_path('app/emergency/public_backup.lock')),
        'max_jobs' => (int) env('RESILIENCE_SNAPSHOT_MAX_JOBS', 300),
        'max_articles' => (int) env('RESILIENCE_SNAPSHOT_MAX_ARTICLES', 200),
    ],

    /*
    |--------------------------------------------------------------------------
    | Pagina de indisponibilidade
    |--------------------------------------------------------------------------
    |
    | Ultimo recurso: nem o MySQL responde, nem o snapshot tem a vaga,
    | artigo ou lista pedida. Ficheiro estatico em public/, servido com 503
    | e Retry-After.
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
