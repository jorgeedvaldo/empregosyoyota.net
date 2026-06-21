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
                "@id": "https://empregosyoyota.net/",
                "url": "https://empregosyoyota.net/",
                "name": "Empregos Yoyota",
                "isPartOf": {
                    "@id": "https://empregosyoyota.net/#website"
                },
                "about": {
                    "@id": "https://empregosyoyota.net/#organization"
                },
                "primaryImageOfPage": {
                    "@id": "https://empregosyoyota.net/#primaryimage"
                },
                "image": {
                    "@id": "https://empregosyoyota.net/#primaryimage"
                },
                "thumbnailUrl": "https://empregosyoyota.net/storage/images/logo-full.jpg",
                "datePublished": "2021-06-05T18:42:05+00:00",
                "dateModified": "2023-03-18T05:03:34+00:00",
                "description": "Empregos Yoyota é um site Angolano de vagas que promove o contato direto entre quem está a procura de uma vaga de emprego, estágio ou bolsa de estudo, e quem está a contratar.",
                "breadcrumb": {
                    "@id": "https://empregosyoyota.net/#breadcrumb"
                },
                "inLanguage": "pt-PT",
                "potentialAction": [
                    {
                    "@type": "ReadAction",
                    "target": [
                        "https://empregosyoyota.net/"
                    ]
                    }
                ]
                },
                {
                "@type": "ImageObject",
                "inLanguage": "pt-PT",
                "@id": "https://empregosyoyota.net/#primaryimage",
                "url": "https://empregosyoyota.net/storage/images/logo-full.jpg",
                "contentUrl": "https://empregosyoyota.net/storage/images/logo-full.jpg",
                "width": 290,
                "height": 290
                },
                {
                "@type": "BreadcrumbList",
                "@id": "https://empregosyoyota.net/#breadcrumb",
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
                "@id": "https://empregosyoyota.net/#website",
                "url": "https://empregosyoyota.net/",
                "name": "Empregos Yoyota",
                "description": "Vagas de emprego estagio e bolsas de estudo",
                "publisher": {
                    "@id": "https://empregosyoyota.net/#organization"
                },
                "potentialAction": [
                    {
                    "@type": "SearchAction",
                    "target": {
                        "@type": "EntryPoint",
                        "urlTemplate": "https://empregosyoyota.net/pesquisar?query={search_term_string}"
                    },
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
                "image": {
                    "@id": "https://empregosyoyota.net/#/schema/logo/image/"
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
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center">
                    <h1 class="hero-title">
                        A tua carreira<br>começa <span class="highlight">agora</span>
                    </h1>

                    <p class="hero-subtitle">
                        Aqui você encontra o seu emprego ideal
                    </p>

                    <div class="search-container">
                        <form action="{{ route('search') }}" method="GET" class="hero-search-form">
                            <div class="search-field">
                                <i class="bi bi-search"></i>
                                <input type="search" class="search-input" placeholder="Cargo, empresa ou competências" name="query"/>
                            </div>
                            <div class="search-divider"></div>
                            <div class="search-field">
                                <i class="bi bi-geo-alt"></i>
                                <input type="text" class="search-input" placeholder="Cidade ou Província" name="location"/>
                            </div>
                            <button type="submit" class="btn search-btn">Pesquisar Vagas</button>
                        </form>
                    </div>

                    <div class="trending-tags">
                        <span class="trending-label">Tendências:</span>
                        <a href="{{ url('/pesquisar?query=Tecnologia') }}" class="trending-tag">Tecnologia</a>
                        <a href="{{ url('/pesquisar?query=Vendas') }}" class="trending-tag">Vendas</a>
                        <a href="{{ url('/pesquisar?query=Construção') }}" class="trending-tag">Construção</a>
                        <a href="{{ url('/pesquisar?query=Administração') }}" class="trending-tag">Administração</a>
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
                <div class="section-dots" aria-hidden="true">. . .</div>
                <h2 class="section-heading text-uppercase">Oportunidades</h2>
                <p class="section-subheading text-muted">Vagas de emprego recentemente publicadas</p>
            </div>
            </div>
            <div class="row">
                <div class="col-md-12 p-0 ml-3 mr-3">
                    <div class="job-list">
                        @foreach($jobs as $job)

                            <a href="{{ url('/empregos/' . $job['slug']) }}" class="job-card-item">
                                <div class="job-card-icon">
                                    <i class="bi bi-briefcase"></i>
                                </div>
                                <div class="job-card-body">
                                    <h3 class="job-card-title">{{ $job['title'] }}</h5>
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
					<center><a href="{{ url('/empregos') }}" class="btn btn-lg btn-block btn-dark mt-4">Ver mais empregos...</a></center>
                </div>
            </div>
        </div>
    </section>
        
    <!-- Vagas por País -->
    <section class="mt-5 mb-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <div class="section-dots" aria-hidden="true">. . .</div>
                    <h2 class="section-heading text-uppercase">Vagas por País</h2>
                    <p class="section-subheading text-muted">Encontre oportunidades de emprego no seu país</p>
                </div>
            </div>
            <div class="row justify-content-center mt-4">
                <div class="col-md-4 col-sm-6 mb-4">
                    <a href="{{ url('/ao/empregos') }}" class="text-decoration-none">
                        <div class="card h-100 text-center shadow-sm border-0" style="border-radius: 12px; transition: transform .2s;">
                            <div class="card-body py-4">
                                <div class="mb-3" style="font-size: 3rem; line-height: 1;">&#127462;&#127476;</div>
                                <h3 class="card-title fw-bold mb-1 h5">Angola</h5>
                                <p class="card-text text-muted small">Vagas de emprego em Angola</p>
                                <span class="btn btn-dark btn-sm mt-2">Ver vagas</span>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-4 col-sm-6 mb-4">
                    <a href="{{ url('/br/empregos') }}" class="text-decoration-none">
                        <div class="card h-100 text-center shadow-sm border-0" style="border-radius: 12px; transition: transform .2s;">
                            <div class="card-body py-4">
                                <div class="mb-3" style="font-size: 3rem; line-height: 1;">&#127463;&#127479;</div>
                                <h3 class="card-title fw-bold mb-1 h5">Brasil</h5>
                                <p class="card-text text-muted small">Vagas de emprego no Brasil</p>
                                <span class="btn btn-dark btn-sm mt-2">Ver vagas</span>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-4 col-sm-6 mb-4">
                    <a href="{{ url('/mz/empregos') }}" class="text-decoration-none">
                        <div class="card h-100 text-center shadow-sm border-0" style="border-radius: 12px; transition: transform .2s;">
                            <div class="card-body py-4">
                                <div class="mb-3" style="font-size: 3rem; line-height: 1;">&#127474;&#127487;</div>
                                <h3 class="card-title fw-bold mb-1 h5">Moçambique</h5>
                                <p class="card-text text-muted small">Vagas de emprego em Moçambique</p>
                                <span class="btn btn-dark btn-sm mt-2">Ver vagas</span>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Divulgação do App (Google Play) -->
    <section class="app-promo-section mt-5 mb-5">
        <div class="container">
            <div class="row align-items-center g-4 p-4 p-md-5"
                 style="background: linear-gradient(135deg, #1a1a1a 0%, #343a40 100%); border-radius: 16px; color: #fff;">
                <div class="col-lg-8 text-center text-lg-start">
                    <h2 class="fw-bold mb-3" style="color: #fff;">
                        <i class="bi bi-google-play me-2" aria-hidden="true"></i>
                        Leve a Empregos Yoyota consigo
                    </h2>
                    <p class="mb-0" style="color: rgba(255, 255, 255, 0.85); font-size: 1.1rem;">
                        Baixe o nosso aplicativo na Google Play e receba notificações das vagas
                        de emprego mais recentes diretamente no seu telemóvel.
                    </p>
                </div>
                <div class="col-lg-4 text-center text-lg-end">
                    <a href="https://play.google.com/store/apps/details?id=net.empregosyoyota.app"
                       target="_blank" rel="noopener"
                       aria-label="Baixar o app Empregos Yoyota na Google Play">
                        <img src="https://play.google.com/intl/pt-BR_pt/badges/static/images/badges/pt-br_badge_web_generic.png"
                             alt="Disponível no Google Play"
                             loading="lazy"
                             style="height: 64px; width: auto;">
                    </a>
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
            <a href="https://empregosyoyota.net/articles/lei-geral-do-trabalho-lei-no-1223-de-27-de-dezembro" target="_blank" class="btn btn-lg btn-primary">
                Consulte a Lei Geral do Trabalho
            </a>
        </div>
    </section>

	
	<!--Artigos-->
    <section class="mt-5 mb-5">
        <div class="container">
			<div class="row">
            <div class="col-lg-12 text-center">
                <div class="section-dots" aria-hidden="true">. . .</div>
                <h2 class="section-heading text-uppercase">O Nosso Blog</h2>
                <p class="section-subheading text-muted">Fique atento/a aos últimos artigos</p>
            </div>
            </div>
    <div class="row mt-5">

        @foreach($articles as $article)
            <div class="col-md-6">
                <a href="{{ url('/articles/' . $article['slug']) }}" class="list-group-item list-group-item-action mb-3">
                    <div class="d-flex w-100 justify-content-between">
                    <h3 class="mb-1 h5"><b>{{ $article['title'] }}</b></h3>
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
                <div class="section-dots" aria-hidden="true">. . .</div>
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
                <h3 class="service-heading">Dicas</h4>
                <p class="text-muted">Encontre dicas útes de como ter uma boa entrevista</p>
            </div>
            <div class="col-md-4">
                <span class="fa-stack fa-4x">
                    <i class="fa fa-circle fa-stack-2x text-dark"></i>
                    <i class="fa fa-search fa-stack-1x fa-inverse"></i>
                </span>
                <h3 class="service-heading">Encontre Empregos</h4>
                <p class="text-muted">Aqui você encontra diversas oportunidades de emprego nas mais diversas categorias</p>
            </div>
            <div class="col-md-4">
                <span class="fa-stack fa-4x">
                    <i class="fa fa-circle fa-stack-2x text-dark"></i>
                    <i class="fa fa-feed fa-stack-1x fa-inverse"></i>
                </span>
                <h3 class="service-heading">Faça Publicidade</h4>
                <p class="text-muted">Este serviço é mais voltado para aqueles que desejam fazer marketing dos seus negócios, e outros no nosso site, temos planos bem baratos para qualquer um aderir</p>
            </div>
            </div>
        </div>
    </section>
@endsection('content')
