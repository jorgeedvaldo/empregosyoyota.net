@extends('template.app')
@section('title', 'Sobre')
@section('description', 'É uma plataforma que reúne oportunidades de emprego no solo angolano, tendo como fonte principal o "Jornal de Angola", criada aos 5 de Dezembro de 2018, a Empregos Yoyota tem ajudado muita gente a encontrar empregos no solo angolano')
@section('canonical_link', url('/about'))
@section('content')
<style>
    /* ==========================================================================
   Estilos das Seções da Página "Sobre"
   ========================================================================== */

/* Hero Section */
.hero-section {
    background: #ffffff;
    padding: 80px 0;
    min-height: 30vh !important;
}

.hero-title {
    font-size: 4rem;
    font-weight: 900;
    color: #000000;
    line-height: 1.1;
    margin-bottom: 2rem;
}

.hero-title .highlight {
    position: relative;
    display: inline-block;
}

.hero-title .highlight::after {
    content: '';
    position: absolute;
    bottom: 8px;
    left: 0;
    right: 0;
    height: 8px;
    background: #000000;
    z-index: -1;
}

.hero-subtitle {
    font-size: 1.25rem;
    color: #6c757d;
    font-weight: 400;
    line-height: 1.6;
    margin-bottom: 3rem;
    max-width: 600px;
}

/* Títulos e Subtítulos de Seção Comuns */
.section-title {
    font-size: 3rem;
    font-weight: 900;
    color: #000000;
    text-align: center;
    margin-bottom: 1rem;
}

.section-subtitle {
    font-size: 1.2rem;
    color: #6c757d;
    text-align: center;
    margin-bottom: 4rem;
    max-width: 600px;
    margin-left: auto;
    margin-right: auto;
}

/* Platform Section */
.platform-section {
    padding: 80px 0;
    background: #f8f9fa;
}

.platform-content {
    font-size: 1.1rem;
    line-height: 1.8;
    color: #6c757d;
    margin-bottom: 2rem;
}

.platform-highlight {
    background: #ffffff;
    padding: 2rem;
    border-radius: 12px;
    border: 1px solid #e9ecef;
    margin-bottom: 2rem;
}

.platform-date {
    font-weight: 700;
    color: #000000;
    font-size: 1.1rem;
}

/* Services Section */
.services-section {
    padding: 80px 0;
    background: #ffffff;
}

.service-card {
    background: #ffffff;
    border: 1px solid #e9ecef;
    border-radius: 16px;
    padding: 2.5rem;
    margin-bottom: 2rem;
    transition: all 0.3s ease;
    box-shadow: 0 4px 20px rgba(0,0,0,0.05);
}

.service-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.1);
}

.service-icon {
    width: 60px;
    height: 60px;
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 1.5rem;
    font-size: 1.5rem;
    color: #000000;
}

.service-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: #000000;
    margin-bottom: 1rem;
}

.service-description {
    color: #6c757d;
    line-height: 1.6;
}

/* Team Section */
.team-section {
    padding: 80px 0;
    background: #f8f9fa;
}

.team-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 3rem;
    max-width: 800px;
    margin: 0 auto;
}

.team-card {
    background: #ffffff;
    border: 1px solid #e9ecef;
    border-radius: 16px;
    padding: 2rem;
    text-align: center;
    transition: all 0.3s ease;
    box-shadow: 0 4px 20px rgba(0,0,0,0.05);
}

.team-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.1);
}

.team-photo {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    margin: 0 auto 1.5rem;
    background: #f8f9fa;
    border: 3px solid #e9ecef;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 3rem;
    color: #6c757d;
}

.team-name {
    font-size: 1.5rem;
    font-weight: 700;
    color: #000000;
    margin-bottom: 0.5rem;
}

.team-role {
    font-size: 1rem;
    color: #6c757d;
    font-weight: 500;
    margin-bottom: 1rem;
}

.team-description {
    font-size: 0.95rem;
    color: #6c757d;
    line-height: 1.6;
    margin-bottom: 1.5rem;
}

.team-social {
    display: flex;
    justify-content: center;
    gap: 1rem;
}

.team-social a {
    width: 40px;
    height: 40px;
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6c757d;
    text-decoration: none;
    transition: all 0.3s ease;
}

.team-social a:hover {
    background: #000000;
    color: #ffffff;
    border-color: #000000;
}

/* Mission Section */
.mission-section {
    padding: 80px 0;
    background: #000000;
    color: #ffffff;
}

.mission-content {
    text-align: center;
    max-width: 800px;
    margin: 0 auto;
}

.mission-title {
    font-size: 2.5rem;
    font-weight: 900;
    margin-bottom: 2rem;
}

.mission-text {
    font-size: 1.2rem;
    line-height: 1.8;
    color: #e9ecef;
}

.footer {
    margin-top: 0 !important; /* Ajustado para a página Sobre */
}
</style>
<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8 mx-auto text-center">
                <h1 class="hero-title">
                    Sobre a <span class="highlight">Plataforma</span>
                </h1>
                
                <p class="hero-subtitle mx-auto">
                    Conheça a história, missão e equipe por trás da plataforma líder em recrutamento e seleção em Angola.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Platform Section -->
<section class="platform-section">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <h2 class="section-title">O que é a Empregos Yoyota?</h2>
                <div class="platform-content">
                    <p>
                        É uma plataforma que reúne oportunidades de emprego no solo angolano, tendo como fonte principal o "Jornal de Angola". A Empregos Yoyota tem ajudado muita gente a encontrar empregos no solo angolano.
                    </p>
                </div>
                
                <div class="platform-highlight">
                    <p class="platform-date">Criada aos 5 de Dezembro de 2018</p>
                    <p class="mb-0">Desde então, temos sido uma ponte confiável entre candidatos e empregadores, facilitando milhares de conexões profissionais em Angola.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Services Section -->
<section class="services-section">
    <div class="container">
        <h2 class="section-title">Nossos Serviços</h2>
        <div class="row">
            <div class="col-lg-4 mb-4">
                <div class="service-card">
                    <div class="service-icon">
                        <i class="bi bi-cursor-fill"></i>
                    </div>
                    <h3 class="service-title">Faça sua candidatura num clique</h3>
                    <p class="service-description">
                        Neste portal, além de você encontrar informações sobre vagas de emprego, também é possível candidatar-se às vagas via e-mail. Com um clique nós enviamos a sua candidatura ao e-mail do empregador.
                    </p>
                </div>
            </div>
            
            <div class="col-lg-4 mb-4">
                <div class="service-card">
                    <div class="service-icon">
                        <i class="bi bi-file-earmark-text"></i>
                    </div>
                    <h3 class="service-title">Peça-nos um currículo moderno</h3>
                    <p class="service-description">
                        Sabemos a importância de ter um currículo moderno e atualizado, nós ajudamos você a ter um currículo que se destaque dos demais candidatos, e assim, tendo muita chance para ser escolhido/a na vaga.
                    </p>
                </div>
            </div>
            
            <div class="col-lg-4 mb-4">
                <div class="service-card">
                    <div class="service-icon">
                        <i class="bi bi-megaphone"></i>
                    </div>
                    <h3 class="service-title">Publicite aqui o seu negócio ou serviço</h3>
                    <p class="service-description">
                        Nós também ajudamos a divulgar o seu negócio ou serviço na nossa plataforma, fale connosco! Chegue a mais pessoas e expanda o seu negócio através da nossa rede.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Team Section -->
<section class="team-section">
    <div class="container">
        <h2 class="section-title">Nossa Equipe</h2>
        <p class="section-subtitle">
            Conheça as pessoas por trás do Empregos Yoyota, dedicadas a transformar o mercado de trabalho em Angola.
        </p>
        
        <div class="team-grid">
            <!-- CEO -->
            <div class="team-card">
                <div class="team-photo">
                    <i class="bi bi-person-circle"></i>
                </div>
                <h3 class="team-name">Edivaldo Jorge</h3>
                <p class="team-role">CEO & Fundador</p>
                <p class="team-description">
                    Visionário e empreendedor, Edivaldo lidera a estratégia e visão do Empregos Yoyota. Com vasta experiência em tecnologia e recursos humanos, ele é responsável por guiar a empresa rumo ao futuro do recrutamento em Angola.
                </p>
                <div class="team-social">
                    <a href="http://linkedin.com/in/jorgeedvaldo"><i class="bi bi-linkedin"></i></a>
                    <a href="http://github.com/jorgeedvaldo"><i class="bi bi-github"></i></a>
                    <a href="http://instagram.com/jorgeedvaldo"><i class="bi bi-instagram"></i></a>
                </div>
            </div>
            
            <!-- Gestor de Conteúdos -->
            <div class="team-card">
                <div class="team-photo">
                    <i class="bi bi-person-circle"></i>
                </div>
                <h3 class="team-name">Gelson Somano</h3>
                <p class="team-role">Gestor de Conteúdos</p>
                <p class="team-description">
                    Especialista em comunicação e marketing digital, Gelson é responsável por toda a estratégia de conteúdo da plataforma. Ele garante que as informações sejam relevantes, atualizadas e úteis para nossa comunidade de usuários.
                </p>
                <div class="team-social">
                    <a href="https://www.linkedin.com/in/gelson-somano-44a130293/"><i class="bi bi-linkedin"></i></a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Mission Section -->
<section class="mission-section">
    <div class="container">
        <div class="mission-content">
            <h2 class="mission-title">Nossa Missão</h2>
            <p class="mission-text">
                Democratizar o acesso ao emprego em Angola, conectando talentos às melhores oportunidades através de uma plataforma inovadora, eficiente e acessível. Desde 2018, trabalhamos para que cada pessoa tenha a chance de encontrar o emprego dos seus sonhos.
            </p>
        </div>
    </div>
</section>
@endsection('content')
