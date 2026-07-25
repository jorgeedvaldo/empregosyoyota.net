<?php

/*
|--------------------------------------------------------------------------
| Landings SEO de vagas (paises e cidades/estados)
|--------------------------------------------------------------------------
|
| Registo central das paginas de aterragem geradas pelo LandingController.
| Cada entrada gera automaticamente: rota, pagina e entrada no sitemap.
|
| country_id: 1 = Angola, 2 = Brasil, 3 = Mocambique, 5 = Portugal
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
    'portugal' => [
        'slug' => 'vagas-de-emprego-em-portugal',
        'name' => 'Portugal',
        'country_id' => 5,
        'type' => 'country',
        'explore' => '/pt/empregos',
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

    // ---- Angola: provincias ----
    'luanda' => [
        'slug' => 'vagas-de-emprego-em-luanda',
        'name' => 'Luanda', 'country_id' => 1, 'type' => 'city', 'province' => 'Luanda', 'explore' => '/ao/empregos',
    ],
    'benguela' => [
        'slug' => 'vagas-de-emprego-em-benguela',
        'name' => 'Benguela', 'country_id' => 1, 'type' => 'city', 'province' => 'Benguela', 'explore' => '/ao/empregos',
    ],
    'huambo' => [
        'slug' => 'vagas-de-emprego-em-huambo',
        'name' => 'Huambo', 'country_id' => 1, 'type' => 'city', 'province' => 'Huambo', 'explore' => '/ao/empregos',
    ],
    'huila' => [
        'slug' => 'vagas-de-emprego-em-huila',
        'name' => 'Huíla', 'country_id' => 1, 'type' => 'city', 'province' => 'Huíla', 'explore' => '/ao/empregos',
    ],
    'cabinda' => [
        'slug' => 'vagas-de-emprego-em-cabinda',
        'name' => 'Cabinda', 'country_id' => 1, 'type' => 'city', 'province' => 'Cabinda', 'explore' => '/ao/empregos',
    ],
    'cuanza-sul' => [
        'slug' => 'vagas-de-emprego-em-cuanza-sul',
        'name' => 'Cuanza Sul', 'country_id' => 1, 'type' => 'city', 'province' => 'Cuanza Sul', 'explore' => '/ao/empregos',
    ],
    'cuanza-norte' => [
        'slug' => 'vagas-de-emprego-em-cuanza-norte',
        'name' => 'Cuanza Norte', 'country_id' => 1, 'type' => 'city', 'province' => 'Cuanza Norte', 'explore' => '/ao/empregos',
    ],
    'malanje' => [
        'slug' => 'vagas-de-emprego-em-malanje',
        'name' => 'Malanje', 'country_id' => 1, 'type' => 'city', 'province' => 'Malanje', 'explore' => '/ao/empregos',
    ],
    'uige' => [
        'slug' => 'vagas-de-emprego-em-uige',
        'name' => 'Uíge', 'country_id' => 1, 'type' => 'city', 'province' => 'Uíge', 'explore' => '/ao/empregos',
    ],
    'bie' => [
        'slug' => 'vagas-de-emprego-em-bie',
        'name' => 'Bié', 'country_id' => 1, 'type' => 'city', 'province' => 'Bié', 'explore' => '/ao/empregos',
    ],
    'moxico' => [
        'slug' => 'vagas-de-emprego-em-moxico',
        'name' => 'Moxico', 'country_id' => 1, 'type' => 'city', 'province' => 'Moxico', 'explore' => '/ao/empregos',
    ],
    'namibe' => [
        'slug' => 'vagas-de-emprego-em-namibe',
        'name' => 'Namibe', 'country_id' => 1, 'type' => 'city', 'province' => 'Namibe', 'explore' => '/ao/empregos',
    ],
    'cunene' => [
        'slug' => 'vagas-de-emprego-em-cunene',
        'name' => 'Cunene', 'country_id' => 1, 'type' => 'city', 'province' => 'Cunene', 'explore' => '/ao/empregos',
    ],
    'lunda-norte' => [
        'slug' => 'vagas-de-emprego-em-lunda-norte',
        'name' => 'Lunda Norte', 'country_id' => 1, 'type' => 'city', 'province' => 'Lunda Norte', 'explore' => '/ao/empregos',
    ],
    'lunda-sul' => [
        'slug' => 'vagas-de-emprego-em-lunda-sul',
        'name' => 'Lunda Sul', 'country_id' => 1, 'type' => 'city', 'province' => 'Lunda Sul', 'explore' => '/ao/empregos',
    ],
    'zaire' => [
        'slug' => 'vagas-de-emprego-em-zaire',
        'name' => 'Zaire', 'country_id' => 1, 'type' => 'city', 'province' => 'Zaire', 'explore' => '/ao/empregos',
    ],
    'bengo' => [
        'slug' => 'vagas-de-emprego-em-bengo',
        'name' => 'Bengo', 'country_id' => 1, 'type' => 'city', 'province' => 'Bengo', 'explore' => '/ao/empregos',
    ],
    'cuando-cubango' => [
        'slug' => 'vagas-de-emprego-em-cuando-cubango',
        'name' => 'Cuando Cubango', 'country_id' => 1, 'type' => 'city', 'province' => 'Cuando Cubango', 'explore' => '/ao/empregos',
    ],

    // ---- Mocambique: provincias ----
    'maputo' => [
        'slug' => 'vagas-de-emprego-em-maputo',
        'name' => 'Maputo', 'country_id' => 3, 'type' => 'city', 'province' => 'Maputo', 'explore' => '/mz/empregos',
    ],
    'gaza' => [
        'slug' => 'vagas-de-emprego-em-gaza',
        'name' => 'Gaza', 'country_id' => 3, 'type' => 'city', 'province' => 'Gaza', 'explore' => '/mz/empregos',
    ],
    'inhambane' => [
        'slug' => 'vagas-de-emprego-em-inhambane',
        'name' => 'Inhambane', 'country_id' => 3, 'type' => 'city', 'province' => 'Inhambane', 'explore' => '/mz/empregos',
    ],
    'sofala' => [
        'slug' => 'vagas-de-emprego-em-sofala',
        'name' => 'Sofala', 'country_id' => 3, 'type' => 'city', 'province' => 'Sofala', 'explore' => '/mz/empregos',
    ],
    'manica' => [
        'slug' => 'vagas-de-emprego-em-manica',
        'name' => 'Manica', 'country_id' => 3, 'type' => 'city', 'province' => 'Manica', 'explore' => '/mz/empregos',
    ],
    'tete' => [
        'slug' => 'vagas-de-emprego-em-tete',
        'name' => 'Tete', 'country_id' => 3, 'type' => 'city', 'province' => 'Tete', 'explore' => '/mz/empregos',
    ],
    'zambezia' => [
        'slug' => 'vagas-de-emprego-em-zambezia',
        'name' => 'Zambézia', 'country_id' => 3, 'type' => 'city', 'province' => 'Zambézia', 'explore' => '/mz/empregos',
    ],
    'nampula' => [
        'slug' => 'vagas-de-emprego-em-nampula',
        'name' => 'Nampula', 'country_id' => 3, 'type' => 'city', 'province' => 'Nampula', 'explore' => '/mz/empregos',
    ],
    'cabo-delgado' => [
        'slug' => 'vagas-de-emprego-em-cabo-delgado',
        'name' => 'Cabo Delgado', 'country_id' => 3, 'type' => 'city', 'province' => 'Cabo Delgado', 'explore' => '/mz/empregos',
    ],
    'niassa' => [
        'slug' => 'vagas-de-emprego-em-niassa',
        'name' => 'Niassa', 'country_id' => 3, 'type' => 'city', 'province' => 'Niassa', 'explore' => '/mz/empregos',
    ],

    // ---- Portugal: distritos e regioes autonomas ----
    'lisboa' => [
        'slug' => 'vagas-de-emprego-em-lisboa',
        'name' => 'Lisboa', 'country_id' => 5, 'type' => 'city', 'province' => 'Lisboa', 'explore' => '/pt/empregos',
    ],
    'porto' => [
        'slug' => 'vagas-de-emprego-no-porto',
        'name' => 'Porto', 'country_id' => 5, 'type' => 'city', 'province' => 'Porto', 'explore' => '/pt/empregos',
    ],
    'braga' => [
        'slug' => 'vagas-de-emprego-em-braga',
        'name' => 'Braga', 'country_id' => 5, 'type' => 'city', 'province' => 'Braga', 'explore' => '/pt/empregos',
    ],
    'coimbra' => [
        'slug' => 'vagas-de-emprego-em-coimbra',
        'name' => 'Coimbra', 'country_id' => 5, 'type' => 'city', 'province' => 'Coimbra', 'explore' => '/pt/empregos',
    ],
    'faro' => [
        'slug' => 'vagas-de-emprego-em-faro',
        'name' => 'Faro', 'country_id' => 5, 'type' => 'city', 'province' => 'Faro', 'explore' => '/pt/empregos',
    ],
    'setubal' => [
        'slug' => 'vagas-de-emprego-em-setubal',
        'name' => 'Setúbal', 'country_id' => 5, 'type' => 'city', 'province' => 'Setúbal', 'explore' => '/pt/empregos',
    ],
    'aveiro' => [
        'slug' => 'vagas-de-emprego-em-aveiro',
        'name' => 'Aveiro', 'country_id' => 5, 'type' => 'city', 'province' => 'Aveiro', 'explore' => '/pt/empregos',
    ],
    'leiria' => [
        'slug' => 'vagas-de-emprego-em-leiria',
        'name' => 'Leiria', 'country_id' => 5, 'type' => 'city', 'province' => 'Leiria', 'explore' => '/pt/empregos',
    ],
    'santarem' => [
        'slug' => 'vagas-de-emprego-em-santarem',
        'name' => 'Santarém', 'country_id' => 5, 'type' => 'city', 'province' => 'Santarém', 'explore' => '/pt/empregos',
    ],
    'viseu' => [
        'slug' => 'vagas-de-emprego-em-viseu',
        'name' => 'Viseu', 'country_id' => 5, 'type' => 'city', 'province' => 'Viseu', 'explore' => '/pt/empregos',
    ],
    'evora' => [
        'slug' => 'vagas-de-emprego-em-evora',
        'name' => 'Évora', 'country_id' => 5, 'type' => 'city', 'province' => 'Évora', 'explore' => '/pt/empregos',
    ],
    'beja' => [
        'slug' => 'vagas-de-emprego-em-beja',
        'name' => 'Beja', 'country_id' => 5, 'type' => 'city', 'province' => 'Beja', 'explore' => '/pt/empregos',
    ],
    'braganca' => [
        'slug' => 'vagas-de-emprego-em-braganca',
        'name' => 'Bragança', 'country_id' => 5, 'type' => 'city', 'province' => 'Bragança', 'explore' => '/pt/empregos',
    ],
    'castelo-branco' => [
        'slug' => 'vagas-de-emprego-em-castelo-branco',
        'name' => 'Castelo Branco', 'country_id' => 5, 'type' => 'city', 'province' => 'Castelo Branco', 'explore' => '/pt/empregos',
    ],
    'guarda' => [
        'slug' => 'vagas-de-emprego-na-guarda',
        'name' => 'Guarda', 'country_id' => 5, 'type' => 'city', 'province' => 'Guarda', 'explore' => '/pt/empregos',
    ],
    'portalegre' => [
        'slug' => 'vagas-de-emprego-em-portalegre',
        'name' => 'Portalegre', 'country_id' => 5, 'type' => 'city', 'province' => 'Portalegre', 'explore' => '/pt/empregos',
    ],
    'viana-do-castelo' => [
        'slug' => 'vagas-de-emprego-em-viana-do-castelo',
        'name' => 'Viana do Castelo', 'country_id' => 5, 'type' => 'city', 'province' => 'Viana do Castelo', 'explore' => '/pt/empregos',
    ],
    'vila-real' => [
        'slug' => 'vagas-de-emprego-em-vila-real',
        'name' => 'Vila Real', 'country_id' => 5, 'type' => 'city', 'province' => 'Vila Real', 'explore' => '/pt/empregos',
    ],
    'acores' => [
        'slug' => 'vagas-de-emprego-nos-acores',
        'name' => 'Açores', 'country_id' => 5, 'type' => 'city', 'province' => 'Açores', 'explore' => '/pt/empregos',
    ],
    'madeira' => [
        'slug' => 'vagas-de-emprego-na-madeira',
        'name' => 'Madeira', 'country_id' => 5, 'type' => 'city', 'province' => 'Madeira', 'explore' => '/pt/empregos',
    ],

];
