@extends('template.app')

@php
    $pageUrl = url('/vagas-de-emprego-em-angola');

    // Vagas a apresentar na listagem (ultimas de Angola)
    $listaJobs = $jobs->take(30);

    // Cartoes de "Filtros recomendados" (rotulo, titulo, link)
    $filtros = [
        ['Pesquisa geral no país',                  'Vagas de emprego em Angola hoje',  url('/ao/empregos')],
        ['Oportunidades na capital',                'Vagas de emprego em Luanda',       route('search', ['query' => 'Luanda'])],
        ['Nível junior e primeira experiência',     'Vagas para iniciantes em Angola',  route('search', ['query' => 'iniciante'])],
        ['Entrada no mercado de trabalho',          'Vagas sem experiencia em Angola',  route('search', ['query' => 'sem experiência'])],
        ['Oportunidades para estudantes',           'Estagios em Angola',               route('search', ['query' => 'estágio'])],
        ['Expanda pesquisa por cidade',             'Vagas em Benguela',                route('search', ['query' => 'Benguela'])],
        ['Pesquisa ampla sem filtro de vagas',      'Trabalho em Angola',               url('/ao/empregos')],
        ['Vagas com processos ativos',              'Recrutamento em Angola',           route('search', ['query' => 'recrutamento'])],
        ['Setor público e oportunidades relacionadas', 'Concursos publicos em Angola',  route('search', ['query' => 'concurso público'])],
        ['Guia de curriculo e candidatura',         'CV para emprego em Angola',        route('search', ['query' => 'CV'])],
    ];

    // Pesquisas relacionadas (pills)
    $relacionadas = [
        'emprego', 'trabalho', 'recrutamento', 'jobs', 'vagas', 'vagas de emprego', 'cv', 'curriculo',
        'concursos publicos', 'concursos publicos em Angola', 'vaga de emprego em Angola',
        'vagas de emprego em Angola', 'empregos em Angola', 'trabalho em Angola',
        'oportunidades de trabalho em Angola', 'recrutamento em Angola',
        'empresas que contratam em Angola', 'portal de empregos Angola', 'site de vagas em Angola',
        'anuncios de emprego em Angola', 'vagas abertas em Angola', 'vagas atualizadas em Angola',
        'vagas para iniciantes em Angola', 'vagas sem experiencia em Angola', 'primeiro emprego em Angola',
        'emprego para jovens em Angola', 'vagas junior em Angola', 'vagas entry level em Angola',
        'estagios em Angola', 'estagios remunerados em Angola', 'vagas part time em Angola',
        'vagas tempo integral em Angola', 'vagas remotas em Angola', 'trabalho remoto em Angola',
        'vagas presenciais em Angola',
    ];

    // FAQ (perguntas frequentes) - usado no conteudo e no schema FAQPage
    $faqs = [
        [
            'q' => 'Como encontrar vagas de emprego em Angola?',
            'a' => 'Na Empregos Yoyota encontra vagas de emprego em Angola atualizadas diariamente. Pode explorar todas as oportunidades na página de vagas de Angola, filtrar por categoria ou pesquisar por cargo, empresa ou cidade como Luanda e Benguela.',
        ],
        [
            'q' => 'As vagas de emprego em Angola são gratuitas?',
            'a' => 'Sim. A consulta de vagas e a candidatura através da Empregos Yoyota são totalmente gratuitas. Nunca é pedido qualquer pagamento para se candidatar a uma vaga.',
        ],
        [
            'q' => 'Existem vagas para quem não tem experiência?',
            'a' => 'Sim. Publicamos regularmente vagas para iniciantes, primeiro emprego, estágios e oportunidades de nível júnior em Angola, ideais para quem está a entrar no mercado de trabalho.',
        ],
        [
            'q' => 'Onde encontro vagas de emprego em Luanda?',
            'a' => 'Luanda concentra grande parte das oportunidades. Use a pesquisa por cidade para ver as vagas de emprego em Luanda, ou explore outras províncias como Benguela e Huíla.',
        ],
        [
            'q' => 'Há estágios e concursos públicos em Angola?',
            'a' => 'Sim. Além de vagas de empresas privadas, divulgamos estágios (incluindo estágios remunerados) e informação sobre concursos públicos e recrutamento no setor público em Angola.',
        ],
        [
            'q' => 'Com que frequência as vagas são atualizadas?',
            'a' => 'As vagas são atualizadas diariamente. Recomendamos visitar a página com frequência ou instalar a nossa aplicação na Google Play para receber as oportunidades mais recentes no telemóvel.',
        ],
    ];
@endphp

@section('title', 'Vagas de Emprego em Angola')
@section('description', 'Vagas de emprego em Angola atualizadas diariamente: oportunidades em Luanda, Benguela e todo o país, estágios, primeiro emprego, recrutamento e concursos públicos. Encontre e candidate-se gratuitamente.')
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
                'name' => 'Vagas de Emprego em Angola - Empregos Yoyota',
                'description' => 'Vagas de emprego em Angola atualizadas diariamente: oportunidades em Luanda, Benguela e todo o país, estágios, primeiro emprego, recrutamento e concursos públicos.',
                'isPartOf' => ['@id' => 'https://empregosyoyota.net/#website'],
                'inLanguage' => 'pt-AO',
                'breadcrumb' => ['@id' => $pageUrl . '/#breadcrumb'],
            ],
            [
                '@type' => 'BreadcrumbList',
                '@id' => $pageUrl . '/#breadcrumb',
                'itemListElement' => [
                    ['@type' => 'ListItem', 'position' => 1, 'name' => 'Início', 'item' => url('/')],
                    ['@type' => 'ListItem', 'position' => 2, 'name' => 'Vagas de Emprego em Angola'],
                ],
            ],
            [
                '@type' => 'ItemList',
                '@id' => $pageUrl . '/#vagas',
                'name' => 'Últimas vagas de emprego em Angola',
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
                'description' => 'Site Angolano com vagas de emprego em Angola e oportunidades de recrutamento.',
                'publisher' => ['@id' => 'https://empregosyoyota.net/#organization'],
                'inLanguage' => 'pt-AO',
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
        /* mantem o chevron mas em tom escuro em vez do azul padrao */
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
        <h1 class="hero-title">Vagas de emprego em <span class="highlight">Angola</span></h1>
        <p class="hero-subtitle" style="max-width: 900px;">
            Esta é a landing principal para quem pesquisa vagas de emprego em Angola. Aqui encontra
            oportunidades atualizadas, links por cidade e categoria, e atalhos para vagas de entrada,
            júnior e estágio num único ponto de discovery orgânico. Também ligamos para buscas amplas
            como trabalho, recrutamento, jobs, CV e concursos públicos.
        </p>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ url('/ao/empregos') }}" class="trending-tag">Explorar vagas em Angola</a>
            <a href="{{ url('/ao/empregos') }}" class="trending-tag">Ver vagas por localização</a>
            <a href="{{ url('/empregos') }}" class="trending-tag">Ver vagas por categoria</a>
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
    <!-- Vagas por província -->
    <section id="cidades" class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <h2 class="h5 fw-bold mb-4">Vagas por cidade e província em Angola</h2>
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
            <h2 class="h5 fw-bold mb-4">Pesquisas relacionadas a vagas de emprego em Angola</h2>
            <div class="d-flex flex-wrap gap-2">
                @foreach($relacionadas as $termo)
                    <a href="{{ route('search', ['query' => $termo]) }}"
                       class="trending-tag">{{ $termo }}</a>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Últimos empregos de Angola -->
    <section class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <h2 class="h5 fw-bold mb-4">Últimos empregos em Angola</h2>
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
                    <p class="text-muted">De momento não há vagas para mostrar. Volte em breve.</p>
                @endforelse
            </div>
            <div class="text-center mt-4">
                <a href="{{ url('/ao/empregos') }}" class="btn btn-dark btn-lg">Ver todas as vagas em Angola</a>
            </div>
        </div>
    </section>

    <!-- Perguntas frequentes (FAQ) -->
    <section class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <h2 class="h5 fw-bold mb-4">Perguntas frequentes sobre vagas de emprego em Angola</h2>
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
