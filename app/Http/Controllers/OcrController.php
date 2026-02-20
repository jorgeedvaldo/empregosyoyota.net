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
            $LastArticles = Article::orderByRaw('id DESC')->get();
            $LastJobs = Job::with('categories')->where('country_id', 1)->orderByRaw('id DESC')->get();

            $categories = Category::orderBy('name')->get();

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
