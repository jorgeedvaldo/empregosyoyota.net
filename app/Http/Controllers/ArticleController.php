<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Job;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        // First page is served from cache to avoid hitting the database
        if ($request->get('page', 1) == 1) {
            $perPage = 16;
            $cachedArticles = Article::getCachedLatest();

            $articles = new LengthAwarePaginator(
                $cachedArticles->slice(0, $perPage)->values(),
                Article::where('country_id', 1)->count(),
                $perPage,
                1,
                ['path' => $request->url(), 'query' => $request->query()]
            );
        } else {
            $articles = Article::where('country_id', 1)->orderByRaw('id DESC')->paginate(16);
        }

        return view('articles', compact('articles'));
    }

    public function getById($id)
    {
        try
        {
            $article = Cache::remember('article_id_' . $id, 1440, function () use ($id) {
                return Article::where('country_id', 1)->where('id', $id)->firstOrFail();
            });

            $LastArticles = Article::getCachedLatest()->reject(function ($value) use ($id) {
                return $value->id == $id;
            })->values();
            $LastJobs = Job::getCachedLatest();

            return view('article', compact('article', 'LastArticles', 'LastJobs'));
        }
        catch(Exception $ex)
        {
            abort(404);
        }
    }

	public function getBySlug($slug)
    {
        try
        {
            $article = Cache::remember('article_' . $slug, 1440, function () use ($slug) {
                return Article::where('country_id', 1)->where('slug', $slug)->firstOrFail();
            });

            $LastArticles = Article::getCachedLatest()->reject(function ($value) use ($slug) {
                return $value->slug === $slug;
            })->values();
			$LastJobs = Job::getCachedLatest();

            return view('article', compact('article', 'LastArticles', 'LastJobs'));
        }

        catch(Exception $ex)
        {
            abort(404);
        }
    }
	
	public function getBySlugAMP($slug)
    {
        try
        {
            $article = Article::where('country_id', 1)->where('slug', $slug)->get()[0];

            return view('amp.article', compact('article'));
        }
        catch(Exception $ex)
        {
            abort(404);
        }
    }
}
