@extends('template.app')
@section('title', $job['title'])
@section('description', strip_tags($job['description']))
@section('url', asset('storage/' . $job['photo']))
@section('canonical_link', url('/empregos/'. $job['slug']))
@section('created_at', $job->created_at)
@section('updated_at', $job->updated_at)
@section('head-scripts')


  <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@graph": [
            {
                "@type": "WebPage",
                "@id": "{{url('/empregos/'. $job['slug'])}}",
                "url": "{{url('/empregos/'. $job['slug'])}}",
                "name": "{{$job['title']}} - Empregos Yoyota Angola",
                "isPartOf": {"@id": "https://ao.empregosyoyota.net/#website"},
                "primaryImageOfPage": {"@id": "{{url('/empregos/'. $job['slug'])}}/#primaryimage"},
                "image": {"@id": "{{url('/empregos/'. $job['slug'])}}/#primaryimage"},
                "thumbnailUrl": "{{asset('storage/' . $job['photo'])}}",
                "datePublished": "{{ date_format(new DateTime($job['created_at']), DATE_ATOM) }}",
                "dateModified": "{{ date_format(new DateTime($job['updated_at']), DATE_ATOM) }}",
                "inLanguage": "pt-PT",
                "potentialAction": [{"@type": "ReadAction", "target": ["{{url('/empregos/'. $job['slug'])}}"]}]
            },
            {
                "@type": "ImageObject",
                "inLanguage": "pt-PT",
                "@id": "{{url('/empregos/'. $job['slug'])}}/#primaryimage",
                "url": "{{asset('storage/' . $job['photo'])}}",
                "contentUrl": "{{asset('storage/' . $job['photo'])}}",
                "width": 918,
                "height": 612
            },
			{
				"@type": "BreadcrumbList",
				"@id": "{{url('/empregos/'. $job['slug'])}}?page&job_listing={{$job['slug']}}&post_type=job_listing&name={{$job['slug']}}/#breadcrumb",
				"itemListElement": [
				  {
					"@type": "ListItem",
					"position": 1,
					"name": "Home",
					"item": "https://ao.empregosyoyota.net"
				  },
				  {
					"@type": "ListItem",
					"position": 2,
					"name": "{{$job['title']}}"
				  }
				]
			},
            {
                "@type": "WebSite",
                "@id": "https://ao.empregosyoyota.net/#website",
                "url": "https://ao.empregosyoyota.net/",
                "name": "Empregos Yoyota Angola",
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
    
        <script type="application/ld+json">
		{
      "@context":"http:\/\/schema.org\/",
      "@type":"JobPosting",
      "datePosted":"{{ date_format(new DateTime($job['created_at']), DATE_ATOM) }}",
      "title":"{{$job['title']}}",
      "description":"{{$job['description']}}",
      "employmentType":["FULL_TIME"],
      "hiringOrganization":{
              "@type":"Organization",
              "name":"{{$job['company']}}",
              "logo":"{{asset('storage/' . $job['photo'])}}"
              },
      "identifier":{
              "@type":"PropertyValue",
              "name":"{{$job['company']}}",
              "value":"https:\/\/empregosyoyota.net\/#identifier"
              },
      "jobLocation":[
        
        {
          "@type":"Place",
          "address":"{{$job['province']}}"
        },
      
        {
            "@type":"Place",
            "address":
                    {
                        "@type":"PostalAddress",
                        "streetAddress":"Luanda",
                        "addressLocality":"Luanda",
                        "addressRegion":"Luanda",
                        "postalCode":"Luanda",
                        "addressCountry":"Luanda"
                    }
        },
        {
            "@type":"Place",
            "address":
                    {
                        "@type":"PostalAddress",
                        "streetAddress":"Angola",
                        "addressLocality":"Angola",
                        "addressRegion":"Angola",
                        "postalCode":"Angola",
                        "addressCountry":"Angola"
                    }
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
       <h1 class="mt-4">{{$job['title']}}</h1>

        <!-- Date/Time -->
        <p>Publicado em: {{ date_format(new DateTime($job['created_at']), 'd-m-Y') }}</p>
		<hr>
		<!-- Botão de Compartilhar via WhatsApp -->
		<div class="container">
			<p>Partilhar</p>
			<a class="btn btn-success mb-2" href="https://api.whatsapp.com/send?text={{$job['title']}}%0A.%0ASe%20você%20deseja%20saber%20mais%20sobre%20esta%20oportunidade,%20por%20favor,%20clique%20no%20link:%20{{url('/empregos/'. $job['slug'])}}%0A." target="_blank">Partilhar via WhatsApp</a> 
			<a class="btn btn-info mb-2" href="https://www.linkedin.com/sharing/share-offsite/?url={{url('/empregos/'. $job['slug'])}}&text={{$job['title']}}%0A.%0ASe%20você%20deseja%20saber%20mais%20sobre%20esta%20oportunidade,%20por%20favor,%20clique%20no%20link:%20{{url('/empregos/'. $job['slug'])}}%0A." target="_blank">Partilhar via LinkedIn</a>
		</div>
        <hr>

        <!-- Preview Image -->
        <img class="img-fluid rounded" src="{{asset('storage/' . $job['photo'])}}" alt="Emprego">

        <hr>
		@php
		  $previousJob = $LastJobs->where('id', '<', $job['id'])->first();
		@endphp
		<a href="https://ao.empregosyoyota.net/empregos/{{$previousJob->slug}}?utm_campaign=next_button&utm_source=next_button" class="btn btn-big btn-dark btn-block mb-3 mt-3">VER PROXIMA VAGA</a>
        <!-- Post Content -->
        <h3>Descrição:</h3>
		<!-- AD 1 -->
		<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-2118765549976668"
     crossorigin="anonymous"></script>
		<!-- AnuncioVizualizacao2 -->
		<ins class="adsbygoogle"
			 style="display:block"
			 data-ad-client="ca-pub-2118765549976668"
			 data-ad-slot="5838441610"
			 data-ad-format="auto"
			 data-full-width-responsive="true"></ins>
		<script>
			 (adsbygoogle = window.adsbygoogle || []).push({});
		</script>
		<!-- AD 2 -->
        <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-2118765549976668" crossorigin="anonymous"></script>
        <ins class="adsbygoogle"
            style="display:block; text-align:center;"
            data-ad-layout="in-article"
            data-ad-format="fluid"
            data-ad-client="ca-pub-2118765549976668"
            data-ad-slot="7583808877">
        </ins>
        <script>
            (adsbygoogle = window.adsbygoogle || []).push({});
        </script>
        <div class="lead content">
            {!!$job['description']!!}
        </div>

        <p>Empresa: {{$job['company']}}</p>
        <p>E-mail ou link de candidatura: {{$job['email_or_link']}} </p>
		  <p><strong><span>Entre no nosso canal do WhatsApp&nbsp;</span></strong><a href="https://whatsapp.com/channel/0029VaCfSeo0bIdgKs7bIB3t"><strong>CLICANDO AQUI</strong></a></p>
			  <hr>
			
		<a href="https://ao.empregosyoyota.net/empregos/{{$previousJob->slug}}?utm_campaign=next_button&utm_source=next_button" class="btn btn-big btn-dark btn-block mb-3">VER PROXIMA VAGA</a>
		  
		<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-2118765549976668"
     crossorigin="anonymous"></script>
		<!-- AnuncioVisualizacao -->
		<ins class="adsbygoogle"
			 style="display:block"
			 data-ad-client="ca-pub-2118765549976668"
			 data-ad-slot="9753835582"
			 data-ad-format="auto"
			 data-full-width-responsive="true"></ins>
		<script>
			 (adsbygoogle = window.adsbygoogle || []).push({});
		</script>
			  
		<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-2118765549976668"
     crossorigin="anonymous"></script>
		<!-- AnuncioVisualizacao3 -->
		<ins class="adsbygoogle"
			 style="display:block"
			 data-ad-client="ca-pub-2118765549976668"
			 data-ad-slot="2166789917"
			 data-ad-format="auto"
			 data-full-width-responsive="true"></ins>
		<script>
			 (adsbygoogle = window.adsbygoogle || []).push({});
		</script>
		
		<section class="labor-law-section mb-5" style="background-color: #f8f8f8; padding: 40px 20px; text-align: center;">
    <div class="container">
        <h2 style="font-size: 28px; color: #333; margin-bottom: 20px;">Conheça a Lei Geral do Trabalho Angolana</h2>
        <p style="font-size: 16px; color: #555; max-width: 600px; margin: 0 auto 30px;">
            Informe-se sobre os seus direitos e deveres como trabalhador em Angola. A Lei Geral do Trabalho regula as relações laborais, garantindo proteção e equilíbrio no mercado de trabalho. Esteja preparado para a sua carreira!
        </p>
        <a href="https://www.ao.empregosyoyota.net/articles/lei-geral-do-trabalho-lei-no-1223-de-27-de-dezembro" target="_blank" class="btn btn-lg btn-primary">
            Consulte a Lei Geral do Trabalho
        </a>
    </div>
</section>
      </div>
      
      

      <!-- Sidebar Widgets Column -->
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
        <!-- Anúncio Adaptável -->
        <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-2118765549976668"
            crossorigin="anonymous"></script>
        <!-- Anúncio Adaptável -->
        <ins class="adsbygoogle"
            style="display:block"
            data-ad-client="ca-pub-2118765549976668"
            data-ad-slot="8002595367"
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
@section('depois')
			  	  <script type="application/ld+json">

		{

      "@context":"http:\/\/schema.org\/",

      "@type":"JobPosting",

      "datePosted":"{{ date_format(new DateTime($job['created_at']), DATE_ATOM) }}",
	  "validThrough" : "{{ date('Y-m-d\TH:i', strtotime($job->created_at . ' +45 days')) }}",

      "title":"{{$job['title']}}",

      "description":"{{$job['description']}}",

      "employmentType":["FULL_TIME"],

      "hiringOrganization":{

              "@type":"Organization",

              "name":"{{$job['company']}}",

              "logo":"{{asset('storage/' . $job['photo'])}}"

              },

      "identifier":{

              "@type":"PropertyValue",

              "name":"{{$job['company']}}",

              "value":"{{url('/empregos/'. $job['slug'])}}"

              },

      "jobLocation":[

        

        {

          "@type":"Place",

          "address":"{{$job['province']}}"

        }

       

    ]

    }

  </script>
@endsection