@extends('template.app')
@section('title', $article['title'])
@section('description', strip_tags($article['description']))
@section('url', asset('storage/' . $article['photo']))
@section('canonical_link', url('/articles/'. $article['slug']))
@section('head-scripts')
<script type="application/ld+json" class="yoast-schema-graph">
    {
        "@context": "https://schema.org",
        "@graph": [
            {
                "@type": "Article",
                "@id": "{{url('/articles/'. $article['slug'])}}/#article",
                "isPartOf": {"@id": "{{url('/articles/'. $article['slug'])}}"},
                "author": {"name": "Edivaldo", "@id": "https://ao.empregosyoyota.net/#/schema/person/4e746ddb32c25bcf75f5e4fa3c48a443"},
                "headline": "{{$article['title']}}",
                "datePublished": "{{ date_format(new DateTime($article['created_at']), DATE_ATOM) }}",
                "dateModified": "{{ date_format(new DateTime($article['updated_at']), DATE_ATOM) }}",
                "mainEntityOfPage": {"@id": "{{url('/articles/'. $article['slug'])}}"},
                "wordCount": 1162,
                "publisher": {"@id": "https://ao.empregosyoyota.net/#organization"},
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
                "isPartOf": {"@id": "https://ao.empregosyoyota.net/#website"},
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
                    {"@type": "ListItem", "position": 1, "name": "Início", "item": "https://ao.empregosyoyota.net/"},
                    {"@type": "ListItem", "position": 2, "name": "{{$article['title']}}"}
                ]
            },
            {
                "@type": "WebSite",
                "@id": "https://ao.empregosyoyota.net/#website",
                "url": "https://ao.empregosyoyota.net/",
                "name": "Empregos Yoyota",
                "description": "Vagas de emprego estagio e bolsas de estudo",
                "publisher": {"@id": "https://ao.empregosyoyota.net/#organization"},
                "potentialAction": [
                    {
                        "@type": "SearchAction",
                        "target": {"@type": "EntryPoint", "urlTemplate": "https://ao.empregosyoyota.net/pesquisar?query={search_term_string}"},
                        "query-input": "required name=search_term_string"
                    }
                ],
                "inLanguage": "pt-PT"
            },
            {
                "@type": "Organization",
                "@id": "https://ao.empregosyoyota.net/#organization",
                "name": "Empregos Yoyota",
                "url": "https://ao.empregosyoyota.net/",
                "logo": {
                    "@type": "ImageObject",
                    "inLanguage": "pt-PT",
                    "@id": "https://ao.empregosyoyota.net/#/schema/logo/image/",
                    "url": "https://ao.empregosyoyota.net/storage/images/logo-full.jpg",
                    "contentUrl": "https://ao.empregosyoyota.net/storage/images/logo-full.jpg",
                    "width": 512,
                    "height": 512,
                    "caption": "Empregos Yoyota"
                },
                "image": {"@id": "https://ao.empregosyoyota.net/#/schema/logo/image/"},
                "sameAs": ["https://web.facebook.com/empregosyoyota"]
            },
            {
                "@type": "Person",
                "@id": "https://ao.empregosyoyota.net/#/schema/person/4e746ddb32c25bcf75f5e4fa3c48a443",
                "name": "Edivaldo",
                "image": {
                    "@type": "ImageObject",
                    "inLanguage": "pt-PT",
                    "@id": "https://ao.empregosyoyota.net/#/schema/person/image/",
                    "url": "https://secure.gravatar.com/avatar/b568c8a12f1d1c77b0199f05f04c00a1?s=96&d=mm&r=g",
                    "contentUrl": "https://secure.gravatar.com/avatar/b568c8a12f1d1c77b0199f05f04c00a1?s=96&d=mm&r=g",
                    "caption": "Edivaldo"
                },
                "sameAs": ["https://ao.empregosyoyota.net"]
            }
        ]
    }
</script>
@endsection
@section('content')

<!-- Page Content -->
<div class="container">

    <div class="row">

      <!-- Post Content Column -->
      <div class="col-lg-8">

        <!-- Title -->
        <h1 class="mt-4">{{$article['title']}}</h1>

        <!-- Date/Time -->
        <p>Publicado em: {{ date_format(new DateTime($article['created_at']), 'd-m-Y') }}</p>

        <hr>
		  
		  <!-- Botão de Compartilhar via WhatsApp -->
		<div class="container">
			<p>Partilhar</p>
			<a class="btn btn-success" href="https://api.whatsapp.com/send?text={{$article['title']}}%0A.%0ASe%20você%20deseja%20saber%20mais,%20por%20favor,%20clique%20no%20link:%20{{url('/articles/'. $article['slug'])}}%0A." target="_blank">Partilhar via WhatsApp</a>
		</div>
        <hr>
		  
		<!-- Preview Image -->
        <img class="img-fluid rounded" src="{{asset('storage/' . $article['photo'])}}" alt="Emprego">
		  
        <!-- Anúncio de artigo -->
        <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
        <ins class="adsbygoogle"
            style="display:block; text-align:center;"
            data-ad-layout="in-article"
            data-ad-format="fluid"
            data-ad-client="ca-pub-2118765549976668"
            data-ad-slot="9222329186"></ins>
        <script>
            (adsbygoogle = window.adsbygoogle || []).push({});
        </script>

        <div class="lead content">
            {!!$article['description']!!}
        </div>

        <hr>

      </div>

      <div class="col-md-4">
		<!-- Card de Últimas Oportunidades Widget -->
        <div class="card my-4">
          <h5 class="card-header">Últimas Oportunidades</h5>
          <div class="card-body">
            <div class="row">
              <div class="col-lg-12">
                <div class="list-group mb-3">
                  @for($i = 0; $i < 5; $i++)
  
                      <a href="{{ url('/empregos/' . $LastJobs[$i]['slug']) }}" class="list-group-item list-group-item-action mb-3">
                          <div class="d-flex w-100 justify-content-between">
                            <p class="mb-1"><b>{{ $LastJobs[$i]['title'] }}</b></p>
                          </div>
                          <p class="mb-1">Empresa: {{ $LastJobs[$i]['company'] }}</p>
                          <small><i class="fa fa-map-marker"></i> Localização: <span>{{ $LastJobs[$i]['province'] }}</span></small>
                      </a>
  
                  @endfor
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- End -->

        <!-- Card de Últimos BlogPosts Widget -->
        <div class="card my-4">
          <h5 class="card-header">Últimas Notícias</h5>
          <div class="card-body">
            <div class="row">
              <div class="col-lg-12">
                <div class="list-group mb-3">
                  @for($i = 0; $i < 5; $i++)
  
                    <a href="{{ url('/articles/' . $LastArticles[$i]['slug']) }}" class="list-group-item list-group-item-action mb-3">
                      <div class="d-flex w-100 justify-content-between">
                      <p class="mb-1"><b>{{ $LastArticles[$i]['title'] }}</b></p>
                      </div>
                      <p class="mb-1">Publicado em: {{ date_format(new DateTime($LastArticles[$i]['created_at']), 'd-m-Y') }}</p>
                      <small><i class="fa fa-journal"></i>   <span> </span></small>
                    </a>
  
                  @endfor
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- End -->
         <!-- Adaptavel 2 no artigo -->
        <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
        <!-- Adaptavel 2 no artigo -->
        <ins class="adsbygoogle"
             style="display:block"
             data-ad-client="ca-pub-2118765549976668"
             data-ad-slot="4901501299"
             data-ad-format="auto"
             data-full-width-responsive="true"></ins>
        <script>
             (adsbygoogle = window.adsbygoogle || []).push({});
        </script>
      </div>

    </div>
    <!-- /.row -->

  </div>
  <!-- /.container -->
@endsection('content')
