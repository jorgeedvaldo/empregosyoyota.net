<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use App\Models\Job;
use Exception;
use Illuminate\Http\Request;

class OcrController extends Controller
{
    public function index()
    {
        try
        {
            $LastArticles = Article::getCachedLatest();
            $LastJobs = Job::getCachedLatest();

            $categories = Category::getCachedAll();

            return view('tools.ocr', compact('categories', 'LastArticles', 'LastJobs'));
        }
        catch(Exception $ex)
        {
            abort(404);
        }
    }
	
	public function indexEn()
    {
    	return view('tools.en.ocr');   
    }
	
	public function indexDashboardEn()
    {
    	return view('tools.en.dashboard');   
    }
	public function indexDashboardPt()
    {
    	return view('tools.dashboard');   
    }
    public function indexQuizPt()
    {
    	return view('tools.quiz');   
    }
}
