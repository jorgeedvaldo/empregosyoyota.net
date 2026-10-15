<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use SplFileInfo;
use Symfony\Component\Finder\Finder;

/**
 * Limpeza das copias de seguranca.
 *
 * O store de ficheiros do Laravel so apaga uma entrada quando alguem a vai
 * ler e a encontra expirada. Numa pasta cujas chaves sao URLs — e portanto
 * mudam quando o conteudo muda — ha copias que nunca mais sao lidas e
 * ficariam la para sempre a ocupar a quota da conta.
 *
 * Este comando apaga o que ja expirou e, se mesmo assim a pasta passar do
 * limite, apaga as copias mais antigas ate caber.
 */
class PruneResilienceCache extends Command
{
    protected $signature = 'resilience:prune
                            {--max-mb=64 : Tamanho maximo da pasta, em MB}
                            {--dry-run : Mostra o que seria apagado, sem apagar}';

    protected $description = 'Apaga copias de seguranca expiradas e mantem a pasta dentro do limite';

    public function handle(): int
    {
        $path = $this->storePath();

        if ($path === null) {
            $this->warn('O store de resiliencia nao e de ficheiros; nao ha nada para limpar.');

            return self::SUCCESS;
        }

        if (! is_dir($path)) {
            $this->info('Ainda nao existe nenhuma copia guardada.');

            return self::SUCCESS;
        }

        $seco = (bool) $this->option('dry-run');
        $ficheiros = [];
        $expirados = 0;
        $bytes = 0;

        foreach (Finder::create()->files()->in($path) as $ficheiro) {
            /** @var SplFileInfo $ficheiro */
            $expiraEm = $this->expiresAt($ficheiro->getPathname());

            if ($expiraEm !== null && $expiraEm <= time()) {
                $expirados++;
                $seco ? null : @unlink($ficheiro->getPathname());

                continue;
            }

            $bytes += $ficheiro->getSize();
            $ficheiros[] = [$ficheiro->getPathname(), $ficheiro->getMTime(), $ficheiro->getSize()];
        }

        $limite = max(1, (int) $this->option('max-mb')) * 1024 * 1024;
        $excedentes = 0;

        if ($bytes > $limite) {
            // Mais antigas primeiro: sao as que menos falta fazem.
            usort($ficheiros, fn ($a, $b) => $a[1] <=> $b[1]);

            foreach ($ficheiros as [$caminho, $mtime, $tamanho]) {
                if ($bytes <= $limite) {
                    break;
                }

                $excedentes++;
                $bytes -= $tamanho;
                $seco ? null : @unlink($caminho);
            }
        }

        $this->info(sprintf(
            '%s %d expirada(s) e %d por excesso de tamanho. Ficam %s.',
            $seco ? 'A apagar (simulacao):' : 'Apagadas:',
            $expirados,
            $excedentes,
            $this->humanize($bytes)
        ));

        return self::SUCCESS;
    }

    /**
     * Caminho da pasta do store configurado, ou null se nao for ficheiros.
     */
    protected function storePath(): ?string
    {
        $store = (string) config('resilience.store', 'resilience');
        $config = (array) config('cache.stores.' . $store, []);

        if (($config['driver'] ?? null) !== 'file' || empty($config['path'])) {
            return null;
        }

        return $config['path'];
    }

    /**
     * O store de ficheiros do Laravel grava o instante de expiracao nos
     * primeiros 10 caracteres do ficheiro.
     */
    protected function expiresAt(string $caminho): ?int
    {
        $handle = @fopen($caminho, 'rb');

        if ($handle === false) {
            return null;
        }

        $prefixo = fread($handle, 10);
        fclose($handle);

        return is_string($prefixo) && ctype_digit($prefixo) ? (int) $prefixo : null;
    }

    protected function humanize(int $bytes): string
    {
        return $bytes >= 1048576
            ? round($bytes / 1048576, 1) . ' MB'
            : round($bytes / 1024) . ' KB';
    }
}
