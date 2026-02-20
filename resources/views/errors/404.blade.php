@extends('template.app')
@section('title', 'Página não encontrada')
@section('description', 'É uma plataforma que reúne oportunidades de emprego no solo angolano, tendo como fonte principal o "Jornal de Angola", criada aos 5 de Dezembro de 2018, a Empregos Yoyota tem ajudado muita gente a encontrar empregos no solo angolano')
@section('content')
<style>
    * {
        font-family: 'Montserrat', sans-serif;
    }
    
    body {
        background: #ffffff;
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
        font-weight: 600;
    }

    .navbar-brand .yoyota {
        font-weight: 300;
    }

    .navbar-nav .nav-link {
        color: #6c757d !important;
        font-weight: 500;
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

    .btn-dark {
        background: #000000;
        border: 1px solid #000000;
        color: #ffffff;
        font-weight: 600;
        border-radius: 25px;
        padding: 0.75rem 2rem;
        transition: all 0.3s ease;
    }

    .btn-dark:hover {
        background: #333333;
        border-color: #333333;
        transform: translateY(-2px);
    }
    
    /* 404 Section */
    .error-section {
        padding: 3rem 0;
        background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
        position: relative;
        overflow: hidden;
        min-height: 80vh;
        display: flex;
        align-items: center;
    }

    .error-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="dots" width="20" height="20" patternUnits="userSpaceOnUse"><circle cx="10" cy="10" r="1" fill="%23000000" opacity="0.05"/></pattern></defs><rect width="100" height="100" fill="url(%23dots)"/></svg>');
        opacity: 0.5;
    }

    .error-content {
        text-align: center;
        position: relative;
        z-index: 2;
        width: 100%;
    }

    .error-number {
        font-size: clamp(6rem, 15vw, 12rem);
        font-weight: 900;
        color: #000000;
        line-height: 1;
        margin-bottom: 1rem;
        position: relative;
        display: inline-block;
    }

    .error-number::after {
        content: '';
        position: absolute;
        bottom: 10px;
        left: 50%;
        transform: translateX(-50%);
        width: 80%;
        height: clamp(10px, 2vw, 20px);
        background: #000000;
        opacity: 0.1;
        border-radius: 50px;
    }

    .error-title {
        font-size: clamp(1.8rem, 5vw, 3rem);
        font-weight: 900;
        color: #000000;
        margin-bottom: 1.5rem;
        line-height: 1.2;
    }

    .error-subtitle {
        font-size: clamp(1rem, 3vw, 1.3rem);
        color: #6c757d;
        font-weight: 400;
        line-height: 1.6;
        margin-bottom: 2rem;
        max-width: 600px;
        margin-left: auto;
        margin-right: auto;
    }

    .search-form {
        background: #ffffff;
        border: 2px solid #e9ecef;
        border-radius: 50px;
        padding: 0.5rem;
        display: flex;
        max-width: 100%;
        width: 100%;
        margin: 0 auto 2rem;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }

    .search-input {
        border: none;
        outline: none;
        flex: 1;
        padding: 0.75rem 1rem;
        font-size: clamp(0.9rem, 2.5vw, 1rem);
        background: transparent;
        min-width: 0;
    }

    .search-input::placeholder {
        color: #6c757d;
    }

    .search-btn {
        background: #000000;
        color: #ffffff;
        border: none;
        border-radius: 50px;
        padding: 0.75rem 1.5rem;
        font-weight: 600;
        transition: all 0.3s ease;
        cursor: pointer;
        white-space: nowrap;
    }

    .search-btn:hover {
        background: #333333;
        transform: scale(1.05);
    }

    .error-actions {
        display: flex;
        gap: 1rem;
        justify-content: center;
        flex-wrap: wrap;
        margin-bottom: 3rem;
    }

    .error-actions .btn {
        font-size: clamp(0.9rem, 2.5vw, 1rem);
        padding: 0.75rem 1.5rem;
    }

    /* Suggestions */
    .suggestions {
        margin-top: 2rem;
    }

    .suggestions-title {
        font-size: clamp(1.3rem, 4vw, 1.5rem);
        font-weight: 700;
        color: #000000;
        margin-bottom: 2rem;
    }

    .suggestion-card {
        background: #ffffff;
        border: 1px solid #e9ecef;
        border-radius: 12px;
        padding: 1.5rem;
        text-align: center;
        transition: all 0.3s ease;
        box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        height: 100%;
    }

    .suggestion-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        border-color: #000000;
    }

    .suggestion-icon {
        width: 50px;
        height: 50px;
        background: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1rem;
        font-size: 1.2rem;
        color: #000000;
        transition: all 0.3s ease;
    }

    .suggestion-card:hover .suggestion-icon {
        background: #000000;
        color: #ffffff;
        border-color: #000000;
    }

    .suggestion-title {
        font-size: clamp(1rem, 3vw, 1.1rem);
        font-weight: 700;
        color: #000000;
        margin-bottom: 0.5rem;
    }

    .suggestion-description {
        font-size: clamp(0.8rem, 2.5vw, 0.9rem);
        color: #6c757d;
        margin-bottom: 1rem;
        line-height: 1.5;
    }

    .suggestion-link {
        color: #000000;
        text-decoration: none;
        font-weight: 600;
        font-size: clamp(0.8rem, 2.5vw, 0.9rem);
        transition: all 0.3s ease;
    }

    .suggestion-link:hover {
        color: #000000;
        text-decoration: underline;
    }

    /* Floating Elements */
    .floating-element {
        position: absolute;
        opacity: 0.1;
        animation: float 6s ease-in-out infinite;
        display: none;
    }

    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-20px); }
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
        flex-wrap: wrap;
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

    /* Media Queries para Responsividade */
    @media (min-width: 992px) {
        .floating-element {
            display: block;
        }
        
        .floating-element:nth-child(1) {
            top: 20%;
            left: 10%;
            animation-delay: 0s;
        }

        .floating-element:nth-child(2) {
            top: 60%;
            right: 15%;
            animation-delay: 2s;
        }

        .floating-element:nth-child(3) {
            bottom: 30%;
            left: 20%;
            animation-delay: 4s;
        }
    }

    @media (max-width: 991px) {
        .error-section {
            padding: 2rem 0;
        }
        
        .suggestions {
            margin-top: 1.5rem;
        }
    }

    @media (max-width: 768px) {
        .navbar-brand {
            font-size: 1.3rem;
        }
        
        .error-section {
            min-height: 70vh;
            padding: 1.5rem 0;
        }

        .error-actions {
            flex-direction: column;
            align-items: center;
            gap: 0.75rem;
        }

        .error-actions .btn {
            width: 100%;
            max-width: 300px;
        }

        .suggestion-card {
            padding: 1.25rem;
        }

        .suggestion-icon {
            width: 45px;
        height: 45px;
            font-size: 1.1rem;
        }
    }

    @media (max-width: 576px) {
        .container {
            padding-left: 1rem;
            padding-right: 1rem;
        }

        .error-section {
            padding: 1rem 0;
            min-height: 60vh;
        }

        .error-content {
            padding: 0 0.5rem;
        }

        .search-form {
            border-radius: 12px;
            flex-direction: column;
            gap: 0;
            max-width: 100%;
        }

        .search-input {
            border-bottom: 1px solid #e9ecef;
            border-radius: 12px 12px 0 0;
            padding: 1rem;
        }

        .search-btn {
            border-radius: 0 0 12px 12px;
            padding: 1rem;
        }

        .suggestions-title {
            margin-bottom: 1.5rem;
        }

        .suggestion-card {
            padding: 1rem;
            margin-bottom: 1rem;
        }

        .footer-bottom-links {
            flex-direction: column;
            align-items: center;
            gap: 0.75rem;
        }

        .footer-section h5 {
            font-size: 1rem;
        }

        .footer-description {
            font-size: 0.9rem;
        }
    }

    @media (max-width: 400px) {
        .navbar-brand {
            font-size: 1.2rem;
        }

        .error-subtitle {
            padding: 0 0.5rem;
        }

        .suggestion-card {
            padding: 0.75rem;
        }

        .suggestion-icon {
            width: 40px;
            height: 40px;
            font-size: 1rem;
        }
    }
</style>
<!-- 404 Error Section -->
<section class="error-section">
    <!-- Floating Elements (apenas em desktop) -->
    <div class="floating-element">
        <i class="bi bi-briefcase" style="font-size: 3rem; color: #000000;"></i>
    </div>
    <div class="floating-element">
        <i class="bi bi-search" style="font-size: 2.5rem; color: #000000;"></i>
    </div>
    <div class="floating-element">
        <i class="bi bi-file-earmark-text" style="font-size: 2rem; color: #000000;"></i>
    </div>

    <div class="container">
        <div class="error-content">
            <div class="error-number">404</div>
            <h1 class="error-title">Oops! Página Não Encontrada</h1>
            <p class="error-subtitle">
                A página que você está procurando pode ter sido removida, teve seu nome alterado ou está temporariamente indisponível.
            </p>

            <!-- Search Form -->
            <div class="row justify-content-center">
                <div class="col-12 col-md-8 col-lg-6">
                    <form class="search-form" action="{{ route('search') }}" method="GET">
                        <input 
                            type="text" 
                            class="search-input" 
                            placeholder="Procurar vagas, empresas..."
                            aria-label="Search" aria-describedby="search-addon" name="query"
                        >
                        <button type="submit" class="search-btn">
                            <i class="bi bi-search"></i>
                            <span class="d-none d-sm-inline ms-1">Buscar</span>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="error-actions">
                <a href="{{ url('/') }}" class="btn btn-dark">
                    <i class="bi bi-house me-2"></i>
                    Voltar ao Início
                </a>
            </div>

            <!-- Suggestions -->
            <div class="suggestions">
                <h3 class="suggestions-title">Talvez você esteja procurando por:</h3>
                <div class="row g-3">
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="suggestion-card">
                            <div class="suggestion-icon">
                                <i class="bi bi-briefcase"></i>
                            </div>
                            <h4 class="suggestion-title">Vagas de Emprego</h4>
                            <p class="suggestion-description">
                                Encontre as melhores oportunidades de trabalho em Angola
                            </p>
                            <a href="{{ url('/empregos') }}" class="suggestion-link">
                                Ver Vagas <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>

                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="suggestion-card">
                            <div class="suggestion-icon">
                                <i class="bi bi-send"></i>
                            </div>
                            <h4 class="suggestion-title">Candidaturas Automáticas</h4>
                            <p class="suggestion-description">
                                Deixe que façamos as candidaturas por você
                            </p>
                            <a href="#" class="suggestion-link">
                                Ver Planos <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>

                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="suggestion-card">
                            <div class="suggestion-icon">
                                <i class="bi bi-file-earmark-text"></i>
                            </div>
                            <h4 class="suggestion-title">Modelos de CV</h4>
                            <p class="suggestion-description">
                                Crie um currículo profissional e moderno
                            </p>
                            <a href="{{ url('articles/varios-modelos-de-curriculos-no-word-para-baixar-de-graca') }}" class="suggestion-link">
                                Ver Modelos <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection('content')
