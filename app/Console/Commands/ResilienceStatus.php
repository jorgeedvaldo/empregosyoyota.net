<?php

namespace App\Console\Commands;

use App\Services\DatabaseCircuitBreaker;
use App\Services\DatabaseFallbackCache;
use App\Services\EmergencySnapshotService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Diagnostico: a cache esta a funcionar? O disjuntor esta aberto? O
 * snapshot de emergencia esta vivo e actualizado?
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
        EmergencySnapshotService $snapshot
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
        $this->line('<comment>Cache de dados (listas, contagens, definicoes — chaves fixas)</comment>');

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

        $this->line('<comment>Snapshot unico de emergencia (vagas, artigos, categorias)</comment>');

        $meta = $snapshot->meta();

        if (! $meta['existe']) {
            $this->warn('  nao existe ainda. Corra: php artisan resilience:snapshot:rebuild');
        } else {
            $this->line('  integro:    ' . ($meta['integro'] ? 'sim' : 'NAO — corrompido, a usar a ultima copia valida'));
            $this->line('  gerado em:  ' . ($meta['gerado_em'] ?? 'desconhecido'));
            $this->line('  idade:      ' . ($meta['idade_segundos'] !== null ? $meta['idade_segundos'] . ' s' : 'n/d'));
            $this->line('  tamanho:    ' . $this->humanize($meta['bytes']));
            $this->line('  vagas:      ' . $meta['vagas']);
            $this->line('  artigos:    ' . $meta['artigos']);
            $this->line('  categorias: ' . $meta['categorias']);

            if ($meta['pendentes'] > 0) {
                $this->warn('  ' . $meta['pendentes'] . ' alteracao(oes) pendente(s) — aplicam-se na proxima escrita bem sucedida.');
            }
        }

        $this->line('');

        return self::SUCCESS;
    }

    protected function humanize(int $bytes): string
    {
        return $bytes >= 1048576
            ? round($bytes / 1048576, 1) . ' MB'
            : round($bytes / 1024, 1) . ' KB';
    }
}
