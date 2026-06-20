<!doctype html>
<html lang="pt-PT">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="icon" href="{{ asset('storage/images/logo-16x16.png') }}" sizes="16x16" />
		<link rel="icon" href="{{ asset('storage/images/logo-32x32.png') }}" sizes="32x32" />
		<link rel="icon" href="{{ asset('storage/images/logo-96x96.png') }}" sizes="96x96" />
		<link rel="apple-touch-icon" href="{{ asset('storage/images/logo-180x180.png') }}" />

        <title>@yield('title') - {{env('APP_NAME')}}</title>
        <meta name="description" content="@yield('description')" />
		<meta name="robots" content="follow, index, max-snippet:-1, max-video-preview:-1, max-image-preview:large"/>

        <!-- Google Analytics (deferred) -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=UA-189086548-1"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', 'UA-189086548-1');
        </script>

        <!-- AdSense -->
        <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-2118765549976668" crossorigin="anonymous"></script>

        <!-- Open Graph / Facebook -->
		<meta property="og:type" content="article" />
		<meta property="og:title" content="@yield('title') - {{env('APP_NAME')}}" />
		<meta property="og:url" content="@yield('canonical_link')" />
		<meta property="og:description" content="@yield('description')" />
		<meta property="article:published_time" content="@yield('created_at')" />
		<meta property="article:modified_time" content="@yield('updated_at')" />
		<meta property="og:site_name" content="Empregos Yoyota" />
		<meta property="og:image" content="@yield('url')" />
		<meta property="og:image:width" content="1200" />
		<meta property="og:image:height" content="700" />
		<meta property="og:image:alt" content="Empregos Yoyota" />
		<meta property="og:locale" content="pt_PT" />
		<meta name="author" content="Edivaldo" />
		<meta name="twitter:text:title" content="@yield('title') - {{env('APP_NAME')}}" />
		<meta name="twitter:image" content="@yield('url')" />
		<meta name="twitter:card" content="summary_large_image" />

		<!-- Feed -->
    	<link rel="alternate" type="application/rss+xml" title="Empregos Yoyota &raquo; Feed" href="{{url('/feed')}}" />

		<!-- Canonical Link -->
        <link rel="canonical" href="@yield('canonical_link')"/>

        <!-- Preconnect to font origins -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>

        <!-- Styles (critical) -->
        <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" />
        <link href="{{ asset('assets/css/style.css') }}?v={{ @filemtime(public_path('assets/css/style.css')) ?: '2' }}" rel="stylesheet" />

        <!-- Non-critical CSS (deferred) -->
        <link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;900&display=swap" onload="this.onload=null;this.rel='stylesheet'">
        <noscript><link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;900&display=swap" rel="stylesheet"></noscript>

        <link rel="preload" as="style" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" onload="this.onload=null;this.rel='stylesheet'">
        <noscript><link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet"></noscript>

        <link rel="preload" as="style" href="{{ asset('assets/css/font-awesome.min.css') }}" onload="this.onload=null;this.rel='stylesheet'">
        <noscript><link href="{{ asset('assets/css/font-awesome.min.css') }}" rel="stylesheet"></noscript>

		@yield('head-scripts')
    </head>

    <body>
        <div id="app">
            <!-- Header -->
            <header class="navbar navbar-expand-lg navbar-light" role="banner">
                <div class="container">
                    <a class="navbar-brand" href="{{ url('/') }}">
                        <span class="empregos">EMPREGOS</span><span class="yoyota">YOYOTA</span>
                    </a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Abrir menu de navegação">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <nav class="collapse navbar-collapse justify-content-end" id="navbarNav" aria-label="Navegação principal">
                        <ul class="navbar-nav ms-auto">
                            <li class="nav-item">
                                <a class="nav-link" href="{{ url('/') }}"><i class="bi bi-house me-1" aria-hidden="true"></i>Início</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ url('/about') }}"><i class="bi bi-info-circle me-1" aria-hidden="true"></i>Sobre</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link active" href="{{ url('/empregos') }}">Oportunidades</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ url('/articles') }}"><i class="bi bi-journal-text me-1" aria-hidden="true"></i>Blog</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ url('/dashboard') }}"><i class="bi bi-tools me-1" aria-hidden="true"></i>Ferramentas</a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </header>

            <main id="main-content">
                @yield('content')
            </main>

            <footer class="footer" role="contentinfo">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-4 mb-4">
                            <a href="{{ url('/') }}" class="footer-brand"><span class="empregos">EMPREGOS</span><span class="yoyota">YOYOTA</span></a>
                            <p class="footer-description">
                                Conectamos talentos às melhores oportunidades de emprego em Angola. A plataforma líder em recrutamento e seleção do país.
                            </p>
                            <div class="social-links">
                                <a href="https://facebook.com/empregosyoyota" aria-label="Facebook"><i class="bi bi-facebook" aria-hidden="true"></i></a>
                                <a href="https://www.linkedin.com/company/empregosyoyota" aria-label="LinkedIn"><i class="bi bi-linkedin" aria-hidden="true"></i></a>
                                <a href="https://instagram.com/empregosyoyota" aria-label="Instagram"><i class="bi bi-instagram" aria-hidden="true"></i></a>
                            </div>
                        </div>

                        <div class="col-lg-2 col-md-6 mb-4">
                            <div class="footer-section">
                                <h5>Navegação</h5>
                                <ul class="footer-links">
                                    <li><a href="{{ url('/') }}">Início</a></li>
                                    <li><a href="{{ url('/about') }}">Sobre</a></li>
                                    <li><a href="{{ url('/empregos') }}">Oportunidades</a></li>
                                    <li><a href="{{ url('/articles') }}">Blog</a></li>
                                    <li><a href="{{ url('/dashboard') }}">Ferramentas</a></li>
                                </ul>
                            </div>
                        </div>

                        <div class="col-lg-2 col-md-6 mb-4">
                            <div class="footer-section">
                                <h5>Vagas por País</h5>
                                <ul class="footer-links">
                                    <li><a href="{{ url('/ao/empregos') }}">&#127462;&#127476; Angola</a></li>
                                    <li><a href="{{ url('/br/empregos') }}">&#127463;&#127479; Brasil</a></li>
                                    <li><a href="{{ url('/mz/empregos') }}">&#127474;&#127487; Moçambique</a></li>
                                </ul>
                            </div>
                        </div>

                        <div class="col-lg-2 col-md-6 mb-4">
                            <div class="footer-section">
                                <h5>Para Candidatos</h5>
                                <ul class="footer-links">
                                    <li><a href="{{ url('/empregos') }}">Buscar Vagas</a></li>
                                    <li><a href="#">Cadastrar Currículo</a></li>
                                    <li><a href="#">Dicas de Carreira</a></li>
                                    <li><a href="#">Preparação para Entrevistas</a></li>
                                    <li><a href="{{ url('articles/salarios-em-angola-saiba-o-salario-do-presidente-da-republica-e-outros-titulares-de-cargos-da-funcao-executiva-do-estado') }}">Salários em Angola</a></li>
                                </ul>
                            </div>
                        </div>

                        <div class="col-lg-2 col-md-6 mb-4">
                            <div class="footer-section">
                                <h5>Para Empresas</h5>
                                <ul class="footer-links">
                                    <li><a href="mailto:geral@empregosyoyota.net">Publicar Vagas</a></li>
                                    <li><a href="#">Buscar Candidatos</a></li>
                                    <li><a href="#">Recursos para RH</a></li>
                                    <li><a href="#">Contato Comercial</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="footer-bottom">
                        <div class="footer-bottom-links">
                            <a href="{{ url('/terms') }}">Termos de Uso e Política de Privacidade</a>
                            <a href="#">Cookies</a>
                            <a href="#">Suporte</a>
                            <a href="#">Contato</a>
                        </div>
                        <p class="copyright">
                            &copy; {{ date('Y') }} Empregos Yoyota. Todos os direitos reservados.
                        </p>
                    </div>
                </div>
            </footer>
        </div>
		@yield('footer-scripts')
		<!-- Bootstrap 5 JS Bundle (deferred) -->
        <script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>
