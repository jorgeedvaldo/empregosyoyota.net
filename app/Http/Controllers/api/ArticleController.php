<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    private $license = 'This API was developed by Edivaldo Jorge (https://github.com/jorgeedvaldo)';

    /**
     * Cria um novo artigo.
     * O campo "photo" deve conter o caminho devolvido por /articles/upload-image.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'photo' => 'nullable|string|max:255',
            'country_id' => 'nullable|integer',
        ]);

        $article = new Article();
        $article->title = $request->input('title');
        $article->description = $request->input('description');
        $article->photo = $request->input('photo', 'article.jpg');
        // country_id nao esta no fillable; definimos explicitamente (default 1 = Angola)
        $article->country_id = (int) $request->input('country_id', 1);
        $article->save();

        return response()->json(
            [
                'message' => 'Article created successfully.',
                'data' => $article->fresh(),
                'license' => $this->license,
            ], 201);
    }

    /**
     * Upload de imagem para images/articles/ com nome unico (sem colisoes).
     */
    public function uploadImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,jpg,png,gif,webp|max:5120', // max. 5 MB
        ]);

        // store() gera automaticamente um nome unico (hash), evitando colisoes
        $path = $request->file('image')->store('images/articles', 'public');

        return response()->json(
            [
                'message' => 'Image uploaded successfully.',
                'path' => $path,
                'url' => asset('storage/' . $path),
                'license' => $this->license,
            ], 201);
    }
}
