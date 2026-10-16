<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Job;
use App\Services\DatabaseFallbackCache;
use App\Services\EmergencySnapshotService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\Finder\Finder;
use Tests\TestCase;

/**
 * O ensaio que da nome ao problema: a pagina inicial verdadeira, com os
 * seus controladores, os seus modelos e o snapshot de emergencia, contra
 * uma base de dados a serio — e depois sem ela.
 *
 * Usa sqlite num ficheiro temporario para nao depender de MySQL nenhum (e
 * muito menos para esgotar as 30 ligacoes do alojamento a ver o que
 * acontece). A "queda" e feita a apontar a ligacao para uma porta onde nao
 * esta ninguem, que e o que o visitante sente quando o limite e atingido.
 */
class PublicPagesSurviveOutageTest extends TestCase
{
    protected $db;
    protected $snapshotPath;
    protected $snapshotLockPath;

    protected function setUp(): void
    {
        parent::setUp();

        config(['app.debug' => false]);

        $this->db = sys_get_temp_dir() . '/yoyota-smoke-' . getmypid() . '.sqlite';
        touch($this->db);

        // Snapshot isolado por teste — nunca o caminho real da aplicacao.
        $this->snapshotPath = sys_get_temp_dir() . '/yoyota-snapshot-' . getmypid() . '.json.gz';
        $this->snapshotLockPath = $this->snapshotPath . '.lock';
        @unlink($this->snapshotPath);
        @unlink($this->snapshotLockPath);
        config([
            'resilience.snapshot.path' => $this->snapshotPath,
            'resilience.snapshot.lock_path' => $this->snapshotLockPath,
        ]);

        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => $this->db,
        ]);
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

        Schema::create('articles', function ($t) {
            $t->increments('id');
            $t->string('title');
            $t->string('slug')->nullable();
            $t->text('description')->nullable();
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

        Schema::create('settings', function ($t) {
            $t->increments('id');
            $t->string('key');
            $t->string('value')->nullable();
            $t->timestamps();
        });

        Schema::create('social_media_jobs', function ($t) {
            $t->increments('id');
            $t->integer('job_id');
            $t->integer('post_status')->default(0);
            $t->timestamps();
        });

        // Vaga e artigo "semeados" directamente na base (sem passar pelos
        // eventos Eloquent) — simula conteudo que ja existia antes desta
        // alteracao, cuja primeira copia no snapshot vem so do
        // "resilience:snapshot:rebuild" ou de uma visita real.
        DB::table('jobs')->insert([
            'title' => 'Tecnico de Contabilidade',
            'slug' => 'tecnico-de-contabilidade',
            'company' => 'Empresa Alfa',
            'province' => 'Luanda',
            'description' => 'Vaga de teste',
            'country_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('articles')->insert([
            'title' => 'Como preparar o seu CV',
            'slug' => 'como-preparar-o-seu-cv',
            'description' => 'Artigo de teste',
            'photo' => 'images/articles/teste.jpg',
            'country_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('categories')->insert(['name' => 'Contabilidade', 'created_at' => now(), 'updated_at' => now()]);
    }

    protected function tearDown(): void
    {
        @unlink($this->db);
        @unlink($this->snapshotPath);
        @unlink($this->snapshotLockPath);

        parent::tearDown();
    }

    /** @test */
    public function ciclo_completo_mysql_bom_escrita_real_time_queda_e_recuperacao(): void
    {
        $cache = app(DatabaseFallbackCache::class);
        $snapshot = app(EmergencySnapshotService::class);

        // --------------------------------------------------------------
        // 1. MySQL disponivel: pagina inicial gerada normalmente.
        // --------------------------------------------------------------
        $this->get('/')->assertOk()->assertSee('Tecnico de Contabilidade');

        $this->assertNotEmpty(Job::getCachedLatest());
        $this->assertNotEmpty(Article::getCachedLatest());

        // --------------------------------------------------------------
        // 2. Cache de dados preenchida: nao precisa de voltar ao MySQL
        //    (a prova esta no passo seguinte, que desliga a base de dados
        //    e as mesmas chaves continuam a responder).
        // --------------------------------------------------------------
        $this->assertTrue($cache->hasFresh('latest_jobs_50'));
        $this->assertTrue($cache->hasEmergency('latest_jobs_50'));

        // --------------------------------------------------------------
        // 3. Escrita em tempo real: uma vaga nova, criada como o painel
        //    Filament ou a API fariam (Eloquent, dispara os eventos).
        //    Sem atraso nenhum: tem de aparecer na pagina publica e no
        //    snapshot imediatamente a seguir, sem warm, sem cron.
        // --------------------------------------------------------------
        // withoutSyncingToSearch(): os testes nao precisam do indice
        // TNTSearch real, so dos eventos Eloquent (saved/deleted) que
        // alimentam o snapshot e invalidam a cache.
        $novaVaga = Job::withoutSyncingToSearch(function () {
            return Job::create([
                'title' => 'Engenheiro de Software',
                'company' => 'Empresa Beta',
                'province' => 'Benguela',
                'description' => 'Vaga nova, publicada agora mesmo.',
                'email_or_link' => 'rh@beta.ao',
            ]);
        });
        $novaVaga->refresh();

        // Apareceu de imediato na listagem publica (MySQL ainda ligado):
        // a cache da listagem foi invalidada no mesmo pedido que gravou.
        $this->get('/empregos')->assertOk()->assertSee('Engenheiro de Software');

        // E tambem na sua propria pagina.
        $this->get('/empregos/' . $novaVaga->slug)->assertOk()->assertSee('Engenheiro de Software');

        // E ja esta no snapshot — sem chamar nenhum comando a mao.
        $this->assertNotNull($snapshot->findJob($novaVaga->slug), 'A vaga devia estar no snapshot logo apos o commit, sem warm nem cron.');
        $this->assertSame('Engenheiro de Software', $snapshot->findJob($novaVaga->slug)['title']);

        // --------------------------------------------------------------
        // 4. O MySQL esgota as 30 ligacoes.
        // --------------------------------------------------------------
        $this->derrubaBaseDeDados();

        // A pagina inicial continua a abrir — dados vindos da copia de
        // emergencia do DatabaseFallbackCache.
        $comFalha = $this->get('/');
        $comFalha->assertOk();
        $comFalha->assertSee('Tecnico de Contabilidade');
        $comFalha->assertDontSee('SQLSTATE');

        // A vaga semeada directamente na base (sem Eloquent) so chega ao
        // snapshot atraves de uma leitura bem sucedida — ja aconteceu no
        // passo 1 (Job::getCachedLatest() nao alimenta o snapshot, so a
        // DatabaseFallbackCache). A pagina individual dela, por isso, so
        // sobrevive a queda se tiver sido visitada por findPublicBySlug()
        // enquanto o MySQL respondia. Confirma-se a seguir que NAO esta
        // no snapshot (nunca foi lida por esse caminho) e por isso da 503
        // — o comportamento honesto, nao um 404 inventado.
        $vagaSemeadaNuncaVisitada = $this->get('/empregos/tecnico-de-contabilidade');
        $vagaSemeadaNuncaVisitada->assertStatus(503);

        // Mas a vaga criada no passo 3 FOI lida (findPublicBySlug, ao
        // visitar a sua pagina), por isso esta no snapshot e sobrevive:
        $vagaNovaComFalha = $this->get('/empregos/' . $novaVaga->slug);
        $vagaNovaComFalha->assertOk();
        $vagaNovaComFalha->assertSee('Engenheiro de Software');
        $vagaNovaComFalha->assertDontSee('SQLSTATE');

        // A listagem tambem sobrevive (contagem + lista, ambas em cache).
        $listagemComFalha = $this->get('/empregos');
        $listagemComFalha->assertOk();
        $listagemComFalha->assertDontSee('SQLSTATE');

        // Uma pagina sem cache nenhuma e sem snapshot: 503 honesto.
        $semCopia = $this->get('/modelos-de-curriculos');
        $semCopia->assertStatus(503);
        $semCopia->assertSee('Serviço temporariamente indisponível', false);
        $semCopia->assertDontSee('SQLSTATE');
        $this->assertNotEmpty($semCopia->headers->get('Retry-After'));

        // --------------------------------------------------------------
        // 5. O MySQL recupera: sem reiniciar nada, o proximo pedido volta
        //    ao normal.
        // --------------------------------------------------------------
        $this->restauraBaseDeDados();

        $outraVaga = Job::withoutSyncingToSearch(function () {
            return Job::create([
                'title' => 'Analista Financeiro',
                'company' => 'Empresa Gama',
                'province' => 'Huambo',
                'description' => 'Confirma que as escritas voltam ao normal.',
                'email_or_link' => 'rh@gama.ao',
            ]);
        });

        $this->get('/empregos/' . $outraVaga->slug)->assertOk()->assertSee('Analista Financeiro');
    }

    /**
     * Fase F / Fase G (passo 9): o essencial de tudo isto. Visitar dezenas
     * de paginas publicas distintas nao pode continuar a criar um
     * ficheiro por pagina — era exactamente isso que enchia os inodes.
     */
    /** @test */
    public function visitar_muitas_paginas_distintas_nao_faz_crescer_a_pasta_de_cache(): void
    {
        Job::withoutSyncingToSearch(function () {
            for ($i = 2; $i <= 40; $i++) {
                Job::create([
                    'title' => "Vaga numero {$i}",
                    'company' => 'Empresa Teste',
                    'province' => 'Luanda',
                    'description' => 'Vaga de carga para o teste de inodes.',
                    'email_or_link' => 'rh@teste.ao',
                ]);
            }
        });

        $antes = $this->contarFicheirosNoStoreDeResiliencia();

        // 40 paginas de vaga distintas + a listagem + a pagina inicial,
        // cada uma visitada varias vezes.
        foreach (Job::pluck('slug') as $slug) {
            $this->get('/empregos/' . $slug)->assertOk();
            $this->get('/empregos/' . $slug)->assertOk(); // outra vez, mesma vaga
        }
        $this->get('/empregos')->assertOk();
        $this->get('/')->assertOk();
        $this->get('/')->assertOk();

        $depois = $this->contarFicheirosNoStoreDeResiliencia();

        // O store de ficheiros do DatabaseFallbackCache so tem chaves
        // FIXAS (latest_jobs_50, categories_all, ...) — visitar 40 vagas
        // distintas nao lhe acrescenta ficheiro nenhum. Tolerancia
        // pequena (+2) para as chaves do disjuntor, que podem ou nao
        // existir consoante o que mais correu neste processo de teste.
        $this->assertLessThanOrEqual(
            $antes + 2,
            $depois,
            'Visitar varias paginas publicas distintas nao pode criar um ficheiro por pagina.'
        );

        // E o snapshot continua a ser UM ficheiro, nao um por vaga.
        $this->assertFileExists(config('resilience.snapshot.path'));
    }

    protected function contarFicheirosNoStoreDeResiliencia(): int
    {
        $path = config('cache.stores.' . config('resilience.store', 'resilience') . '.path');

        if (! is_dir($path)) {
            return 0;
        }

        return iterator_count(Finder::create()->files()->in($path));
    }

    /**
     * Aponta a ligacao para uma porta onde nao esta ninguem: qualquer
     * consulta passa a falhar como falharia com o erro 1226.
     */
    protected function derrubaBaseDeDados(): void
    {
        config([
            'database.default' => 'mysql',
            'database.connections.mysql.host' => '127.0.0.1',
            'database.connections.mysql.port' => 1,
            'database.connections.mysql.database' => 'inexistente',
        ]);
        DB::purge('mysql');
    }

    protected function restauraBaseDeDados(): void
    {
        config(['database.default' => 'sqlite']);
        DB::purge('mysql');
    }
}
