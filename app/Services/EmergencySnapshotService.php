<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Uma única cópia de emergência dos dados públicos, em vez de um ficheiro
 * por vaga/artigo/URL visitado.
 *
 * Fica em dois caminhos fixos, que nunca mudam de nome por conteúdo:
 *
 *   storage/app/emergency/public_backup.json.gz   (o próprio snapshot)
 *   storage/app/emergency/public_backup.lock      (o bloqueio exclusivo)
 *
 * Guarda só o que uma página pública precisa para se renderizar sem MySQL:
 * as vagas e artigos mais recentes (por omissão, 300 e 200 — ver
 * config/resilience.php), e a lista de categorias. Está limitado de
 * propósito: um JSON com TODO o conteúdo publicado desde 2018 cresceria
 * sem parar; um JSON com "o suficiente para o site continuar a funcionar"
 * fica do mesmo tamanho não importa há quantos anos o site existe.
 *
 * Cada vaga/artigo gravado ou apagado corrige só a SUA entrada (não
 * reconstrói a lista toda a partir do MySQL) e volta a escrever o ficheiro
 * inteiro de forma atómica — ficheiro temporário, depois rename(), que no
 * mesmo sistema de ficheiros é instantâneo e indivisível: quem estiver a
 * ler nunca vê a escrita a meio.
 *
 * Duas escritas em simultâneo (dois processos PHP-FPM a gravar vagas ao
 * mesmo tempo) são evitadas com flock() não bloqueante: quem não
 * conseguir o bloqueio não espera — poe a sua alteração numa fila curta
 * ("pendentes") e sai; a proxima escrita bem sucedida aplica as
 * alterações pendentes primeiro, antes da sua própria. A cópia em disco
 * nunca fica a meio escrita, e nenhuma alteração se perde, só adia.
 */
class EmergencySnapshotService
{
    const SCHEMA_VERSION = 1;

    /** Chave da fila de alteracoes por aplicar, na cache por omissao
     *  (ficheiros) — um unico ficheiro, reutilizado, nao um por pendente. */
    const PENDING_CACHE_KEY = 'emergency_snapshot_pending';

    protected string $path;
    protected string $lockPath;
    protected string $tmpPath;

    protected int $maxJobs;
    protected int $maxArticles;

    /** Memoriza a leitura do snapshot durante o pedido: varias chamadas a
     *  findJob()/findArticle() no mesmo pedido nao leem o disco outra vez. */
    protected ?array $memo = null;

    public function __construct(?string $path = null, ?string $lockPath = null, ?int $maxJobs = null, ?int $maxArticles = null)
    {
        $this->path = $path ?? (string) config('resilience.snapshot.path', storage_path('app/emergency/public_backup.json.gz'));
        $this->lockPath = $lockPath ?? (string) config('resilience.snapshot.lock_path', storage_path('app/emergency/public_backup.lock'));
        $this->tmpPath = $this->path . '.tmp';
        $this->maxJobs = $maxJobs ?? (int) config('resilience.snapshot.max_jobs', 300);
        $this->maxArticles = $maxArticles ?? (int) config('resilience.snapshot.max_articles', 200);
    }

    // =========================================================================
    // Leitura
    // =========================================================================

    public function findJob($slugOrId): ?array
    {
        $data = $this->load();

        if ($data === null) {
            return null;
        }

        $id = is_numeric($slugOrId) ? (int) $slugOrId : ($data['job_slug_index'][$slugOrId] ?? null);

        return $id !== null ? ($data['jobs'][$id] ?? null) : null;
    }

    public function findArticle($slugOrId): ?array
    {
        $data = $this->load();

        if ($data === null) {
            return null;
        }

        $id = is_numeric($slugOrId) ? (int) $slugOrId : ($data['article_slug_index'][$slugOrId] ?? null);

        return $id !== null ? ($data['articles'][$id] ?? null) : null;
    }

    /**
     * As N mais recentes, por id descendente — igual ao que
     * Job::getCachedLatest()/Article::getCachedLatest() devolviam.
     */
    public function latestJobs(int $limit = 50): array
    {
        $data = $this->load();

        if ($data === null) {
            return [];
        }

        $ids = array_keys($data['jobs']);
        rsort($ids, SORT_NUMERIC);

        return array_map(fn ($id) => $data['jobs'][$id], array_slice($ids, 0, $limit));
    }

    public function latestArticles(int $limit = 50): array
    {
        $data = $this->load();

        if ($data === null) {
            return [];
        }

        $ids = array_keys($data['articles']);
        rsort($ids, SORT_NUMERIC);

        return array_map(fn ($id) => $data['articles'][$id], array_slice($ids, 0, $limit));
    }

    public function categories(): array
    {
        return $this->load()['categories'] ?? [];
    }

    public function jobsCount(): int
    {
        return count($this->load()['jobs'] ?? []);
    }

    public function articlesCount(): int
    {
        return count($this->load()['articles'] ?? []);
    }

    public function exists(): bool
    {
        return is_file($this->path);
    }

    /**
     * Metricas simples: tamanho, idade, integridade — para
     * "resilience:status" e para confirmar que o snapshot esta vivo.
     */
    public function meta(): array
    {
        if (! is_file($this->path)) {
            return [
                'existe' => false,
                'integro' => false,
            ];
        }

        $data = $this->load();
        $geradoEm = $data['generated_at'] ?? null;

        return [
            'existe' => true,
            'integro' => $data !== null,
            'bytes' => filesize($this->path) ?: 0,
            'gerado_em' => $geradoEm,
            'idade_segundos' => $geradoEm ? max(0, time() - strtotime($geradoEm)) : null,
            'vagas' => $data ? count($data['jobs']) : 0,
            'artigos' => $data ? count($data['articles']) : 0,
            'categorias' => $data ? count($data['categories']) : 0,
            'pendentes' => count($this->pendingOps()),
        ];
    }

    // =========================================================================
    // Escrita — uma entrada de cada vez, nunca uma reconstrucao completa
    // =========================================================================

    public function upsertJob(array $job): bool
    {
        return $this->applyOrQueue(['op' => 'upsert_job', 'data' => $job]);
    }

    public function removeJob(int $id): bool
    {
        return $this->applyOrQueue(['op' => 'remove_job', 'id' => $id]);
    }

    public function upsertArticle(array $article): bool
    {
        return $this->applyOrQueue(['op' => 'upsert_article', 'data' => $article]);
    }

    public function removeArticle(int $id): bool
    {
        return $this->applyOrQueue(['op' => 'remove_article', 'id' => $id]);
    }

    public function replaceCategories(array $categories): bool
    {
        return $this->applyOrQueue(['op' => 'replace_categories', 'data' => $categories]);
    }

    /**
     * Reconstroi o snapshot inteiro a partir do MySQL. E a unica operacao
     * "pesada" deste servico — usada so para gerar a primeira copia valida
     * (comando resilience:snapshot:rebuild) ou numa reconciliacao manual,
     * nunca automaticamente a cada escrita.
     */
    public function rebuild(array $jobs, array $articles, array $categories): bool
    {
        return $this->withLock(function () use ($jobs, $articles, $categories) {
            $data = $this->emptySnapshot();

            foreach ($jobs as $job) {
                $this->putJob($data, $job);
            }
            foreach ($articles as $article) {
                $this->putArticle($data, $article);
            }
            $data['categories'] = array_values($categories);

            return $data;
        }, clearPendingOnSuccess: true);
    }

    // =========================================================================
    // Mecanica do bloqueio + fila de pendentes
    // =========================================================================

    /**
     * Tenta aplicar a alteracao imediatamente (bloqueio exclusivo nao
     * bloqueante). Se outro processo estiver a escrever agora, a alteracao
     * fica na fila de pendentes e sai: nao ha espera, nao ha ligacao extra
     * nenhuma, so a garantia de que a proxima escrita bem sucedida a inclui.
     *
     * @return bool  true se ficou gravada agora; false se ficou pendente.
     */
    protected function applyOrQueue(array $op): bool
    {
        $applied = $this->withLock(function () use ($op) {
            $data = $this->load() ?? $this->emptySnapshot();

            // Primeiro as alteracoes que ficaram para tras doutras vezes
            // que o bloqueio estava ocupado, depois esta.
            foreach ($this->pendingOps() as $pending) {
                $this->applyOp($data, $pending);
            }
            $this->applyOp($data, $op);

            return $data;
        }, clearPendingOnSuccess: true);

        if (! $applied) {
            $this->enqueuePending($op);
        }

        return $applied;
    }

    protected function applyOp(array &$data, array $op): void
    {
        switch ($op['op']) {
            case 'upsert_job':
                $this->putJob($data, $op['data']);
                break;
            case 'remove_job':
                $this->dropJob($data, $op['id']);
                break;
            case 'upsert_article':
                $this->putArticle($data, $op['data']);
                break;
            case 'remove_article':
                $this->dropArticle($data, $op['id']);
                break;
            case 'replace_categories':
                $data['categories'] = array_values($op['data']);
                break;
        }
    }

    protected function putJob(array &$data, array $job): void
    {
        $id = (int) $job['id'];

        // Slug pode ter mudado (raro, mas o generateSlug() permite):
        // remove o indice antigo antes de escrever o novo.
        if (isset($data['jobs'][$id]['slug'])) {
            unset($data['job_slug_index'][$data['jobs'][$id]['slug']]);
        }

        $data['jobs'][$id] = $job;
        if (! empty($job['slug'])) {
            $data['job_slug_index'][$job['slug']] = $id;
        }

        $this->evictOldest($data['jobs'], $data['job_slug_index'], $this->maxJobs);
    }

    protected function dropJob(array &$data, int $id): void
    {
        if (isset($data['jobs'][$id]['slug'])) {
            unset($data['job_slug_index'][$data['jobs'][$id]['slug']]);
        }
        unset($data['jobs'][$id]);
    }

    protected function putArticle(array &$data, array $article): void
    {
        $id = (int) $article['id'];

        if (isset($data['articles'][$id]['slug'])) {
            unset($data['article_slug_index'][$data['articles'][$id]['slug']]);
        }

        $data['articles'][$id] = $article;
        if (! empty($article['slug'])) {
            $data['article_slug_index'][$article['slug']] = $id;
        }

        $this->evictOldest($data['articles'], $data['article_slug_index'], $this->maxArticles);
    }

    protected function dropArticle(array &$data, int $id): void
    {
        if (isset($data['articles'][$id]['slug'])) {
            unset($data['article_slug_index'][$data['articles'][$id]['slug']]);
        }
        unset($data['articles'][$id]);
    }

    /**
     * Mantem o snapshot limitado: ao ultrapassar o maximo, cai a entrada
     * mais antiga (o id mais baixo). E isto que garante um ficheiro de
     * tamanho fixo nao importa ha quantos anos o site existe.
     */
    protected function evictOldest(array &$records, array &$slugIndex, int $max): void
    {
        while (count($records) > $max) {
            $oldest = min(array_keys($records));

            if (isset($records[$oldest]['slug'])) {
                unset($slugIndex[$records[$oldest]['slug']]);
            }
            unset($records[$oldest]);
        }
    }

    /**
     * @param  \Closure $mutator  Recebe nada, devolve o snapshot completo
     *                            (array) a gravar.
     */
    protected function withLock(\Closure $mutator, bool $clearPendingOnSuccess): bool
    {
        $this->ensureDirectory();

        $handle = @fopen($this->lockPath, 'c');
        if ($handle === false) {
            Log::warning('[resiliencia] nao foi possivel abrir o ficheiro de bloqueio do snapshot.', ['caminho' => $this->lockPath]);

            return false;
        }

        if (! flock($handle, LOCK_EX | LOCK_NB)) {
            fclose($handle);

            return false;
        }

        try {
            $data = $mutator();
            $data['version'] = self::SCHEMA_VERSION;
            $data['generated_at'] = now()->toAtomString();

            if (! $this->writeAtomic($data)) {
                return false;
            }

            $this->memo = $data;

            if ($clearPendingOnSuccess) {
                Cache::forget(self::PENDING_CACHE_KEY);
            }

            return true;
        } finally {
            flock($handle, LOCK_UN);
            fclose($handle);
        }
    }

    /**
     * Ficheiro temporario + rename(): ou a escrita nao aconteceu (o
     * ficheiro antigo continua intacto) ou aconteceu por inteiro — nunca
     * ha uma leitura a meio de uma escrita.
     */
    protected function writeAtomic(array $data): bool
    {
        try {
            $json = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            if ($json === false) {
                throw new \RuntimeException('json_encode falhou: ' . json_last_error_msg());
            }

            $gz = gzencode($json, 6);
            if ($gz === false) {
                throw new \RuntimeException('gzencode falhou.');
            }

            if (file_put_contents($this->tmpPath, $gz) === false) {
                throw new \RuntimeException('nao foi possivel escrever o ficheiro temporario.');
            }

            if (! rename($this->tmpPath, $this->path)) {
                throw new \RuntimeException('rename() do snapshot falhou.');
            }

            return true;
        } catch (Throwable $e) {
            @unlink($this->tmpPath);

            // A copia antiga fica tal como estava — e exactamente o que se
            // quer quando a escrita falha a meio.
            Log::warning('[resiliencia] falha a escrever o snapshot de emergencia; mantida a copia anterior.', [
                'erro' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /** Tempo maximo (segundos) a esperar pelo bloqueio so para
     *  acrescentar a fila de pendentes — nunca indefinido: um pedido web
     *  nao pode ficar parado a espera de outro processo. */
    const PENDING_LOCK_TIMEOUT = 0.2;
    const PENDING_LOCK_RETRY_US = 10000; // 10 ms

    /**
     * Acrescenta a alteracao a fila de pendentes.
     *
     * Tenta o MESMO ficheiro de bloqueio do snapshot, com um limite curto
     * de espera (ao contrario da escrita principal, que desiste de
     * imediato): e uma operacao breve — ler e voltar a escrever um array
     * pequeno na cache — por isso vale a pena esperar um instante para
     * duas chamadas concorrentes nao se atropelarem. Mas so um instante:
     * ao fim de PENDING_LOCK_TIMEOUT desiste do bloqueio e escreve na
     * mesma, sem bloquear o pedido indefinidamente — o preco e uma janela
     * estreita em que duas chamadas concorrentes podiam, em teoria, fazer
     * uma perder a alteracao da outra; preferivel a um pedido pendurado.
     */
    protected function enqueuePending(array $op): void
    {
        $this->ensureDirectory();

        $handle = @fopen($this->lockPath, 'c');
        if ($handle === false) {
            Log::warning('[resiliencia] nao foi possivel sinalizar a alteracao pendente do snapshot.');

            $this->appendPending($op);

            return;
        }

        $conseguiu = $this->acquireWithTimeout($handle);

        try {
            $this->appendPending($op);
        } finally {
            if ($conseguiu) {
                flock($handle, LOCK_UN);
            }
            fclose($handle);
        }
    }

    /**
     * @return bool  true se conseguiu o bloqueio dentro do limite de tempo.
     */
    protected function acquireWithTimeout($handle): bool
    {
        $fim = microtime(true) + self::PENDING_LOCK_TIMEOUT;

        do {
            if (flock($handle, LOCK_EX | LOCK_NB)) {
                return true;
            }
            usleep(self::PENDING_LOCK_RETRY_US);
        } while (microtime(true) < $fim);

        return false;
    }

    protected function appendPending(array $op): void
    {
        $pending = $this->pendingOps();
        $pending[] = $op;

        // Fila curta de propósito (nunca um ficheiro por operação): um
        // unico valor na cache por omissao, reescrito no lugar.
        Cache::put(self::PENDING_CACHE_KEY, $pending, now()->addDay());
    }

    protected function pendingOps(): array
    {
        return (array) Cache::get(self::PENDING_CACHE_KEY, []);
    }

    protected function ensureDirectory(): void
    {
        $dir = dirname($this->path);
        if (! is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }
    }

    protected function emptySnapshot(): array
    {
        return [
            'version' => self::SCHEMA_VERSION,
            'generated_at' => now()->toAtomString(),
            'jobs' => [],
            'job_slug_index' => [],
            'articles' => [],
            'article_slug_index' => [],
            'categories' => [],
        ];
    }

    /**
     * Le e descomprime o snapshot. Memorizado por pedido; devolve null se
     * o ficheiro nao existir ou estiver corrompido (sem rebentar o
     * pedido — o chamador trata uma copia em falta como "sem rede").
     */
    protected function load(): ?array
    {
        if ($this->memo !== null) {
            return $this->memo;
        }

        if (! is_file($this->path)) {
            return null;
        }

        try {
            $raw = file_get_contents($this->path);
            if ($raw === false) {
                return null;
            }

            $json = @gzdecode($raw);
            if ($json === false) {
                throw new \RuntimeException('gzdecode falhou — ficheiro corrompido.');
            }

            $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
            if (! is_array($data) || ! isset($data['jobs'], $data['articles'])) {
                throw new \RuntimeException('estrutura inesperada no snapshot.');
            }

            $data += ['job_slug_index' => [], 'article_slug_index' => [], 'categories' => []];

            return $this->memo = $data;
        } catch (Throwable $e) {
            Log::warning('[resiliencia] snapshot de emergencia corrompido ou ilegivel.', ['erro' => $e->getMessage()]);

            return null;
        }
    }
}
