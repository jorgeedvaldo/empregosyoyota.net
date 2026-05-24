<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use App\Models\Job;
use Exception;
use Illuminate\Http\Request;

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

    public function index()
    {
        $jobs = Job::where('country_id', 1)->orderByRaw('id DESC')->paginate(30);
        $categories = Category::orderBy('name')->get();
        return view('jobs', compact('jobs', 'categories'));
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

        $categories = Category::orderBy('name')->get();

        return view('jobs', compact('jobs', 'categories', 'country'));
    }

    public function getById($id)
    {
        try {
            $job = Job::with('categories')->where('country_id', 1)->where('id', $id)->firstOrFail();

            $LastArticles = Article::orderByRaw('id DESC')->get();
            $LastJobs = Job::with('categories')->where('country_id', 1)->where('id', '<>', $id)->orderByRaw('id DESC')->get();

            $categories = Category::orderBy('name')->get();

            return view('job', compact('job', 'categories', 'LastArticles', 'LastJobs'));
        } catch (Exception $ex) {
            abort(404);
        }
    }

    public function getByCategoryId($id)
    {
        try {
            $categories = Category::orderBy('name')->get();

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
            $job = Job::with('categories')->where('slug', $slug)->firstOrFail();

            $LastArticles = Article::orderByRaw('id DESC')->get();
            $LastJobs = Job::with('categories')
                ->where('country_id', $job->country_id)
                ->where('slug', '<>', $slug)
                ->orderByRaw('id DESC')
                ->get();

            $categories = Category::orderBy('name')->get();

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

        $categories = Category::orderBy('name')->get();

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
