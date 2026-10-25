<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Services\EmergencySnapshotService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ApiArticleUpdateTest extends TestCase
{
    protected $db;
    protected $snapshotPath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->db = sys_get_temp_dir() . '/yoyota-api-update-' . getmypid() . '.sqlite';
        touch($this->db);
        $this->snapshotPath = sys_get_temp_dir() . '/yoyota-api-update-snap-' . getmypid() . '.json.gz';
        @unlink($this->snapshotPath);
        config([
            'resilience.snapshot.path' => $this->snapshotPath,
            'resilience.snapshot.lock_path' => $this->snapshotPath . '.lock',
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => $this->db,
        ]);
        DB::purge('sqlite');

        Schema::create('articles', function ($t) {
            $t->increments('id');
            $t->string('title');
            $t->string('slug')->nullable();
            $t->text('description')->nullable();
            $t->string('photo')->nullable();
            $t->integer('country_id')->default(1);
            $t->timestamps();
        });
    }

    protected function tearDown(): void
    {
        @unlink($this->db);
        @unlink($this->snapshotPath);
        @unlink($this->snapshotPath . '.lock');
        parent::tearDown();
    }

    /** @test */
    public function edita_um_artigo_parcialmente_via_api(): void
    {
        $artigo = Article::create([
            'title' => 'Titulo Original',
            'description' => 'Descricao original',
            'photo' => 'original.jpg',
        ]);
        $slugOriginal = $artigo->slug;

        $resposta = $this->postJson("/api/article/update/{$artigo->id}", [
            'title' => 'Titulo Editado',
        ]);

        $resposta->assertOk();
        $resposta->assertJsonPath('data.title', 'Titulo Editado');
        $resposta->assertJsonPath('data.description', 'Descricao original');

        $artigo->refresh();
        $this->assertSame('Titulo Editado', $artigo->title);
        $this->assertSame('Descricao original', $artigo->description, 'Campos nao enviados nao podem ser apagados.');
        $this->assertSame($slugOriginal, $artigo->slug, 'O slug nao pode mudar ao editar.');

        $snapshot = app(EmergencySnapshotService::class);
        $this->assertSame('Titulo Editado', $snapshot->findArticle($artigo->id)['title'], 'A edicao tem de sincronizar o snapshot de imediato.');
    }

    /** @test */
    public function editar_um_artigo_inexistente_da_404(): void
    {
        $resposta = $this->postJson('/api/article/update/999', ['title' => 'X']);
        $resposta->assertStatus(404);
    }

    /** @test */
    public function validacao_rejeita_titulo_vazio(): void
    {
        $artigo = Article::create(['title' => 'X', 'description' => 'Y', 'photo' => 'x.jpg']);

        $resposta = $this->postJson("/api/article/update/{$artigo->id}", ['title' => '']);

        $resposta->assertStatus(422);
    }

    /** @test */
    public function aceita_tambem_o_metodo_put(): void
    {
        $artigo = Article::create(['title' => 'X', 'description' => 'Y', 'photo' => 'x.jpg']);

        $resposta = $this->putJson("/api/article/update/{$artigo->id}", ['title' => 'Z']);

        $resposta->assertOk();
        $this->assertSame('Z', $artigo->fresh()->title);
    }
}
