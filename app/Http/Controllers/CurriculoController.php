<?php

namespace App\Http\Controllers;

use App\Models\Curriculo;
use Illuminate\Http\Request;

class CurriculoController extends Controller
{
    public function index()
    {
        $curriculos = Curriculo::orderByRaw('id DESC')->paginate(15);
        return view('curriculos', compact('curriculos'));
    }

    public function getBySlug($slug)
    {
        try
        {
            $curriculo = Curriculo::where('slug', $slug)->get()[0];
            return view('curriculo', compact('curriculo'));
        }
        catch(Exception $ex)
        {
            abort(404);
        }
    }
}
