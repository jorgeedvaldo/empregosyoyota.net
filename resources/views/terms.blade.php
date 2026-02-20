@extends('template.app')
@section('title', 'Termos e Condições')
@section('description', 'É uma plataforma que reúne oportunidades de emprego no solo angolano, tendo como fonte principal o "Jornal de Angola", criada aos 5 de Dezembro de 2018, a Empregos Yoyota tem ajudado muita gente a encontrar empregos no solo angolano')
@section('canonical_link', url('/terms'))
@section('content')
<style>
    * {
        font-family: 'Montserrat', sans-serif;
    }
    
    body {
        background: #ffffff;
        color: #000000;
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
    
    /* Hero Section */
    .hero-section {
        background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('{{ asset('storage/images/bg_2.jpg') }}');
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
        color: #ffffff;
        padding: 120px 0;
        text-align: center;
        position: relative;
    }

    .hero-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.4);
        z-index: 1;
    }

    .hero-content {
        position: relative;
        z-index: 2;
    }

    .hero-title {
        font-size: clamp(2.5rem, 8vw, 4.5rem);
        font-weight: 900;
        line-height: 1.2;
        margin-bottom: 2rem;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
        color: #ffffff !important;
    }

    .hero-subtitle {
        font-size: clamp(1.1rem, 3vw, 1.4rem);
        font-weight: 400;
        opacity: 0.9;
        max-width: 600px;
        margin: 0 auto;
        text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
        color: #ffffff !important;
    }

    /* Content Section */
    .content-section {
        padding: 80px 0;
        background: #ffffff;
    }

    .content-container {
        max-width: 800px;
        margin: 0 auto;
    }

    .section-title {
        font-size: clamp(1.8rem, 4vw, 2.5rem);
        font-weight: 900;
        color: #000000;
        margin-bottom: 2rem;
        position: relative;
        padding-bottom: 1rem;
    }

    .section-title::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 60px;
        height: 4px;
        background: #000000;
        border-radius: 2px;
    }

    .section-content {
        font-size: 1.1rem;
        line-height: 1.8;
        color: #333333;
        margin-bottom: 3rem;
    }

    .subsection-title {
        font-size: clamp(1.3rem, 3vw, 1.6rem);
        font-weight: 700;
        color: #000000;
        margin: 3rem 0 1.5rem 0;
        position: relative;
        padding-left: 1rem;
    }

    .subsection-title::before {
        content: '';
        position: absolute;
        left: 0;
        top: 50%;
        transform: translateY(-50%);
        width: 4px;
        height: 100%;
        background: #000000;
        border-radius: 2px;
    }

    .subsection-content {
        font-size: 1rem;
        line-height: 1.7;
        color: #555555;
        margin-bottom: 2rem;
    }

    .subsection-content p {
        margin-bottom: 1.5rem;
    }

    .subsection-content ul {
        padding-left: 2rem;
        margin-bottom: 1.5rem;
    }

    .subsection-content li {
        margin-bottom: 0.75rem;
        position: relative;
    }

    .subsection-content li::marker {
        color: #000000;
        font-weight: 700;
    }

    /* Highlight Box */
    .highlight-box {
        background: #f8f9fa;
        border: 1px solid #e9ecef;
        border-left: 4px solid #000000;
        padding: 2rem;
        margin: 2rem 0;
        border-radius: 0 8px 8px 0;
    }

    .highlight-box h4 {
        font-size: 1.2rem;
        font-weight: 700;
        color: #000000;
        margin-bottom: 1rem;
    }

    .highlight-box p {
        margin-bottom: 0;
        color: #555555;
        line-height: 1.6;
    }

    /* Contact Info */
    .contact-info {
        background: #000000;
        color: #ffffff;
        padding: 3rem 0;
        text-align: center;
    }

    .contact-title {
        font-size: 1.8rem;
        font-weight: 700;
        margin-bottom: 1rem;
    }

    .contact-text {
        font-size: 1.1rem;
        margin-bottom: 2rem;
        opacity: 0.9;
    }

    .contact-email {
        font-size: 1.2rem;
        font-weight: 600;
        color: #ffffff;
        text-decoration: none;
        border: 2px solid #ffffff;
        padding: 0.75rem 2rem;
        border-radius: 25px;
        display: inline-block;
        transition: all 0.3s ease;
    }

    .contact-email:hover {
        background: #ffffff;
        color: #000000;
        transform: translateY(-2px);
    }

    /* Last Updated */
    .last-updated {
        background: #f8f9fa;
        padding: 2rem 0;
        text-align: center;
        border-top: 1px solid #e9ecef;
    }

    .last-updated p {
        margin: 0;
        color: #6c757d;
        font-size: 0.9rem;
        font-weight: 500;
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

    .footer-bottom-links a.active {
        color: #ffffff;
        font-weight: 600;
    }

    .copyright {
        text-align: center;
        color: #6c757d;
        font-size: 0.9rem;
        margin: 0;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .hero-section {
            padding: 80px 0;
            background-attachment: scroll;
        }

        .content-section {
            padding: 40px 0;
        }

        .subsection-title {
            padding-left: 0.75rem;
        }

        .subsection-title::before {
            width: 3px;
        }

        .highlight-box {
            padding: 1.5rem;
            margin: 1.5rem 0;
        }

        .contact-info {
            padding: 2rem 0;
        }

        .footer-bottom-links {
            flex-direction: column;
            align-items: center;
            gap: 1rem;
        }
    }

    @media (max-width: 576px) {
        .hero-section {
            padding: 60px 0;
        }

        .content-section {
            padding: 30px 0;
        }

        .section-title::after {
            width: 40px;
            height: 3px;
        }

        .subsection-content ul {
            padding-left: 1.5rem;
        }

        .highlight-box {
            padding: 1rem;
        }

        .contact-email {
            font-size: 1rem;
            padding: 0.6rem 1.5rem;
        }
    }
</style>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="hero-content">
            <h1 class="hero-title">
                Termos, Condições e<br>
                Políticas de Uso
            </h1>
            <p class="hero-subtitle">
                Transparência e confiança são fundamentais para nossa relação com você
            </p>
        </div>
    </div>
</section>

<!-- Content Section -->
<section class="content-section">
    <div class="container">
        <div class="content-container">
            
            <!-- Introdução -->
            <h2 class="section-title">Termos e Condições de Uso</h2>
            <div class="section-content">
                <p>
                    Bem-vindo ao Empregos Yoyota. Este site é uma plataforma que partilha oportunidades de emprego encontradas em fontes confiáveis. Nós não somos responsáveis pelo recrutamento ou seleção de candidatos para as oportunidades apresentadas. Nós apenas partilhamos informações que consideramos úteis e relevantes para ajudar as pessoas a encontrar emprego.
                </p>
                <p>
                    Ao utilizar nossos serviços, você concorda com estes termos e condições. Por favor, leia-os cuidadosamente antes de usar nossa plataforma.
                </p>
            </div>

            <!-- Conteúdo Disponível -->
            <h3 class="subsection-title">Conteúdo Disponível</h3>
            <div class="subsection-content">
                <p>
                    Você está autorizado a usar o conteúdo apresentado neste site, desde que creditado corretamente ao nosso site. Por favor, mencione a fonte quando compartilhar informações obtidas em nosso site. Nós apreciamos sua consideração e colaboração em manter a integridade do nosso conteúdo.
                </p>
                
                <div class="highlight-box">
                    <h4><i class="bi bi-info-circle me-2"></i>Importante</h4>
                    <p>
                        Todas as informações sobre vagas de emprego são coletadas de fontes públicas e confiáveis. Recomendamos sempre verificar a autenticidade das oportunidades diretamente com as empresas anunciantes.
                    </p>
                </div>

                <p>Os usuários podem:</p>
                <ul>
                    <li>Navegar e pesquisar vagas de emprego gratuitamente</li>
                    <li>Compartilhar conteúdo desde que creditado adequadamente</li>
                    <li>Utilizar nossos serviços premium mediante assinatura</li>
                    <li>Entrar em contato conosco para esclarecimentos</li>
                </ul>
            </div>

            <!-- Política de Privacidade -->
            <h3 class="subsection-title">Política de Privacidade</h3>
            <div class="subsection-content">
                <p>
                    Nós valorizamos a privacidade de nossos usuários. Nós coletamos informações básicas apenas para fins de melhoria do site e entrega de conteúdo personalizado. Nós não compartilhamos as informações pessoais dos usuários com terceiros, exceto em casos em que seja exigido por lei.
                </p>

                <h4 style="font-weight: 600; margin: 2rem 0 1rem 0;">Informações que Coletamos:</h4>
                <ul>
                    <li>Dados de navegação (páginas visitadas, tempo de permanência)</li>
                    <li>Informações fornecidas voluntariamente (nome, email, CV)</li>
                    <li>Dados técnicos (endereço IP, tipo de navegador)</li>
                    <li>Preferências de busca e interações com o site</li>
                </ul>

                <h4 style="font-weight: 600; margin: 2rem 0 1rem 0;">Como Utilizamos suas Informações:</h4>
                <ul>
                    <li>Melhorar a experiência do usuário em nossa plataforma</li>
                    <li>Personalizar conteúdo e recomendações de vagas</li>
                    <li>Enviar notificações sobre novas oportunidades (com seu consentimento)</li>
                    <li>Analisar tendências de uso para aprimorar nossos serviços</li>
                </ul>
            </div>

            <!-- Uso de Cookies -->
            <h3 class="subsection-title">Uso de Cookies</h3>
            <div class="subsection-content">
                <p>
                    Este site usa cookies para melhorar a experiência do usuário e entregar conteúdo personalizado. Cookies são pequenos arquivos de texto que são armazenados no seu dispositivo quando você visita um site.
                </p>

                <h4 style="font-weight: 600; margin: 2rem 0 1rem 0;">Tipos de Cookies que Utilizamos:</h4>
                <ul>
                    <li><strong>Cookies Essenciais:</strong> Necessários para o funcionamento básico do site</li>
                    <li><strong>Cookies de Performance:</strong> Ajudam-nos a entender como os visitantes interagem com o site</li>
                    <li><strong>Cookies de Funcionalidade:</strong> Permitem que o site lembre suas preferências</li>
                    <li><strong>Cookies de Marketing:</strong> Utilizados para entregar anúncios relevantes</li>
                </ul>

                <p>
                    Você pode controlar e/ou deletar cookies conforme desejar. Você pode deletar todos os cookies que já estão no seu computador e pode configurar a maioria dos navegadores para impedir que sejam colocados.
                </p>
            </div>

            <!-- Serviços Premium -->
            <h3 class="subsection-title">Serviços Premium</h3>
            <div class="subsection-content">
                <p>
                    Oferecemos serviços premium, incluindo candidaturas automáticas, criação de CV profissional e consultoria de carreira. Estes serviços são cobrados conforme os planos apresentados em nossa plataforma.
                </p>

                <h4 style="font-weight: 600; margin: 2rem 0 1rem 0;">Condições dos Serviços Premium:</h4>
                <ul>
                    <li>Pagamento antecipado conforme plano escolhido</li>
                    <li>Cancelamento possível a qualquer momento</li>
                    <li>Reembolso conforme política específica de cada serviço</li>
                    <li>Suporte prioritário para assinantes</li>
                </ul>

                <div class="highlight-box">
                    <h4><i class="bi bi-shield-check me-2"></i>Garantia de Qualidade</h4>
                    <p>
                        Comprometemo-nos a fornecer serviços de alta qualidade. Caso não esteja satisfeito, entre em contato conosco dentro de 7 dias para resolvermos a situação.
                    </p>
                </div>
            </div>

            <!-- Responsabilidades -->
            <h3 class="subsection-title">Responsabilidades e Limitações</h3>
            <div class="subsection-content">
                <p>
                    O Empregos Yoyota atua como intermediário entre candidatos e oportunidades de emprego. Não garantimos a contratação nem somos responsáveis pelos processos seletivos das empresas anunciantes.
                </p>

                <h4 style="font-weight: 600; margin: 2rem 0 1rem 0;">Limitações de Responsabilidade:</h4>
                <ul>
                    <li>Não somos responsáveis por decisões de contratação das empresas</li>
                    <li>Não garantimos a veracidade de todas as informações de terceiros</li>
                    <li>Não nos responsabilizamos por danos indiretos ou consequenciais</li>
                    <li>Nossa responsabilidade é limitada ao valor pago pelos serviços premium</li>
                </ul>
            </div>

            <!-- Modificações -->
            <h3 class="subsection-title">Modificações dos Termos</h3>
            <div class="subsection-content">
                <p>
                    Reservamo-nos o direito de modificar estes termos e condições a qualquer momento. As alterações entrarão em vigor imediatamente após a publicação no site. Recomendamos que revise periodicamente esta página para se manter informado sobre eventuais mudanças.
                </p>
                <p>
                    O uso continuado de nossos serviços após as modificações constitui aceitação dos novos termos.
                </p>
            </div>

            <!-- Lei Aplicável -->
            <h3 class="subsection-title">Lei Aplicável</h3>
            <div class="subsection-content">
                <p>
                    Estes termos e condições são regidos pelas leis da República de Angola. Qualquer disputa relacionada ao uso deste site será resolvida nos tribunais competentes de Angola.
                </p>
            </div>

        </div>
    </div>
</section>

<!-- Contact Info -->
<section class="contact-info">
    <div class="container">
        <h3 class="contact-title">Dúvidas sobre nossos Termos?</h3>
        <p class="contact-text">
            Entre em contato conosco para esclarecimentos sobre nossos termos e políticas
        </p>
        <a href="mailto:geral@empregosyoyota.net" class="contact-email">
            <i class="bi bi-envelope me-2"></i>
            geral@empregosyoyota.net
        </a>
    </div>
</section>

<!-- Last Updated -->
<section class="last-updated">
    <div class="container">
        <p>
            <i class="bi bi-calendar-event me-2"></i>
            Última atualização: 30 de Janeiro de 2024
        </p>
    </div>
</section>
@endsection('content')
