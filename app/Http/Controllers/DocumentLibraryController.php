<?php

namespace App\Http\Controllers;

class DocumentLibraryController extends Controller
{
    public function index()
    {
        return view('documentos-legais');
    }
}
