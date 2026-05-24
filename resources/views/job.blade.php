@extends('template.app')

@php
    $countryIdToCode = [1 => 'ao', 2 => 'br', 3 => 'mz'];
    $countryCode     = $countryIdToCode[$job->country_id] ?? 'ao';
    $countryNames    = ['ao' => 'Angola', 'br' => 'Brasil', 'mz' => 'Moçambique'];
    $countryISO      = ['ao' => 'AO',     'br' => 'BR',     'mz' => 'MZ'];
    $countryName     = $countryNames[$countryCode];

    $jobUrl  = url("/empregos/{$job['slug']}");
    $jobsUrl = url("/empregos");
    $siteUrl = url('/');

    $previousJob    = $LastJobs->where('id', '<', $job['id'])->first();
    $previousJobUrl = $previousJob ? url("/empregos/{$previousJob->slug}") : null;
@endphp

@section('title', $job['title'])
@section('description', strip_tags($job['description']))
@section('url', asset('storage/' . $job['photo']))
@section('canonical_link', $jobUrl)
@section('created_at', $job->created_at)
@section('updated_at', $job->updated_at)

@section('head-scripts')
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
                {"@type": "ListItem", "position": 2, "name": "Empregos {{ $countryName }}", "item": "{{ $jobsUrl }}"},
                {"@type": "ListItem", "position": 3, "name": {{ json_encode($job['title']) }}}
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
            "title": {{ json_encode($job['title']) }},
            "description": {{ json_encode(strip_tags($job['description'])) }},
            "employmentType": ["FULL_TIME"],
            "hiringOrganization": {
                "@type": "Organization",
                "name": {{ json_encode($job['company']) }},
                "logo": "{{ asset('storage/' . $job['photo']) }}"
            },
            "identifier": {
                "@type": "PropertyValue",
                "name": {{ json_encode($job['company']) }},
                "value": "{{ $jobUrl }}"
            },
            "jobLocation": {
                "@type": "Place",
                "address": {
                    "@type": "PostalAddress",
                    "addressLocality": {{ json_encode($job['province']) }},
                    "addressCountry": "{{ $countryISO[$countryCode] }}"
                }
            }
        }
    ]
}
</script>
@endsection

@section('content')
<div class="container mt-4">
    <div class="row">

        <!-- Conteúdo Principal -->
        <div class="col-lg-8">

            <h1>{{ $job['title'] }}</h1>
            <p class="text-muted small">Publicado em: {{ date_format(new DateTime($job['created_at']), 'd-m-Y') }}</p>
            <hr>

            <!-- Partilhar -->
            <p class="fw-bold mb-2">Partilhar</p>
            <a class="btn btn-success btn-sm me-2 mb-3"
               href="https://api.whatsapp.com/send?text={{ urlencode($job['title'] . "\n" . $jobUrl) }}"
               target="_blank" rel="noopener">
                <i class="bi bi-whatsapp me-1"></i> WhatsApp
            </a>
            <a class="btn btn-primary btn-sm mb-3"
               href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode($jobUrl) }}"
               target="_blank" rel="noopener">
                <i class="bi bi-linkedin me-1"></i> LinkedIn
            </a>
            <hr>

            <img class="img-fluid rounded mb-3" src="{{ asset('storage/' . $job['photo']) }}" alt="{{ $job['title'] }}">

            @if($previousJobUrl)
            <a href="{{ $previousJobUrl }}" class="btn btn-dark w-100 mb-3">VER PROXIMA VAGA</a>
            @endif

            <h3>Descrição:</h3>

            <!-- Anúncio 1 -->
            <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-2118765549976668" crossorigin="anonymous"></script>
            <ins class="adsbygoogle"
                 style="display:block"
                 data-ad-client="ca-pub-2118765549976668"
                 data-ad-slot="5838441610"
                 data-ad-format="auto"
                 data-full-width-responsive="true"></ins>
            <script>(adsbygoogle = window.adsbygoogle || []).push({});</script>

            <!-- Anúncio em artigo -->
            <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-2118765549976668" crossorigin="anonymous"></script>
            <ins class="adsbygoogle"
                 style="display:block; text-align:center;"
                 data-ad-layout="in-article"
                 data-ad-format="fluid"
                 data-ad-client="ca-pub-2118765549976668"
                 data-ad-slot="7583808877"></ins>
            <script>(adsbygoogle = window.adsbygoogle || []).push({});</script>

            <div class="lead content mt-3">
                {!! $job['description'] !!}
            </div>

            <p class="mt-3"><strong>Empresa:</strong> {{ $job['company'] }}</p>
            <p><strong>E-mail ou link de candidatura:</strong> {{ $job['email_or_link'] }}</p>

            <p>
                <strong>Entre no nosso canal do WhatsApp </strong>
                <a href="https://whatsapp.com/channel/0029VaCfSeo0bIdgKs7bIB3t"><strong>CLICANDO AQUI</strong></a>
            </p>
            <hr>

            @if($previousJobUrl)
            <a href="{{ $previousJobUrl }}" class="btn btn-dark w-100 mb-3">VER PROXIMA VAGA</a>
            @endif

            <!-- Anúncio adaptável -->
            <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-2118765549976668" crossorigin="anonymous"></script>
            <ins class="adsbygoogle"
                 style="display:block"
                 data-ad-client="ca-pub-2118765549976668"
                 data-ad-slot="9753835582"
                 data-ad-format="auto"
                 data-full-width-responsive="true"></ins>
            <script>(adsbygoogle = window.adsbygoogle || []).push({});</script>

            <!-- Anúncio 4 -->
            <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-2118765549976668" crossorigin="anonymous"></script>
            <ins class="adsbygoogle"
                 style="display:block"
                 data-ad-client="ca-pub-2118765549976668"
                 data-ad-slot="2166789917"
                 data-ad-format="auto"
                 data-full-width-responsive="true"></ins>
            <script>(adsbygoogle = window.adsbygoogle || []).push({});</script>

            @if($countryCode === 'ao')
            <section class="my-4 p-4 text-center rounded" style="background-color: #f8f8f8;">
                <h2 style="font-size: 22px; color: #333; margin-bottom: 16px;">Conheça a Lei Geral do Trabalho Angolana</h2>
                <p class="text-muted" style="max-width: 600px; margin: 0 auto 20px;">
                    Informe-se sobre os seus direitos e deveres como trabalhador em Angola.
                    A Lei Geral do Trabalho regula as relações laborais no mercado de trabalho angolano.
                </p>
                <a href="{{ url('/articles/lei-geral-do-trabalho-lei-no-1223-de-27-de-dezembro') }}" class="btn btn-primary">
                    Consulte a Lei Geral do Trabalho
                </a>
            </section>
            @endif

        </div>

        <!-- Sidebar -->
        <div class="col-md-4">

            <!-- Últimas Oportunidades do mesmo país -->
            <div class="card my-4">
                <h5 class="card-header">Últimas Oportunidades</h5>
                <div class="card-body p-2">
                    <div class="list-group">
                        @foreach($LastJobs->take(5) as $lastJob)
                        @php
                            $lastJobUrl = isset($country)
                                ? url("/{$country}/empregos/{$lastJob['slug']}")
                                : url("/empregos/{$lastJob['slug']}");
                        @endphp
                        <a href="{{ $lastJobUrl }}" class="list-group-item list-group-item-action mb-2">
                            <p class="mb-1 fw-bold">{{ $lastJob['title'] }}</p>
                            <p class="mb-1 small">Empresa: {{ $lastJob['company'] }}</p>
                            <small><i class="fa fa-map-marker"></i> {{ $lastJob['province'] }}</small>
                        </a>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Últimas Notícias -->
            <div class="card my-4">
                <h5 class="card-header">Últimas Notícias</h5>
                <div class="card-body p-2">
                    <div class="list-group">
                        @foreach($LastArticles->take(5) as $article)
                        <a href="{{ url('/articles/' . $article['slug']) }}" class="list-group-item list-group-item-action mb-2">
                            <p class="mb-1 fw-bold">{{ $article['title'] }}</p>
                            <small>{{ date_format(new DateTime($article['created_at']), 'd-m-Y') }}</small>
                        </a>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Anúncio sidebar -->
            <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-2118765549976668" crossorigin="anonymous"></script>
            <ins class="adsbygoogle"
                 style="display:block"
                 data-ad-client="ca-pub-2118765549976668"
                 data-ad-slot="8002595367"
                 data-ad-format="auto"
                 data-full-width-responsive="true"></ins>
            <script>(adsbygoogle = window.adsbygoogle || []).push({});</script>

        </div>
    </div>
</div>
@endsection
