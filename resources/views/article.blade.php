@extends('template.app')
@section('title', $article['title'])
@section('description', strip_tags($article['description']))
@section('url', asset('storage/' . $article['photo']))
@section('canonical_link', url('/articles/'. $article['slug']))
@section('head-scripts')
{{-- Pre-carrega a imagem principal (elemento LCP) para acelerar o carregamento --}}
@if(!empty($article['photo']))
<link rel="preload" as="image" href="{{ asset('storage/' . $article['photo']) }}" fetchpriority="high">
@endif
<script type="application/ld+json" class="yoast-schema-graph">
    {
        "@context": "https://schema.org",
        "@graph": [
            {
                "@type": "Article",
                "@id": "{{url('/articles/'. $article['slug'])}}/#article",
                "isPartOf": {"@id": "{{url('/articles/'. $article['slug'])}}"},
                "author": {"name": "Edivaldo", "@id": "https://empregosyoyota.net/#/schema/person/4e746ddb32c25bcf75f5e4fa3c48a443"},
                "headline": "{{$article['title']}}",
                "datePublished": "{{ date_format(new DateTime($article['created_at']), DATE_ATOM) }}",
                "dateModified": "{{ date_format(new DateTime($article['updated_at']), DATE_ATOM) }}",
                "mainEntityOfPage": {"@id": "{{url('/articles/'. $article['slug'])}}"},
                "wordCount": 1162,
                "publisher": {"@id": "https://empregosyoyota.net/#organization"},
                "image": {"@id": "{{url('/articles/'. $article['slug'])}}/#primaryimage"},
                "thumbnailUrl": "{{asset('storage/' . $article['photo'])}}",
                "keywords": ["{{$article['title']}}"],
                "articleSection": ["Emprego"],
                "inLanguage": "pt-PT"
            },
            {
                "@type": "WebPage",
                "@id": "{{url('/articles/'. $article['slug'])}}",
                "url": "{{url('/articles/'. $article['slug'])}}",
                "name": "{{$article['title']}}",
                "isPartOf": {"@id": "https://empregosyoyota.net/#website"},
                "primaryImageOfPage": {"@id": "{{url('/articles/'. $article['slug'])}}/#primaryimage"},
                "image": {"@id": "{{url('/articles/'. $article['slug'])}}/#primaryimage"},
                "thumbnailUrl": "{{asset('storage/' . $article['photo'])}}",
                "datePublished": "{{ date_format(new DateTime($article['created_at']), DATE_ATOM) }}",
                "dateModified": "{{ date_format(new DateTime($article['updated_at']), DATE_ATOM) }}",
                "breadcrumb": {"@id": "{{url('/articles/'. $article['slug'])}}/#breadcrumb"},
                "inLanguage": "pt-PT",
                "potentialAction": [{"@type": "ReadAction", "target": ["{{url('/articles/'. $article['slug'])}}"]}]
            },
            {
                "@type": "ImageObject",
                "inLanguage": "pt-PT",
                "@id": "{{url('/articles/'. $article['slug'])}}/#primaryimage",
                "url": "{{asset('storage/' . $article['photo'])}}",
                "contentUrl": "{{asset('storage/' . $article['photo'])}}",
                "width": 918,
                "height": 612
            },
            {
                "@type": "BreadcrumbList",
                "@id": "{{url('/articles/'. $article['slug'])}}/#breadcrumb",
                "itemListElement": [
                    {"@type": "ListItem", "position": 1, "name": "Início", "item": "https://empregosyoyota.net/"},
                    {"@type": "ListItem", "position": 2, "name": "{{$article['title']}}"}
                ]
            },
            {
                "@type": "WebSite",
                "@id": "https://empregosyoyota.net/#website",
                "url": "https://empregosyoyota.net/",
                "name": "Empregos Yoyota",
                "description": "Vagas de emprego estagio e bolsas de estudo",
                "publisher": {"@id": "https://empregosyoyota.net/#organization"},
                "potentialAction": [
                    {
                        "@type": "SearchAction",
                        "target": {"@type": "EntryPoint", "urlTemplate": "https://empregosyoyota.net/pesquisar?query={search_term_string}"},
                        "query-input": "required name=search_term_string"
                    }
                ],
                "inLanguage": "pt-PT"
            },
            {
                "@type": "Organization",
                "@id": "https://empregosyoyota.net/#organization",
                "name": "Empregos Yoyota",
                "url": "https://empregosyoyota.net/",
                "logo": {
                    "@type": "ImageObject",
                    "inLanguage": "pt-PT",
                    "@id": "https://empregosyoyota.net/#/schema/logo/image/",
                    "url": "https://empregosyoyota.net/storage/images/logo-full.jpg",
                    "contentUrl": "https://empregosyoyota.net/storage/images/logo-full.jpg",
                    "width": 512,
                    "height": 512,
                    "caption": "Empregos Yoyota"
                },
                "image": {"@id": "https://empregosyoyota.net/#/schema/logo/image/"},
                "sameAs": ["https://web.facebook.com/empregosyoyota"]
            },
            {
                "@type": "Person",
                "@id": "https://empregosyoyota.net/#/schema/person/4e746ddb32c25bcf75f5e4fa3c48a443",
                "name": "Edivaldo",
                "image": {
                    "@type": "ImageObject",
                    "inLanguage": "pt-PT",
                    "@id": "https://empregosyoyota.net/#/schema/person/image/",
                    "url": "https://secure.gravatar.com/avatar/b568c8a12f1d1c77b0199f05f04c00a1?s=96&d=mm&r=g",
                    "contentUrl": "https://secure.gravatar.com/avatar/b568c8a12f1d1c77b0199f05f04c00a1?s=96&d=mm&r=g",
                    "caption": "Edivaldo"
                },
                "sameAs": ["https://empregosyoyota.net"]
            }
        ]
    }
</script>
@endsection
@section('content')
<style>
    .article-hero {
        background: #ffffff;
        border-bottom: 1px solid #e9ecef;
        padding: 48px 0 40px;
    }

    .article-breadcrumb {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        color: #6c757d;
        text-decoration: none;
        font-weight: 600;
        font-size: 0.9rem;
        margin-bottom: 1.5rem;
    }

    .article-breadcrumb:hover { color: #000000; }

    .article-badge {
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
        margin-bottom: 1.25rem;
    }

    .article-title {
        font-size: clamp(1.75rem, 4.5vw, 2.75rem);
        font-weight: 900;
        color: #000000;
        line-height: 1.2;
        margin-bottom: 1.5rem;
    }

    .btn-share-article {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: #25D366;
        color: #ffffff;
        border-radius: 25px;
        padding: 0.75rem 1.6rem;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .btn-share-article:hover { background: #1da851; color: #ffffff; }

    .article-section { padding: 3rem 0; }

    .article-image-card {
        border-radius: 16px;
        overflow: hidden;
        margin-bottom: 1.5rem;
        box-shadow: 0 4px 20px rgba(0,0,0,0.05);
    }

    .article-card {
        background: #ffffff;
        border: 1px solid #e9ecef;
        border-radius: 16px;
        padding: 2rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 4px 20px rgba(0,0,0,0.05);
    }

    .article-card .content {
        color: #333333;
        line-height: 1.8;
        font-size: 1.05rem;
    }

    .article-card .content p { margin-bottom: 1rem; }
    .article-card .content ul,
    .article-card .content ol { padding-left: 1.5rem; margin-bottom: 1rem; }
    .article-card .content li { margin-bottom: 0.5rem; }
    .article-card .content img { max-width: 100%; height: auto; border-radius: 8px; }

    .article-sidebar-card {
        background: #ffffff;
        border: 1px solid #e9ecef;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        margin-bottom: 1.5rem;
    }

    .article-sidebar-card-header {
        background: #000000;
        color: #ffffff;
        font-weight: 800;
        font-size: 1.05rem;
        padding: 1rem 1.5rem;
        display: flex;
        align-items: center;
    }

    .article-sidebar-item {
        display: block;
        padding: 1rem 1.5rem;
        text-decoration: none;
        color: inherit;
        border-bottom: 1px solid #f1f1f1;
        transition: background 0.2s ease;
    }

    .article-sidebar-item:last-child { border-bottom: none; }
    .article-sidebar-item:hover { background: #f8f9fa; }

    .article-sidebar-item-title {
        font-weight: 700;
        color: #000000;
        font-size: 0.95rem;
        margin-bottom: 0.25rem;
    }

    .article-sidebar-item-meta { color: #6c757d; font-size: 0.85rem; }

    @media (max-width: 768px) {
        .article-hero { padding: 32px 0 28px; }
        .article-section { padding: 2rem 0; }
        .article-card { padding: 1.5rem; }
    }
</style>

<!-- Hero -->
<section class="article-hero">
    <div class="container">
        <a href="{{ url('/articles') }}" class="article-breadcrumb"><i class="bi bi-arrow-right" style="transform:scaleX(-1);"></i> Voltar aos artigos</a>

        <div>
            <span class="article-badge"><i class="bi bi-calendar3"></i> {{ date_format(new DateTime($article['created_at']), 'd-m-Y') }}</span>
        </div>

        <h1 class="article-title">{{ $article['title'] }}</h1>

        <a class="btn-share-article"
           href="https://api.whatsapp.com/send?text={{ urlencode($article['title'] . "\n" . url('/articles/' . $article['slug'])) }}"
           target="_blank" rel="noopener">
            <i class="bi bi-whatsapp"></i> Partilhar via WhatsApp
        </a>
    </div>
</section>

<section class="article-section">
<div class="container">
    <div class="row">

      <!-- Post Content Column -->
      <div class="col-lg-8">

        <div class="article-image-card">
            <img class="img-fluid" src="{{ asset('storage/' . $article['photo']) }}" alt="{{ $article['title'] }}" fetchpriority="high" decoding="async" style="width:100%;height:auto;display:block;">
        </div>

        <!-- Anúncio de artigo -->
        @include('partials.adsense', ['slot' => '9222329186', 'layout' => 'in-article', 'format' => 'fluid', 'style' => 'display:block; text-align:center;'])

        <div class="article-card">
            <div class="content">
                {!! $article['description'] !!}
            </div>
        </div>

        <!-- Divulgação do App -->
        @include('partials.app-download')

      </div>

      <div class="col-lg-4">

        <!-- Últimas Oportunidades -->
        <div class="article-sidebar-card">
            <div class="article-sidebar-card-header"><i class="bi bi-briefcase me-2"></i>Últimas Oportunidades</div>
            @foreach($LastJobs->take(5) as $lastJob)
            <a href="{{ url('/empregos/' . $lastJob['slug']) }}" class="article-sidebar-item">
                <p class="article-sidebar-item-title">{{ $lastJob['title'] }}</p>
                <p class="article-sidebar-item-meta mb-1">{{ $lastJob['company'] }}</p>
                <span class="article-sidebar-item-meta"><i class="bi bi-geo-alt"></i> {{ $lastJob['province'] }}</span>
            </a>
            @endforeach
        </div>

        <!-- Últimas Notícias -->
        <div class="article-sidebar-card">
            <div class="article-sidebar-card-header"><i class="bi bi-journal-text me-2"></i>Últimas Notícias</div>
            @foreach($LastArticles->take(5) as $lastArticle)
            <a href="{{ url('/articles/' . $lastArticle['slug']) }}" class="article-sidebar-item">
                <p class="article-sidebar-item-title">{{ $lastArticle['title'] }}</p>
                <span class="article-sidebar-item-meta">{{ date_format(new DateTime($lastArticle['created_at']), 'd-m-Y') }}</span>
            </a>
            @endforeach
        </div>

        <!-- Adaptavel 2 no artigo -->
        @include('partials.adsense', ['slot' => '4901501299', 'format' => 'auto', 'responsive' => true])
      </div>

    </div>
    <!-- /.row -->

</div>
</section>
@endsection('content')
