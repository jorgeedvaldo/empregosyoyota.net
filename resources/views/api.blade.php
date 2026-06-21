@extends('template.app')

@section('title', 'Documentação da API')
@section('description', 'Documentação pública da API da Empregos Yoyota: consulte vagas de emprego em Angola via endpoints REST com exemplos de requisições GET em cURL, JavaScript, PHP e Python.')
@section('canonical_link', url('/api-docs'))

@section('head-scripts')
<style>
    .api-doc-hero {
        background: linear-gradient(135deg, #1a1a1a 0%, #343a40 100%);
        color: #fff;
        padding: 60px 0;
    }
    .api-doc-hero h1 { font-weight: 700; }
    .api-doc pre {
        background: #1e1e1e;
        color: #e6e6e6;
        padding: 18px 20px;
        border-radius: 10px;
        overflow-x: auto;
        font-size: 0.875rem;
        line-height: 1.5;
    }
    .api-doc pre code { color: inherit; background: none; padding: 0; }
    .api-doc code:not(pre code) {
        background: #f1f3f5;
        color: #c7254e;
        padding: 2px 6px;
        border-radius: 4px;
        font-size: 0.875em;
    }
    .api-doc .endpoint-badge {
        font-family: monospace;
        font-weight: 700;
        font-size: 0.8rem;
        letter-spacing: 0.5px;
    }
    .api-doc .endpoint-url {
        font-family: monospace;
        font-size: 0.95rem;
        word-break: break-all;
    }
    .api-doc h2 { font-weight: 700; margin-top: 2.5rem; margin-bottom: 1rem; }
    .api-doc .table code { white-space: nowrap; }
    .api-doc .toc a { text-decoration: none; }
</style>
@endsection

@section('content')
<section class="api-doc-hero text-center">
    <div class="container">
        <h1 class="mb-3">
            <svg xmlns="http://www.w3.org/2000/svg" width="34" height="34" fill="currentColor" viewBox="0 0 16 16" class="me-2" style="vertical-align: -5px;" aria-hidden="true">
                <path d="M5.854 4.854a.5.5 0 1 0-.708-.708l-3.5 3.5a.5.5 0 0 0 0 .708l3.5 3.5a.5.5 0 0 0 .708-.708L2.707 8l3.147-3.146zm4.292 0a.5.5 0 0 1 .708-.708l3.5 3.5a.5.5 0 0 1 0 .708l-3.5 3.5a.5.5 0 0 1-.708-.708L13.293 8l-3.147-3.146z"/>
            </svg>
            API Empregos Yoyota
        </h1>
        <p class="lead mb-0" style="color: rgba(255,255,255,0.85); max-width: 720px; margin: 0 auto;">
            Aceda às vagas de emprego publicadas na Empregos Yoyota de forma gratuita,
            através de uma API REST simples que devolve JSON. Ideal para integrar vagas
            no seu site, aplicativo ou bot.
        </p>
    </div>
</section>

<div class="api-doc container my-5">
    <div class="row">
        <div class="col-lg-9">

            <!-- Visão geral -->
            <h2 id="visao-geral">Visão geral</h2>
            <p>
                A API é pública e <strong>somente leitura</strong> para os endpoints abaixo —
                não é necessária autenticação nem chave de acesso para consultar vagas.
                Todas as respostas são devolvidas em formato <code>application/json</code>
                e seguem sempre a mesma estrutura (envelope):
            </p>
<pre><code>{
  "message": "Operation performed successfully.",
  "data": { ... },
  "license": "This API was developed by Edivaldo Jorge (https://github.com/jorgeedvaldo)"
}</code></pre>

            <ul>
                <li><code>message</code> — estado da operação.</li>
                <li><code>data</code> — o conteúdo pedido (objeto paginado na listagem; array no detalhe).</li>
                <li><code>license</code> — informação de autoria. Agradecemos que mantenha a atribuição ao reutilizar os dados.</li>
            </ul>

            <p class="mt-3">
                <strong>URL base:</strong>
                <span class="endpoint-url">{{ url('/api') }}</span>
            </p>

            <!-- Endpoint 1 -->
            <h2 id="listar-vagas">1. Listar vagas</h2>
            <p>
                <span class="badge bg-success endpoint-badge">GET</span>
                <span class="endpoint-url">{{ url('/api/jobs') }}</span>
            </p>
            <p>
                Devolve as vagas de emprego de <strong>Angola</strong>, ordenadas da mais
                recente para a mais antiga, <strong>paginadas em 50 por página</strong>.
            </p>

            <h5 class="mt-4">Parâmetros de query</h5>
            <table class="table table-bordered table-sm">
                <thead class="table-light">
                    <tr><th>Parâmetro</th><th>Tipo</th><th>Obrigatório</th><th>Descrição</th></tr>
                </thead>
                <tbody>
                    <tr>
                        <td><code>page</code></td>
                        <td>inteiro</td>
                        <td>Não</td>
                        <td>Número da página a obter. Por omissão é <code>1</code>. Ex.: <code>?page=2</code>.</td>
                    </tr>
                </tbody>
            </table>

            <h5 class="mt-4">Exemplo de requisição</h5>
<pre><code>curl -s "https://empregosyoyota.net/api/jobs?page=1" \
  -H "Accept: application/json"</code></pre>

            <h5 class="mt-4">Exemplo de resposta</h5>
<pre><code>{
  "message": "Operation performed successfully.",
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1234,
        "title": "Técnico de Informática",
        "slug": "tecnico-de-informatica",
        "company": "Empresa Exemplo, Lda",
        "province": "Luanda",
        "description": "&lt;p&gt;Descrição completa da vaga...&lt;/p&gt;",
        "email_or_link": "rh@empresa.co.ao",
        "photo": "job.jpg",
        "country_id": 1,
        "created_at": "2026-06-20T10:15:00.000000Z",
        "updated_at": "2026-06-20T10:15:00.000000Z",
        "categories": [
          {
            "id": 3,
            "name": "Tecnologia",
            "pivot": { "job_id": 1234, "category_id": 3 }
          }
        ]
      }
    ],
    "first_page_url": "https://empregosyoyota.net/api/jobs?page=1",
    "from": 1,
    "last_page": 12,
    "next_page_url": "https://empregosyoyota.net/api/jobs?page=2",
    "path": "https://empregosyoyota.net/api/jobs",
    "per_page": 50,
    "prev_page_url": null,
    "to": 50,
    "total": 587
  },
  "license": "This API was developed by Edivaldo Jorge (https://github.com/jorgeedvaldo)"
}</code></pre>
            <p class="text-muted small">
                Nota: o objeto <code>data</code> é um paginador padrão do Laravel. Use os campos
                <code>next_page_url</code>, <code>last_page</code> e <code>total</code> para
                percorrer todas as páginas.
            </p>

            <!-- Endpoint 2 -->
            <h2 id="obter-vaga">2. Obter uma vaga por ID</h2>
            <p>
                <span class="badge bg-success endpoint-badge">GET</span>
                <span class="endpoint-url">{{ url('/api/jobs/{id}') }}</span>
            </p>
            <p>
                Devolve os dados de uma única vaga, incluindo as suas categorias.
                Substitua <code>{id}</code> pelo identificador numérico da vaga
                (campo <code>id</code> devolvido na listagem).
            </p>

            <h5 class="mt-4">Parâmetros de caminho</h5>
            <table class="table table-bordered table-sm">
                <thead class="table-light">
                    <tr><th>Parâmetro</th><th>Tipo</th><th>Obrigatório</th><th>Descrição</th></tr>
                </thead>
                <tbody>
                    <tr>
                        <td><code>id</code></td>
                        <td>inteiro</td>
                        <td>Sim</td>
                        <td>ID da vaga a consultar.</td>
                    </tr>
                </tbody>
            </table>

            <h5 class="mt-4">Exemplo de requisição</h5>
<pre><code>curl -s "https://empregosyoyota.net/api/jobs/1234" \
  -H "Accept: application/json"</code></pre>

            <h5 class="mt-4">Exemplo de resposta</h5>
<pre><code>{
  "message": "Operation performed successfully.",
  "data": [
    {
      "id": 1234,
      "title": "Técnico de Informática",
      "slug": "tecnico-de-informatica",
      "company": "Empresa Exemplo, Lda",
      "province": "Luanda",
      "description": "&lt;p&gt;Descrição completa da vaga...&lt;/p&gt;",
      "email_or_link": "rh@empresa.co.ao",
      "photo": "job.jpg",
      "country_id": 1,
      "created_at": "2026-06-20T10:15:00.000000Z",
      "updated_at": "2026-06-20T10:15:00.000000Z",
      "categories": [
        { "id": 3, "name": "Tecnologia" }
      ]
    }
  ],
  "license": "This API was developed by Edivaldo Jorge (https://github.com/jorgeedvaldo)"
}</code></pre>
            <p class="text-muted small">
                Se o ID não existir, <code>data</code> será um array vazio <code>[]</code>.
            </p>

            <!-- Campos -->
            <h2 id="campos">Campos de uma vaga</h2>
            <table class="table table-bordered table-sm">
                <thead class="table-light">
                    <tr><th>Campo</th><th>Tipo</th><th>Descrição</th></tr>
                </thead>
                <tbody>
                    <tr><td><code>id</code></td><td>inteiro</td><td>Identificador único da vaga.</td></tr>
                    <tr><td><code>title</code></td><td>texto</td><td>Título da vaga.</td></tr>
                    <tr><td><code>slug</code></td><td>texto</td><td>Identificador amigável usado nos links do site.</td></tr>
                    <tr><td><code>company</code></td><td>texto</td><td>Nome da empresa.</td></tr>
                    <tr><td><code>province</code></td><td>texto</td><td>Província / localização.</td></tr>
                    <tr><td><code>description</code></td><td>HTML</td><td>Descrição completa da vaga (pode conter marcação HTML).</td></tr>
                    <tr><td><code>email_or_link</code></td><td>texto</td><td>E-mail ou link para candidatura.</td></tr>
                    <tr><td><code>photo</code></td><td>texto</td><td>Nome do ficheiro da imagem. URL completo: <code>{{ url('/storage') }}/&lt;photo&gt;</code></td></tr>
                    <tr><td><code>country_id</code></td><td>inteiro</td><td>País da vaga (1 = Angola).</td></tr>
                    <tr><td><code>created_at</code></td><td>data ISO 8601</td><td>Data de publicação.</td></tr>
                    <tr><td><code>updated_at</code></td><td>data ISO 8601</td><td>Data da última atualização.</td></tr>
                    <tr><td><code>categories</code></td><td>array</td><td>Lista de categorias associadas à vaga.</td></tr>
                </tbody>
            </table>

            <!-- Exemplos de código -->
            <h2 id="exemplos">Exemplos de consumo</h2>

            <h5 class="mt-4"><i class="bi bi-search me-1"></i> JavaScript (fetch)</h5>
@verbatim
<pre><code>fetch('https://empregosyoyota.net/api/jobs', {
  headers: { 'Accept': 'application/json' }
})
  .then(response => response.json())
  .then(result => {
    const jobs = result.data.data; // array de vagas
    jobs.forEach(job => console.log(job.title, '-', job.company));
  })
  .catch(error => console.error(error));</code></pre>
@endverbatim

            <h5 class="mt-4"><i class="bi bi-search me-1"></i> PHP</h5>
@verbatim
<pre><code>&lt;?php
$response = file_get_contents('https://empregosyoyota.net/api/jobs');
$result = json_decode($response, true);

foreach ($result['data']['data'] as $job) {
    echo $job['title'] . ' - ' . $job['company'] . PHP_EOL;
}</code></pre>
@endverbatim

            <h5 class="mt-4"><i class="bi bi-search me-1"></i> Python (requests)</h5>
@verbatim
<pre><code>import requests

response = requests.get('https://empregosyoyota.net/api/jobs',
                        headers={'Accept': 'application/json'})
result = response.json()

for job in result['data']['data']:
    print(job['title'], '-', job['company'])</code></pre>
@endverbatim

            <h5 class="mt-4"><i class="bi bi-search me-1"></i> Obter uma vaga específica (cURL)</h5>
<pre><code>curl -s "https://empregosyoyota.net/api/jobs/1234" -H "Accept: application/json"</code></pre>

            <!-- Boas práticas -->
            <h2 id="boas-praticas">Boas práticas e termos de uso</h2>
            <ul>
                <li>Os dados são fornecidos <strong>gratuitamente</strong> para uso lícito.</li>
                <li>Mantenha a atribuição presente no campo <code>license</code> e, sempre que possível, indique a Empregos Yoyota como fonte.</li>
                <li>Faça uso responsável: evite pedidos excessivos. Recomenda-se aplicar cache do lado do cliente, já que as vagas são atualizadas periodicamente.</li>
                <li>O conteúdo de <code>description</code> pode incluir HTML — sanitize-o antes de o apresentar.</li>
            </ul>

            <div class="card border-0 shadow-sm my-4" style="border-radius: 12px;">
                <div class="card-body p-4 text-center">
                    <h5 class="fw-bold mb-2">Dúvidas ou precisa de mais endpoints?</h5>
                    <p class="text-muted mb-3">Entre em contacto e teremos todo o gosto em ajudar.</p>
                    <a href="mailto:geral@empregosyoyota.net" class="btn btn-dark">
                        <i class="bi bi-envelope me-1"></i> geral@empregosyoyota.net
                    </a>
                </div>
            </div>

        </div>

        <!-- Índice lateral -->
        <div class="col-lg-3">
            <div class="toc sticky-top" style="top: 90px;">
                <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                    <div class="card-header bg-white fw-bold">Nesta página</div>
                    <div class="list-group list-group-flush">
                        <a href="#visao-geral" class="list-group-item list-group-item-action">Visão geral</a>
                        <a href="#listar-vagas" class="list-group-item list-group-item-action">1. Listar vagas</a>
                        <a href="#obter-vaga" class="list-group-item list-group-item-action">2. Obter vaga por ID</a>
                        <a href="#campos" class="list-group-item list-group-item-action">Campos de uma vaga</a>
                        <a href="#exemplos" class="list-group-item list-group-item-action">Exemplos de consumo</a>
                        <a href="#boas-praticas" class="list-group-item list-group-item-action">Boas práticas</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
