<?php

namespace App\Console\Commands;

use App\Models\Article;
use Illuminate\Console\Command;

class GenerateArticleThumbnails extends Command
{
    protected $signature = 'articles:generate-thumbs {--force : Regenerar mesmo os artigos que ja tem thumb}';

    protected $description = 'Gera as thumbs otimizadas (Intervention Image) para artigos que ainda nao tem.';

    public function handle(): int
    {
        $force = (bool) $this->option('force');
        $articles = Article::whereNotNull('photo')->get();
        $total = $articles->count();

        if ($total === 0) {
            $this->info('Nenhum artigo por processar.');
            return self::SUCCESS;
        }

        $bar = $this->output->createProgressBar($total);
        $bar->start();

        foreach ($articles as $article) {
            $article->generateThumbnail($force);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("Thumbs verificadas/geradas para {$total} artigo(s).");

        return self::SUCCESS;
    }
}
