@extends('template.app')
@section('title', $title ?? 'Vagas de Emprego')
@section('description', 'É uma plataforma que reúne vagas de emprego em Angola, tendo como fonte principal o "Jornal de Angola", criada aos 5 de Dezembro de 2018, a Empregos Yoyota tem ajudado muita gente a encontrar empregos no solo angolano')
@section('canonical_link', url('/empregos'))
@section('head-scripts')
<script type="application/ld+json" class="yoast-schema-graph">
{
  "@context": "https://schema.org",
  "@graph": [
    {
      "@type": "WebPage",
      "@id": "https://empregosyoyota.net/empregos/",
      "url": "https://empregosyoyota.net/empregos/",
      "name": "Vagas de Emprego em Angola - Empregos Yoyota",
      "isPartOf": {
        "@id": "https://empregosyoyota.net/#website"
      },
      "datePublished": "2025-01-13T00:00:00+00:00",
      "dateModified": "2025-01-13T00:00:00+00:00",
      "breadcrumb": {
        "@id": "https://empregosyoyota.net/empregos/#breadcrumb"
      },
      "inLanguage": "pt-AO",
      "potentialAction": [
        {
          "@type": "ReadAction",
          "target": [
            "https://empregosyoyota.net/empregos/"
          ]
        }
      ]
    },
    {
      "@type": "BreadcrumbList",
      "@id": "https://empregosyoyota.net/empregos/#breadcrumb",
      "itemListElement": [
        {
          "@type": "ListItem",
          "position": 1,
          "name": "Home",
          "item": "https://empregosyoyota.net/"
        },
        {
          "@type": "ListItem",
          "position": 2,
          "name": "Vagas de Emprego em Angola"
        }
      ]
    },
    {
      "@type": "WebSite",
      "@id": "https://empregosyoyota.net/#website",
      "url": "https://empregosyoyota.net/",
      "name": "Empregos Yoyota",
      "description": "Site Angolano com vagas de emprego em Angola e oportunidades de recrutamento.",
      "publisher": {
        "@id": "https://empregosyoyota.net/#organization"
      },
      "potentialAction": [
        {
          "@type": "SearchAction",
          "target": {
            "@type": "EntryPoint",
            "urlTemplate": "https://empregosyoyota.net/?s={search_term_string}"
          },
          "query-input": {
            "@type": "PropertyValueSpecification",
            "valueRequired": true,
            "valueName": "search_term_string"
          }
        }
      ],
      "inLanguage": "pt-AO"
    },
    {
      "@type": "Organization",
      "@id": "https://empregosyoyota.net/#organization",
      "name": "Empregos Yoyota",
      "url": "https://empregosyoyota.net/",
      "logo": {
        "@type": "ImageObject",
        "inLanguage": "pt-AO",
        "@id": "https://empregosyoyota.net/#/schema/logo/image/",
        "url": "https://empregosyoyota.net/assets/logo-yoyota.png",
        "contentUrl": "https://empregosyoyota.net/assets/logo-yoyota.png",
        "width": 1200,
        "height": 1200,
        "caption": "Empregos Yoyota"
      },
      "image": {
        "@id": "https://empregosyoyota.net/#/schema/logo/image/"
      },
      "sameAs": [
        "https://www.facebook.com/EmpregosYoyota",
        "https://x.com/empregosyoyota",
        "https://www.instagram.com/empregosyoyota/",
        "https://www.youtube.com/channel/EmpregosYoyota",
        "https://www.linkedin.com/company/empregosyoyota/"
      ]
    }
  ]
}
</script>

@endsection('head-scripts')
@section('content')

<section class="jobs-hero">
    <div class="container">
        <h1 class="jobs-hero-title">{{ $title ?? 'Vagas de Emprego' }}</h1>
        <p class="jobs-hero-subtitle">Encontre as melhores oportunidades de emprego, atualizadas todos os dias.</p>
        <span class="jobs-hero-count"><i class="bi bi-briefcase"></i> {{ $jobs->total() }} vaga(s) encontrada(s)</span>
    </div>
</section>

<div class="container my-5">
    <div class="row">
        <div class="col-lg-8">
            <form action="{{ route('search') }}" method="GET" class="jobs-search-form">
                <input type="search" class="jobs-search-input" placeholder="Pesquisar por cargo, empresa..." aria-label="Search" name="query"/>
                <input type="text" class="jobs-search-input" placeholder="Cidade ou Província" aria-label="Localização" name="location"/>
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
            <div class="mt-4">{{ $jobs->links() }}</div>
            @else
            <div class="jobs-empty">
                <i class="bi bi-search" style="font-size:2rem;"></i>
                <p class="mt-2 mb-0">Nenhuma vaga encontrada de momento.</p>
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
