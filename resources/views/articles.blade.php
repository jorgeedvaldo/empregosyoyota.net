@extends('template.app')
@section('title', 'Artigos')
@section('description', 'É uma plataforma que reúne oportunidades de emprego no solo angolano, tendo como fonte principal o "Jornal de Angola", criada aos 5 de Dezembro de 2018, a Empregos Yoyota tem ajudado muita gente a encontrar empregos no solo angolano')
@section('canonical_link', url('/articles'))
@section('content')

<section class="jobs-hero">
    <div class="container">
        <h1 class="jobs-hero-title">Blog</h1>
        <p class="jobs-hero-subtitle">Artigos, dicas de carreira e novidades sobre o mercado de trabalho.</p>
        <span class="jobs-hero-count"><i class="bi bi-journal-text"></i> {{ $articles->total() }} artigo(s)</span>
    </div>
</section>

<div class="container my-5">
    <div class="blog-grid">
        @foreach($articles as $article)
            <a href="{{ url('/articles/' . $article['slug']) }}" class="blog-card">
                <div class="blog-card-image">
                    <img src="{{ $article['photo_thumb_url'] }}" alt="{{ $article['title'] }}" loading="lazy" decoding="async">
                </div>
                <div class="blog-card-body">
                    <span class="blog-card-date"><i class="bi bi-calendar3"></i> {{ date_format(new DateTime($article['created_at']), 'd-m-Y') }}</span>
                    <h3 class="blog-card-title">{{ $article['title'] }}</h3>
                    <span class="blog-card-more">Ler mais <i class="bi bi-arrow-right"></i></span>
                </div>
            </a>
        @endforeach
    </div>

    <div class="mt-4">{{ $articles->links() }}</div>
</div>
@endsection('content')
