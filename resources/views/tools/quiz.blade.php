<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Game</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: system-ui, -apple-system, sans-serif;
        }

        body {
            min-height: 100vh;
            background: black;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }

        .container {
            background: white;
            padding: 2rem;
            border-radius: 0.75rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            max-width: 28rem;
            width: 100%;
            transform-origin: center;
            transition: transform 0.5s, opacity 0.5s;
        }

        .container.fade-out {
            transform: scale(0.95);
            opacity: 0;
        }

        .start-screen {
            text-align: center;
        }

        .brain-icon {
            font-size: 5rem;
            margin-bottom: 1.5rem;
            animation: bounce 1s infinite;
            display: inline-block;
        }

        .title {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 1rem;
            animation: pulse 2s infinite;
        }

        .subtitle {
            color: #666;
            margin-bottom: 2rem;
            animation: fadeIn 1s;
        }

        .button {
            background: black;
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            cursor: pointer;
            font-size: 0.875rem;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.2s;
        }

        .button:hover {
            transform: scale(1.05);
            background: #333;
        }

        .button:disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: none;
        }

        .game-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .timer {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: color 0.3s;
        }

        .timer.warning {
            color: #ef4444;
        }

        .help-buttons {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1.5rem;
            gap: 0.5rem;
        }

        .help-buttons .button {
            flex: 1;
        }

        .question {
            font-size: 1.25rem;
            font-weight: bold;
            margin-bottom: 1.5rem;
            animation: fadeIn 0.5s;
            line-height: 1.4;
        }

        .options {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .option {
            width: 100%;
            padding: 1rem 1.25rem;
            border-radius: 0.5rem;
            border: 2px solid black;
            background: white;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 500;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: all 0.2s;
        }

        .option:hover:not(:disabled):not(.correct):not(.wrong) {
            transform: scale(1.02);
            background: #f8f8f8;
        }

        .option.correct {
            background: #22c55e;
            border-color: #22c55e;
            color: white;
        }

        .option.wrong {
            background: #ef4444;
            border-color: #ef4444;
            color: white;
        }

        .option.eliminated {
            opacity: 0.25;
            cursor: not-allowed;
            background: #f3f4f6;
            border-color: #e5e7eb;
        }

        .result-screen {
            text-align: center;
        }

        .result-title {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 1rem;
        }

        .score {
            font-size: 3.5rem;
            font-weight: bold;
            margin: 1.5rem 0;
            animation: scaleIn 0.5s;
        }

        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.8; }
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes scaleIn {
            from { transform: scale(0.8); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }

        @media (max-width: 640px) {
            .container {
                padding: 1.5rem;
            }

            .title {
                font-size: 2rem;
            }

            .brain-icon {
                font-size: 4rem;
            }

            .help-buttons {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div id="app" class="container"></div>

    <script>
        const questions = [
    {
        "id": 1,
        "question": "Qual é a capital de Angola?",
        "options": ["Luanda", "Benguela", "Huambo", "Lobito"],
        "correctAnswer": 0
    },
    {
        "id": 2,
        "question": "Qual é a língua oficial de Angola?",
        "options": ["Inglês", "Português", "Francês", "Espanhol"],
        "correctAnswer": 1
    },
    {
        "id": 3,
        "question": "Quantas províncias tem Angola?",
        "options": ["16", "18", "20", "22"],
        "correctAnswer": 1
    },
    {
        "id": 4,
        "question": "Qual é a moeda oficial de Angola?",
        "options": ["Dólar", "Euro", "Kwanza", "Rand"],
        "correctAnswer": 2
    },
    {
        "id": 5,
        "question": "Em que continente fica Angola?",
        "options": ["Ásia", "Europa", "África", "América"],
        "correctAnswer": 2
    },
    {
        "id": 6,
        "question": "Qual é o maior rio de Angola?",
        "options": ["Congo", "Cubango", "Cunene", "Cuanza"],
        "correctAnswer": 3
    },
    {
        "id": 7,
        "question": "Qual animal é um símbolo nacional de Angola?",
        "options": ["Leão", "Elefante", "Palanca Negra", "Girafa"],
        "correctAnswer": 2
    },
    {
        "id": 8,
        "question": "Qual é a cor predominante na bandeira de Angola?",
        "options": ["Verde", "Vermelho", "Azul", "Amarelo"],
        "correctAnswer": 1
    },
    {
        "id": 9,
        "question": "Em que oceano Angola tem costa?",
        "options": ["Pacífico", "Atlântico", "Índico", "Ártico"],
        "correctAnswer": 1
    },
    {
        "id": 10,
        "question": "Qual é o nome da árvore símbolo de Angola?",
        "options": ["Acácia", "Pinheiro", "Eucalipto", "Baobá"],
        "correctAnswer": 3
    },
    {
        "id": 11,
        "question": "Quem foi o primeiro presidente de Angola?",
        "options": ["José Eduardo dos Santos", "Jonas Savimbi", "Agostinho Neto", "João Lourenço"],
        "correctAnswer": 2
    },
    {
        "id": 12,
        "question": "Em que ano Angola se tornou independente?",
        "options": ["1975", "1965", "1985", "1995"],
        "correctAnswer": 0
    },
    {
        "id": 13,
        "question": "Qual país colonizou Angola?",
        "options": ["Espanha", "França", "Portugal", "Reino Unido"],
        "correctAnswer": 2
    },
    {
        "id": 14,
        "question": "Qual é a maior cidade portuária de Angola?",
        "options": ["Luanda", "Namibe", "Lobito", "Soyo"],
        "correctAnswer": 2
    },
    {
        "id": 15,
        "question": "Qual é o principal produto de exportação de Angola?",
        "options": ["Café", "Diamantes", "Petróleo", "Algodão"],
        "correctAnswer": 2
    },
    {
        "id": 16,
        "question": "Qual grupo étnico é o maior em Angola?",
        "options": ["Ambundu", "Ovimbundu", "Bakongo", "Chokwe"],
        "correctAnswer": 1
    },
    {
        "id": 17,
        "question": "Qual é o nome do deserto no sul de Angola?",
        "options": ["Sahara", "Kalahari", "Namibe", "Atacama"],
        "correctAnswer": 2
    },
    {
        "id": 18,
        "question": "Qual é a dança tradicional mais conhecida de Angola?",
        "options": ["Kizomba", "Semba", "Kuduro", "Rebita"],
        "correctAnswer": 1
    },
    {
        "id": 19,
        "question": "Quantos países fazem fronteira com Angola?",
        "options": ["2", "3", "4", "5"],
        "correctAnswer": 2
    },
    {
        "id": 20,
        "question": "Qual é o lema nacional de Angola?",
        "options": ["Unidade e Progresso", "Paz e Justiça", "Virtus Unita Fortior", "Liberdade ou Morte"],
        "correctAnswer": 2
    },
    {
        "id": 21,
        "question": "Qual é a capital da província do Huambo?",
        "options": ["Kuito", "Huambo", "Malanje", "Lubango"],
        "correctAnswer": 1
    },
    {
        "id": 22,
        "question": "Qual é o nome do hino nacional de Angola?",
        "options": ["Pátria Querida", "Angola Avante", "Unidos Venceremos", "Hino da Liberdade"],
        "correctAnswer": 1
    },
    {
        "id": 23,
        "question": "Qual cidade é conhecida como a 'Cidade das Acácias Rubras'?",
        "options": ["Luanda", "Benguela", "Huambo", "Lobito"],
        "correctAnswer": 2
    },
    {
        "id": 24,
        "question": "Qual é a maior barragem de Angola?",
        "options": ["Cambambe", "Capanda", "Lauca", "Matala"],
        "correctAnswer": 2
    },
    {
        "id": 25,
        "question": "Qual foi o movimento que liderou a independência de Angola?",
        "options": ["UNITA", "MPLA", "FNLA", "PAIGC"],
        "correctAnswer": 1
    },
    {
        "id": 26,
        "question": "Qual província é conhecida pelas suas minas de diamantes?",
        "options": ["Bengo", "Lunda Norte", "Zaire", "Uíge"],
        "correctAnswer": 1
    },
    {
        "id": 27,
        "question": "Qual é o nome da planta endêmica de Angola conhecida como 'planta do deserto'?",
        "options": ["Baobá", "Acácia", "Welwitschia", "Imbondeiro"],
        "correctAnswer": 2
    },
    {
        "id": 28,
        "question": "Qual é o segundo maior porto de Angola?",
        "options": ["Luanda", "Namibe", "Lobito", "Cabinda"],
        "correctAnswer": 2
    },
    {
        "id": 29,
        "question": "Qual é o nome do forte histórico em Luanda?",
        "options": ["Forte de Belas", "Forte de São Miguel", "Forte de Benguela", "Forte de Namibe"],
        "correctAnswer": 1
    },
    {
        "id": 30,
        "question": "Qual é a província mais ao norte de Angola?",
        "options": ["Zaire", "Cabinda", "Uíge", "Bengo"],
        "correctAnswer": 1
    },
    {
        "id": 31,
        "question": "Qual é o nome do navegador português que chegou a Angola em 1482?",
        "options": ["Vasco da Gama", "Bartolomeu Dias", "Diogo Cão", "Pedro Álvares Cabral"],
        "correctAnswer": 2
    },
    {
        "id": 32,
        "question": "Qual é o principal ingrediente da moamba de galinha?",
        "options": ["Mandioca", "Arroz", "Óleo de palma", "Feijão"],
        "correctAnswer": 2
    },
    {
        "id": 33,
        "question": "Qual é o nome da rainha guerreira que resistiu aos portugueses?",
        "options": ["Cleopatra", "Nzinga Mbandi", "Dandara", "Aqualtune"],
        "correctAnswer": 1
    },
    {
        "id": 34,
        "question": "Qual é o principal estádio de futebol de Angola?",
        "options": ["Estádio da Cidadela", "Estádio 11 de Novembro", "Estádio dos Coqueiros", "Estádio do Ferroviário"],
        "correctAnswer": 1
    },
    {
        "id": 35,
        "question": "Qual é a província onde fica o Parque Nacional da Kissama?",
        "options": ["Luanda", "Bengo", "Cuanza Sul", "Namibe"],
        "correctAnswer": 1
    },
    {
        "id": 36,
        "question": "Qual é o nome do tratado que dividiu terras entre Portugal e Espanha em 1494?",
        "options": ["Tratado de Lisboa", "Tratado de Madrid", "Tratado de Tordesilhas", "Tratado de Utrecht"],
        "correctAnswer": 2
    },
    {
        "id": 37,
        "question": "Qual é o nome do mercado de artesanato famoso ao sul de Luanda?",
        "options": ["Mercado do Roque", "Mercado de São Paulo", "Mercado de Futungo", "Mercado da Cuca"],
        "correctAnswer": 2
    },
    {
        "id": 38,
        "question": "Qual é a província mais populosa de Angola?",
        "options": ["Huambo", "Luanda", "Benguela", "Cuanza Norte"],
        "correctAnswer": 1
    },
    {
        "id": 39,
        "question": "Qual é o nome do escritor angolano autor de 'O Vendedor de Passados'?",
        "options": ["Pepetela", "Ondjaki", "José Eduardo Agualusa", "Luandino Vieira"],
        "correctAnswer": 2
    },
    {
        "id": 40,
        "question": "Qual é o nome do parque nacional no deserto do Namibe?",
        "options": ["Parque da Kissama", "Parque do Bicuar", "Parque do Iona", "Parque do Mupa"],
        "correctAnswer": 2
    },
    {
        "id": 41,
        "question": "Qual é a capital da província da Lunda Sul?",
        "options": ["Dundo", "Saurimo", "Luena", "Malanje"],
        "correctAnswer": 1
    },
    {
        "id": 42,
        "question": "Qual é o nome do instrumento musical tradicional angolano feito de madeira e cordas?",
        "options": ["Marimba", "Ngoma", "Kissange", "Dikanza"],
        "correctAnswer": 2
    },
    {
        "id": 43,
        "question": "Qual é a província onde fica a cidade de Malanje?",
        "options": ["Cuanza Norte", "Malanje", "Uíge", "Lunda Norte"],
        "correctAnswer": 1
    },
    {
        "id": 44,
        "question": "Qual é o nome da cachoeira mais famosa de Angola?",
        "options": ["Cachoeira de Tundavala", "Cachoeira do Binga", "Cachoeira de Kalandula", "Cachoeira do Ruacaná"],
        "correctAnswer": 2
    },
    {
        "id": 45,
        "question": "Qual é o significado de 'MPLA'?",
        "options": ["Movimento para a Paz e Liberdade de Angola", "Movimento Popular de Libertação de Angola", "Movimento Patriótico de Libertação Africana", "Movimento Progressista de Luanda e Angola"],
        "correctAnswer": 1
    },
    {
        "id": 46,
        "question": "Qual é o nome do arquipélago ao largo da costa de Angola?",
        "options": ["Arquipélago de Cabo Ledo", "Arquipélago de Mussulo", "Arquipélago de Soyo", "Arquipélago de Namibe"],
        "correctAnswer": 1
    },
    {
        "id": 47,
        "question": "Qual é a província onde fica a cidade de Lubango?",
        "options": ["Namibe", "Huíla", "Cunene", "Cuando Cubango"],
        "correctAnswer": 1
    },
    {
        "id": 48,
        "question": "Qual é o nome do rei do Congo que se converteu ao cristianismo em 1491?",
        "options": ["Afonso I", "Dom João", "Nzinga Mbemba", "Kimpanzu"],
        "correctAnswer": 2
    },
    {
        "id": 49,
        "question": "Qual é o nome da batalha decisiva pela independência de Angola em 1975?",
        "options": ["Batalha de Quifangondo", "Batalha de Cuito Cuanavale", "Batalha do Ebo", "Batalha de Kifangondo"],
        "correctAnswer": 0
    },
    {
        "id": 50,
        "question": "Qual é o nome do rio que forma a fronteira entre Angola e a Namíbia?",
        "options": ["Cunene", "Cubango", "Cuanza", "Congo"],
        "correctAnswer": 0
    },
    {
        "id": 51,
        "question": "Qual é o nome da estátua tradicional angolana que representa um pensador?",
        "options": ["O Guerreiro", "O Pensador", "O Sábio", "O Guardião"],
        "correctAnswer": 1
    },
    {
        "id": 52,
        "question": "Qual é o nome do primeiro jornal publicado em Angola?",
        "options": ["O Angolense", "Gazeta de Luanda", "Jornal de Angola", "A Província"],
        "correctAnswer": 1
    },
    {
        "id": 53,
        "question": "Qual é a província onde fica o enclave de Cabinda?",
        "options": ["Zaire", "Cabinda", "Bengo", "Luanda"],
        "correctAnswer": 1
    },
    {
        "id": 54,
        "question": "Qual é o nome do estilo musical criado em Angola nos anos 2000?",
        "options": ["Semba", "Kizomba", "Kuduro", "Rebita"],
        "correctAnswer": 2
    },
    {
        "id": 55,
        "question": "Qual é o nome da montanha mais alta de Angola?",
        "options": ["Serra da Leba", "Tundavala", "Monte Moco", "Morro do Muxima"],
        "correctAnswer": 2
    },
    {
        "id": 56,
        "question": "Qual é o nome do tratado que acabou com a guerra civil em Angola em 2002?",
        "options": ["Acordo de Lusaka", "Acordo de Bicesse", "Memorando de Luanda", "Tratado de Alvor"],
        "correctAnswer": 2
    },
    {
        "id": 57,
        "question": "Qual é o nome do líder da UNITA que faleceu em 2002?",
        "options": ["Isaías Samakuva", "Jonas Savimbi", "Abel Chivukuvuku", "Holden Roberto"],
        "correctAnswer": 1
    },
    {
        "id": 58,
        "question": "Qual é o nome da cooperativa de diamantes de Angola?",
        "options": ["Catoca", "Endiama", "Sodiam", "Angoldiam"],
        "correctAnswer": 1
    },
    {
        "id": 59,
        "question": "Qual é a província onde fica a cidade de Kuito?",
        "options": ["Huambo", "Bié", "Cuanza Sul", "Malanje"],
        "correctAnswer": 1
    },
    {
        "id": 60,
        "question": "Qual é o nome da organização regional que Angola integra?",
        "options": ["ECOWAS", "SADC", "EAC", "AU"],
        "correctAnswer": 1
    },
    {
        "id": 61,
        "question": "Qual é o nome do maior campo de petróleo offshore de Angola?",
        "options": ["Dália", "Girassol", "Pazflor", "Kaombo"],
        "correctAnswer": 1
    },
    {
        "id": 62,
        "question": "Qual é o nome da poetisa angolana autora de 'Sagrada Esperança'?",
        "options": ["Maria Eugénia Neto", "Alda Lara", "Ana Paula Ribeiro", "Conceição Lima"],
        "correctAnswer": 1
    },
    {
        "id": 63,
        "question": "Qual é o nome do acordo de paz assinado em 1991?",
        "options": ["Acordo de Lusaka", "Acordo de Bicesse", "Memorando de Luanda", "Tratado de Alvor"],
        "correctAnswer": 1
    },
    {
        "id": 64,
        "question": "Qual é o nome do cabo que marca o extremo sul de Angola?",
        "options": ["Cabo Ledo", "Cabo Negro", "Cabo de Santa Maria", "Cabo Frio"],
        "correctAnswer": 1
    },
    {
        "id": 65,
        "question": "Qual é o nome da batalha que marcou o fim da guerra colonial em Angola?",
        "options": ["Batalha de Quifangondo", "Batalha de Cuito Cuanavale", "Batalha do Ebo", "Batalha de Kifangondo"],
        "correctAnswer": 1
    },
    {
        "id": 66,
        "question": "Qual é o nome do grupo étnico que criou 'O Pensador'?",
        "options": ["Ovimbundu", "Chokwe", "Ambundu", "Bakongo"],
        "correctAnswer": 1
    },
    {
        "id": 67,
        "question": "Qual é o nome da cidade angolana famosa pela produção de café?",
        "options": ["Uíge", "Gabela", "Malanje", "Soyo"],
        "correctAnswer": 1
    },
    {
        "id": 68,
        "question": "Qual é o nome do rei português que ordenou a colonização de Angola?",
        "options": ["D. Manuel I", "D. Sebastião", "D. João II", "D. Afonso V"],
        "correctAnswer": 2
    },
    {
        "id": 69,
        "question": "Qual é o nome da reserva natural que protege a palanca negra?",
        "options": ["Reserva do Bicuar", "Reserva do Luando", "Reserva do Iona", "Reserva da Kissama"],
        "correctAnswer": 1
    },
    {
        "id": 70,
        "question": "Qual é o nome do escritor angolano que ganhou o Prêmio Camões em 2007?",
        "options": ["Pepetela", "José Eduardo Agualusa", "Luandino Vieira", "Ondjaki"],
        "correctAnswer": 2
    },
    {
        "id": 71,
        "question": "Qual é o nome da estrada panorâmica na Huíla?",
        "options": ["Serra da Chela", "Serra da Leba", "Serra do Moco", "Serra da Tundavala"],
        "correctAnswer": 1
    },
    {
        "id": 72,
        "question": "Qual é o nome do líder da FNLA durante a luta pela independência?",
        "options": ["Jonas Savimbi", "Holden Roberto", "Agostinho Neto", "Lúcio Lara"],
        "correctAnswer": 1
    },
    {
        "id": 73,
        "question": "Qual é o nome do tratado que reconheceu a independência de Angola em 1975?",
        "options": ["Tratado de Lusaka", "Tratado de Alvor", "Tratado de Bicesse", "Tratado de Madrid"],
        "correctAnswer": 1
    },
    {
        "id": 74,
        "question": "Qual é o nome do vulcão extinto em Cabinda?",
        "options": ["Vulcão do Cacongo", "Vulcão do Tchiowa", "Vulcão do Maiombe", "Vulcão do Zombo"],
        "correctAnswer": 1
    },
    {
        "id": 75,
        "question": "Qual é o nome do rio que nasce no planalto do Bié?",
        "options": ["Cuanza", "Cubango", "Cunene", "Congo"],
        "correctAnswer": 1
    },
    {
        "id": 76,
        "question": "Qual é o nome da universidade pública mais antiga de Angola?",
        "options": ["Universidade Católica de Angola", "Universidade Agostinho Neto", "Universidade Mandume", "Universidade Kimpa Vita"],
        "correctAnswer": 1
    },
    {
        "id": 77,
        "question": "Qual é o nome da operação militar cubana em apoio ao MPLA?",
        "options": ["Operação Savana", "Operação Carlota", "Operação Cobra", "Operação Leopardo"],
        "correctAnswer": 1
    },
    {
        "id": 78,
        "question": "Qual é o nome da primeira-dama que lançou a campanha 'Nascer Livre para Brilhar'?",
        "options": ["Maria José dos Santos", "Ana Dias Lourenço", "Isabel dos Santos", "Lurdes Van-Dúnem"],
        "correctAnswer": 1
    },
    {
        "id": 79,
        "question": "Qual é o nome do missionário português que fundou Benguela em 1617?",
        "options": ["Paulo Dias de Novais", "Manuel Cerveira Pereira", "Diogo Cão", "Francisco de Almeida"],
        "correctAnswer": 1
    },
    {
        "id": 80,
        "question": "Qual é o nome da província onde ocorreu a Batalha de Cuito Cuanavale?",
        "options": ["Huíla", "Cuando Cubango", "Cunene", "Bié"],
        "correctAnswer": 1
    },
    {
        "id": 81,
        "question": "Qual é o nome do antropólogo que estudou as culturas do sul de Angola?",
        "options": ["José Redinha", "Carlos Estermann", "António Carreira", "Henrique Galvão"],
        "correctAnswer": 1
    },
    {
        "id": 82,
        "question": "Qual é o nome do festival bienal promovido por Angola e UNESCO?",
        "options": ["Festival de Kizomba", "Bienal de Luanda", "Bienal do Semba", "Festival do Namibe"],
        "correctAnswer": 1
    },
    {
        "id": 83,
        "question": "Qual é o nome da linha imaginária que atravessa Angola perto de Luanda?",
        "options": ["Equador", "Trópico de Câncer", "Trópico de Capricórnio", "Meridiano de Greenwich"],
        "correctAnswer": 2
    },
    {
        "id": 84,
        "question": "Qual é o nome do rei do Ndongo antes da chegada dos portugueses?",
        "options": ["Ngola Mbandi", "Ngola Kiluanji", "Afonso I", "Kimpanzu"],
        "correctAnswer": 1
    },
    {
        "id": 85,
        "question": "Qual é o nome da empresa estatal de petróleo de Angola?",
        "options": ["Endiama", "Sonangol", "Angopetrol", "Petroangola"],
        "correctAnswer": 1
    },
    {
        "id": 86,
        "question": "Qual é o nome da cidade destruída durante a guerra civil e reconstruída como Nova Lisboa?",
        "options": ["Kuito", "Huambo", "Luena", "Malanje"],
        "correctAnswer": 1
    },
    {
        "id": 87,
        "question": "Qual é o nome do explorador inglês que atravessou Angola no século XIX?",
        "options": ["Henry Stanley", "David Livingstone", "Richard Burton", "John Speke"],
        "correctAnswer": 1
    },
    {
        "id": 88,
        "question": "Qual é o nome do dialecto bantu falado em Cabinda?",
        "options": ["Kimbundu", "Fiote", "Umbundu", "Kikongo"],
        "correctAnswer": 1
    },
    {
        "id": 89,
        "question": "Qual é o nome do acordo que estabeleceu a transição para a independência em 1974?",
        "options": ["Acordo de Lusaka", "Acordo de Alvor", "Acordo de Bicesse", "Acordo de Luanda"],
        "correctAnswer": 1
    },
    {
        "id": 90,
        "question": "Qual é o nome do grupo étnico que domina a região do Cuando Cubango?",
        "options": ["Ovimbundu", "Nganguela", "Chokwe", "Ambundu"],
        "correctAnswer": 1
    },
    {
        "id": 91,
        "question": "Qual é o nome do bispo português que evangelizou Angola no século XVI?",
        "options": ["Domingos de Almeida", "Manuel de Paiva", "Henrique de Coimbra", "João de Sousa"],
        "correctAnswer": 2
    },
    {
        "id": 92,
        "question": "Qual é o nome do médico angolano que criou o primeiro hospital moderno em Luanda?",
        "options": ["José Mendes de Almeida", "António de Almeida", "Domingos Van-Dúnem", "Américo Boavida"],
        "correctAnswer": 3
    },
    {
        "id": 93,
        "question": "Qual é o nome da operação sul-africana contra o MPLA em 1975?",
        "options": ["Operação Leopardo", "Operação Savana", "Operação Cobra", "Operação Falcão"],
        "correctAnswer": 1
    },
    {
        "id": 94,
        "question": "Qual é o nome do acordo que criou a CPLP, da qual Angola faz parte?",
        "options": ["Acordo de Luanda", "Acordo de Brasília", "Acordo de Lisboa", "Acordo de São Tomé"],
        "correctAnswer": 1
    },
    {
        "id": 95,
        "question": "Qual é o nome do chefe militar português que conquistou o reino de Benguela?",
        "options": ["Paulo Dias de Novais", "Manuel Cerveira Pereira", "Salvador Correia de Sá", "Luís de Almeida"],
        "correctAnswer": 1
    },
    {
        "id": 96,
        "question": "Qual é o nome da sociedade secreta do povo Chokwe?",
        "options": ["Ngola", "Mukanda", "Kalunga", "Tukula"],
        "correctAnswer": 1
    },
    {
        "id": 97,
        "question": "Qual é o nome do rio que atravessa a cidade de Malanje?",
        "options": ["Cuanza", "Cubango", "Lucala", "Cunene"],
        "correctAnswer": 2
    },
    {
        "id": 98,
        "question": "Qual é o nome do primeiro governador colonial de Angola nomeado em 1575?",
        "options": ["Manuel Cerveira Pereira", "Paulo Dias de Novais", "Diogo Cão", "Salvador de Sá"],
        "correctAnswer": 1
    },
    {
        "id": 99,
        "question": "Qual é o nome da expedição portuguesa que explorou o interior de Angola em 1877?",
        "options": ["Expedição de Serpa Pinto", "Expedição de Livingstone", "Expedição de Capelo e Ivens", "Expedição de Almeida"],
        "correctAnswer": 2
    },
    {
        "id": 100,
        "question": "Qual é o nome do manuscrito do século XVII que descreve os costumes do reino do Ndongo?",
        "options": ["Códice de Luanda", "Relação de Garcia Mendes Castelo Branco", "Livro de Fernão de Sousa", "Crónica de António de Oliveira Cadornega"],
        "correctAnswer": 3
    }
];

        const QUESTION_TIME_LIMIT = 30;
        let currentQuestion = 0;
        let score = 0;
        let timeLeft = QUESTION_TIME_LIMIT;
        let timerInterval;
        let fiftyFiftyUsed = false;
        let skipUsed = false;
        let eliminatedOptions = [];

        const app = document.getElementById('app');

        function renderStartScreen() {
            app.innerHTML = `
                <div class="start-screen">
                    <div class="brain-icon">🧠</div>
                    <h1 class="title">Quiz Game</h1>
                    <p class="subtitle">Teste seu conhecimento sobre Angola!</p>
                    <button class="button" onclick="startGame()">
                        ▶️ Iniciar Jogo
                    </button>
                    
                    <a class="button" href="https://empregosyoyota.net">
                        Voltar à Página Inicial
                    </a>
                </div>
            `;
        }

        function startGame() {
            app.classList.add('fade-out');
            setTimeout(() => {
                app.classList.remove('fade-out');
                renderGame();
                startTimer();
            }, 500);
        }

        function renderGame() {
            const question = questions[currentQuestion];
            app.innerHTML = `
                <div class="game-header">
                    <span>Pergunta ${currentQuestion + 1}/${questions.length}</span>
                    <div class="timer ${timeLeft <= 5 ? 'warning' : ''}">
                        ⏱️ ${timeLeft}s
                    </div>
                    <span>Pontuação: ${score}</span>
                </div>

                <div class="help-buttons">
                    <button class="button" onclick="useFiftyFifty()" ${fiftyFiftyUsed ? 'disabled' : ''}>
                        ➗ 50:50
                    </button>
                    <button class="button" onclick="useSkip()" ${skipUsed ? 'disabled' : ''}>
                        ⏭️ Pular
                    </button>
                    <button class="button" onclick="resetQuiz()">
                        🔄 Reiniciar
                    </button>
                </div>

                <h2 class="question">${question.question}</h2>

                <div class="options">
                    ${question.options.map((option, index) => `
                        <button 
                            class="option ${eliminatedOptions.includes(index) ? 'eliminated' : ''}"
                            onclick="selectAnswer(${index})"
                            ${eliminatedOptions.includes(index) ? 'disabled' : ''}
                        >
                            ${option}
                            ${eliminatedOptions.includes(index) ? '' : '<span class="check"></span>'}
                        </button>
                    `).join('')}
                </div>
            `;
        }

        function renderResultScreen() {
            clearInterval(timerInterval);
            app.innerHTML = `
                <div class="result-screen">
                    <h2 class="result-title">Jogo Concluído!</h2>
                    <p>Sua Pontuação:</p>
                    <div class="score">${score} / ${questions.length}</div>
                    <button class="button" onclick="resetQuiz()">
                        🔄 Tentar Novamente
                    </button>
                </div>
            `;
        }

        function startTimer() {
            clearInterval(timerInterval);
            timerInterval = setInterval(() => {
                timeLeft--;
                if (timeLeft <= 0) {
                    handleTimeUp();
                }
                const timerElement = document.querySelector('.timer');
                if (timerElement) {
                    timerElement.textContent = `⏱️ ${timeLeft}s`;
                    timerElement.classList.toggle('warning', timeLeft <= 5);
                }
            }, 1000);
        }

        function handleTimeUp() {
            clearInterval(timerInterval);
            resetQuiz(); // Reinicia o jogo se o tempo acabar
        }

        function selectAnswer(index) {
            clearInterval(timerInterval);
            const options = document.querySelectorAll('.option');
            const correctAnswer = questions[currentQuestion].correctAnswer;

            options[correctAnswer].classList.add('correct');
            if (index !== correctAnswer) {
                options[index].classList.add('wrong');
                setTimeout(() => {
                    resetQuiz(); // Reinicia o jogo se a resposta estiver errada
                }, 1000);
            } else {
                score++;
                options.forEach(option => option.disabled = true);
                setTimeout(() => {
                    if (currentQuestion < questions.length - 1) {
                        currentQuestion++;
                        timeLeft = QUESTION_TIME_LIMIT;
                        eliminatedOptions = [];
                        renderGame();
                        startTimer();
                    } else {
                        renderResultScreen();
                    }
                }, 1000);
            }
        }

        function useFiftyFifty() {
            if (fiftyFiftyUsed) return;
            
            const correctAnswer = questions[currentQuestion].correctAnswer;
            const wrongAnswers = [0, 1, 2, 3].filter(i => i !== correctAnswer);
            const toEliminate = wrongAnswers
                .sort(() => Math.random() - 0.5)
                .slice(0, 2);
            
            eliminatedOptions = toEliminate;
            fiftyFiftyUsed = true;
            renderGame();
        }

        function useSkip() {
            if (skipUsed) return;
            
            skipUsed = true;
            if (currentQuestion < questions.length - 1) {
                currentQuestion++;
                timeLeft = QUESTION_TIME_LIMIT;
                eliminatedOptions = [];
                renderGame();
                startTimer();
            } else {
                renderResultScreen();
            }
        }

        function resetQuiz() {
            currentQuestion = 0;
            score = 0;
            timeLeft = QUESTION_TIME_LIMIT;
            fiftyFiftyUsed = false;
            skipUsed = false;
            eliminatedOptions = [];
            clearInterval(timerInterval);
            app.classList.add('fade-out');
            setTimeout(() => {
                app.classList.remove('fade-out');
                renderStartScreen();
            }, 500);
        }

        // Inicializa o jogo
        renderStartScreen();
    </script>
</body>
</html>