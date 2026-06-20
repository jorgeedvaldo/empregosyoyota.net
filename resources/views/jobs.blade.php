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
<div class="container">
    <div class="row mt-5">
        <div class="col-md-8 p-0 mr-3">
            <form action="{{ route('search') }}" method="GET" class="input-group mb-3">
                <input type="search" class="form-control rounded mr-2" placeholder="Digite a sua pesquisa" aria-label="Search" aria-describedby="search-addon" name="query"/>
                <button type="submit" class="btn btn-dark" data-mdb-ripple-init>Pesquisar</button>
            </form>
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
            {{ $jobs->links() }}
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
