<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Job;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $jobs = Job::where('country_id', 1)->orderByRaw('id DESC')->paginate(15);
		$articles = Article::where('country_id', 1)->orderByRaw('id DESC')->paginate(8);
        return view('home', compact('jobs', 'articles'));
    }
}
