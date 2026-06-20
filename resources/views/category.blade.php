@extends('template.app')
@section('title', 'Categorias')
@section('description', 'É uma plataforma que reúne oportunidades de emprego no solo angolano, tendo como fonte principal o "Jornal de Angola", criada aos 5 de Dezembro de 2018, a Empregos Yoyota tem ajudado muita gente a encontrar empregos no solo angolano')
@section('canonical_link', url('/categories/'. $category['id']))
@section('content')
<div class="container">
    <div class="row mt-5">
        <div class="col-md-3 p-0 mb-3">
			<div class="nav flex-column nav-pills p-3 m-0 bg-white border">
                <p class="text-muted">Filtrar</p>
                <a href="{{ url('/empregos') }}" class = "nav-link">Todos</a>
				@foreach($categories as $item)
                    <a href="{{ url('/categories/' . $item['id']) }}" class = "nav-link">{{ $item['name'] }}</a>
                @endforeach
    		</div>
        </div>

        <div class="col-md-8 p-0 ml-3">
            <div class="job-list">
                @foreach($categoryJobs as $job)

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
        </div>

    </div>
</div>
@endsection('content')
