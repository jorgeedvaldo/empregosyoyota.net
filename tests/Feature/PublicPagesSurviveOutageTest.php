<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Job;
use App\Services\PublicPageCache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * O ensaio que da nome ao problema: a pagina inicial verdadeira, com os
 * seus controladores e as suas vistas, contra uma base de dados a serio —
 * e depois sem ela.
 *
 * Usa sqlite num ficheiro temporario para nao depender de MySQL nenhum (e
 * muito menos para esgotar as 30 ligacoes do alojamento a ver o que
 * acontece). A "queda" e feita a apontar a ligacao para uma porta onde nao
 * esta ninguem, que e o que o visitante sente quando o limite e atingido.
 */
class PublicPagesSurviveOutageTest extends TestCase
{
    protected $db;

    protected function setUp(): void
    {
        parent::setUp();

        config(['app.debug' => false]);

        $this->db = sys_get_temp_dir() . '/yoyota-smoke-' . getmypid() . '.sqlite';
        touch($this->db);

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

        parent::tearDown();
    }

    /** @test */
    public function as_paginas_publicas_sobrevivem_a_queda_do_mysql(): void
    {
        $cache = app(\App\Services\DatabaseFallbackCache::class);

        // 1. MySQL disponivel: pagina gerada normalmente.
        $normal = $this->get('/');
        $normal->assertOk();
        $normal->assertSee('Tecnico de Contabilidade');
        $this->assertNull($normal->headers->get(PublicPageCache::HEADER), 'A primeira visita e gerada, nao servida da cache.');

        $this->assertNotEmpty(Job::getCachedLatest());
        $this->assertNotEmpty(Article::getCachedLatest());

        // 2. Cache preenchida: servida sem tocar na base de dados.
        $cacheada = $this->get('/');
        $cacheada->assertOk();
        $this->assertSame('hit', $cacheada->headers->get(PublicPageCache::HEADER));

        // A listagem de vagas tambem passa a ter copia — e o que o
        // "resilience:warm --pages" faz de meia em meia hora.
        $this->get('/empregos')->assertOk();

        // 3. Publicou-se conteudo (as copias normais deixam de valer) e o
        //    MySQL esgotou as ligacoes. As paginas voltam a ser geradas,
        //    agora a partir das copias de emergencia dos dados.
        app(PublicPageCache::class)->bumpVersion();
        foreach (['latest_jobs_50', 'latest_articles_50', 'categories_all', 'jobs_count_country_1'] as $chave) {
            $cache->forget($chave);
        }

        $this->derrubaBaseDeDados();

        $comDadosDeEmergencia = $this->get('/');
        $comDadosDeEmergencia->assertOk();
        $comDadosDeEmergencia->assertSee('Tecnico de Contabilidade');
        $comDadosDeEmergencia->assertDontSee('SQLSTATE');

        // A paginacao de /empregos precisa de uma contagem: se ela nao
        // viesse tambem da cache, esta pagina respondia 503.
        $listagem = $this->get('/empregos');
        $listagem->assertOk();
        $listagem->assertSee('Tecnico de Contabilidade');
        $listagem->assertDontSee('SQLSTATE');

        // 4. Pior cenario: nem as copias de emergencia dos dados existem.
        //    Entra a copia guardada da propria pagina.
        app(PublicPageCache::class)->bumpVersion();
        foreach (['latest_jobs_50', 'latest_articles_50', 'categories_all', 'jobs_count_country_1'] as $chave) {
            $cache->purge($chave);
        }

        $comPaginaDeEmergencia = $this->get('/');
        $comPaginaDeEmergencia->assertOk();
        $comPaginaDeEmergencia->assertSee('Tecnico de Contabilidade');
        $comPaginaDeEmergencia->assertDontSee('SQLSTATE');
        $this->assertSame('emergency', $comPaginaDeEmergencia->headers->get(PublicPageCache::HEADER));

        // 5. Uma pagina que nunca foi visitada e para a qual nao ha copia
        //    nenhuma: 503 honesto, sem nada tecnico a vista.
        $semCopia = $this->get('/modelos-de-curriculos');
        $semCopia->assertStatus(503);
        $semCopia->assertSee('Serviço temporariamente indisponível', false);
        $semCopia->assertDontSee('SQLSTATE');
        $this->assertNotEmpty($semCopia->headers->get('Retry-After'));
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
}
