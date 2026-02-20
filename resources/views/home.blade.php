@extends('template.app')
@section('title', 'Início')
@section('description', 'É uma plataforma que reúne oportunidades de emprego no solo angolano, tendo como fonte principal o "Jornal de Angola", criada aos 5 de Dezembro de 2018, a Empregos Yoyota tem ajudado muita gente a encontrar empregos no solo angolano')
@section('canonical_link', url('/'))
@section('head-scripts')
<script type="application/ld+json" class="yoast-schema-graph">
        {
            "@context": "https://schema.org",
            "@graph": [
                {
                "@type": "WebPage",
                "@id": "https://ao.empregosyoyota.net/",
                "url": "https://ao.empregosyoyota.net/",
                "name": "Empregos Yoyota",
                "isPartOf": {
                    "@id": "https://ao.empregosyoyota.net/#website"
                },
                "about": {
                    "@id": "https://ao.empregosyoyota.net/#organization"
                },
                "primaryImageOfPage": {
                    "@id": "https://ao.empregosyoyota.net/#primaryimage"
                },
                "image": {
                    "@id": "https://ao.empregosyoyota.net/#primaryimage"
                },
                "thumbnailUrl": "https://ao.empregosyoyota.net/storage/images/logo-full.jpg",
                "datePublished": "2021-06-05T18:42:05+00:00",
                "dateModified": "2023-03-18T05:03:34+00:00",
                "description": "Empregos Yoyota é um site Angolano de vagas que promove o contato direto entre quem está a procura de uma vaga de emprego, estágio ou bolsa de estudo, e quem está a contratar.",
                "breadcrumb": {
                    "@id": "https://ao.empregosyoyota.net/#breadcrumb"
                },
                "inLanguage": "pt-PT",
                "potentialAction": [
                    {
                    "@type": "ReadAction",
                    "target": [
                        "https://ao.empregosyoyota.net/"
                    ]
                    }
                ]
                },
                {
                "@type": "ImageObject",
                "inLanguage": "pt-PT",
                "@id": "https://ao.empregosyoyota.net/#primaryimage",
                "url": "https://ao.empregosyoyota.net/storage/images/logo-full.jpg",
                "contentUrl": "https://ao.empregosyoyota.net/storage/images/logo-full.jpg",
                "width": 290,
                "height": 290
                },
                {
                "@type": "BreadcrumbList",
                "@id": "https://ao.empregosyoyota.net/#breadcrumb",
                "itemListElement": [
                    {
                    "@type": "ListItem",
                    "position": 1,
                    "name": "Início"
                    }
                ]
                },
                {
                "@type": "WebSite",
                "@id": "https://ao.empregosyoyota.net/#website",
                "url": "https://ao.empregosyoyota.net/",
                "name": "Empregos Yoyota",
                "description": "Vagas de emprego estagio e bolsas de estudo",
                "publisher": {
                    "@id": "https://ao.empregosyoyota.net/#organization"
                },
                "potentialAction": [
                    {
                    "@type": "SearchAction",
                    "target": {
                        "@type": "EntryPoint",
                        "urlTemplate": "https://ao.empregosyoyota.net/pesquisar?query={search_term_string}"
                    },
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
                "image": {
                    "@id": "https://ao.empregosyoyota.net/#/schema/logo/image/"
                },
                "sameAs": [
                    "https://web.facebook.com/empregosyoyota"
                ]
                }
            ]
        }

    </script>
<script type="application/ld+json" class="reviews-schema" data-type="all">
															{
									"@context": "http://schema.org", 
									"@type": "Product", 
									"name": "Empregos Yoyota", 
																			"aggregateRating": { 
											"@type": "AggregateRating", 
											"bestRating":5,
											"ratingValue": 4.50,
											"reviewCount": 36979										}
																	}
														</script>
@endsection
@section('content')
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="hero-title">
                        A tua <span class="highlight">carreira</span><br>
                        começa agora
                    </h1>
                    
                    <p class="hero-subtitle">
                        Rápido, fácil e eficiente - conectamos talentos às melhores oportunidades de emprego em Angola com uma plataforma moderna e intuitiva.
                    </p>
                    
                    <div class="search-container">
                        <div class="d-flex">
                            <form action="{{ route('search') }}" method="GET" class="input-group mb-3">
                                <input type="search" class="form-control search-input flex-grow-1" placeholder="Digite o cargo ou palavra-chave..." aria-label="Search" aria-describedby="search-addon" name="query"/>
                                <button type="submit" class="btn search-btn">Pesquisar</button>
                            </form>
                        </div>
                    </div>
                    
                    <div class="stats-container">
                        <div class="stat-item">
                            <div class="stat-number">12.7k+</div>
                            <div class="stat-label">Vagas ativas</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number">~15k</div>
                            <div class="stat-label">Candidatos registrados</div>
                        </div>
                    </div>
                    
                    <div class="rating-container">
                        <div class="stars">
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-half"></i>
                        </div>
                        <span class="rating-text">4.7</span>
                        <span class="rating-label">Avaliação dos usuários</span>
                    </div>
                </div>
                
                <div class="col-lg-6">
                    <div class="hero-image position-relative">
                        <!-- Job Card 1 -->
                        <div class="job-card job-card-1">
                            <div class="company-logo">
                                <i class="bi bi-code-slash" style="color: #000000;"></i>
                            </div>
                            <div class="job-title">Desenvolvedor Full Stack</div>
                            <div class="job-company">TechSolutions Angola</div>
                            <div class="job-location">
                                <i class="bi bi-geo-alt"></i>
                                <span>Luanda</span>
                            </div>
                        </div>
                        
                        <!-- Job Card 2 -->
                        <div class="job-card job-card-2">
                            <div class="company-logo">
                                <i class="bi bi-graph-up" style="color: #000000;"></i>
                            </div>
                            <div class="job-title">Analista de Marketing</div>
                            <div class="job-company">Marketing Pro</div>
                            <div class="job-location">
                                <i class="bi bi-geo-alt"></i>
                                <span>Luanda</span>
                            </div>
                        </div>
                        
                        <!-- Job Card 3 -->
                        <div class="job-card job-card-3">
                            <div class="company-logo">
                                <i class="bi bi-gear" style="color: #000000;"></i>
                            </div>
                            <div class="job-title">Engenheiro Civil</div>
                            <div class="job-company">Construções SA</div>
                            <div class="job-location">
                                <i class="bi bi-geo-alt"></i>
                                <span>Luanda</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!--OPORTUNIDADES-->
    <section class="mt-5 mb-5">
        <div class="container">
            <div class="row">
            <div class="col-lg-12 text-center">
                <h1 class="section-heading text-uppercase">. . .</h1>
                <h2 class="section-heading text-uppercase">Oportunidades</h2>
                <p class="section-subheading text-muted">Vagas de emprego recentemente publicadas</p>
            </div>
            </div>
            <div class="row">
                <div class="col-md-12 p-0 ml-3 mr-3">
                    <div class="list-group">
                        @foreach($jobs as $job)

                            <a href="{{ url('/empregos/' . $job['slug']) }}" class="list-group-item list-group-item-action mb-3">
                                <div class="d-flex w-100 justify-content-between">
                                <h5 class="mb-1"><b>{{ $job['title'] }}</b></h5>
                                <small>Publicado em: {{ date_format(new DateTime($job['created_at']), 'd-m-Y') }}</small>
                                </div>
                                <p class="mb-1">Empresa: {{ $job['company'] }}</p>
                                <small><i class="fa fa-map-marker"></i> Localização: <span>{{ $job['province'] }}</span></small>
                            </a>

                        @endforeach
                    </div>
					<center><a href="{{ url('/empregos') }}" class = "btn btn-lg btn-block btn-dark">Ver mais empregos...</a></center>
                </div>
            </div>
        </div>
    </section>
        
    <!--Lei Geral do Trabalho-->
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

	
	<!--Artigos-->
    <section class="mt-5 mb-5">
        <div class="container">
			<div class="row">
            <div class="col-lg-12 text-center">
                <h1 class="section-heading text-uppercase">. . .</h1>
                <h2 class="section-heading text-uppercase">O Nosso Blog</h2>
                <p class="section-subheading text-muted">Fique atento/a aos últimos artigos</p>
            </div>
            </div>
    <div class="row mt-5">

        @foreach($articles as $article)
            <div class="col-md-6">
                <a href="{{ url('/articles/' . $article['slug']) }}" class="list-group-item list-group-item-action mb-3">
                    <div class="d-flex w-100 justify-content-between">
                    <h5 class="mb-1"><b>{{ $article['title'] }}</b></h5>
                    <small>Publicado em: {{ date_format(new DateTime($article['created_at']), 'd-m-Y') }}</small>
                    </div>
                    <p class="mb-1"> </p>
                    <small><i class="fa fa-journal"></i>   <span> </span></small>
                </a>
            </div>
          @endforeach
			<div class="col-md-12">
                <center><a href="{{ url('/articles') }}" class = "btn btn-lg btn-block btn-dark">Ver mais artigos...</a></center>
            </div>
    </div>
</div>
    </section>

    <!--SERVIÇOS-->
    <section id="services" class="mt-5 mb-5">
        <div class="container">
            <div class="row">
            <div class="col-lg-12 text-center">
                <h1 class="section-heading text-uppercase">. . .</h1>
                <h2 class="section-heading text-uppercase">Serviços</h2>
                <p class="section-subheading text-muted">Oferecemos diversos serviços, com soluções inteligentes.</p>
            </div>
            </div>
            <div class="row text-center">
            <div class="col-md-4">
                <span class="fa-stack fa-4x">
                    <i class="fa fa-circle fa-stack-2x text-dark"></i>
                    <i class="fa fa-asterisk fa-stack-1x fa-inverse"></i>
                </span>
                <h4 class="service-heading">Dicas</h4>
                <p class="text-muted">Encontre dicas útes de como ter uma boa entrevista</p>
            </div>
            <div class="col-md-4">
                <span class="fa-stack fa-4x">
                    <i class="fa fa-circle fa-stack-2x text-dark"></i>
                    <i class="fa fa-search fa-stack-1x fa-inverse"></i>
                </span>
                <h4 class="service-heading">Encontre Empregos</h4>
                <p class="text-muted">Aqui você encontra diversas oportunidades de emprego nas mais diversas categorias</p>
            </div>
            <div class="col-md-4">
                <span class="fa-stack fa-4x">
                    <i class="fa fa-circle fa-stack-2x text-dark"></i>
                    <i class="fa fa-feed fa-stack-1x fa-inverse"></i>
                </span>
                <h4 class="service-heading">Faça Publicidade</h4>
                <p class="text-muted">Este serviço é mais voltado para aqueles que desejam fazer marketing dos seus negócios, e outros no nosso site, temos planos bem baratos para qualquer um aderir</p>
            </div>
            </div>
        </div>
    </section>
@endsection('content')
