<?php

namespace App\Http\Controllers;

use App\Models\Job;
use Illuminate\Http\Request;

class LandingController extends Controller
{
    /**
     * Renderiza uma landing SEO de vagas a partir do registo config/landings.php.
     */
    public function show(string $key)
    {
        $cfg = config('landings.' . $key);
        abort_if(!$cfg, 404);

        $location   = $cfg['name'];
        $type       = $cfg['type'];
        $exploreUrl = url($cfg['explore']);
        $pageUrl    = url($cfg['slug']);

        // Ultimas vagas do local
        $query = Job::where('country_id', $cfg['country_id']);
        if ($type === 'city' && !empty($cfg['province'])) {
            $query->where('province', 'LIKE', '%' . $cfg['province'] . '%');
        }
        $listaJobs = $query->orderByRaw('id DESC')->limit(30)->get();

        // Helper para gerar links de pesquisa
        $s = fn ($q) => route('search', ['query' => $q]);

        $relacionadas = $this->relacionadas($location);
        $filtros      = $this->filtros($location, $exploreUrl, $s);
        $faqs         = $this->faqs($location);

        // Interligacao de cidades/provincias (nas paginas de pais)
        $cidadesLinks = [];
        if ($type === 'country') {
            foreach (config('landings') as $c) {
                if (($c['type'] ?? null) === 'city' && ($c['country_id'] ?? null) == $cfg['country_id']) {
                    $cidadesLinks[] = ['name' => $c['name'], 'url' => url($c['slug'])];
                }
            }
        }

        // Botoes do cabecalho
        $buttons = [
            ['label' => 'Explorar vagas em ' . $location, 'url' => $exploreUrl],
            ['label' => 'Ver vagas por categoria', 'url' => url('/empregos')],
        ];
        if (!empty($cidadesLinks)) {
            $buttons[] = ['label' => 'Ver vagas por cidade', 'url' => $pageUrl . '#cidades'];
        }

        // Texto introdutorio
        if ($type === 'country') {
            $intro = "Esta é a página principal de vagas de emprego em {$location}. Aqui encontra oportunidades "
                . "atualizadas em todo o país, com atalhos para vagas de entrada, júnior e estágio, além de buscas "
                . "amplas como trabalho, recrutamento, jobs, CV e oportunidades remotas.";
        } else {
            $intro = "Procura vagas de emprego em {$location}? Reunimos as oportunidades de trabalho mais recentes em "
                . "{$location}, incluindo vagas para iniciantes, estágios, primeiro emprego e oportunidades remotas e "
                . "presenciais. Consulte e candidate-se gratuitamente.";
        }

        $metaTitle       = 'Vagas de Emprego em ' . $location;
        $metaDescription = "Vagas de emprego em {$location} atualizadas: oportunidades, estágios, primeiro emprego, "
            . "recrutamento e trabalho remoto. Encontre e candidate-se gratuitamente na Empregos Yoyota.";
        $breadcrumbName  = 'Vagas de Emprego em ' . $location;

        return view('landing-vagas', compact(
            'location', 'pageUrl', 'metaTitle', 'metaDescription', 'breadcrumbName',
            'intro', 'buttons', 'filtros', 'relacionadas', 'cidadesLinks',
            'listaJobs', 'exploreUrl', 'faqs'
        ));
    }

    private function relacionadas(string $loc): array
    {
        $base = ['emprego', 'trabalho', 'recrutamento', 'jobs', 'vagas', 'vagas de emprego', 'cv', 'curriculo'];

        $locTerms = [
            "vagas de emprego em {$loc}", "vaga de emprego em {$loc}", "empregos em {$loc}",
            "trabalho em {$loc}", "oportunidades de trabalho em {$loc}", "recrutamento em {$loc}",
            "empresas que contratam em {$loc}", "vagas abertas em {$loc}", "vagas atualizadas em {$loc}",
            "vagas para iniciantes em {$loc}", "vagas sem experiencia em {$loc}", "primeiro emprego em {$loc}",
            "emprego para jovens em {$loc}", "vagas junior em {$loc}", "estagios em {$loc}",
            "estagios remunerados em {$loc}", "vagas part time em {$loc}", "vagas tempo integral em {$loc}",
            "vagas remotas em {$loc}", "trabalho remoto em {$loc}", "vagas presenciais em {$loc}",
        ];

        return array_merge($base, $locTerms);
    }

    private function filtros(string $loc, string $exploreUrl, callable $s): array
    {
        return [
            ['Pesquisa geral', "Vagas de emprego em {$loc} hoje", $exploreUrl],
            ['Nível júnior e primeira experiência', "Vagas para iniciantes em {$loc}", $s('iniciante')],
            ['Entrada no mercado de trabalho', "Vagas sem experiência em {$loc}", $s('sem experiência')],
            ['Oportunidades para estudantes', "Estágios em {$loc}", $s('estágio')],
            ['Trabalho à distância', "Vagas remotas em {$loc}", $s('remoto')],
            ['Vagas com processos ativos', "Recrutamento em {$loc}", $s('recrutamento')],
            ['Pesquisa ampla sem filtro', "Trabalho em {$loc}", $exploreUrl],
            ['Guia de currículo e candidatura', "CV para emprego em {$loc}", $s('CV')],
        ];
    }

    private function faqs(string $loc): array
    {
        return [
            [
                'q' => "Como encontrar vagas de emprego em {$loc}?",
                'a' => "Na Empregos Yoyota encontra vagas de emprego em {$loc} atualizadas regularmente. Pode explorar todas as oportunidades, filtrar por categoria ou pesquisar por cargo e empresa.",
            ],
            [
                'q' => "As vagas de emprego em {$loc} são gratuitas?",
                'a' => "Sim. Consultar vagas e candidatar-se através da Empregos Yoyota é totalmente gratuito. Nunca é pedido qualquer pagamento para se candidatar a uma vaga.",
            ],
            [
                'q' => "Existem vagas para quem não tem experiência em {$loc}?",
                'a' => "Sim. Publicamos regularmente vagas para iniciantes, primeiro emprego e estágios em {$loc}, ideais para quem está a entrar no mercado de trabalho.",
            ],
            [
                'q' => "Há vagas remotas em {$loc}?",
                'a' => "Sim. Além de vagas presenciais, divulgamos oportunidades de trabalho remoto e híbrido que podem ser realizadas a partir de {$loc}.",
            ],
            [
                'q' => "Com que frequência as vagas em {$loc} são atualizadas?",
                'a' => "As vagas são atualizadas com frequência. Recomendamos visitar a página regularmente ou instalar a nossa aplicação na Google Play para receber as novidades no telemóvel.",
            ],
        ];
    }
}
