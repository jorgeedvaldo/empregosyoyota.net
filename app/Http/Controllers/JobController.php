<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use App\Models\Job;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

class JobController extends Controller
{
    private function getCountryIdFromCode($country)
    {
        $countries = [
            'ao' => 1,
            'br' => 2,
            'mz' => 3,
            'pt' => 5,
        ];

        return $countries[$country] ?? null;
    }

    public function index(Request $request)
    {
        // First page is served from cache to avoid hitting the database
        if ($request->get('page', 1) == 1) {
            $perPage = 30;
            $cachedJobs = Job::getCachedLatest();

            $jobs = new LengthAwarePaginator(
                $cachedJobs->slice(0, $perPage)->values(),
                Job::where('country_id', 1)->count(),
                $perPage,
                1,
                ['path' => $request->url(), 'query' => $request->query()]
            );
        } else {
            $jobs = Job::where('country_id', 1)->orderByRaw('id DESC')->paginate(30);
        }

        $categories = Category::getCachedAll();
        return view('jobs', compact('jobs', 'categories'));
    }

    private function getCountryName($country)
    {
        $names = [
            'ao' => 'Angola',
            'br' => 'Brasil',
            'mz' => 'Moçambique',
            'pt' => 'Portugal',
        ];

        return $names[$country] ?? null;
    }

    public function getByCountry($country)
    {
        $countryId = $this->getCountryIdFromCode($country);

        if (!$countryId) {
            abort(404);
        }

        $jobs = Job::where('country_id', $countryId)
            ->orderByRaw('id DESC')
            ->paginate(30);

        $categories = Category::getCachedAll();

        $title = 'Vagas de Emprego ' . $this->getCountryName($country);

        return view('jobs', compact('jobs', 'categories', 'country', 'title'));
    }

    public function vagasAngola()
    {
        // Landing SEO. Reaproveita o cache das ultimas vagas de Angola.
        $jobs = Job::getCachedLatest();
        $categories = Category::getCachedAll();

        // Interligacao das provincias de Angola (a partir de config/landings.php)
        $cidadesLinks = [];
        foreach (config('landings') as $c) {
            if (($c['type'] ?? null) === 'city' && ($c['country_id'] ?? null) == 1) {
                $cidadesLinks[] = ['name' => $c['name'], 'url' => url($c['slug'])];
            }
        }

        return view('vagas-de-emprego-em-angola', compact('jobs', 'categories', 'cidadesLinks'));
    }

    public function getById($id)
    {
        try {
            $job = Cache::remember('job_id_' . $id, 1440, function () use ($id) {
                return Job::with('categories')->where('country_id', 1)->where('id', $id)->firstOrFail();
            });

            $LastArticles = Article::getCachedLatest();
            $LastJobs = Job::getCachedLatest()->reject(function ($value) use ($id) {
                return $value->id == $id;
            })->values();

            $categories = Category::getCachedAll();

            return view('job', compact('job', 'categories', 'LastArticles', 'LastJobs'));
        } catch (Exception $ex) {
            abort(404);
        }
    }

    public function getByCategoryId($id)
    {
        try {
            $categories = Category::getCachedAll();

            $category = Category::with(['jobs' => function ($query) {
                $query->where('country_id', 1)->orderByRaw('id DESC');
            }])->where('id', $id)->firstOrFail();

            $categoryJobs = $category->jobs;
            return view('category', compact('category', 'categoryJobs', 'categories'));
        } catch (Exception $ex) {
            abort(404);
        }
    }

    public function getBySlug($slug)
    {
        try {
            $job = Cache::remember('job_' . $slug, 1440, function () use ($slug) {
                return Job::with('categories')->where('slug', $slug)->firstOrFail();
            });

            $LastArticles = Article::getCachedLatest();
            $LastJobs = Job::getCachedLatest()->reject(function ($value) use ($slug) {
                return $value->slug === $slug;
            })->values();

            $categories = Category::getCachedAll();

            return view('job', compact('job', 'categories', 'LastArticles', 'LastJobs'));
        } catch (Exception $ex) {
            abort(404);
        }
    }

    public function getBySlugAMP($slug)
    {
        try {
            $job = Job::with('categories')->where('country_id', 1)->where('slug', $slug)->firstOrFail();

            return view('amp.job', compact('job'));
        } catch (Exception $ex) {
            abort(404);
        }
    }

    /**
     * Quantos candidatos por relevancia (TNTSearch) sao trazidos antes
     * de reordenar por relevancia + recencia. Maior = melhor qualidade
     * de reordenacao, mas mais lento.
     */
    const SEARCH_CANDIDATE_POOL = 300;

    /** Peso da relevancia textual vs. da recencia no ranking combinado. */
    const SEARCH_RELEVANCE_WEIGHT = 0.65;
    const SEARCH_RECENCY_WEIGHT = 0.35;

    /** Meia-vida (dias) do decaimento de recencia: com este valor, uma
     *  vaga com este numero de dias tem metade do "peso" de recencia
     *  de uma vaga publicada hoje. */
    const SEARCH_RECENCY_HALFLIFE_DAYS = 60;

    public function search(Request $request)
    {
        $query = trim((string) $request->input('query'));
        $location = trim((string) $request->input('location'));
        $perPage = 30;
        $page = max(1, (int) $request->get('page', 1));

        if ($query === '' && $location === '') {
            // Sem termo nem localizacao: lista simples, mais recentes primeiro.
            $jobs = Job::orderByRaw('id DESC')->paginate($perPage);
        } elseif ($query === '') {
            // So localizacao: filtro direto por provincia, sem passar pelo
            // indice de texto (nao ha nada para o TNTSearch classificar).
            $jobs = Job::where('province', 'LIKE', "%{$location}%")
                ->orderByRaw('id DESC')
                ->paginate($perPage);
        } else {
            // Traz um conjunto maior de candidatos ordenados por relevancia
            // (titulo, empresa, provincia, descricao) via TNTSearch/Scout.
            $candidates = Job::search($query)->take(self::SEARCH_CANDIDATE_POOL)->get()->values();

            // Com localizacao tambem preenchida, restringe aos candidatos
            // cuja provincia bate com o texto indicado.
            if ($location !== '') {
                $candidates = $candidates->filter(function ($job) use ($location) {
                    return stripos((string) $job->province, $location) !== false;
                })->values();
            }

            // Reordena combinando relevancia + recencia, para que vagas
            // antigas com match textual forte nao dominem sempre sobre
            // vagas recentes igualmente relevantes.
            $total = $candidates->count();
            $now = now();

            $ranked = $candidates
                ->map(function ($job, $index) use ($total, $now) {
                    $relevanceScore = $total > 1 ? 1 - ($index / ($total - 1)) : 1;
                    $daysOld = max(0, $now->diffInDays($job->created_at));
                    $recencyScore = exp(-$daysOld / self::SEARCH_RECENCY_HALFLIFE_DAYS);

                    $job->searchScore = (self::SEARCH_RELEVANCE_WEIGHT * $relevanceScore)
                        + (self::SEARCH_RECENCY_WEIGHT * $recencyScore);

                    return $job;
                })
                ->sortByDesc('searchScore')
                ->values();

            $jobs = new LengthAwarePaginator(
                $ranked->slice(($page - 1) * $perPage, $perPage)->values(),
                $ranked->count(),
                $perPage,
                $page,
                ['path' => $request->url(), 'query' => $request->query()]
            );
        }

        $categories = Category::getCachedAll();

        return view('search', compact('categories', 'jobs', 'query', 'location'));
    }

    public function feedGenerator()
    {
        // Feed com vagas (Angola, Brasil e Mocambique) e artigos, ordenados
        // por data. Em cache 30 min (invalidado ao criar/editar vaga ou artigo).
        $items = Cache::remember('rss_feed_items', 1800, function () {
            $jobs = Job::orderByRaw('id DESC')->limit(500)->get()->map(function ($job) {
                return [
                    'title' => $job->title,
                    'url' => url('/empregos/' . $job->slug),
                    'description' => $job->description,
                    'pubDate' => optional($job->created_at)->format(DATE_ATOM),
                    'sort' => optional($job->created_at)->timestamp ?? 0,
                    'guid' => url('/empregos/' . $job->slug),
                    'categories' => ['Emprego'],
                ];
            });

            $articles = Article::where('country_id', 1)->orderByRaw('id DESC')->limit(300)->get()->map(function ($article) {
                return [
                    'title' => $article->title,
                    'url' => url('/articles/' . $article->slug),
                    'description' => $article->description,
                    'pubDate' => optional($article->created_at)->format(DATE_ATOM),
                    'sort' => optional($article->created_at)->timestamp ?? 0,
                    'guid' => url('/articles/' . $article->slug),
                    'categories' => ['Artigo'],
                ];
            });

            return $jobs->concat($articles)->sortByDesc('sort')->take(500)->values();
        });

        return response()->view('xml.feed', compact('items'))->header('Content-Type', 'text/xml');
    }
}
