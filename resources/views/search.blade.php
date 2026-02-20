@extends('template.app')
@section('title', 'Oportunidades de Emprego')
@section('description', 'É uma plataforma que reúne oportunidades de emprego no solo angolano, tendo como fonte principal o "Jornal de Angola", criada aos 5 de Dezembro de 2018, a Empregos Yoyota tem ajudado muita gente a encontrar empregos no solo angolano')
@section('canonical_link', url('/empregos'))
@section('content')
<section class="capa-sobre mb-5" style="background-image: url('{{ asset('storage/images/bg_6.jpg') }}');">
    <div class="container">
        <div class="row">
            <div class="col-md-12 mt-4 p-4 center-all">
                @if(isset($query))
                    <h1 class="text-white">Resultados da pesquisa: {{ $query }}</h1>
                @endif
            </div>
        </div>
    </div>
</section>

<div class="container">
    <div class="row mt-5">
        <div class="col-md-8 p-0 mr-3">
            <form action="{{ route('search') }}" method="GET" class="input-group mb-3">
                <input type="search" class="form-control rounded mr-2" placeholder="Digite a sua pesquisa" aria-label="Search" aria-describedby="search-addon" name="query" value="{{ $query }}"/>
                <button type="submit" class="btn btn-dark" data-mdb-ripple-init>Pesquisar</button>
            </form>
            <div class="list-group">
                @foreach($jobs as $job)

                    <a href="{{ url('/empregos/' . $job['slug']) }}" class="list-group-item list-group-item-action mb-3">
                        <div class="d-flex w-100 justify-content-between">
                        <h5 class="mb-1"><b>{{ $job['title'] }}</b></h5>
                        <small>Publicado em: {{ date_format(new DateTime($job['created_at']), 'd-m-Y') }}</small>
                        </div>
                        <p class="mb-1">Empresa: {{ $job['company'] }}</p>
                        <small><i class="fa fa-map-marker"></i> Localização: <span>{{ $job['province'] }}</span></small>
                    </a>

                @endforeach
            </div>
            {{ $jobs->withQueryString()->links() }}
        </div>

        <div class="col-md-3 p-0 mb-3">
			<div class="nav flex-column nav-pills p-3 m-0 bg-white border">
                <p class="text-muted">Filtrar</p>
                <a href="{{ url('/empregos') }}" class = "nav-link">Todos</a>
				@foreach($categories as $item)
                    <a href="{{ url('/categories/' . $item['id']) }}" class = "nav-link">{{ $item['name'] }}</a>
                @endforeach
    		</div>
        </div>

    </div>
</div>
@endsection('content')
