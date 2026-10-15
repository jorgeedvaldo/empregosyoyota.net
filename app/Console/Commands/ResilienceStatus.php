<?php

namespace App\Console\Commands;

use App\Services\DatabaseCircuitBreaker;
use App\Services\DatabaseFallbackCache;
use App\Services\PublicPageCache;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Diagnostico: a cache esta a funcionar? O disjuntor esta aberto?
 *
 * E o comando a correr no Terminal do cPanel quando o site parece estranho,
 * antes de se mexer em seja no que for.
 */
class ResilienceStatus extends Command
{
    protected $signature = 'resilience:status
                            {--reset : Fecha o disjuntor a mao}';

    protected $description = 'Mostra o estado da tolerancia a falhas do MySQL';

    public function handle(
        DatabaseCircuitBreaker $breaker,
        DatabaseFallbackCache $cache,
        PublicPageCache $pages
    ): int {
        if ($this->option('reset')) {
            $breaker->reset();
            $this->info('Disjuntor reposto.');
        }

        $this->line('');
        $this->line('<comment>Ligacao ao MySQL</comment>');

        try {
            $inicio = microtime(true);
            DB::connection()->getPdo();
            DB::select('select 1');
            $ms = round((microtime(true) - $inicio) * 1000);
            $this->line("  responde ({$ms} ms)");
        } catch (Throwable $e) {
            $this->error('  nao responde: ' . $e->getMessage());
        }

        $this->line('');
        $this->line('<comment>Disjuntor</comment>');
        $this->line('  activo:   ' . ($breaker->enabled() ? 'sim' : 'nao'));
        $this->line('  estado:   ' . ($breaker->isOpen() ? 'ABERTO (a servir copias)' : 'fechado'));
        $this->line('  falhas:   ' . $breaker->failures());

        if ($breaker->isOpen()) {
            $this->line('  reabre em ' . $breaker->retryAfter() . ' s');
        }

        $this->line('');
        $this->line('<comment>Cache de dados</comment>');

        $linhas = [];
        $chaves = [
            'latest_jobs_50',
            'latest_articles_50',
            'jobs_count_country_1',
            'articles_count_country_1',
            'categories_all',
            'categories_with_jobs',
            'rss_feed_items',
        ];

        foreach ($chaves as $chave) {
            $linhas[] = [
                $chave,
                $cache->hasFresh($chave) ? 'sim' : 'nao',
                $cache->hasEmergency($chave) ? 'sim' : 'NAO',
            ];
        }

        $this->table(['chave', 'copia normal', 'copia de emergencia'], $linhas);

        $this->line('<comment>Cache de paginas</comment>');
        $this->line('  activa:  ' . ($pages->enabled() ? 'sim' : 'nao'));
        $this->line('  versao:  ' . $pages->version());
        $this->line('  pasta:   ' . $this->storeSize());
        $this->line('');

        return self::SUCCESS;
    }

    protected function storeSize(): string
    {
        $config = (array) config('cache.stores.' . config('resilience.store', 'resilience'), []);
        $path = $config['path'] ?? null;

        if (($config['driver'] ?? null) !== 'file' || ! $path || ! is_dir($path)) {
            return 'n/d';
        }

        $bytes = 0;
        $ficheiros = 0;

        foreach (\Symfony\Component\Finder\Finder::create()->files()->in($path) as $ficheiro) {
            $bytes += $ficheiro->getSize();
            $ficheiros++;
        }

        return sprintf('%s, %d ficheiro(s) (%s)', $path, $ficheiros, $bytes >= 1048576
            ? round($bytes / 1048576, 1) . ' MB'
            : round($bytes / 1024) . ' KB');
    }
}
