<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Job;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $jobs = Job::getCachedLatest()->take(15);
        $articles = Article::getCachedLatest()->take(8);
        return view('home', compact('jobs', 'articles'));
    }
}
