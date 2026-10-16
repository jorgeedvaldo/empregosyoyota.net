<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Finder\Finder;

/**
 * Relata — e, so se pedido explicitamente, remove — os ficheiros deixados
 * pelo mecanismo antigo de cache de HTML por URL (storage/framework/cache/
 * resilience), substituido pelo snapshot unico de emergencia.
 *
 * O novo "resilience" store (DatabaseFallbackCache + disjuntor) continua a
 * usar a MESMA pasta, mas so com um punhado de chaves fixas — por isso
 * este comando nunca apaga a pasta toda, so os ficheiros que pareçam
 * antigos (idade acima de "--older-than-days", por omissao 1) OU que nao
 * batem certo com as poucas chaves que o mecanismo actual usa.
 *
 * Nunca corre sozinho. Por omissao so relata; "--delete" apaga, e mesmo
 * assim pede confirmacao a menos que "--force" tambem seja passado.
 */
class CleanupLegacyResilienceCache extends Command
{
    protected $signature = 'resilience:cleanup-legacy
                            {--delete : Remove os ficheiros identificados como antigos (por omissao so relata)}
                            {--force : Nao pedir confirmacao antes de apagar}
                            {--older-than-days=1 : So considera "antigo" o que nao e tocado ha pelo menos estes dias}';

    protected $description = 'Relata (e, so se pedido, remove) os ficheiros do antigo mecanismo de cache por URL';

    /**
     * Prefixos das poucas chaves que o mecanismo actual ainda grava neste
     * store (DatabaseFallbackCache + disjuntor). O store de ficheiros do
     * Laravel parte a chave num hash sha1 — nao da para "ver o nome" no
     * ficheiro, por isso a triagem e so pela IDADE (mtime), nunca pelo
     * conteudo.
     */
    public function handle(): int
    {
        $path = $this->storePath();

        if ($path === null) {
            $this->warn('O store de resiliencia nao e de ficheiros; nao ha nada a limpar.');

            return self::SUCCESS;
        }

        if (! is_dir($path)) {
            $this->info('A pasta ainda nao existe — nada a limpar.');

            return self::SUCCESS;
        }

        $limiar = now()->subDays(max(0, (int) $this->option('older-than-days')))->getTimestamp();

        $antigos = [];
        $recentesBytes = 0;
        $recentesCount = 0;

        foreach (Finder::create()->files()->in($path) as $ficheiro) {
            if ($ficheiro->getMTime() < $limiar) {
                $antigos[] = $ficheiro->getPathname();
            } else {
                $recentesBytes += $ficheiro->getSize();
                $recentesCount++;
            }
        }

        $bytesAntigos = array_sum(array_map('filesize', $antigos));

        $this->line('');
        $this->line("Ficheiros com mais de {$this->option('older-than-days')} dia(s) (candidatos a antigos): "
            . count($antigos) . ' (' . $this->humanize($bytesAntigos) . ')');
        $this->line('Ficheiros recentes (do mecanismo actual, NAO tocados): '
            . $recentesCount . ' (' . $this->humanize($recentesBytes) . ')');
        $this->line('');

        if (empty($antigos)) {
            $this->info('Nada para limpar.');

            return self::SUCCESS;
        }

        if (! $this->option('delete')) {
            $this->info('Modo relatorio (sem --delete, nada foi apagado).');
            $this->line('Para apagar: php artisan resilience:cleanup-legacy --delete');

            return self::SUCCESS;
        }

        if (! $this->option('force') && ! $this->confirm(
            'Vai apagar ' . count($antigos) . ' ficheiro(s) (' . $this->humanize($bytesAntigos) . '). Confirma?'
        )) {
            $this->warn('Cancelado — nada foi apagado.');

            return self::SUCCESS;
        }

        $apagados = 0;
        foreach ($antigos as $ficheiro) {
            if (@unlink($ficheiro)) {
                $apagados++;
            }
        }

        $this->removeEmptyDirectories($path);

        $this->info("Apagados {$apagados} de " . count($antigos) . ' ficheiro(s).');

        return self::SUCCESS;
    }

    protected function removeEmptyDirectories(string $root): void
    {
        $dirs = iterator_to_array(Finder::create()->directories()->in($root));

        // Do mais fundo para o mais raso, para uma pasta so ficar "vazia"
        // depois de as suas subpastas (agora vazias) terem sido removidas.
        usort($dirs, fn ($a, $b) => substr_count($b->getPathname(), '/') <=> substr_count($a->getPathname(), '/'));

        foreach ($dirs as $dir) {
            @rmdir($dir->getPathname());
        }
    }

    protected function storePath(): ?string
    {
        $config = (array) config('cache.stores.' . config('resilience.store', 'resilience'), []);

        if (($config['driver'] ?? null) !== 'file' || empty($config['path'])) {
            return null;
        }

        return $config['path'];
    }

    protected function humanize(int $bytes): string
    {
        if ($bytes >= 1073741824) {
            return round($bytes / 1073741824, 2) . ' GB';
        }

        return $bytes >= 1048576
            ? round($bytes / 1048576, 1) . ' MB'
            : round($bytes / 1024, 1) . ' KB';
    }
}
