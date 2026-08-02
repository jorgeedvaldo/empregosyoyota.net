@extends('template.app')
@section('title', 'Oportunidades de Emprego')
@section('description', 'É uma plataforma que reúne oportunidades de emprego no solo angolano, tendo como fonte principal o "Jornal de Angola", criada aos 5 de Dezembro de 2018, a Empregos Yoyota tem ajudado muita gente a encontrar empregos no solo angolano')
@section('canonical_link', url('/empregos'))
@section('content')

<section class="jobs-hero">
    <div class="container">
        <h1 class="jobs-hero-title">
            @if(!empty($query))
                Resultados para &ldquo;{{ $query }}&rdquo;
            @else
                Pesquisar Vagas
            @endif
        </h1>
        <p class="jobs-hero-subtitle">Encontre as melhores oportunidades de emprego, atualizadas todos os dias.</p>
        <span class="jobs-hero-count"><i class="bi bi-briefcase"></i> {{ $jobs->total() }} vaga(s) encontrada(s)</span>
    </div>
</section>

<div class="container my-5">
    <div class="row">
        <div class="col-lg-8">
            <form action="{{ route('search') }}" method="GET" class="jobs-search-form">
                <input type="search" class="jobs-search-input" placeholder="Digite a sua pesquisa" aria-label="Search" name="query" value="{{ $query }}"/>
                <button type="submit" class="jobs-search-btn">Pesquisar</button>
            </form>

            @if($jobs->count() > 0)
            <div class="job-list">
                @foreach($jobs as $job)

                    <a href="{{ url('/empregos/' . $job['slug']) }}" class="job-card-item">
                        <div class="job-card-icon">
                            <i class="bi bi-briefcase"></i>
                        </div>
                        <div class="job-card-body">
                            <h5 class="job-card-title">{{ $job['title'] }}</h5>
                            <div class="job-card-meta">
                                <span><i class="bi bi-building"></i> {{ $job['company'] }}</span>
                                <span><i class="bi bi-geo-alt"></i> {{ $job['province'] }}</span>
                                <span><i class="bi bi-calendar3"></i> {{ date_format(new DateTime($job['created_at']), 'd-m-Y') }}</span>
                            </div>
                        </div>
                        <div class="job-card-arrow">
                            <i class="bi bi-arrow-right"></i>
                        </div>
                    </a>

                @endforeach
            </div>
            <div class="mt-4">{{ $jobs->withQueryString()->links() }}</div>
            @else
            <div class="jobs-empty">
                <i class="bi bi-search" style="font-size:2rem;"></i>
                <p class="mt-2 mb-0">Nenhuma vaga encontrada para esta pesquisa.</p>
            </div>
            @endif
        </div>

        <div class="col-lg-4">
            <div class="filter-card">
                <div class="filter-card-header">Filtrar por Categoria</div>
                <div class="filter-card-body">
                    <a href="{{ url('/empregos') }}" class="filter-link">Todos</a>
                    @foreach($categories as $item)
                        <a href="{{ url('/categories/' . $item['id']) }}" class="filter-link">{{ $item['name'] }}</a>
                    @endforeach
                </div>
            </div>
        </div>

    </div>
</div>
@endsection('content')
