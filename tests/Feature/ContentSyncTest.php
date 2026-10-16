<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Category;
use App\Models\Job;
use App\Services\EmergencySnapshotService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * Fase B/D: confirmar primeiro o commit no MySQL, so depois invalidar a
 * cache de leitura e sincronizar o snapshot — nunca ao contrario, e nunca
 * dentro de uma transaccao que pode vir a ser desfeita.
 */
class ContentSyncTest extends TestCase
{
    protected $db;
    protected $snapshotPath;
    protected $snapshotLockPath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->db = sys_get_temp_dir() . '/yoyota-sync-' . getmypid() . '.sqlite';
        touch($this->db);

        $this->snapshotPath = sys_get_temp_dir() . '/yoyota-sync-snapshot-' . getmypid() . '.json.gz';
        $this->snapshotLockPath = $this->snapshotPath . '.lock';
        @unlink($this->snapshotPath);
        config([
            'resilience.snapshot.path' => $this->snapshotPath,
            'resilience.snapshot.lock_path' => $this->snapshotLockPath,
        ]);

        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => $this->db]);
        DB::purge('sqlite');

        Schema::create('jobs', function ($t) {
            $t->increments('id');
            $t->string('title');
            $t->string('slug')->nullable();
            $t->string('company')->nullable();
            $t->string('province')->nullable();
            $t->text('description')->nullable();
            $t->string('email_or_link')->nullable();
            $t->string('photo')->nullable();
            $t->integer('country_id')->default(1);
            $t->timestamps();
        });
        Schema::create('categories', function ($t) {
            $t->increments('id');
            $t->string('name');
            $t->timestamps();
        });
        Schema::create('category_jobs', function ($t) {
            $t->increments('id');
            $t->integer('job_id');
            $t->integer('category_id');
        });
        Schema::create('articles', function ($t) {
            $t->increments('id');
            $t->string('title');
            $t->string('slug')->nullable();
            $t->text('description')->nullable();
            $t->string('photo')->nullable();
            $t->integer('country_id')->default(1);
            $t->timestamps();
        });
        Schema::create('social_media_jobs', function ($t) {
            $t->increments('id');
            $t->integer('job_id');
            $t->integer('post_status')->default(0);
            $t->timestamps();
        });
    }

    protected function tearDown(): void
    {
        @unlink($this->db);
        @unlink($this->snapshotPath);
        @unlink($this->snapshotLockPath);

        parent::tearDown();
    }

    protected function criar(array $overrides = []): Job
    {
        return Job::withoutSyncingToSearch(fn () => Job::create(array_merge([
            'title' => 'Vaga de Teste',
            'company' => 'Empresa',
            'province' => 'Luanda',
            'description' => 'Descricao',
            'email_or_link' => 'rh@empresa.ao',
        ], $overrides)));
    }

    /** @test */
    public function criar_uma_vaga_sincroniza_o_snapshot_sem_comandos_a_mais(): void
    {
        $vaga = $this->criar();

        $snapshot = app(EmergencySnapshotService::class);

        $this->assertNotNull($snapshot->findJob($vaga->id), 'Logo apos o commit, sem warm nem cron.');
        $this->assertSame($vaga->title, $snapshot->findJob($vaga->id)['title']);
    }

    /** @test */
    public function editar_uma_vaga_actualiza_so_essa_entrada_no_snapshot(): void
    {
        $vaga = $this->criar(['title' => 'Original']);
        $outra = $this->criar(['title' => 'Intacta']);

        $vaga->update(['title' => 'Editada']);

        $snapshot = app(EmergencySnapshotService::class);
        $this->assertSame('Editada', $snapshot->findJob($vaga->id)['title']);
        $this->assertSame('Intacta', $snapshot->findJob($outra->id)['title']);
    }

    /** @test */
    public function apagar_uma_vaga_remove_a_do_snapshot(): void
    {
        $vaga = $this->criar();
        $snapshot = app(EmergencySnapshotService::class);
        $this->assertNotNull($snapshot->findJob($vaga->id));

        $vaga->delete();

        $this->assertNull(app(EmergencySnapshotService::class)->findJob($vaga->id));
    }

    /** @test */
    public function editar_uma_vaga_invalida_a_cache_de_leitura_de_imediato(): void
    {
        Job::getCachedLatest(); // preenche a cache

        $vaga = $this->criar(['title' => 'Antes']);
        Job::getCachedLatest(); // relê, agora com a vaga

        $vaga->update(['title' => 'Depois']);

        $lista = Job::getCachedLatest();
        $this->assertTrue($lista->firstWhere('id', $vaga->id)->title === 'Depois', 'A proxima leitura tem de ver o titulo novo, nao o antigo em cache.');
    }

    /** @test */
    public function um_rollback_nao_sincroniza_nada(): void
    {
        $snapshot = app(EmergencySnapshotService::class);

        try {
            DB::transaction(function () {
                $this->criar(['title' => 'Nunca Deve Aparecer']);

                throw new \RuntimeException('simula uma falha a meio da transaccao');
            });
        } catch (\RuntimeException $e) {
            // esperado
        }

        $this->assertSame(0, Job::count(), 'A vaga nao pode ter ficado gravada.');
        $this->assertSame([], $snapshot->latestJobs(), 'E por isso tambem nao pode estar no snapshot.');
    }

    /** @test */
    public function uma_transaccao_bem_sucedida_sincroniza_apos_o_commit(): void
    {
        $snapshot = app(EmergencySnapshotService::class);

        $vaga = DB::transaction(function () {
            return $this->criar(['title' => 'Dentro De Uma Transaccao']);
        });

        $this->assertNotNull($snapshot->findJob($vaga->id), 'Depois do commit, a sincronizacao tem de ter acontecido.');
        $this->assertSame('Dentro De Uma Transaccao', $snapshot->findJob($vaga->id)['title']);
    }

    /** @test */
    public function mudar_categorias_substitui_a_lista_completa_no_snapshot(): void
    {
        Category::create(['name' => 'TI']);
        Category::create(['name' => 'Saude']);

        $snapshot = app(EmergencySnapshotService::class);
        $this->assertCount(2, $snapshot->categories());

        Category::where('name', 'Saude')->first()->delete();

        $this->assertCount(1, app(EmergencySnapshotService::class)->categories());
    }

    /** @test */
    public function criar_um_artigo_sincroniza_o_snapshot(): void
    {
        $artigo = Article::create([
            'title' => 'Artigo de Teste',
            'description' => 'Conteudo',
            'photo' => 'article.jpg',
        ]);

        $snapshot = app(EmergencySnapshotService::class);
        $this->assertNotNull($snapshot->findArticle($artigo->id));
        $this->assertSame('Artigo de Teste', $snapshot->findArticle($artigo->slug)['title']);
    }

    /** @test */
    public function vinte_escritas_seguidas_deixam_o_snapshot_com_vinte_vagas_sem_rebentar(): void
    {
        for ($i = 0; $i < 20; $i++) {
            $this->criar(['title' => "Vaga {$i}"]);
        }

        $this->assertSame(20, Job::count());
        $this->assertSame(20, app(EmergencySnapshotService::class)->jobsCount());
        // O teste de contagem de ficheiros (que prova que isto nao cria um
        // ficheiro por vaga) esta em PublicPagesSurviveOutageTest.
    }
}
