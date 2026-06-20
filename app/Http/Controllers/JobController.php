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

    public function search(Request $request)
    {
        $query = $request->input('query');

        $jobs = Job::where(function ($q) use ($query) {
                $q->where('title', 'LIKE', "%{$query}%")
                  ->orWhere('description', 'LIKE', "%{$query}%");
            })
            ->orderByRaw('id DESC')
            ->paginate(30);

        $categories = Category::getCachedAll();

        return view('search', compact('categories', 'jobs', 'query'));
    }

    public function siteMapGenerator()
    {
        $jobs = Job::orderByRaw('id DESC')->paginate(500);
        $articles = Article::orderByRaw('id DESC')->paginate(300);
        return response()->view('xml.sitemap', compact('jobs', 'articles'))->header('Content-Type', 'text/xml');
    }

    public function feedGenerator()
    {
        $jobs = Job::orderByRaw('id DESC')->paginate(10);
        return response()->view('xml.feed', compact('jobs'))->header('Content-Type', 'text/xml');
    }
}
