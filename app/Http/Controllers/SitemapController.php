<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use App\Models\Curriculo;
use App\Models\Job;

class SitemapController extends Controller
{
    /**
     * Numero maximo de URLs por sub-sitemap.
     * (o limite do protocolo e 50.000; usamos um valor menor para
     *  manter os ficheiros leves.)
     */
    const CHUNK = 2000;

    /** Tempo de vida da cache dos sitemaps (6 horas). */
    const TTL = 21600;

    /**
     * Cache versionada: a chave inclui uma "versao" que e incrementada
     * sempre que uma vaga/artigo/categoria/curriculo muda, invalidando
     * instantaneamente todos os sub-sitemaps sem os ter de enumerar.
     */
    private function cached(string $suffix, \Closure $callback)
    {
        $version = (int) \Illuminate\Support\Facades\Cache::rememberForever('sitemap_version', fn () => 1);

        return \Illuminate\Support\Facades\Cache::remember(
            "sitemap_v{$version}_{$suffix}",
            self::TTL,
            $callback
        );
    }

    /**
     * Indice de sitemaps: /sitemap.xml
     */
    public function index()
    {
        $sitemaps = $this->cached('index', function () {
            $now = now()->toAtomString();
            $sitemaps = [];

            $sitemaps[] = ['loc' => url('/sitemap-pages.xml'), 'lastmod' => $now];
            $sitemaps[] = ['loc' => url('/sitemap-categories.xml'), 'lastmod' => $now];

            foreach ($this->pageRange(Job::count()) as $i) {
                $sitemaps[] = ['loc' => url("/sitemap-jobs-{$i}.xml"), 'lastmod' => $now];
            }
            foreach ($this->pageRange(Article::where('country_id', 1)->count()) as $i) {
                $sitemaps[] = ['loc' => url("/sitemap-articles-{$i}.xml"), 'lastmod' => $now];
            }
            foreach ($this->pageRange(Curriculo::count()) as $i) {
                $sitemaps[] = ['loc' => url("/sitemap-curriculos-{$i}.xml"), 'lastmod' => $now];
            }

            return $sitemaps;
        });

        return $this->xml('xml.sitemap-index', ['sitemaps' => $sitemaps]);
    }

    /**
     * Paginas fixas + landings SEO: /sitemap-pages.xml
     */
    public function pages()
    {
        $now = now()->toAtomString();
        $urls = [];

        // Homepage
        $urls[] = ['loc' => url('/'), 'lastmod' => $now, 'changefreq' => 'daily', 'priority' => '1.0'];

        // Listagens e landings principais (conteudo que muda com frequencia)
        $daily = [
            '/empregos', '/articles', '/modelos-de-curriculos',
            '/ao/empregos', '/br/empregos', '/mz/empregos', '/pt/empregos',
            '/vagas-de-emprego-em-angola',
        ];
        foreach ($daily as $path) {
            $urls[] = ['loc' => url($path), 'lastmod' => $now, 'changefreq' => 'daily', 'priority' => '0.8'];
        }

        // Paginas institucionais / ferramentas (mudam pouco)
        $static = ['/about', '/terms', '/api-docs', '/onlineocr', '/en/onlineocr', '/quiz', '/dashboard', '/en/dashboard', '/biblioteca-de-documentos-legais'];
        foreach ($static as $path) {
            $urls[] = ['loc' => url($path), 'lastmod' => $now, 'changefreq' => 'monthly', 'priority' => '0.4'];
        }

        // Landings SEO de vagas por pais/cidade
        foreach (config('landings') as $cfg) {
            $urls[] = ['loc' => url($cfg['slug']), 'lastmod' => $now, 'changefreq' => 'weekly', 'priority' => '0.7'];
        }

        return $this->xml('xml.sitemap-urlset', ['urls' => $urls]);
    }

    /**
     * Categorias: /sitemap-categories.xml
     */
    public function categories()
    {
        $urls = $this->cached('categories', function () {
            $now = now()->toAtomString();

            return Category::orderBy('id')->get()->map(function ($c) use ($now) {
                return [
                    'loc' => url('/categories/' . $c->id),
                    'lastmod' => optional($c->updated_at)->toAtomString() ?: $now,
                    'changefreq' => 'weekly',
                    'priority' => '0.6',
                ];
            })->all();
        });

        return $this->xml('xml.sitemap-urlset', ['urls' => $urls]);
    }

    /**
     * Vagas paginadas: /sitemap-jobs-{page}.xml
     */
    public function jobs($page)
    {
        $page = max(1, (int) $page);

        $urls = $this->cached("jobs_{$page}", fn () => $this->chunkUrls(
            Job::orderByRaw('id DESC'),
            $page,
            fn ($job) => [
                'loc' => url('/empregos/' . $job->slug),
                'lastmod' => optional($job->updated_at)->toAtomString(),
                'changefreq' => 'weekly',
                'priority' => '0.7',
                'image' => $job->photo ? asset('storage/' . $job->photo) : null,
            ]
        ));

        return $this->xml('xml.sitemap-urlset', ['urls' => $urls]);
    }

    /**
     * Artigos paginados: /sitemap-articles-{page}.xml
     */
    public function articles($page)
    {
        $page = max(1, (int) $page);

        $urls = $this->cached("articles_{$page}", fn () => $this->chunkUrls(
            Article::where('country_id', 1)->orderByRaw('id DESC'),
            $page,
            fn ($article) => [
                'loc' => url('/articles/' . $article->slug),
                'lastmod' => optional($article->updated_at)->toAtomString(),
                'changefreq' => 'monthly',
                'priority' => '0.6',
                'image' => $article->photo ? asset('storage/' . $article->photo) : null,
            ]
        ));

        return $this->xml('xml.sitemap-urlset', ['urls' => $urls]);
    }

    /**
     * Modelos de curriculo paginados: /sitemap-curriculos-{page}.xml
     */
    public function curriculos($page)
    {
        $page = max(1, (int) $page);

        $urls = $this->cached("curriculos_{$page}", fn () => $this->chunkUrls(
            Curriculo::orderByRaw('id DESC'),
            $page,
            fn ($cv) => [
                'loc' => url('/modelos-de-curriculos/' . $cv->slug),
                'lastmod' => optional($cv->updated_at)->toAtomString(),
                'changefreq' => 'monthly',
                'priority' => '0.5',
                'image' => $cv->photo ? asset('storage/' . $cv->photo) : null,
            ]
        ));

        return $this->xml('xml.sitemap-urlset', ['urls' => $urls]);
    }

    /**
     * Devolve o intervalo de paginas [1..N] para um dado total.
     */
    private function pageRange(int $total): array
    {
        return range(1, max(1, (int) ceil($total / self::CHUNK)));
    }

    /**
     * Aplica o "chunk" a uma query e mapeia cada registo para uma entrada de URL.
     */
    private function chunkUrls($query, int $page, callable $map): array
    {
        $page = max(1, $page);

        $items = $query->skip(($page - 1) * self::CHUNK)
            ->take(self::CHUNK)
            ->get();

        abort_if($items->isEmpty() && $page > 1, 404);

        return $items->map($map)->all();
    }

    private function xml(string $view, array $data)
    {
        return response()
            ->view($view, $data)
            ->header('Content-Type', 'text/xml; charset=UTF-8');
    }
}
