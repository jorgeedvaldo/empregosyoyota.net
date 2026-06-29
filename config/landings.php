<?php

/*
|--------------------------------------------------------------------------
| Landings SEO de vagas (paises e cidades/estados)
|--------------------------------------------------------------------------
|
| Registo central das paginas de aterragem geradas pelo LandingController.
| Cada entrada gera automaticamente: rota, pagina e entrada no sitemap.
|
| country_id: 1 = Angola, 2 = Brasil, 3 = Mocambique
| type:       'country' ou 'city'
| province:   (apenas cidades) termo usado para filtrar as vagas pela
|             coluna "province".
|
*/

return [

    // ---- Paises ----
    'mocambique' => [
        'slug' => 'vagas-de-emprego-em-mocambique',
        'name' => 'Moçambique',
        'country_id' => 3,
        'type' => 'country',
        'explore' => '/mz/empregos',
    ],
    'brasil' => [
        'slug' => 'vagas-de-emprego-no-brasil',
        'name' => 'Brasil',
        'country_id' => 2,
        'type' => 'country',
        'explore' => '/br/empregos',
    ],

    // ---- Brasil: cidades e estados ----
    'sao-paulo' => [
        'slug' => 'vagas-de-emprego-em-sao-paulo',
        'name' => 'São Paulo', 'country_id' => 2, 'type' => 'city', 'province' => 'São Paulo', 'explore' => '/br/empregos',
    ],
    'rio-de-janeiro' => [
        'slug' => 'vagas-de-emprego-em-rio-de-janeiro',
        'name' => 'Rio de Janeiro', 'country_id' => 2, 'type' => 'city', 'province' => 'Rio de Janeiro', 'explore' => '/br/empregos',
    ],
    'manaus' => [
        'slug' => 'vagas-de-emprego-em-manaus',
        'name' => 'Manaus', 'country_id' => 2, 'type' => 'city', 'province' => 'Manaus', 'explore' => '/br/empregos',
    ],
    'santa-catarina' => [
        'slug' => 'vagas-de-emprego-em-santa-catarina',
        'name' => 'Santa Catarina', 'country_id' => 2, 'type' => 'city', 'province' => 'Santa Catarina', 'explore' => '/br/empregos',
    ],
    'belo-horizonte' => [
        'slug' => 'vagas-de-emprego-em-belo-horizonte',
        'name' => 'Belo Horizonte', 'country_id' => 2, 'type' => 'city', 'province' => 'Belo Horizonte', 'explore' => '/br/empregos',
    ],
    'brasilia' => [
        'slug' => 'vagas-de-emprego-em-brasilia',
        'name' => 'Brasília', 'country_id' => 2, 'type' => 'city', 'province' => 'Brasília', 'explore' => '/br/empregos',
    ],
    'salvador' => [
        'slug' => 'vagas-de-emprego-em-salvador',
        'name' => 'Salvador', 'country_id' => 2, 'type' => 'city', 'province' => 'Salvador', 'explore' => '/br/empregos',
    ],
    'fortaleza' => [
        'slug' => 'vagas-de-emprego-em-fortaleza',
        'name' => 'Fortaleza', 'country_id' => 2, 'type' => 'city', 'province' => 'Fortaleza', 'explore' => '/br/empregos',
    ],
    'curitiba' => [
        'slug' => 'vagas-de-emprego-em-curitiba',
        'name' => 'Curitiba', 'country_id' => 2, 'type' => 'city', 'province' => 'Curitiba', 'explore' => '/br/empregos',
    ],
    'porto-alegre' => [
        'slug' => 'vagas-de-emprego-em-porto-alegre',
        'name' => 'Porto Alegre', 'country_id' => 2, 'type' => 'city', 'province' => 'Porto Alegre', 'explore' => '/br/empregos',
    ],
    'recife' => [
        'slug' => 'vagas-de-emprego-em-recife',
        'name' => 'Recife', 'country_id' => 2, 'type' => 'city', 'province' => 'Recife', 'explore' => '/br/empregos',
    ],
    'goiania' => [
        'slug' => 'vagas-de-emprego-em-goiania',
        'name' => 'Goiânia', 'country_id' => 2, 'type' => 'city', 'province' => 'Goiânia', 'explore' => '/br/empregos',
    ],
    'belem' => [
        'slug' => 'vagas-de-emprego-em-belem',
        'name' => 'Belém', 'country_id' => 2, 'type' => 'city', 'province' => 'Belém', 'explore' => '/br/empregos',
    ],
    'campinas' => [
        'slug' => 'vagas-de-emprego-em-campinas',
        'name' => 'Campinas', 'country_id' => 2, 'type' => 'city', 'province' => 'Campinas', 'explore' => '/br/empregos',
    ],
    'florianopolis' => [
        'slug' => 'vagas-de-emprego-em-florianopolis',
        'name' => 'Florianópolis', 'country_id' => 2, 'type' => 'city', 'province' => 'Florianópolis', 'explore' => '/br/empregos',
    ],
    'minas-gerais' => [
        'slug' => 'vagas-de-emprego-em-minas-gerais',
        'name' => 'Minas Gerais', 'country_id' => 2, 'type' => 'city', 'province' => 'Minas Gerais', 'explore' => '/br/empregos',
    ],

];
