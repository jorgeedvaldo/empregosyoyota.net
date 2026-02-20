<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Job;
use App\Models\Draft;
use App\Models\CategoryJob;

class JobController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Job::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return Draft::create($request->all());
    }

   public function get()
    {
        $data = Job::where('country_id','1')->with('categories')->latest()->paginate(50);
        $license = 'This API was developed by Edivaldo Jorge (https://github.com/jorgeedvaldo)';
        $message = 'Operation performed successfully.';
        return response()->json(
            [
                'message' => $message,
                'data' => $data,
                'license' => $license
            ]);
    }

    public function getById($id)
    {
        $data = Job::with('categories')->where('id', $id)->get();
        $license = 'This API was developed by Edivaldo Jorge (https://github.com/jorgeedvaldo)';
        $message = 'Operation performed successfully.';
        return response()->json(
            [
                'message' => $message,
                'data' => $data,
                'license' => $license
            ]);
    }

    public function setCategories(Request $request)
    {
        return CategoryJob::create($request->all());
    }
}
