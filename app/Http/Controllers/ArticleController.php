<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Job;
use App\Services\DatabaseFallbackCache;
use App\Exceptions\DatabaseUnavailableException;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        // First page is served from cache to avoid hitting the database
        if ($request->get('page', 1) == 1) {
            $perPage = 18;
            $cachedArticles = Article::getCachedLatest();

            $articles = new LengthAwarePaginator(
                $cachedArticles->slice(0, $perPage)->values(),
                Article::getCachedCount(),
                $perPage,
                1,
                ['path' => $request->url(), 'query' => $request->query()]
            );
        } else {
            $articles = Article::where('country_id', 1)->orderByRaw('id DESC')->paginate(18);
        }

        return view('articles', compact('articles'));
    }

    public function getById($id)
    {
        try
        {
            $article = app(DatabaseFallbackCache::class)->remember('article_id_' . $id, 1440, function () use ($id) {
                return Article::where('country_id', 1)->where('id', $id)->firstOrFail();
            });

            $LastArticles = Article::getCachedLatest()->reject(function ($value) use ($id) {
                return $value->id == $id;
            })->values();
            $LastJobs = Job::getCachedLatest();

            return view('article', compact('article', 'LastArticles', 'LastJobs'));
        } catch (DatabaseUnavailableException $e) {
            // MySQL em baixo nao e "pagina inexistente": deixa seguir para
            // o Handler, que serve a copia guardada ou responde 503.
            throw $e;
        } catch (Exception $ex) {
            abort(404);
        }
    }

	public function getBySlug($slug)
    {
        try
        {
            $article = app(DatabaseFallbackCache::class)->remember('article_' . $slug, 1440, function () use ($slug) {
                return Article::where('country_id', 1)->where('slug', $slug)->firstOrFail();
            });

            $LastArticles = Article::getCachedLatest()->reject(function ($value) use ($slug) {
                return $value->slug === $slug;
            })->values();
			$LastJobs = Job::getCachedLatest();

            return view('article', compact('article', 'LastArticles', 'LastJobs'));
        } catch (DatabaseUnavailableException $e) {
            // MySQL em baixo nao e "pagina inexistente": deixa seguir para
            // o Handler, que serve a copia guardada ou responde 503.
            throw $e;
        } catch (Exception $ex) {
            abort(404);
        }
    }
	
	public function getBySlugAMP($slug)
    {
        try
        {
            $article = Article::where('country_id', 1)->where('slug', $slug)->get()[0];

            return view('amp.article', compact('article'));
        } catch (DatabaseUnavailableException $e) {
            // MySQL em baixo nao e "pagina inexistente": deixa seguir para
            // o Handler, que serve a copia guardada ou responde 503.
            throw $e;
        } catch (Exception $ex) {
            abort(404);
        }
    }
}
