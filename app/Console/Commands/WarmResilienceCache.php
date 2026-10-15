<?php

namespace App\Console\Commands;

use App\Models\Article;
use App\Models\Category;
use App\Models\Job;
use App\Services\DatabaseCircuitBreaker;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Throwable;

/**
 * Enche as copias de seguranca enquanto o MySQL esta bom.
 *
 * A rede de seguranca so apanha aquilo que ja la estava: uma pagina que
 * nunca foi visitada nao tem copia para servir. Com este comando no cron, o
 * essencial do site esta sempre guardado antes de ser preciso.
 */
class WarmResilienceCache extends Command
{
    protected $signature = 'resilience:warm
                            {--pages : Renderiza tambem as paginas publicas principais}';

    protected $description = 'Actualiza as copias de seguranca usadas quando o MySQL nao responde';

    public function handle(DatabaseCircuitBreaker $breaker): int
    {
        if ($breaker->isOpen()) {
            $this->warn('Disjuntor aberto (MySQL em falha ha ' . $breaker->failures() . ' tentativas). Nada a aquecer.');

            return self::SUCCESS;
        }

        $falhas = 0;

        foreach ($this->datasets() as $nome => $callback) {
            try {
                $callback();
                $this->line("  dados: {$nome}");
            } catch (Throwable $e) {
                $falhas++;
                $this->error("  dados: {$nome} — {$e->getMessage()}");
            }
        }

        if ($this->option('pages')) {
            $falhas += $this->warmPages();
        }

        if ($falhas > 0) {
            $this->warn("Concluido com {$falhas} falha(s).");

            return self::FAILURE;
        }

        $this->info('Copias de seguranca actualizadas.');

        return self::SUCCESS;
    }

    /**
     * As consultas que praticamente todas as paginas publicas fazem.
     *
     * Chamar os metodos getCached*() e de proposito: passam pelo
     * DatabaseFallbackCache e portanto reescrevem as duas copias, a normal
     * e a de emergencia.
     */
    protected function datasets(): array
    {
        return [
            'ultimas vagas' => fn () => Job::getCachedLatest(),
            'total de vagas' => fn () => Job::getCachedCount(),
            'ultimos artigos' => fn () => Article::getCachedLatest(),
            'total de artigos' => fn () => Article::getCachedCount(),
            'categorias' => fn () => Category::getCachedAll(),
            'categorias com contagem' => fn () => Category::getCachedWithJobs(),
        ];
    }

    /**
     * Renderiza as paginas mais visitadas atraves da propria aplicacao,
     * para que fiquem guardadas no PublicPageCache.
     */
    protected function warmPages(): int
    {
        $falhas = 0;
        $kernel = app(\Illuminate\Contracts\Http\Kernel::class);

        // Tratar pedidos deixa o pedido "actual" do container apontado para
        // o ultimo que se simulou. Como o agendador corre mais comandos no
        // mesmo processo, repoe-se o original no fim.
        $pedidoOriginal = app()->bound('request') ? app('request') : null;

        try {
            foreach ($this->urls() as $url) {
                try {
                    $response = $kernel->handle(Request::create($url, 'GET'));

                    $estado = $response->getStatusCode();
                    $this->line("  pagina: {$url} ({$estado})");

                    if ($estado !== 200) {
                        $falhas++;
                    }
                } catch (Throwable $e) {
                    $falhas++;
                    $this->error("  pagina: {$url} — {$e->getMessage()}");
                }
            }
        } finally {
            if ($pedidoOriginal !== null) {
                app()->instance('request', $pedidoOriginal);
            }
        }

        return $falhas;
    }

    /**
     * @return array<int, string>
     */
    protected function urls(): array
    {
        $urls = [
            url('/'),
            url('/empregos'),
            url('/articles'),
            url('/vagas-de-emprego-em-angola'),
            url('/modelos-de-curriculos'),
            url('/about'),
        ];

        foreach ((array) config('landings') as $landing) {
            if (! empty($landing['slug'])) {
                $urls[] = url('/' . $landing['slug']);
            }
        }

        return $urls;
    }
}
