@extends('template.app')
@section('title', 'Ferramentas')
@section('description', 'É uma plataforma que reúne oportunidades de emprego no solo angolano, tendo como fonte principal o "Jornal de Angola", criada aos 5 de Dezembro de 2018, a Empregos Yoyota tem ajudado muita gente a encontrar empregos no solo angolano')
@section('canonical_link', url('/dashboard'))
@section('content')
<style>
    * {
        font-family: 'Montserrat', sans-serif;
    }
    
    body {
        background: #f8f9fa;
        color: #000000;
        min-height: 100vh;
    }
    
    /* Header Styles */
    .navbar {
        background: #ffffff !important;
        border-bottom: 1px solid #e9ecef;
        padding: 1rem 0;
    }

    .navbar-brand {
        font-size: 1.5rem;
        color: #000000 !important;
        text-decoration: none;
    }

    .navbar-brand .empregos {
        font-weight: 600; /* Semi-bold */
    }

    .navbar-brand .yoyota {
        font-weight: 300; /* Light */
    }

    .navbar-nav .nav-link {
        color: #6c757d !important;
        //font-weight: 500;
        margin: 0 0.5rem;
        transition: color 0.3s ease;
    }

    .navbar-nav .nav-link:hover {
        color: #000000 !important;
    }

    .navbar-nav .nav-link.active {
        background: #000000;
        color: #ffffff !important;
        border-radius: 25px;
        padding: 0.5rem 1rem !important;
    }

    .btn-outline-dark {
        border: 1px solid #000000;
        color: #000000;
        font-weight: 600;
        border-radius: 25px;
        padding: 0.5rem 1.5rem;
        transition: all 0.3s ease;
    }

    .btn-outline-dark:hover {
        background: #000000;
        color: #ffffff;
    }
    
    /* Main Content */
    .main-content {
        padding: 4rem 0;
        flex: 1;
    }
    
    .page-title {
        font-size: 3rem;
        font-weight: 900;
        color: #000000;
        text-align: center;
        margin-bottom: 4rem;
    }
    
    /* App Grid */
    .apps-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 2rem;
        max-width: 1200px;
        margin: 0 auto;
    }
    
    .app-card {
        background: #ffffff;
        border: 1px solid #e9ecef;
        border-radius: 16px;
        padding: 3rem 2rem;
        text-align: center;
        transition: all 0.3s ease;
        box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        position: relative;
        overflow: hidden;
    }
    
    .app-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.1);
    }
    
    .app-card.featured {
        border-color: #000000;
        background: linear-gradient(135deg, #000000 0%, #333333 100%);
        color: #ffffff;
    }
    
    .app-card.featured::before {
        content: 'NOVO';
        position: absolute;
        top: 1rem;
        right: 1rem;
        background: #ffffff;
        color: #000000;
        padding: 0.25rem 0.75rem;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 700;
    }
    
    .app-icon {
        width: 80px;
        height: 80px;
        background: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 2rem;
        font-size: 2rem;
        color: #000000;
        transition: all 0.3s ease;
    }
    
    .app-card.featured .app-icon {
        background: rgba(255,255,255,0.1);
        border-color: rgba(255,255,255,0.2);
        color: #ffffff;
    }
    
    .app-card:hover .app-icon {
        transform: scale(1.1);
    }
    
    .app-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #000000;
        margin-bottom: 1rem;
        line-height: 1.3;
    }
    
    .app-card.featured .app-title {
        color: #ffffff;
    }
    
    .app-description {
        color: #6c757d;
        line-height: 1.6;
        margin-bottom: 2rem;
        font-size: 0.95rem;
    }
    
    .app-card.featured .app-description {
        color: #e9ecef;
    }
    
    .app-button {
        background: #000000;
        color: #ffffff;
        border: 2px solid #000000;
        font-weight: 600;
        padding: 0.75rem 2rem;
        border-radius: 25px;
        text-decoration: none;
        display: inline-block;
        transition: all 0.3s ease;
        font-size: 0.9rem;
    }
    
    .app-button:hover {
        background: #333333;
        border-color: #333333;
        color: #ffffff;
        transform: translateY(-2px);
    }
    
    .app-card.featured .app-button {
        background: #ffffff;
        color: #000000;
        border-color: #ffffff;
    }
    
    .app-card.featured .app-button:hover {
        background: #f8f9fa;
        border-color: #f8f9fa;
        color: #000000;
    }
    
    .app-button.disabled {
        background: #6c757d;
        border-color: #6c757d;
        cursor: not-allowed;
        opacity: 0.7;
    }
    
    .app-button.disabled:hover {
        background: #6c757d;
        border-color: #6c757d;
        transform: none;
    }
    
    /* Footer Styles */
    .footer {
        background: #000000;
        color: #ffffff;
        padding: 4rem 0 2rem 0;
        margin-top: 0;
    }

    .footer-brand {
        font-size: 1.5rem;
        color: #ffffff;
        text-decoration: none;
        margin-bottom: 1rem;
        display: inline-block;
    }

    .footer-brand .empregos {
        font-weight: 600;
    }

    .footer-brand .yoyota {
        font-weight: 300;
    }

    .footer-description {
        color: #6c757d;
        font-size: 0.95rem;
        line-height: 1.6;
        margin-bottom: 2rem;
    }

    .footer-section h5 {
        color: #ffffff;
        font-weight: 700;
        font-size: 1.1rem;
        margin-bottom: 1.5rem;
    }

    .footer-links {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .footer-links li {
        margin-bottom: 0.75rem;
    }

    .footer-links a {
        color: #6c757d;
        text-decoration: none;
        font-weight: 500;
        transition: color 0.3s ease;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .footer-links a:hover {
        color: #ffffff;
    }

    .footer-links a.active {
        background: #ffffff;
        color: #000000;
        border-radius: 25px;
        padding: 0.5rem 1rem;
        font-weight: 600;
    }

    .social-links {
        display: flex;
        gap: 1rem;
        margin-top: 1rem;
    }

    .social-links a {
        width: 40px;
        height: 40px;
        background: #333333;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ffffff;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .social-links a:hover {
        background: #ffffff;
        color: #000000;
        transform: translateY(-2px);
    }

    .footer-bottom {
        border-top: 1px solid #333333;
        padding-top: 2rem;
        margin-top: 3rem;
    }

    .footer-bottom-links {
        display: flex;
        gap: 2rem;
        justify-content: center;
        margin-bottom: 1rem;
    }

    .footer-bottom-links a {
        color: #6c757d;
        text-decoration: none;
        font-size: 0.9rem;
        font-weight: 500;
        transition: color 0.3s ease;
    }

    .footer-bottom-links a:hover {
        color: #ffffff;
    }

    .copyright {
        text-align: center;
        color: #6c757d;
        font-size: 0.9rem;
        margin: 0;
    }
    
    @media (max-width: 768px) {
        .page-title {
            font-size: 2rem;
        }
        
        .apps-grid {
            grid-template-columns: 1fr;
            gap: 1.5rem;
        }
        
        .app-card {
            padding: 2rem 1.5rem;
        }
        
        .header-nav {
            gap: 1rem;
        }
    }
    
    @media (max-width: 576px) {
        .main-content {
            padding: 2rem 0;
        }
        
        .page-title {
            margin-bottom: 2rem;
        }
    }
</style>
<!-- Main Content -->
<main class="main-content">
    <div class="container">
        <h1 class="page-title">Ferramentas</h1>
        
        <div class="apps-grid">
            <!-- Candidaturas Automáticas - NOVO -->
            <div class="app-card featured">
                <div class="app-icon">
                    <i class="bi bi-send-fill"></i>
                </div>
                <h2 class="app-title">Candidaturas Automáticas</h2>
                <p class="app-description">
                    Deixe que nós façamos as candidaturas por você! Com base no seu CV, aplicamos automaticamente às vagas que combinam com o seu perfil.
                </p>
                <a href="https://pay.kuenha.com/856ed35c-7b33-4e98-9352-954d22bc56a2" class="app-button">
                    Ver Planos
                </a>
            </div>
            
            <!-- Modelos de Currículo -->
            <div class="app-card">
                <div class="app-icon">
                    <i class="bi bi-file-earmark-text-fill"></i>
                </div>
                <h2 class="app-title">Modelos de Currículo</h2>
                <p class="app-description">
                    Explore modelos de currículos profissionais para destacar suas habilidades e experiências.
                </p>
                <a href="{{url('articles/varios-modelos-de-curriculos-no-word-para-baixar-de-graca')}}" class="app-button">
                    Ver Modelos
                </a>
            </div>
            
            <!-- Lei Geral do Trabalho -->
            <div class="app-card">
                <div class="app-icon">
                    <i class="bi bi-person-video2"></i>
                </div>
                <h2 class="app-title">Lei Geral do Trabalho</h2>
                <p class="app-description">
                    Saiba sobre os seus direitos, Consulte a Lei Geral do Trabalho.
                </p>
                <a href="{{url('articles/lei-geral-do-trabalho-lei-no-1223-de-27-de-dezembro')}}" class="app-button">
                    Consultar
                </a>
            </div>
            
            <!-- OCR Online -->
            <div class="app-card">
                <div class="app-icon">
                    <i class="bi bi-camera-fill"></i>
                </div>
                <h2 class="app-title">OCR Online</h2>
                <p class="app-description">
                    Uma ferramenta gratuita para converter imagens e PDFs em texto com suporte a múltiplos idiomas.
                </p>
                <a href="{{url('onlineocr')}}" class="app-button">
                    Ir para OCR App
                </a>
            </div>
            
            <!-- Quiz Angola -->
            <div class="app-card">
                <div class="app-icon">
                    <i class="bi bi-question-circle-fill"></i>
                </div>
                <h2 class="app-title">Quiz - Teste o Seu Conhecimento</h2>
                <p class="app-description">
                    Um jogo poderoso para testar o seu conhecimento sobre Angola.
                </p>
                <a href="{{url('quiz')}}" class="app-button">
                    Ir para o Quiz
                </a>
            </div>
        </div>
    </div>
</main>
@endsection('content')
