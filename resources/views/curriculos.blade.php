@extends('template.app')
@section('title', 'Modelos de Currículos')
@section('description', 'É uma plataforma que reúne oportunidades de emprego no solo angolano, tendo como fonte principal o "Jornal de Angola", criada aos 5 de Dezembro de 2018, a Empregos Yoyota tem ajudado muita gente a encontrar empregos no solo angolano')
@section('canonical_link', url('/modelos-de-curriculos'))
@section('content')

<section class="capa-sobre mb-5" style="background-image: url('{{ asset('storage/images/bg_5.jpg') }}');">
    <div class="container">
        <div class="row">
            <div class="col-md-12 mt-4 p-4 big-text center-all">
                <p class="text-white">Modelos de Currículos</p>
            </div>
        </div>
    </div>
</section>

<div class="container">
    <div class="row mt-5">

        @foreach($curriculos as $curriculo)
            <div class="col-md-4 mb-5">
                <div class="card" style="width: auto;">
                    <img src="{{asset('storage/' . $curriculo['photo'])}}" class="card-img-top my-thumb" alt="...">
                    <div class="card-body">
                    <a href="{{ url('/modelos-de-curriculos/' . $curriculo['slug']) }}"><h5 class="card-title">{{ $curriculo['title'] }}</h5></a>
                    <p class="card-text">{!! \Illuminate\Support\Str::limit(strip_tags($curriculo['description']), 91, $end='...') !!}</p>
                    <a href="{{ $curriculo['link'] }}" class="btn btn-dark">Download</a>
                    </div>
                </div>
            </div>
        @endforeach
        {{ $curriculos->links() }}
    </div>
</div>
@endsection('content')
