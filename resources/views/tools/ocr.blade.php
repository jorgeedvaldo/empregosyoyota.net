@extends('template.app')
@section('title', 'Converter Imagem e PDF em Texto - Online OCR')
@section('description', 'Conhecida como Online OCR. Ferramenta online para converter imagens e PDFs em texto com suporte para múltiplos idiomas. Aqui podes fazer o reconhecimento de texto, converter imagem em texto, converter PDF em texto e ter uma ferramenta OCR grátis.')

@section('canonical_link', url('/onlineocr'))
@section('created_at', '2024-08-30 12:00:40')
@section('updated_at', '2024-08-30 12:00:40')

@section('content')
<!-- Page Content -->
<div class="container">

    <div class="row">

      <!-- Post Content Column -->
      <div class="col-lg-8">
		<!-- AD 1 -->
		<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-2118765549976668"
     crossorigin="anonymous"></script>
		<!-- AnuncioVizualizacao2 -->
		<ins class="adsbygoogle"
			 style="display:block"
			 data-ad-client="ca-pub-2118765549976668"
			 data-ad-slot="5838441610"
			 data-ad-format="auto"
			 data-full-width-responsive="true"></ins>
		<script>
			 (adsbygoogle = window.adsbygoogle || []).push({});
		</script>
		<!-- AD 2 -->
        <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-2118765549976668" crossorigin="anonymous"></script>
        <ins class="adsbygoogle"
            style="display:block; text-align:center;"
            data-ad-layout="in-article"
            data-ad-format="fluid"
            data-ad-client="ca-pub-2118765549976668"
            data-ad-slot="7583808877">
        </ins>
        <script>
            (adsbygoogle = window.adsbygoogle || []).push({});
        </script>
	  
		  <h1 class="text-center mb-4">Converter Imagem e PDF em Texto - Online OCR</h1>
        <div class="mb-3">
            <label for="fileInput" class="form-label">Carregar Imagem ou PDF</label>
            <input class="form-control" type="file" id="fileInput" accept=".jpg,.jpeg,.png,.pdf">
        </div>
        <div class="mb-3">
            <label for="languageSelect" class="form-label">Selecionar Idioma</label>
            <select class="form-select" id="languageSelect">
                <!-- 50 languages for Tesseract.js -->
                <option value="eng">English</option>
                <option value="por" selected>Português</option>
                <option value="spa">Spanish</option>
                <option value="fra">French</option>
                <option value="deu">German</option>
                <option value="ita">Italian</option>
                <option value="rus">Russian</option>
                <option value="jpn">Japanese</option>
                <option value="chi_sim">Chinese (Simplified)</option>
                <option value="chi_tra">Chinese (Traditional)</option>
                <!-- Add more languages as needed -->
                <option value="ara">Arabic</option>
                <option value="hin">Hindi</option>
                <option value="kor">Korean</option>
                <option value="heb">Hebrew</option>
                <option value="tur">Turkish</option>
                <option value="vie">Vietnamese</option>
                <option value="tha">Thai</option>
                <option value="gre">Greek</option>
                <option value="swe">Swedish</option>
                <option value="nor">Norwegian</option>
                <option value="dan">Danish</option>
                <option value="fin">Finnish</option>
                <option value="pol">Polish</option>
                <option value="ukr">Ukrainian</option>
                <option value="hun">Hungarian</option>
                <option value="cze">Czech</option>
                <option value="slk">Slovak</option>
                <option value="dut">Dutch</option>
                <option value="bul">Bulgarian</option>
                <option value="ron">Romanian</option>
                <option value="lit">Lithuanian</option>
                <option value="lav">Latvian</option>
                <option value="est">Estonian</option>
                <option value="isl">Icelandic</option>
                <option value="mlt">Maltese</option>
                <option value="slo">Slovenian</option>
                <option value="hrv">Croatian</option>
                <option value="srp">Serbian</option>
                <option value="mkd">Macedonian</option>
                <option value="bos">Bosnian</option>
                <option value="alb">Albanian</option>
                <option value="eus">Basque</option>
                <option value="cat">Catalan</option>
                <option value="glg">Galician</option>
                <option value="tel">Telugu</option>
                <option value="tam">Tamil</option>
                <option value="ben">Bengali</option>
            </select>
        </div>
        <div id="pageSelector" class="mb-3" style="display: none;">
            <label for="pageNumber" class="form-label">Selecionar Página</label>
            <select class="form-select" id="pageNumber">
                <!-- Opções de páginas serão preenchidas dinamicamente -->
            </select>
        </div>
        <div class="mb-3">
            <button id="convertBtn" class="btn btn-primary">Converter</button>
        </div>
        <div class="mb-3">
            <label for="output" class="form-label">Resultado</label>
            <textarea class="form-control" id="output" rows="10"></textarea>
        </div>
        <button id="copyBtn" class="btn btn-secondary">Copiar</button>

        <article class="mt-5">
    <h2>O que é OCR Online? Descubra Tudo Sobre Esta Tecnologia Revolucionária</h2>
    <p>
        O OCR, ou Reconhecimento Óptico de Caracteres, é uma tecnologia essencial que tem mudado drasticamente a forma como interagimos com documentos digitais. Com o avanço da tecnologia, o OCR online se tornou uma ferramenta indispensável para empresas, estudantes, pesquisadores e até para o uso pessoal. Mas o que exatamente é OCR e por que sua versão online é tão importante?
    </p>
    <p>
        OCR (Optical Character Recognition, em inglês) é uma tecnologia inovadora que permite a conversão de diferentes tipos de documentos — como imagens digitalizadas de documentos impressos, arquivos PDF ou até mesmo fotos tiradas com câmeras digitais — em dados editáveis e pesquisáveis. Imagine ter uma pilha de documentos antigos que precisam ser digitalizados. Ao invés de digitar manualmente todo o conteúdo, o OCR online entra em cena, convertendo rapidamente essas imagens em texto digital.
    </p>
    <p>
        A popularidade do OCR online cresceu exponencialmente, principalmente devido à sua conveniência e eficiência. Com apenas alguns cliques, é possível converter documentos inteiros em texto editável, economizando horas de trabalho manual. Ferramentas que oferecem a funcionalidade de "online convert PDF to text" se tornaram especialmente valiosas em um mundo onde a digitalização de documentos é essencial para manter a produtividade e a organização.
    </p>

    <h3>Como Funciona a Tecnologia Online OCR? Um Mergulho na Eficiência</h3>
    <p>
        A tecnologia OCR é mais complexa do que parece à primeira vista. Ela funciona analisando a estrutura de um documento ou imagem, identificando padrões que correspondem a caracteres específicos. Pense no OCR como um olho humano, mas com a capacidade de identificar e processar grandes volumes de texto com uma precisão impressionante.
    </p>
    <p>
        Quando você utiliza uma ferramenta de OCR online, o processo começa com o upload do documento, seja ele uma imagem ou um PDF. A ferramenta então utiliza algoritmos avançados para decompor a imagem em pequenas partes, identificando formas e padrões que correspondem a letras e números. Este processo é conhecido como segmentação. Depois de segmentado, o OCR compara cada segmento com uma vasta biblioteca de caracteres armazenados em sua base de dados.
    </p>
    <p>
        Nossa ferramenta de OCR online utiliza o <strong>Tesseract.js</strong>, uma das bibliotecas mais poderosas e amplamente utilizadas no mundo do OCR. O Tesseract.js é capaz de reconhecer texto em mais de 100 idiomas, tornando-o uma escolha ideal para usuários de todo o mundo. Além disso, utilizamos o <strong>PDF.js</strong> para lidar com documentos PDF, o que nos permite oferecer uma solução robusta para quem precisa converter PDF para texto online. Esta combinação de tecnologias garante que o texto seja convertido de maneira rápida, precisa e confiável.
    </p>

    <h3>Vantagens do Uso de OCR Online: Por Que Você Deve Considerar</h3>
    <p>
        A utilização de OCR online traz consigo uma série de vantagens que vão além da simples conversão de documentos. Vamos explorar em detalhes algumas dessas vantagens, destacando como elas podem beneficiar tanto indivíduos quanto empresas:
    </p>
    <ul>
        <li><strong>Acessibilidade Imediata:</strong> Com o OCR online, não há necessidade de instalar softwares complexos no seu computador. Tudo o que você precisa é de uma conexão com a internet e um navegador. Isso significa que você pode acessar a ferramenta de qualquer lugar, a qualquer momento, sem se preocupar com compatibilidade de sistema ou espaço de armazenamento.</li>
        <li><strong>Eficiência e Economia de Tempo:</strong> O tempo é um recurso precioso, e o OCR online permite economizar horas que seriam gastas digitando manualmente o conteúdo de documentos. Com suporte a mais de 50 idiomas, nossa ferramenta pode converter textos em várias línguas com extrema precisão, tornando-a ideal para uso em ambientes internacionais.</li>
        <li><strong>Versatilidade:</strong> Uma das grandes vantagens do OCR online é sua capacidade de lidar com diferentes tipos de documentos. Seja uma imagem escaneada ou um PDF, nossa ferramenta é capaz de extrair texto de quase qualquer tipo de documento. Isso a torna extremamente versátil, permitindo que você converta documentos de diversas naturezas sem precisar de múltiplas ferramentas.</li>
        <li><strong>Produtividade Amplificada:</strong> Ao converter documentos em texto editável, o OCR online permite que você faça edições, revisões e pesquisas no conteúdo convertido. Isso aumenta significativamente sua produtividade, especialmente em cenários onde a precisão e a rapidez são cruciais.</li>
        <li><strong>Armazenamento e Pesquisa Facilitada:</strong> Ao converter documentos físicos em arquivos digitais editáveis, você pode armazená-los de maneira mais organizada e eficiente. Além disso, o texto convertido pode ser facilmente pesquisado, tornando mais simples encontrar informações específicas em grandes volumes de documentos.</li>
    </ul>

    <h3>Funcionalidade Avançada da Nossa Ferramenta OCR: Mais do que Simples Conversão</h3>
    <p>
        Nossa ferramenta de OCR online foi projetada para ser extremamente intuitiva e fácil de usar, mas não se deixe enganar por sua simplicidade. Por trás de sua interface amigável, há uma poderosa tecnologia que garante resultados rápidos e precisos.
    </p>
    <p>
        Quando você utiliza nossa ferramenta, todo o processo é feito em poucos cliques. Você simplesmente carrega a imagem ou o PDF que deseja converter, escolhe o idioma de reconhecimento, e a ferramenta faz o resto. Em questão de segundos, o texto é extraído e apresentado em um formato editável, pronto para ser copiado ou utilizado conforme necessário.
    </p>
    <p>
        Além disso, a ferramenta suporta uma ampla gama de formatos de arquivo, incluindo os formatos de imagem mais comuns, como JPEG e PNG, bem como PDFs. Isso significa que, independentemente do tipo de documento com o qual você esteja lidando, nossa ferramenta de OCR online pode processá-lo com eficiência.
    </p>
    <p>
        Outro ponto forte de nossa ferramenta é sua capacidade de preservar a formatação original do texto, tanto quanto possível. Isso inclui a manutenção de quebras de linha, espaçamento entre parágrafos e até mesmo a organização de tabelas em alguns casos. Assim, você não apenas converte o texto, mas também mantém a integridade do documento original.
    </p>

    <h3>Por que Escolher Nossa Ferramenta de OCR Online? A Melhor Opção no Mercado</h3>
    <p>
        Existem muitas ferramentas de OCR disponíveis online, então o que torna a nossa a melhor escolha? A resposta está em nossa combinação de simplicidade, eficiência e resultados de alta qualidade. Aqui estão alguns dos motivos pelos quais nossa ferramenta se destaca:
    </p>
    <ul>
        <li><strong>Solução Gratuita e Poderosa:</strong> Enquanto muitas ferramentas de OCR cobram pelo uso ou oferecem funcionalidades limitadas na versão gratuita, nós fornecemos uma solução completa sem custos. Isso significa que você pode converter documentos ilimitados sem se preocupar com restrições.</li>
        <li><strong>Suporte a Vários Idiomas:</strong> A maioria das ferramentas de OCR online se limita a um ou dois idiomas, mas nossa ferramenta suporta mais de 50 línguas, permitindo a conversão de documentos em múltiplos idiomas com a mesma eficiência.</li>
        <li><strong>Tecnologia de Ponta:</strong> Utilizamos as bibliotecas mais avançadas disponíveis, como Tesseract.js e PDF.js, para garantir que nossos usuários obtenham os melhores resultados possíveis. Essa tecnologia de ponta nos permite oferecer uma precisão de reconhecimento de texto que poucas outras ferramentas conseguem igualar.</li>
        <li><strong>SEO Focado:</strong> Além de oferecer uma ferramenta robusta, também nos preocupamos com a visibilidade online. Nossa abordagem focada em SEO garante que nossa ferramenta seja facilmente encontrada por quem precisa, ajudando mais pessoas a descobrir e utilizar nossa solução.</li>
        <li><strong>Facilidade de Uso:</strong> Não importa se você é um especialista em tecnologia ou um usuário casual, nossa ferramenta foi projetada para ser intuitiva e fácil de usar. Com uma interface limpa e um processo simplificado, qualquer pessoa pode converter documentos em texto com facilidade.</li>
    </ul>

    <h3>Conclusão: OCR Online - O Futuro da Digitalização de Documentos</h3>
    <p>
        A tecnologia OCR tem revolucionado a maneira como lidamos com documentos no mundo digital. Seja para digitalizar documentos antigos, extrair texto de imagens ou converter PDFs em conteúdo editável, o OCR online se provou uma ferramenta indispensável.
    </p>
    <p>
        Nossa ferramenta de OCR online não é apenas uma solução de conversão, mas sim uma ferramenta completa que ajuda a simplificar e acelerar seus processos diários. Experimente agora e descubra como é fácil transformar imagens e PDFs em texto de alta qualidade com apenas alguns cliques. E lembre-se, com a tecnologia de OCR online, você está sempre a um passo de transformar seus documentos físicos em arquivos digitais prontos para edição e pesquisa. Se você precisa converter PDF para texto online ou transformar imagens em documentos editáveis, nossa ferramenta é a escolha ideal para você.
    </p>
</article>


			  
		<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-2118765549976668"
     crossorigin="anonymous"></script>
		<!-- AnuncioVisualizacao -->
		<ins class="adsbygoogle"
			 style="display:block"
			 data-ad-client="ca-pub-2118765549976668"
			 data-ad-slot="9753835582"
			 data-ad-format="auto"
			 data-full-width-responsive="true"></ins>
		<script>
			 (adsbygoogle = window.adsbygoogle || []).push({});
		</script>
			  
		<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-2118765549976668"
     crossorigin="anonymous"></script>
		<!-- AnuncioVisualizacao3 -->
		<ins class="adsbygoogle"
			 style="display:block"
			 data-ad-client="ca-pub-2118765549976668"
			 data-ad-slot="2166789917"
			 data-ad-format="auto"
			 data-full-width-responsive="true"></ins>
		<script>
			 (adsbygoogle = window.adsbygoogle || []).push({});
		</script>
      </div>

      <!-- Sidebar Widgets Column -->
      <div class="col-md-4">

        <!-- Card de Últimas Oportunidades Widget -->
        <div class="card my-4">
          <h5 class="card-header">Últimas Oportunidades</h5>
          <div class="card-body">
            <div class="row">
              <div class="col-lg-12">
                <div class="list-group mb-3">
                  @for($i = 0; $i < 5; $i++)
  
                      <a href="{{ url('/empregos/' . $LastJobs[$i]['slug']) }}" class="list-group-item list-group-item-action mb-3">
                          <div class="d-flex w-100 justify-content-between">
                            <p class="mb-1"><b>{{ $LastJobs[$i]['title'] }}</b></p>
                          </div>
                          <p class="mb-1">Empresa: {{ $LastJobs[$i]['company'] }}</p>
                          <small><i class="fa fa-map-marker"></i> Localização: <span>{{ $LastJobs[$i]['province'] }}</span></small>
                      </a>
  
                  @endfor
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- End -->

        <!-- Card de Últimos BlogPosts Widget -->
        <div class="card my-4">
          <h5 class="card-header">Últimas Notícias</h5>
          <div class="card-body">
            <div class="row">
              <div class="col-lg-12">
                <div class="list-group mb-3">
                  @for($i = 0; $i < 5; $i++)
  
                    <a href="{{ url('/articles/' . $LastArticles[$i]['slug']) }}" class="list-group-item list-group-item-action mb-3">
                      <div class="d-flex w-100 justify-content-between">
                      <p class="mb-1"><b>{{ $LastArticles[$i]['title'] }}</b></p>
                      </div>
                      <p class="mb-1">Publicado em: {{ date_format(new DateTime($LastArticles[$i]['created_at']), 'd-m-Y') }}</p>
                      <small><i class="fa fa-journal"></i>   <span> </span></small>
                    </a>
  
                  @endfor
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- End -->
        <!-- Anúncio Adaptável -->
        <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-2118765549976668"
            crossorigin="anonymous"></script>
        <!-- Anúncio Adaptável -->
        <ins class="adsbygoogle"
            style="display:block"
            data-ad-client="ca-pub-2118765549976668"
            data-ad-slot="8002595367"
            data-ad-format="auto"
            data-full-width-responsive="true"></ins>
        <script>
            (adsbygoogle = window.adsbygoogle || []).push({});
        </script>

      </div>

    </div>
    <!-- /.row -->

  </div>
  <!-- /.container -->

@endsection('content')

@section('footer-scripts')
<script src="https://cdn.jsdelivr.net/npm/tesseract.js@5/dist/tesseract.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.min.js"></script>
    <script>
        const fileInput = document.getElementById('fileInput');
        const output = document.getElementById('output');
        const convertBtn = document.getElementById('convertBtn');
        const languageSelect = document.getElementById('languageSelect');
        const pageSelector = document.getElementById('pageSelector');
        const pageNumber = document.getElementById('pageNumber');
        const copyBtn = document.getElementById('copyBtn');
        //add by EJ
        const desiredWidth = 1000;
        //*********
        let pdfDoc = null;
        let selectedPage = 1;

        fileInput.addEventListener('change', async (event) => {
            const file = event.target.files[0];
            pageSelector.style.display = 'none'; // Esconder a seleção de páginas inicialmente
            pageNumber.innerHTML = ''; // Limpar as opções de páginas

            if (file.type === 'application/pdf') {
                const fileReader = new FileReader();
                fileReader.onload = async function() {
                    const typedarray = new Uint8Array(this.result);
                    pdfDoc = await pdfjsLib.getDocument(typedarray).promise;
                    pageSelector.style.display = 'block';
                    for (let i = 1; i <= pdfDoc.numPages; i++) {
                        const option = document.createElement('option');
                        option.value = i;
                        option.textContent = `Página ${i}`;
                        pageNumber.appendChild(option);
                    }
                };
                fileReader.readAsArrayBuffer(file);
            } else {
                pdfDoc = null;
            }
        });

        pageNumber.addEventListener('change', (event) => {
            selectedPage = event.target.value;
        });

        convertBtn.addEventListener('click', async () => {
            output.value = "Reconhecendo...";
            if (pdfDoc) {
                const page = await pdfDoc.getPage(parseInt(selectedPage));
                const viewport = page.getViewport({ scale: 1 });
                const canvas = document.createElement('canvas');
                const context = canvas.getContext('2d');
                canvas.width = desiredWidth;
                canvas.height = (desiredWidth / viewport.width) * viewport.height;
                const renderContext = {
                    canvasContext: context,
                    viewport: page.getViewport({ scale: desiredWidth / viewport.width }),
                };
                await page.render(renderContext).promise;
                const dataUrl = canvas.toDataURL('image/jpeg', 0.8);
                performOCR(dataUrl);
            } else {
                const file = fileInput.files[0];
                if (file) {
                    const imageUrl = URL.createObjectURL(file);
                    performOCR(imageUrl);
                }
            }
        });

        async function performOCR(imageUrl) {
            const worker = await Tesseract.createWorker(languageSelect.value);
            const { data: { text } } = await worker.recognize(imageUrl);
            output.value = text;
            await worker.terminate();
        }

        copyBtn.addEventListener('click', () => {
            output.select();
            document.execCommand('copy');
        });
    </script>
@endsection
