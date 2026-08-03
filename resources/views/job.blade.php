@extends('template.app')

@php
    $countryIdToCode = [1 => 'ao', 2 => 'br', 3 => 'mz', 5 => 'pt'];
    $countryCode     = $countryIdToCode[$job->country_id] ?? 'ao';
    $countryNames    = ['ao' => 'Angola', 'br' => 'Brasil', 'mz' => 'Moçambique', 'pt' => 'Portugal'];
    $countryISO      = ['ao' => 'AO',     'br' => 'BR',     'mz' => 'MZ',         'pt' => 'PT'];
    $countryName     = $countryNames[$countryCode];

    // Canal/grupo de WhatsApp de vagas, especifico por pais.
    $whatsappChannels = [
        'ao' => ['url' => 'https://whatsapp.com/channel/0029VaCfSeo0bIdgKs7bIB3t', 'label' => 'canal'],
        'br' => ['url' => 'https://whatsapp.com/channel/0029VaRkVRf6GcGIcaA7LS0S', 'label' => 'canal'],
        'mz' => ['url' => 'https://chat.whatsapp.com/BLXhPWYKjQW4th1arYBvuY?s=cl&p=a&ilr=4', 'label' => 'grupo'],
        'pt' => ['url' => 'https://whatsapp.com/channel/0029VbDbMVCInlqM8SYu2B0q', 'label' => 'canal'],
    ];
    $whatsappChannel = $whatsappChannels[$countryCode] ?? $whatsappChannels['ao'];

    $jobUrl  = isset($country)
        ? url("/{$country}/empregos/{$job['slug']}")
        : url("/empregos/{$job['slug']}");

    $jobsUrl = isset($country)
        ? url("/{$country}/empregos")
        : url('/empregos');

    $siteUrl = url('/');

    $previousJob    = $LastJobs->where('id', '<', $job['id'])->first();
    $previousJobUrl = $previousJob
        ? (isset($country)
            ? url("/{$country}/empregos/{$previousJob->slug}")
            : url("/empregos/{$previousJob->slug}"))
        : null;
@endphp

@section('title', $job['title'])
@section('description', strip_tags($job['description']))
@section('url', asset('storage/' . $job['photo']))
@section('canonical_link', $jobUrl)
@section('created_at', $job->created_at)
@section('updated_at', $job->updated_at)

@section('head-scripts')
{{-- Pre-carrega a imagem principal (elemento LCP) para acelerar o carregamento --}}
@if(!empty($job['photo']))
<link rel="preload" as="image" href="{{ asset('storage/' . $job['photo']) }}" fetchpriority="high">
@endif
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@graph": [
        {
            "@type": "WebPage",
            "@id": "{{ $jobUrl }}",
            "url": "{{ $jobUrl }}",
            "name": "{{ $job['title'] }} - Empregos Yoyota",
            "isPartOf": {"@id": "{{ $siteUrl }}/#website"},
            "primaryImageOfPage": {"@id": "{{ $jobUrl }}/#primaryimage"},
            "image": {"@id": "{{ $jobUrl }}/#primaryimage"},
            "thumbnailUrl": "{{ asset('storage/' . $job['photo']) }}",
            "datePublished": "{{ date_format(new DateTime($job['created_at']), DATE_ATOM) }}",
            "dateModified": "{{ date_format(new DateTime($job['updated_at']), DATE_ATOM) }}",
            "inLanguage": "pt-PT",
            "potentialAction": [{"@type": "ReadAction", "target": ["{{ $jobUrl }}"]}]
        },
        {
            "@type": "ImageObject",
            "inLanguage": "pt-PT",
            "@id": "{{ $jobUrl }}/#primaryimage",
            "url": "{{ asset('storage/' . $job['photo']) }}",
            "contentUrl": "{{ asset('storage/' . $job['photo']) }}",
            "width": 918,
            "height": 612
        },
        {
            "@type": "BreadcrumbList",
            "@id": "{{ $jobUrl }}/#breadcrumb",
            "itemListElement": [
                {"@type": "ListItem", "position": 1, "name": "Início", "item": "{{ $siteUrl }}"},
                {"@type": "ListItem", "position": 2, "name": "Vagas de Empregos", "item": "{{ $jobsUrl }}"},
                {"@type": "ListItem", "position": 3, "name": {!! json_encode($job['title']) !!}}
            ]
        },
        {
            "@type": "WebSite",
            "@id": "{{ $siteUrl }}/#website",
            "url": "{{ $siteUrl }}/",
            "name": "Empregos Yoyota",
            "description": "Vagas de emprego, estágio e bolsas de estudo",
            "publisher": {"@id": "{{ $siteUrl }}/#organization"},
            "potentialAction": [{
                "@type": "SearchAction",
                "target": {"@type": "EntryPoint", "urlTemplate": "{{ url('/pesquisar') }}?query={search_term_string}"},
                "query-input": "required name=search_term_string"
            }],
            "inLanguage": "pt-PT"
        },
        {
            "@type": "Organization",
            "@id": "{{ $siteUrl }}/#organization",
            "name": "Empregos Yoyota",
            "url": "{{ $siteUrl }}/",
            "logo": {
                "@type": "ImageObject",
                "inLanguage": "pt-PT",
                "@id": "{{ $siteUrl }}/#/schema/logo/image/",
                "url": "{{ asset('storage/images/logo-full.jpg') }}",
                "contentUrl": "{{ asset('storage/images/logo-full.jpg') }}",
                "width": 512,
                "height": 512,
                "caption": "Empregos Yoyota"
            },
            "image": {"@id": "{{ $siteUrl }}/#/schema/logo/image/"},
            "sameAs": ["https://web.facebook.com/empregosyoyota"]
        },
        {
            "@type": "JobPosting",
            "datePosted": "{{ date_format(new DateTime($job['created_at']), DATE_ATOM) }}",
            "validThrough": "{{ date('Y-m-d\TH:i', strtotime($job->created_at . ' +45 days')) }}",
            "title": {!! json_encode($job['title']) !!},
            "description": {!! json_encode($job['description']) !!},
            "employmentType": ["FULL_TIME"],
            "hiringOrganization": {
                "@type": "Organization",
                "name": {!! json_encode($job['company']) !!},
                "logo": "{{ asset('storage/' . $job['photo']) }}"
            },
            "identifier": {
                "@type": "PropertyValue",
                "name": {!! json_encode($job['company']) !!},
                "value": "{{ $jobUrl }}"
            },
            "jobLocation": {
                "@type": "Place",
                "address": {
                    "@type": "PostalAddress",
                    "addressLocality": {!! json_encode($job['province']) !!},
                    "addressCountry": "{{ $countryISO[$countryCode] }}"
                }
            }
        }
    ]
}
</script>
@endsection

@section('content')
<style>
    .job-hero {
        background: #ffffff;
        border-bottom: 1px solid #e9ecef;
        padding: 48px 0 40px;
    }

    .job-breadcrumb {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        color: #6c757d;
        text-decoration: none;
        font-weight: 600;
        font-size: 0.9rem;
        margin-bottom: 1.5rem;
    }

    .job-breadcrumb:hover {
        color: #000000;
    }

    .job-badges {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-bottom: 1.25rem;
    }

    .job-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        background: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 20px;
        padding: 0.4rem 1rem;
        font-size: 0.85rem;
        font-weight: 600;
        color: #333333;
    }

    .job-title {
        font-size: clamp(1.75rem, 4.5vw, 2.75rem);
        font-weight: 900;
        color: #000000;
        line-height: 1.2;
        margin-bottom: 0.75rem;
    }

    .job-company {
        font-size: 1.15rem;
        font-weight: 600;
        color: #6c757d;
        margin-bottom: 1.75rem;
    }

    .job-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
    }

    .btn-apply {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: #000000;
        color: #ffffff;
        border: 2px solid #000000;
        border-radius: 25px;
        padding: 0.75rem 2rem;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .btn-apply:hover {
        background: #ffffff;
        color: #000000;
    }

    .btn-share {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        border-radius: 25px;
        padding: 0.75rem 1.4rem;
        font-weight: 600;
        font-size: 0.9rem;
        text-decoration: none;
        color: #ffffff;
        transition: all 0.3s ease;
    }

    .btn-share.whatsapp { background: #25D366; }
    .btn-share.whatsapp:hover { background: #1da851; }
    .btn-share.linkedin { background: #0A66C2; }
    .btn-share.linkedin:hover { background: #084d92; }

    .job-section { padding: 3rem 0; }

    .job-image-card {
        border-radius: 16px;
        overflow: hidden;
        margin-bottom: 1.5rem;
        box-shadow: 0 4px 20px rgba(0,0,0,0.05);
    }

    .btn-next-job {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        width: 100%;
        background: #ffffff;
        color: #000000;
        border: 2px solid #000000;
        border-radius: 25px;
        padding: 0.75rem 1.5rem;
        font-weight: 700;
        text-decoration: none;
        margin-bottom: 1.5rem;
        transition: all 0.3s ease;
    }

    .btn-next-job:hover {
        background: #000000;
        color: #ffffff;
    }

    .job-card {
        background: #ffffff;
        border: 1px solid #e9ecef;
        border-radius: 16px;
        padding: 2rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 4px 20px rgba(0,0,0,0.05);
    }

    .job-card-title {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 1.3rem;
        font-weight: 800;
        color: #000000;
        margin-bottom: 1.25rem;
    }

    .job-card .content {
        color: #333333;
        line-height: 1.8;
        font-size: 1.05rem;
    }

    .job-card .content p { margin-bottom: 1rem; }
    .job-card .content ul,
    .job-card .content ol { padding-left: 1.5rem; margin-bottom: 1rem; }
    .job-card .content li { margin-bottom: 0.5rem; }

    .job-meta-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .job-meta-list li {
        display: flex;
        flex-wrap: wrap;
        gap: 0.4rem 0.75rem;
        padding: 0.75rem 0;
        border-bottom: 1px solid #f1f1f1;
    }

    .job-meta-list li:last-child { border-bottom: none; }

    .job-meta-label {
        font-weight: 700;
        color: #000000;
        min-width: 180px;
    }

    .job-meta-value { color: #555555; word-break: break-word; }

    .whatsapp-cta {
        background: #000000;
        color: #ffffff;
        border-radius: 16px;
        padding: 1.75rem 2rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .whatsapp-cta p { opacity: 0.8; font-size: 0.9rem; }

    .whatsapp-cta a {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        background: #25D366;
        color: #ffffff;
        border-radius: 25px;
        padding: 0.65rem 1.5rem;
        text-decoration: none;
        font-weight: 700;
        white-space: nowrap;
    }

    .law-banner {
        background: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 16px;
        padding: 2.5rem;
        text-align: center;
        margin: 0.5rem 0 1.5rem;
    }

    .law-banner h2 {
        font-size: 1.4rem;
        font-weight: 800;
        color: #000000;
        margin-bottom: 1rem;
    }

    .law-banner p {
        color: #6c757d;
        max-width: 600px;
        margin: 0 auto 1.5rem;
    }

    .sidebar-card {
        background: #ffffff;
        border: 1px solid #e9ecef;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        margin-bottom: 1.5rem;
    }

    .sidebar-card-header {
        background: #000000;
        color: #ffffff;
        font-weight: 800;
        font-size: 1.05rem;
        padding: 1rem 1.5rem;
        display: flex;
        align-items: center;
    }

    .sidebar-item {
        display: block;
        padding: 1rem 1.5rem;
        text-decoration: none;
        color: inherit;
        border-bottom: 1px solid #f1f1f1;
        transition: background 0.2s ease;
    }

    .sidebar-item:last-child { border-bottom: none; }
    .sidebar-item:hover { background: #f8f9fa; }

    .sidebar-item-title {
        font-weight: 700;
        color: #000000;
        font-size: 0.95rem;
        margin-bottom: 0.25rem;
    }

    .sidebar-item-meta { color: #6c757d; font-size: 0.85rem; }

    @media (max-width: 768px) {
        .job-hero { padding: 32px 0 28px; }
        .job-section { padding: 2rem 0; }
        .job-card { padding: 1.5rem; }
        .whatsapp-cta { flex-direction: column; align-items: flex-start; }
        .job-meta-label { min-width: 100%; }
    }
</style>

@php
    $emailOrLink = trim((string) ($job['email_or_link'] ?? ''));
    $applyHref = null;
    if ($emailOrLink !== '') {
        if (filter_var($emailOrLink, FILTER_VALIDATE_EMAIL)) {
            $applyHref = 'mailto:' . $emailOrLink;
        } elseif (preg_match('/^https?:\/\//i', $emailOrLink)) {
            $applyHref = $emailOrLink;
        }
    }
@endphp

<!-- Hero -->
<section class="job-hero">
    <div class="container">
        <a href="{{ $jobsUrl }}" class="job-breadcrumb"><i class="bi bi-arrow-right" style="transform:scaleX(-1);"></i> Voltar às vagas</a>

        <div class="job-badges">
            <span class="job-badge"><i class="bi bi-geo-alt"></i> {{ $job['province'] }}</span>
            <span class="job-badge">{{ $countryName }}</span>
            <span class="job-badge"><i class="bi bi-briefcase"></i> Tempo Inteiro</span>
            <span class="job-badge"><i class="bi bi-calendar3"></i> {{ date_format(new DateTime($job['created_at']), 'd-m-Y') }}</span>
        </div>

        <h1 class="job-title">{{ $job['title'] }}</h1>
        <p class="job-company"><i class="bi bi-building me-1"></i>{{ $job['company'] }}</p>

        <div class="job-actions">
            @if($applyHref)
                @if(str_starts_with($applyHref, 'http'))
                <a href="{{ $applyHref }}" class="btn-apply" target="_blank" rel="noopener">
                    <i class="bi bi-send-fill"></i> Candidatar-se
                </a>
                @else
                <a href="{{ $applyHref }}" class="btn-apply">
                    <i class="bi bi-send-fill"></i> Candidatar-se
                </a>
                @endif
            @else
                <a href="#candidatura" class="btn-apply">
                    <i class="bi bi-send-fill"></i> Ver Como Candidatar-se
                </a>
            @endif

            <a class="btn-share whatsapp"
               href="https://api.whatsapp.com/send?text={{ urlencode($job['title'] . "\n" . $jobUrl) }}"
               target="_blank" rel="noopener">
                <i class="bi bi-whatsapp"></i> Partilhar
            </a>
            <a class="btn-share linkedin"
               href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode($jobUrl) }}"
               target="_blank" rel="noopener">
                <i class="bi bi-linkedin"></i> LinkedIn
            </a>
        </div>
    </div>
</section>

<section class="job-section">
<div class="container">
    <div class="row">

        <!-- Conteúdo Principal -->
        <div class="col-lg-8">

            <div class="job-image-card">
                <img class="img-fluid" src="{{ asset('storage/' . $job['photo']) }}" alt="{{ $job['title'] }}" fetchpriority="high" decoding="async" style="width:100%;height:auto;display:block;">
            </div>

            @if($previousJobUrl)
            <a href="{{ $previousJobUrl }}" class="btn-next-job">Ver Próxima Vaga <i class="bi bi-arrow-right"></i></a>
            @endif

            <!-- Anúncio 1 -->
            @include('partials.adsense', ['slot' => '5838441610', 'format' => 'auto', 'responsive' => true])

            <!-- Anúncio em artigo -->
            @include('partials.adsense', ['slot' => '7583808877', 'layout' => 'in-article', 'format' => 'fluid', 'style' => 'display:block; text-align:center;'])

            <div class="job-card">
                <h2 class="job-card-title"><i class="bi bi-file-earmark-text"></i> Descrição da Vaga</h2>
                <div class="content">
                    {!! $job['description'] !!}
                </div>
            </div>

            <div class="job-card" id="candidatura">
                <h2 class="job-card-title"><i class="bi bi-send"></i> Como Candidatar-se</h2>
                <ul class="job-meta-list">
                    <li><span class="job-meta-label">Empresa</span><span class="job-meta-value">{{ $job['company'] }}</span></li>
                    <li><span class="job-meta-label">E-mail ou link de candidatura</span><span class="job-meta-value">{{ $job['email_or_link'] }}</span></li>
                </ul>
            </div>

            <div class="whatsapp-cta">
                <div>
                    <strong>Entre no nosso {{ $whatsappChannel['label'] }} do WhatsApp</strong>
                    <p class="mb-0">Receba as vagas de emprego em {{ $countryName }} em primeira mão.</p>
                </div>
                <a href="{{ $whatsappChannel['url'] }}" target="_blank" rel="noopener">
                    <i class="bi bi-whatsapp"></i> Entrar no {{ ucfirst($whatsappChannel['label']) }}
                </a>
            </div>

            <!-- Divulgação do App -->
            @include('partials.app-download')

            @if($previousJobUrl)
            <a href="{{ $previousJobUrl }}" class="btn-next-job">Ver Próxima Vaga <i class="bi bi-arrow-right"></i></a>
            @endif

            <!-- Anúncio adaptável -->
            @include('partials.adsense', ['slot' => '9753835582', 'format' => 'auto', 'responsive' => true])

            <!-- Anúncio 4 -->
            @include('partials.adsense', ['slot' => '2166789917', 'format' => 'auto', 'responsive' => true])

            @if($countryCode === 'ao')
            <div class="law-banner">
                <h2>Conheça a Lei Geral do Trabalho Angolana</h2>
                <p>
                    Informe-se sobre os seus direitos e deveres como trabalhador em Angola.
                    A Lei Geral do Trabalho regula as relações laborais no mercado de trabalho angolano.
                </p>
                <a href="{{ url('/articles/lei-geral-do-trabalho-lei-no-1223-de-27-de-dezembro') }}" class="btn-apply">
                    Consulte a Lei Geral do Trabalho
                </a>
            </div>
            @endif

        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">

            <!-- Últimas Oportunidades do mesmo país -->
            <div class="sidebar-card">
                <div class="sidebar-card-header"><i class="bi bi-briefcase me-2"></i>Últimas Oportunidades</div>
                @foreach($LastJobs->take(5) as $lastJob)
                @php
                    $lastJobUrl = isset($country)
                        ? url("/{$country}/empregos/{$lastJob['slug']}")
                        : url("/empregos/{$lastJob['slug']}");
                @endphp
                <a href="{{ $lastJobUrl }}" class="sidebar-item">
                    <p class="sidebar-item-title">{{ $lastJob['title'] }}</p>
                    <p class="sidebar-item-meta mb-1">{{ $lastJob['company'] }}</p>
                    <span class="sidebar-item-meta"><i class="bi bi-geo-alt"></i> {{ $lastJob['province'] }}</span>
                </a>
                @endforeach
            </div>

            <!-- Últimas Notícias -->
            <div class="sidebar-card">
                <div class="sidebar-card-header"><i class="bi bi-journal-text me-2"></i>Últimas Notícias</div>
                @foreach($LastArticles->take(5) as $article)
                <a href="{{ url('/articles/' . $article['slug']) }}" class="sidebar-item">
                    <p class="sidebar-item-title">{{ $article['title'] }}</p>
                    <span class="sidebar-item-meta">{{ date_format(new DateTime($article['created_at']), 'd-m-Y') }}</span>
                </a>
                @endforeach
            </div>

            <!-- Anúncio sidebar -->
            @include('partials.adsense', ['slot' => '8002595367', 'format' => 'auto', 'responsive' => true])

        </div>
    </div>
</div>
</section>
@endsection
