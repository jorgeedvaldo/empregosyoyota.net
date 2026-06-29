@extends('template.app')

@section('title', $metaTitle)
@section('description', $metaDescription)
@section('canonical_link', $pageUrl)
@section('url', asset('storage/images/logo-180x180.png'))

@section('head-scripts')
@php
    $jobItems = [];
    $pos = 1;
    foreach ($listaJobs as $job) {
        $jobItems[] = [
            '@type' => 'ListItem',
            'position' => $pos++,
            'name' => $job['title'],
            'url' => url('/empregos/' . $job['slug']),
        ];
    }

    $schema = [
        '@context' => 'https://schema.org',
        '@graph' => [
            [
                '@type' => 'WebPage',
                '@id' => $pageUrl . '/#webpage',
                'url' => $pageUrl,
                'name' => $metaTitle . ' - Empregos Yoyota',
                'description' => $metaDescription,
                'isPartOf' => ['@id' => 'https://empregosyoyota.net/#website'],
                'inLanguage' => 'pt',
                'breadcrumb' => ['@id' => $pageUrl . '/#breadcrumb'],
            ],
            [
                '@type' => 'BreadcrumbList',
                '@id' => $pageUrl . '/#breadcrumb',
                'itemListElement' => [
                    ['@type' => 'ListItem', 'position' => 1, 'name' => 'Início', 'item' => url('/')],
                    ['@type' => 'ListItem', 'position' => 2, 'name' => $breadcrumbName],
                ],
            ],
            [
                '@type' => 'ItemList',
                '@id' => $pageUrl . '/#vagas',
                'name' => 'Últimas vagas de emprego em ' . $location,
                'numberOfItems' => count($jobItems),
                'itemListElement' => $jobItems,
            ],
            [
                '@type' => 'FAQPage',
                '@id' => $pageUrl . '/#faq',
                'mainEntity' => array_map(function ($f) {
                    return [
                        '@type' => 'Question',
                        'name' => $f['q'],
                        'acceptedAnswer' => ['@type' => 'Answer', 'text' => $f['a']],
                    ];
                }, $faqs),
            ],
            [
                '@type' => 'WebSite',
                '@id' => 'https://empregosyoyota.net/#website',
                'url' => 'https://empregosyoyota.net/',
                'name' => 'Empregos Yoyota',
                'description' => 'Site com vagas de emprego em Angola, Brasil e Moçambique e oportunidades de recrutamento.',
                'publisher' => ['@id' => 'https://empregosyoyota.net/#organization'],
                'inLanguage' => 'pt',
            ],
            [
                '@type' => 'Organization',
                '@id' => 'https://empregosyoyota.net/#organization',
                'name' => 'Empregos Yoyota',
                'url' => 'https://empregosyoyota.net/',
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => 'https://empregosyoyota.net/assets/logo-yoyota.png',
                    'width' => 1200,
                    'height' => 1200,
                ],
                'sameAs' => [
                    'https://www.facebook.com/EmpregosYoyota',
                    'https://x.com/empregosyoyota',
                    'https://www.instagram.com/empregosyoyota/',
                    'https://www.linkedin.com/company/empregosyoyota/',
                ],
            ],
        ],
    ];
@endphp
<script type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}</script>

<style>
    /* FAQ alinhado com a identidade do site (preto/cinza, sem azuis) */
    .faq-accordion .accordion-item {
        border: 1px solid #e9ecef;
        border-radius: 10px !important;
        margin-bottom: 0.75rem;
        overflow: hidden;
    }
    .faq-accordion .accordion-button {
        font-weight: 600;
        color: #1a1a1a;
        background-color: #fff;
    }
    .faq-accordion .accordion-button:not(.collapsed) {
        color: #000;
        background-color: #f8f9fa;
        box-shadow: none;
    }
    .faq-accordion .accordion-button:focus {
        box-shadow: none;
        border-color: #e9ecef;
    }
    .faq-accordion .accordion-button:not(.collapsed)::after {
        filter: grayscale(1) brightness(0.2);
    }
    .faq-accordion .accordion-body {
        color: #555;
    }
</style>
@endsection

@section('content')
<div class="container my-4">

    <!-- Cabeçalho da landing -->
    <section class="hero-section rounded mb-4" style="padding: 3rem 2rem;">
        <h1 class="hero-title">Vagas de emprego em <span class="highlight">{{ $location }}</span></h1>
        <p class="hero-subtitle" style="max-width: 900px;">{{ $intro }}</p>
        <div class="d-flex flex-wrap gap-2">
            @foreach($buttons as $btn)
                <a href="{{ $btn['url'] }}" class="trending-tag">{{ $btn['label'] }}</a>
            @endforeach
        </div>
    </section>

    <!-- Filtros recomendados -->
    <section class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <h2 class="h5 fw-bold mb-4">Filtros recomendados</h2>
            <div class="row g-3">
                @foreach($filtros as $f)
                <div class="col-md-4">
                    <a href="{{ $f[2] }}" class="d-block h-100 p-3 border rounded text-decoration-none text-reset filtro-card">
                        <small class="text-primary d-block mb-1">{{ $f[0] }}</small>
                        <span class="fw-bold">{{ $f[1] }}</span>
                    </a>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    @if(!empty($cidadesLinks))
    <!-- Vagas por cidade e estado -->
    <section id="cidades" class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <h2 class="h5 fw-bold mb-4">Vagas por cidade e estado no {{ $location }}</h2>
            <div class="d-flex flex-wrap gap-2">
                @foreach($cidadesLinks as $cidade)
                    <a href="{{ $cidade['url'] }}" class="trending-tag">Vagas em {{ $cidade['name'] }}</a>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- Pesquisas relacionadas -->
    <section class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <h2 class="h5 fw-bold mb-4">Pesquisas relacionadas a vagas de emprego em {{ $location }}</h2>
            <div class="d-flex flex-wrap gap-2">
                @foreach($relacionadas as $termo)
                    <a href="{{ route('search', ['query' => $termo]) }}" class="trending-tag">{{ $termo }}</a>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Últimos empregos -->
    <section class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <h2 class="h5 fw-bold mb-4">Últimos empregos em {{ $location }}</h2>
            <div class="job-list">
                @forelse($listaJobs as $job)
                    <a href="{{ url('/empregos/' . $job['slug']) }}" class="job-card-item">
                        <div class="job-card-icon">
                            <i class="bi bi-briefcase"></i>
                        </div>
                        <div class="job-card-body">
                            <h3 class="job-card-title h6 mb-1">{{ $job['title'] }}</h3>
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
                @empty
                    <p class="text-muted">De momento não há vagas para mostrar em {{ $location }}. Volte em breve ou explore todas as oportunidades.</p>
                @endforelse
            </div>
            <div class="text-center mt-4">
                <a href="{{ $exploreUrl }}" class="btn btn-dark btn-lg">Ver todas as vagas em {{ $location }}</a>
            </div>
        </div>
    </section>

    <!-- Perguntas frequentes (FAQ) -->
    <section class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <h2 class="h5 fw-bold mb-4">Perguntas frequentes sobre vagas de emprego em {{ $location }}</h2>
            <div class="accordion faq-accordion" id="faqAccordion">
                @foreach($faqs as $i => $faq)
                <div class="accordion-item">
                    <h3 class="accordion-header" id="faqHeading{{ $i }}">
                        <button class="accordion-button {{ $i === 0 ? '' : 'collapsed' }}" type="button"
                                data-bs-toggle="collapse" data-bs-target="#faqCollapse{{ $i }}"
                                aria-expanded="{{ $i === 0 ? 'true' : 'false' }}" aria-controls="faqCollapse{{ $i }}">
                            {{ $faq['q'] }}
                        </button>
                    </h3>
                    <div id="faqCollapse{{ $i }}" class="accordion-collapse collapse {{ $i === 0 ? 'show' : '' }}"
                         aria-labelledby="faqHeading{{ $i }}" data-bs-parent="#faqAccordion">
                        <div class="accordion-body text-muted">
                            {{ $faq['a'] }}
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

</div>
@endsection
