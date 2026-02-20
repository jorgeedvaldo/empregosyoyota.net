@extends('template.app')
@section('title', 'Artigos')
@section('description', 'É uma plataforma que reúne oportunidades de emprego no solo angolano, tendo como fonte principal o "Jornal de Angola", criada aos 5 de Dezembro de 2018, a Empregos Yoyota tem ajudado muita gente a encontrar empregos no solo angolano')
@section('canonical_link', url('/articles'))
@section('content')

<section class="capa-sobre mb-5" style="background-image: url('{{ asset('storage/images/bg_5.jpg') }}');">
    <div class="container">
        <div class="row">
            <div class="col-md-12 mt-4 p-4 big-text center-all">
                <p class="text-white">Blog</p>
            </div>
        </div>
    </div>
</section>

<div class="container">
    <div class="row mt-5">

        @foreach($articles as $article)
            <div class="col-md-6">
                <a href="{{ url('/articles/' . $article['slug']) }}" class="list-group-item list-group-item-action mb-3">
                    <div class="d-flex w-100 justify-content-between">
                    <h5 class="mb-1"><b>{{ $article['title'] }}</b></h5>
                    <small>Publicado em: {{ date_format(new DateTime($article['created_at']), 'd-m-Y') }}</small>
                    </div>
                    <p class="mb-1"> </p>
                    <small><i class="fa fa-journal"></i>   <span> </span></small>
                </a>
            </div>
          @endforeach

          {{ $articles->links() }}
    </div>
</div>
@endsection('content')
