@extends('template.app')
@section('title', 'Biblioteca de Documentos Legais')
@section('description', 'Modelos de contratos, cartas e documentos laborais para candidatos e empresas em Angola, Brasil, Moçambique e Portugal. Consulte e peça o modelo que precisa, gratuitamente.')
@section('canonical_link', url('/biblioteca-de-documentos-legais'))
@section('content')
<style>
    .docs-hero {
        background: #ffffff;
        padding: 80px 0 40px;
    }

    .docs-hero-title {
        font-size: clamp(2.2rem, 6vw, 3.5rem);
        font-weight: 900;
        color: #000000;
        margin-bottom: 1.5rem;
    }

    .docs-hero-subtitle {
        font-size: 1.2rem;
        color: #6c757d;
        max-width: 700px;
        margin: 0 auto;
        line-height: 1.6;
    }

    .docs-disclaimer {
        background: #f8f9fa;
        border: 1px solid #e9ecef;
        border-left: 4px solid #000000;
        border-radius: 0 8px 8px 0;
        padding: 1.5rem 2rem;
        max-width: 800px;
        margin: 2.5rem auto 0;
        color: #555555;
        font-size: 0.95rem;
        line-height: 1.6;
    }

    .docs-section {
        padding: 60px 0;
    }

    .docs-section.alt {
        background: #f8f9fa;
    }

    .docs-section-title {
        font-size: 2rem;
        font-weight: 900;
        color: #000000;
        text-align: center;
        margin-bottom: 0.75rem;
    }

    .docs-section-subtitle {
        text-align: center;
        color: #6c757d;
        max-width: 600px;
        margin: 0 auto 3rem;
    }

    .doc-card {
        background: #ffffff;
        border: 1px solid #e9ecef;
        border-radius: 16px;
        padding: 2rem;
        height: 100%;
        box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        transition: all 0.3s ease;
    }

    .doc-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.1);
    }

    .doc-icon {
        width: 52px;
        height: 52px;
        background: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.3rem;
        color: #000000;
        margin-bottom: 1.25rem;
    }

    .doc-title {
        font-size: 1.15rem;
        font-weight: 700;
        color: #000000;
        margin-bottom: 0.6rem;
    }

    .doc-description {
        color: #6c757d;
        font-size: 0.95rem;
        line-height: 1.6;
        margin-bottom: 1.25rem;
    }

    .doc-request {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        font-size: 0.9rem;
        font-weight: 600;
        color: #000000;
        text-decoration: none;
    }

    .doc-request:hover {
        text-decoration: underline;
    }

    .docs-cta {
        background: #000000;
        color: #ffffff;
        padding: 4rem 0;
        text-align: center;
    }

    .docs-cta h2 {
        font-size: 1.8rem;
        font-weight: 900;
        margin-bottom: 1rem;
    }

    .docs-cta p {
        color: #e9ecef;
        margin-bottom: 2rem;
    }

    .docs-cta a {
        color: #ffffff;
        border: 2px solid #ffffff;
        padding: 0.75rem 2rem;
        border-radius: 25px;
        text-decoration: none;
        font-weight: 600;
        display: inline-block;
        transition: all 0.3s ease;
    }

    .docs-cta a:hover {
        background: #ffffff;
        color: #000000;
    }
</style>

@php
    $mailBase = 'geral@empregosyoyota.net';
    $mailto = fn ($doc) => "mailto:{$mailBase}?subject=" . rawurlencode("Pedido de modelo: {$doc}");

    $candidatoDocs = [
        ['icon' => 'bi-file-earmark-text', 'title' => 'Carta de Demissão', 'desc' => 'Modelo para o trabalhador comunicar formalmente à empresa o pedido de demissão.'],
        ['icon' => 'bi-file-earmark-text', 'title' => 'Aviso Prévio do Trabalhador', 'desc' => 'Documento que formaliza o período de aviso prévio antes da saída da empresa.'],
        ['icon' => 'bi-file-earmark-text', 'title' => 'Carta de Referência Profissional', 'desc' => 'Modelo para pedir ou redigir uma referência profissional junto de um antigo empregador.'],
        ['icon' => 'bi-file-earmark-text', 'title' => 'Reclamação Laboral', 'desc' => 'Modelo base para apresentar uma reclamação formal relacionada com direitos laborais.'],
        ['icon' => 'bi-file-earmark-text', 'title' => 'Requerimento de Férias', 'desc' => 'Modelo simples para o trabalhador solicitar formalmente o gozo de férias.'],
        ['icon' => 'bi-file-earmark-text', 'title' => 'Procuração para Fins Laborais', 'desc' => 'Documento que autoriza terceiros a tratar de assuntos laborais em nome do trabalhador.'],
    ];

    $empresaDocs = [
        ['icon' => 'bi-file-earmark-text', 'title' => 'Regulamento Interno da Empresa', 'desc' => 'Modelo de regulamento com normas de conduta, horários e regras internas da empresa.'],
        ['icon' => 'bi-file-earmark-text', 'title' => 'Carta de Advertência Disciplinar', 'desc' => 'Modelo para notificar formalmente um colaborador sobre uma falta disciplinar.'],
        ['icon' => 'bi-file-earmark-text', 'title' => 'Notificação de Rescisão de Contrato', 'desc' => 'Modelo para a empresa comunicar formalmente o términus do contrato de trabalho.'],
        ['icon' => 'bi-file-earmark-text', 'title' => 'Ficha de Avaliação de Desempenho', 'desc' => 'Modelo para avaliar periodicamente o desempenho dos colaboradores.'],
        ['icon' => 'bi-file-earmark-text', 'title' => 'Política de Uso de Equipamentos e Internet', 'desc' => 'Modelo de política interna sobre o uso de equipamentos, internet e redes sociais.'],
        ['icon' => 'bi-file-earmark-text', 'title' => 'Ficha de Registo de Ponto e Assiduidade', 'desc' => 'Modelo para controlo de horários de entrada, saída e assiduidade dos colaboradores.'],
    ];

    $geraisDocs = [
        ['icon' => 'bi-file-earmark-text', 'title' => 'Contrato de Trabalho a Termo Certo', 'desc' => 'Modelo de contrato de trabalho com duração determinada, para empresa e trabalhador.'],
        ['icon' => 'bi-file-earmark-text', 'title' => 'Contrato de Trabalho por Tempo Indeterminado', 'desc' => 'Modelo de contrato de trabalho sem prazo definido de duração.'],
        ['icon' => 'bi-file-earmark-text', 'title' => 'Acordo de Rescisão por Mútuo Acordo', 'desc' => 'Modelo de acordo para términus do contrato por consenso entre as partes.'],
        ['icon' => 'bi-file-earmark-text', 'title' => 'Declaração de Vínculo Empregatício', 'desc' => 'Modelo de declaração que comprova a relação de trabalho entre empresa e colaborador.'],
        ['icon' => 'bi-file-earmark-text', 'title' => 'Termo de Confidencialidade (NDA)', 'desc' => 'Modelo de acordo de confidencialidade para proteger informações sensíveis de ambas as partes.'],
        ['icon' => 'bi-file-earmark-text', 'title' => 'Recibo de Vencimento / Folha de Pagamento', 'desc' => 'Modelo de recibo para comprovar o pagamento do salário mensal ao trabalhador.'],
    ];
@endphp

<!-- Hero -->
<section class="docs-hero">
    <div class="container text-center">
        <h1 class="docs-hero-title">Biblioteca de Documentos Legais</h1>
        <p class="docs-hero-subtitle mx-auto">
            Modelos de contratos, cartas e documentos laborais para ajudar candidatos e empresas em
            Angola, Brasil, Moçambique e Portugal.
        </p>
        <div class="docs-disclaimer">
            <i class="bi bi-info-circle me-2"></i>
            Estes modelos são um ponto de partida e têm carácter meramente informativo — não substituem
            aconselhamento jurídico. As leis laborais variam entre Angola, Brasil, Moçambique e Portugal,
            por isso recomendamos validar cada documento com a legislação local ou com um advogado antes
            de o utilizar.
        </div>
    </div>
</section>

<!-- Documentos para Candidatos -->
<section class="docs-section">
    <div class="container">
        <h2 class="docs-section-title">Documentos para Candidatos</h2>
        <p class="docs-section-subtitle">Modelos pensados para quem procura emprego ou já está empregado.</p>
        <div class="row g-4">
            @foreach ($candidatoDocs as $doc)
            <div class="col-lg-4 col-md-6">
                <div class="doc-card">
                    <div class="doc-icon"><i class="bi {{ $doc['icon'] }}"></i></div>
                    <h3 class="doc-title">{{ $doc['title'] }}</h3>
                    <p class="doc-description">{{ $doc['desc'] }}</p>
                    <a href="{{ $mailto($doc['title']) }}" class="doc-request">
                        <i class="bi bi-envelope"></i> Pedir por e-mail
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Documentos para Empresas -->
<section class="docs-section alt">
    <div class="container">
        <h2 class="docs-section-title">Documentos para Empresas</h2>
        <p class="docs-section-subtitle">Modelos pensados para empregadores e equipas de recursos humanos.</p>
        <div class="row g-4">
            @foreach ($empresaDocs as $doc)
            <div class="col-lg-4 col-md-6">
                <div class="doc-card">
                    <div class="doc-icon"><i class="bi {{ $doc['icon'] }}"></i></div>
                    <h3 class="doc-title">{{ $doc['title'] }}</h3>
                    <p class="doc-description">{{ $doc['desc'] }}</p>
                    <a href="{{ $mailto($doc['title']) }}" class="doc-request">
                        <i class="bi bi-envelope"></i> Pedir por e-mail
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Documentos Gerais -->
<section class="docs-section">
    <div class="container">
        <h2 class="docs-section-title">Documentos Gerais</h2>
        <p class="docs-section-subtitle">Modelos usados tanto por candidatos/trabalhadores como por empresas.</p>
        <div class="row g-4">
            @foreach ($geraisDocs as $doc)
            <div class="col-lg-4 col-md-6">
                <div class="doc-card">
                    <div class="doc-icon"><i class="bi {{ $doc['icon'] }}"></i></div>
                    <h3 class="doc-title">{{ $doc['title'] }}</h3>
                    <p class="doc-description">{{ $doc['desc'] }}</p>
                    <a href="{{ $mailto($doc['title']) }}" class="doc-request">
                        <i class="bi bi-envelope"></i> Pedir por e-mail
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- CTA -->
<section class="docs-cta">
    <div class="container">
        <h2>Não encontrou o modelo que procura?</h2>
        <p>Fale connosco e ajudamos a encontrar ou adaptar o documento certo para si ou para a sua empresa.</p>
        <a href="mailto:geral@empregosyoyota.net">
            <i class="bi bi-envelope me-2"></i>geral@empregosyoyota.net
        </a>
    </div>
</section>
@endsection('content')
