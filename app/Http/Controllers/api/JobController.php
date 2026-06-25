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

    /**
     * Upload an image to images/jobs/ and return its path.
     * The file is stored with a unique, randomly generated name to
     * avoid collisions.
     */
    public function uploadImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,jpg,png,gif,webp|max:5120', // máx. 5 MB
        ]);

        // store() gera automaticamente um nome unico (hash), evitando colisoes
        $path = $request->file('image')->store('images/jobs', 'public');

        $license = 'This API was developed by Edivaldo Jorge (https://github.com/jorgeedvaldo)';
        $message = 'Image uploaded successfully.';

        return response()->json(
            [
                'message' => $message,
                'path' => $path,
                'url' => asset('storage/' . $path),
                'license' => $license
            ], 201);
    }
}
