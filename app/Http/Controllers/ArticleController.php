<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Job;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ArticleController extends Controller
{
    public function index()
    {
        $articles = Article::where('country_id', 1)->orderByRaw('id DESC')->paginate(16);
        return view('articles', compact('articles'));
    }

    public function getById($id)
    {
        try
        {
            $article = Article::where('country_id', 1)->where('id', $id)->get()[0];

            $LastArticles = Article::where('country_id', 1)->where('id', '<>', $id)->orderByRaw('id DESC')->get();
            $LastJobs = Job::with('categories')->where('country_id', 1)->orderByRaw('id DESC')->get();

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
            $article = Article::where('country_id', 1)->where('slug', $slug)->get()[0];

            $LastArticles = Article::where('country_id', 1)->where('slug', '<>', $slug)->orderByRaw('id DESC')->get();
			$LastJobs = Job::with('categories')->where('country_id', 1)->orderByRaw('id DESC')->get();

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
