<?php

namespace Tests\Feature;

use App\Services\EmergencySnapshotService;
use Tests\TestCase;

/**
 * O snapshot unico de emergencia: uma entrada de cada vez, nunca um
 * ficheiro por vaga; escrita atomica; bloqueio exclusivo nao bloqueante
 * com fila de pendentes para quando ha contencao.
 */
class EmergencySnapshotServiceTest extends TestCase
{
    protected string $path;
    protected string $lockPath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->path = sys_get_temp_dir() . '/snapshot-teste-' . uniqid() . '.json.gz';
        $this->lockPath = $this->path . '.lock';
    }

    protected function tearDown(): void
    {
        @unlink($this->path);
        @unlink($this->lockPath);

        parent::tearDown();
    }

    protected function service(): EmergencySnapshotService
    {
        return new EmergencySnapshotService($this->path, $this->lockPath, maxJobs: 5, maxArticles: 5);
    }

    /** @test */
    public function comeca_sem_ficheiro_nenhum(): void
    {
        $service = $this->service();

        $this->assertFalse($service->exists());
        $this->assertNull($service->findJob('inexistente'));
        $this->assertSame([], $service->latestJobs());
    }

    /** @test */
    public function grava_e_le_uma_vaga(): void
    {
        $service = $this->service();

        $this->assertTrue($service->upsertJob(['id' => 1, 'slug' => 'vaga-um', 'title' => 'Vaga Um']));

        $this->assertTrue($service->exists());
        $this->assertSame('Vaga Um', $service->findJob(1)['title']);
        $this->assertSame('Vaga Um', $service->findJob('vaga-um')['title'], 'A pesquisa por slug tem de funcionar.');
    }

    /** @test */
    public function e_mesmo_um_unico_ficheiro(): void
    {
        $service = $this->service();

        for ($i = 1; $i <= 5; $i++) {
            $service->upsertJob(['id' => $i, 'slug' => "vaga-{$i}", 'title' => "Vaga {$i}"]);
        }

        $this->assertFileExists($this->path);
        $this->assertCount(1, glob(dirname($this->path) . '/snapshot-teste-*.json.gz'), 'Nao pode haver mais do que o UM ficheiro do snapshot.');
    }

    /** @test */
    public function actualizar_uma_vaga_nao_mexe_nas_outras(): void
    {
        $service = $this->service();
        $service->upsertJob(['id' => 1, 'slug' => 'vaga-um', 'title' => 'Original']);
        $service->upsertJob(['id' => 2, 'slug' => 'vaga-dois', 'title' => 'Vaga Dois']);

        $service->upsertJob(['id' => 1, 'slug' => 'vaga-um', 'title' => 'Editada']);

        $this->assertSame('Editada', $service->findJob(1)['title']);
        $this->assertSame('Vaga Dois', $service->findJob(2)['title']);
    }

    /** @test */
    public function mudar_o_slug_actualiza_o_indice_antigo(): void
    {
        $service = $this->service();
        $service->upsertJob(['id' => 1, 'slug' => 'slug-antigo', 'title' => 'X']);

        $service->upsertJob(['id' => 1, 'slug' => 'slug-novo', 'title' => 'X']);

        $this->assertNull($service->findJob('slug-antigo'), 'O slug antigo nao pode continuar a apontar para a vaga.');
        $this->assertNotNull($service->findJob('slug-novo'));
    }

    /** @test */
    public function remover_uma_vaga_tira_a_tambem_do_indice_de_slugs(): void
    {
        $service = $this->service();
        $service->upsertJob(['id' => 1, 'slug' => 'vaga-um', 'title' => 'X']);

        $service->removeJob(1);

        $this->assertNull($service->findJob(1));
        $this->assertNull($service->findJob('vaga-um'));
    }

    /** @test */
    public function mantem_so_as_mais_recentes_ate_ao_limite(): void
    {
        // maxJobs=5 (ver service()).
        $service = $this->service();

        for ($i = 1; $i <= 8; $i++) {
            $service->upsertJob(['id' => $i, 'slug' => "vaga-{$i}", 'title' => "Vaga {$i}"]);
        }

        $this->assertSame(5, $service->jobsCount(), 'O numero de vagas guardadas nao pode crescer sem parar.');
        $this->assertNull($service->findJob(1), 'A mais antiga (id 1) tem de ter caido.');
        $this->assertNull($service->findJob(3), 'A terceira mais antiga tambem.');
        $this->assertNotNull($service->findJob(8), 'A mais recente tem de ficar.');

        // E o tamanho fica fixo nao importa quantas mais entrarem.
        for ($i = 9; $i <= 20; $i++) {
            $service->upsertJob(['id' => $i, 'slug' => "vaga-{$i}", 'title' => "Vaga {$i}"]);
        }
        $this->assertSame(5, $service->jobsCount());
    }

    /** @test */
    public function as_mais_recentes_vem_por_ordem_de_id_descendente(): void
    {
        $service = $this->service();
        $service->upsertJob(['id' => 1, 'slug' => 'a', 'title' => 'A']);
        $service->upsertJob(['id' => 3, 'slug' => 'c', 'title' => 'C']);
        $service->upsertJob(['id' => 2, 'slug' => 'b', 'title' => 'B']);

        $titulos = array_column($service->latestJobs(), 'title');

        $this->assertSame(['C', 'B', 'A'], $titulos);
    }

    /** @test */
    public function categorias_sao_substituidas_por_inteiro(): void
    {
        $service = $this->service();
        $service->replaceCategories([['id' => 1, 'name' => 'TI']]);
        $service->replaceCategories([['id' => 2, 'name' => 'Saude']]);

        $this->assertSame([['id' => 2, 'name' => 'Saude']], $service->categories());
    }

    /** @test */
    public function uma_escrita_falhada_preserva_a_copia_anterior(): void
    {
        $service = $this->service();
        $service->upsertJob(['id' => 1, 'slug' => 'vaga-um', 'title' => 'Original']);

        // Aponta para um caminho onde a escrita vai falhar (directoria
        // que nao existe e nao pode ser criada: um ficheiro no lugar).
        $caminhoImpossivel = sys_get_temp_dir() . '/nao-e-uma-pasta-' . uniqid();
        file_put_contents($caminhoImpossivel, 'bloqueia a criacao da pasta');
        $servicoQuebrado = new EmergencySnapshotService($caminhoImpossivel . '/snapshot.json.gz', $caminhoImpossivel . '/snapshot.lock');

        $resultado = $servicoQuebrado->upsertJob(['id' => 2, 'slug' => 'vaga-dois', 'title' => 'Nunca Grava']);

        $this->assertFalse($resultado);
        @unlink($caminhoImpossivel);

        // O snapshot original (caminho valido) nunca foi tocado por isto.
        $this->assertSame('Original', $service->findJob(1)['title']);
    }

    /** @test */
    public function um_ficheiro_corrompido_e_tratado_como_sem_copia_nenhuma(): void
    {
        file_put_contents($this->path, 'isto nao e gzip nenhum');

        $service = $this->service();

        $this->assertNull($service->findJob('qualquer'));
        $this->assertSame([], $service->latestJobs());

        $meta = $service->meta();
        $this->assertTrue($meta['existe']);
        $this->assertFalse($meta['integro']);
    }

    /** @test */
    public function com_o_bloqueio_ocupado_a_alteracao_fica_pendente_e_nao_se_perde(): void
    {
        $service = $this->service();
        $service->upsertJob(['id' => 1, 'slug' => 'vaga-um', 'title' => 'Primeira']);

        // Simula outro processo a escrever: pega o MESMO ficheiro de
        // bloqueio em exclusivo e nao o larga.
        $handleConcorrente = fopen($this->lockPath, 'c');
        flock($handleConcorrente, LOCK_EX);

        try {
            $resultado = $service->upsertJob(['id' => 2, 'slug' => 'vaga-dois', 'title' => 'Segunda']);

            $this->assertFalse($resultado, 'Com o bloqueio ocupado, a escrita nao pode acontecer agora.');
            $this->assertNull($service->findJob(2), 'Ainda nao esta na copia em disco.');
        } finally {
            flock($handleConcorrente, LOCK_UN);
            fclose($handleConcorrente);
        }

        // Bloqueio libertado: a proxima escrita bem sucedida inclui a
        // alteracao que ficou pendente, sem se ter perdido.
        $this->assertTrue($service->upsertJob(['id' => 3, 'slug' => 'vaga-tres', 'title' => 'Terceira']));

        $this->assertNotNull($service->findJob(2), 'A alteracao pendente tinha de ter sido aplicada.');
        $this->assertNotNull($service->findJob(3));
        $this->assertNotNull($service->findJob(1));
    }

    /** @test */
    public function reconstrucao_completa_substitui_tudo(): void
    {
        $service = $this->service();
        $service->upsertJob(['id' => 99, 'slug' => 'vai-desaparecer', 'title' => 'X']);

        $ok = $service->rebuild(
            [['id' => 1, 'slug' => 'a', 'title' => 'A']],
            [['id' => 10, 'slug' => 'art', 'title' => 'Artigo']],
            [['id' => 1, 'name' => 'Categoria']]
        );

        $this->assertTrue($ok);
        $this->assertNull($service->findJob(99), 'A reconstrucao substitui, nao acrescenta.');
        $this->assertNotNull($service->findJob(1));
        $this->assertNotNull($service->findArticle(10));
        $this->assertCount(1, $service->categories());
    }

    /** @test */
    public function as_metricas_reportam_tamanho_idade_e_integridade(): void
    {
        $service = $this->service();

        $this->assertFalse($service->meta()['existe']);

        $service->upsertJob(['id' => 1, 'slug' => 'a', 'title' => 'A']);

        $meta = $service->meta();
        $this->assertTrue($meta['existe']);
        $this->assertTrue($meta['integro']);
        $this->assertGreaterThan(0, $meta['bytes']);
        $this->assertSame(0, $meta['idade_segundos']);
        $this->assertSame(1, $meta['vagas']);
    }
}
