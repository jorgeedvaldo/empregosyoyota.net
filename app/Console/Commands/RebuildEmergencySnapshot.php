<?php

namespace App\Console\Commands;

use App\Models\Article;
use App\Models\Category;
use App\Models\Job;
use App\Services\EmergencySnapshotService;
use Illuminate\Console\Command;

/**
 * Gera (ou regenera por inteiro) o snapshot unico de emergencia a partir
 * do MySQL.
 *
 * E o unico sitio deste mecanismo que faz uma reconstrucao completa — por
 * isso NAO corre automaticamente a cada vaga gravada (isso seria, de
 * novo, uma consulta pesada por escrita). Usa-se a mao:
 *
 *   - logo a seguir a publicar esta alteracao, para gerar a primeira
 *     copia valida (antes disso o snapshot nao existe e uma falha do
 *     MySQL nessa janela cai directamente na pagina de 503);
 *   - como reconciliacao manual, se algum dia houver duvidas sobre se o
 *     snapshot esta coerente com o MySQL (nunca por rotina — as escritas
 *     normais ja o mantêm actualizado sozinhas).
 *
 * Nao e chamado por nenhum Cron Job nem pelo Scheduler do Laravel.
 */
class RebuildEmergencySnapshot extends Command
{
    protected $signature = 'resilience:snapshot:rebuild';

    protected $description = 'Reconstroi por inteiro o snapshot de emergencia a partir do MySQL (uso manual, nao automatico)';

    public function handle(EmergencySnapshotService $snapshot): int
    {
        $this->info('A ler do MySQL...');

        $maxJobs = (int) config('resilience.snapshot.max_jobs', 300);
        $maxArticles = (int) config('resilience.snapshot.max_articles', 200);

        try {
            $jobs = Job::orderByRaw('id DESC')->limit($maxJobs)->get()
                ->map(fn ($job) => $job->toSnapshotArray())
                ->all();

            $articles = Article::orderByRaw('id DESC')->limit($maxArticles)->get()
                ->map(fn ($article) => $article->toSnapshotArray())
                ->all();

            $categories = Category::orderBy('name')->get(['id', 'name'])->toArray();
        } catch (\Throwable $e) {
            $this->error('O MySQL nao respondeu: ' . $e->getMessage());
            $this->line('Nada foi alterado — a copia anterior do snapshot (se existir) continua intacta. Tente novamente quando a base de dados estiver disponivel.');

            return self::FAILURE;
        }

        $this->line('  ' . count($jobs) . ' vaga(s), ' . count($articles) . ' artigo(s), ' . count($categories) . ' categoria(s).');

        if (! $snapshot->rebuild($jobs, $articles, $categories)) {
            $this->error('Nao foi possivel escrever o snapshot (bloqueio ocupado por outro processo?). Tente novamente.');

            return self::FAILURE;
        }

        $this->info('Snapshot gerado com sucesso.');
        $this->line('Confirme com: php artisan resilience:status');

        return self::SUCCESS;
    }
}
