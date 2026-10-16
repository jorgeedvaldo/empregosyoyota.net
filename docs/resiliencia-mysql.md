# Quando o MySQL fica sem ligações

O alojamento permite **30 ligações simultâneas** a este utilizador do MySQL.
Ao chegar lá, o servidor recusa as seguintes e o Laravel atira:

```text
SQLSTATE[42000] [1226] User 'yqqnyhrgnt_edv' has exceeded the 'max_user_connections' resource (current value: 30)
```

Antes, isso era um 500 para quem estivesse a visitar o site. Agora não é: as
páginas públicas continuam a abrir com o conteúdo que já existia, actualizado
**em tempo real** a cada vaga ou artigo publicado — sem Cron Job nenhum. Só
quando não há mesmo nada guardado é que aparece uma página de
indisponibilidade, com 503, nunca com um erro técnico à vista.

## Histórico: duas versões deste mecanismo

A primeira versão (ainda visível no histórico do git) guardava o HTML já
renderizado de cada página pública visitada, num ficheiro por página. Em
produção isso cresceu para **~67 mil ficheiros, ~42 mil directórios e 3,6 GB**
em `storage/framework/cache/resilience` — um problema de **inodes**, não só de
espaço: um alojamento partilhado tem um tecto de inodes separado do tecto de
espaço em disco, e esse mecanismo não tinha limite nenhum sem um Cron Job a
limpar o que sobrava.

Esta versão substitui-o por **um único ficheiro**, que guarda só os *dados*
(não o HTML) das vagas e artigos mais recentes, e que se corrige sozinho a
cada escrita — sem crescer com o tempo, sem depender de Cron Job nenhum para
não encher a conta de inodes.

## As três camadas

Vistas de fora para dentro, cada uma apanha o que a anterior deixou passar:

| Camada | Onde vive | O que faz |
| --- | --- | --- |
| Dados: listas e contagens | `App\Services\DatabaseFallbackCache` | Um punhado de chaves fixas (`latest_jobs_50`, `categories_all`, …), cada uma com cópia normal + cópia de emergência |
| Dados: uma vaga/artigo | `App\Services\EmergencySnapshotService` | **Um único ficheiro**, actualizado a cada escrita, com as N vagas/artigos mais recentes |
| Disjuntor | `App\Services\DatabaseCircuitBreaker` | Depois de 3 falhas, ninguém mais tenta ligar-se durante 30 s |

E, por baixo de tudo, `public/database-unavailable.html`: ficheiro estático,
servido com 503, para quando não há cópia nenhuma do que foi pedido.

### O snapshot único (`EmergencySnapshotService`)

Dois caminhos fixos, que nunca mudam de nome:

```text
storage/app/emergency/public_backup.json.gz   (o próprio snapshot)
storage/app/emergency/public_backup.lock      (o bloqueio exclusivo)
```

Guarda as **N vagas e M artigos mais recentes** (por omissão 300 e 200 — ver
`config/resilience.php`), mais a lista de categorias. É limitado de propósito:
um site com 8 anos de conteúdo não faz o ficheiro crescer sem parar, porque ao
ultrapassar o limite a entrada *mais antiga* cai (por id, não por data de
acesso). Ao fim de muitos anos de conteúdo, o ficheiro continua do mesmo
tamanho.

Cálculo do tamanho esperado: uma descrição típica ocupa 2-5 KB; com 300 vagas
+ 200 artigos o JSON bruto anda pelos poucos MB, e o gzip (texto HTML
repetitivo) costuma reduzir isso para algumas centenas de KB — perfeitamente
seguro de carregar inteiro para memória num pedido PHP num alojamento
partilhado.

**Cada vaga ou artigo gravado corrige só a sua própria entrada** — nunca
reconstrói a lista toda, e nunca volta a consultar o MySQL para o fazer (usa
os dados do próprio modelo que acabou de gravar). A escrita é atómica:
ficheiro temporário + `rename()`, que no mesmo sistema de ficheiros é
indivisível — quem estiver a ler nunca vê uma escrita a meio, e se a escrita
falhar a meio (disco cheio, por exemplo), a cópia anterior fica intacta.

Duas escritas em simultâneo (dois pedidos a gravar vagas ao mesmo tempo) são
evitadas com `flock()` **não bloqueante**: quem não consegue o bloqueio não
espera — põe a sua alteração numa fila curta de "pendentes" (um valor na cache
por omissão, não um ficheiro por alteração) e sai; a próxima escrita bem
sucedida aplica as alterações pendentes primeiro, antes da sua própria. Nada
se perde, só se adia.

### A cache de dados (listas e contagens)

`DatabaseFallbackCache` continua a funcionar como antes, para um punhado fixo
de chaves: as últimas 50 vagas/artigos, as categorias, as contagens da
paginação, o feed RSS, as definições (`settings`). Cada uma tem cópia normal
(curta, invalidada a cada escrita) e cópia de emergência (longa, só lida
quando o MySQL falha). Isto nunca foi o que causava o problema de inodes — são
sempre as mesmas ~10 chaves, não importa quantas vagas o site tenha.

O que **mudou**: já não há cache nenhuma à frente da leitura de uma vaga ou
artigo *individual* (`job_id_X`, `job_slug_X`, …). Essas consultas vão sempre
directas ao MySQL — ele continua a ser a fonte principal — e só recorrem ao
snapshot se a consulta falhar. Isto elimina outra fonte de crescimento sem
limite (um ficheiro por vaga/artigo alguma vez visitado) e torna as leituras
individuais verdadeiramente em tempo real.

### O disjuntor

Um MySQL em baixo é pior do que parece: cada pedido que chega tenta abrir a
sua ligação, espera pelo erro e só depois desiste. Com o tecto nas 30, as
tentativas falhadas passam a ser parte do problema.

À terceira falha o circuito abre e, durante 30 segundos, nenhum pedido tenta
ligar-se — excepto **uma sonda de 5 em 5 segundos**, que serve para dar pela
recuperação. Quando a sonda passa, fecha-se tudo e a vida continua, sem
ninguém ter de reiniciar nada.

Enquanto o circuito está aberto, qualquer consulta falha de imediato
(`DB::beforeExecuting`), incluindo as que não passam por nenhuma cache —
paginação, pesquisa, painel. Na consola isto nunca se aplica, para o
`artisan` continuar utilizável durante uma falha.

## Sincronização em tempo real, sem Cron Job

A cada vaga, artigo ou categoria **gravado ou apagado**:

1. Confirma-se primeiro o commit no MySQL (`DB::afterCommit` — executa de
   imediato se não houver transacção aberta, ou só depois do commit se
   houver; **um rollback nunca chega a sincronizar nada**).
2. Invalida-se a cópia normal da cache de listas/contagens.
3. Pede-se a sincronização do snapshot com **essa única entrada** (sem
   reconstruir a lista toda, sem nova consulta ao MySQL).
4. Adquire-se um bloqueio não bloqueante.
5. Actualiza-se só os dados necessários.
6. Substitui-se a cópia por inteiro, de forma atómica.
7. Liberta-se o bloqueio.

Se o bloqueio estiver ocupado, a alteração fica pendente (passo 3) e aplica-se
sozinha na próxima escrita bem sucedida — nunca se perde, só se adia.

Isto cobre as duas vias reais de escrita no projecto: o painel Filament
(`JobResource`, `ArticleResource`, `CategoryResource` — `save()`/`delete()`
normais do Eloquent) e a API (`api/ArticleController::store()`, que também
usa `save()`). Não há, hoje, nenhuma escrita por `DB::table(...)->update()`
ou `Model::insert()` que contorne os eventos do Eloquent — se um dia
aparecer uma, essa escrita específica não vai invalidar a cache nem
sincronizar o snapshot sozinha (ver secção "Limitações").

## O que está protegido

Páginas públicas com fonte de dados resiliente (cache de listas + snapshot):

- `/` — página inicial
- `/empregos` e `/empregos/{slug}` (vagas individuais)
- `/articles` e `/articles/{slug}` (artigos individuais)
- `/{país}/empregos` (listagem; usa a cache de categorias)

Fora, de propósito:

- `/pesquisar` — os resultados mudam com cada termo escrito; guardá-los não
  seria comportável. Continua a aproveitar a cache de categorias, mas numa
  falha responde 503.
- `/categories/{id}` — não tem cópia de emergência própria (listagem de
  vagas por categoria); numa falha responde 503.
- `/modelos-de-curriculos`, `/biblioteca-de-documentos-legais`, `/about`,
  `/terms` — sem dados dinâmicos relevantes ao MySQL além do que já está
  nas cache de listas; numa falha de MySQL respondem 503.
- `/admin` e o painel Filament, `/api/*` — nunca passam por aqui.

## Operações que escrevem

Login, registo, recuperação de palavra-passe, publicação de conteúdos,
candidaturas, pagamentos, perfis, painel: **nada disto é fingido**. Com o
MySQL em baixo a resposta é 503 com `Retry-After`, para a pessoa voltar a
tentar. Nunca um sucesso inventado, nunca um formulário que parece ter sido
gravado e não foi.

## Tempos de cache

| O quê | Cópia normal | Cópia de emergência |
| --- | --- | --- |
| Últimas vagas, últimos artigos, categorias, contagens | 1440 s (24 min) | 7 dias |
| Feed RSS | 1800 s (30 min) | 7 dias |
| Definições (`settings`) | 30 dias | 7 dias |
| Vaga/artigo individual | (sem cache — MySQL directo) | no snapshot, actualizado em tempo real |

O snapshot não tem "TTL" no sentido normal: não expira por tempo, é
actualizado **a cada escrita** e só se torna desactualizado se uma vaga for
editada enquanto o MySQL está em baixo (impossível — se o MySQL está em
baixo, não há escrita nenhuma a acontecer).

## Alterações no `.env`

**Nenhuma é obrigatória.** O projecto já usava `CACHE_DRIVER=file` e
`SESSION_DRIVER=file`, que é o que esta solução precisa — nada depende do
MySQL para guardar cache ou sessões.

```dotenv
CACHE_DRIVER=file
SESSION_DRIVER=file
```

Afinação opcional, com estes valores por omissão:

```dotenv
RESILIENCE_BREAKER=true              # disjuntor ligado
RESILIENCE_BREAKER_FAILURES=3        # falhas até abrir o circuito
RESILIENCE_BREAKER_COOLDOWN=30       # segundos de arrefecimento
RESILIENCE_SNAPSHOT_MAX_JOBS=300     # quantas vagas o snapshot guarda
RESILIENCE_SNAPSHOT_MAX_ARTICLES=200 # quantos artigos o snapshot guarda
```

## Depois de publicar

O `git pull` traz ficheiros e mais nada. Esta alteração traz um **provider**
(`config/app.php`) e **configuração nova**, por isso, no Terminal do cPanel,
uma vez:

```bash
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

Se a configuração estiver em cache (`php artisan config:cache`), **sem o
`config:clear` nada disto entra em funcionamento**.

### Gerar a primeira cópia do snapshot (importante)

O snapshot começa **vazio**. Só passa a ter uma vaga ou artigo depois de essa
vaga/artigo ser gravado ou apagado pela aplicação (Filament, API) — conteúdo
que já existia antes desta alteração, que ninguém voltar a editar, não entra
sozinho. Para que a rede de segurança cubra também o conteúdo existente desde
o primeiro dia:

```bash
php artisan resilience:snapshot:rebuild
```

Corre uma vez, a seguir a publicar. Lê o MySQL (as N vagas/artigos mais
recentes) e escreve a primeira cópia válida. Não é chamado automaticamente
por nada — nem Cron Job, nem Scheduler — e pode voltar a ser corrido à mão,
sem perigo nenhum, sempre que quiser confirmar que o snapshot está coerente
com o MySQL.

### Índices (opcional, mas recomendado)

Há uma migração (de uma alteração anterior) que acrescenta índices a
`jobs.country_id`, `jobs.slug`, `articles.country_id`, `articles.slug` e
`curriculos.slug`. Não é obrigatória para nada do que está descrito aqui, mas
ajuda o MySQL a responder mais depressa, o que por si só reduz a frequência
com que se chega ao limite das 30 ligações.

```bash
php artisan migrate
```

A migração é defensiva: salta a tabela, a coluna ou o índice que já existir.

### Nenhum Cron Job é necessário

Ao contrário da versão anterior deste mecanismo, **nada aqui depende de um
Cron Job**. A sincronização do snapshot acontece a cada escrita, em tempo
real; não há ficheiros por limpar porque não há crescimento sem limite para
limpar.

## Confirmar que está a funcionar

### Pelo terminal

```bash
php artisan resilience:status
```

Mostra se o MySQL responde e em quanto tempo, o estado do disjuntor, que
chaves de lista têm cópia normal e cópia de emergência, e o estado do
snapshot: se existe, se está íntegro, a idade, o tamanho e quantas
vagas/artigos/categorias guarda.

```text
Snapshot unico de emergencia (vagas, artigos, categorias)
  integro:    sim
  gerado em:  2026-10-16T14:32:05+00:00
  idade:      184 s
  tamanho:    612.4 KB
  vagas:      300
  artigos:    200
  categorias: 18
```

Se aparecer "nao existe ainda", corra `php artisan resilience:snapshot:rebuild`.

### Teste prático: publique uma vaga e confirme

1. Publique (ou edite) uma vaga no painel.
2. Corra `php artisan resilience:status` — o número de vagas no snapshot não
   muda necessariamente (o limite mantém-se fixo), mas a vaga está lá: para
   confirmar isso especificamente, use `php artisan tinker` e chame
   `app(\App\Services\EmergencySnapshotService::class)->findJob($slug)`.
3. A vaga já apareceu na página pública no mesmo instante em que gravou — sem
   esperar, sem warm, sem cron.

### No log

Durante uma falha, `storage/logs/laravel.log` leva linhas `[resiliencia]`, no
máximo uma por minuto para não encher o disco:

```bash
grep resiliencia storage/logs/laravel.log | tail
```

## Limitações do alojamento e do desenho

- **As 30 ligações não aumentam.** Isto não contorna o limite: reduz a
  frequência com que se lá chega e evita que o site caia quando se lá chega.
- **Sem Redis nem memcached** garantidos num alojamento partilhado. A cache de
  listas é de ficheiros; o snapshot é um único ficheiro próprio.
- **Sem acesso root**: nada disto mexe em configurações globais, nem
  reinicia processos, nem toca em ficheiros de outros sites.
- **O snapshot só cobre as N vagas/artigos mais recentes.** Uma vaga muito
  antiga, fora do limite configurado, não tem cópia de emergência — numa
  falha do MySQL, a sua página individual responde 503 em vez de 200. Para a
  grande maioria do tráfego (conteúdo recente) isto não é problema; para
  aumentar a cobertura, suba `RESILIENCE_SNAPSHOT_MAX_JOBS`/`_ARTICLES` (à
  custa de mais memória por pedido a decodificar o JSON).
- **Conteúdo gravado por fora do Eloquent não sincroniza.** Uma eventual
  escrita futura via `DB::table('jobs')->update(...)` ou `Model::insert()`
  em massa não dispara os eventos `saved`/`deleted`, logo não invalida a
  cache nem actualiza o snapshot para essas linhas. Hoje (nesta auditoria) não
  existe nenhuma escrita assim no projecto — só `save()`/`delete()` normais,
  pelo painel Filament e pela API.
- **A fila de alterações pendentes tem uma janela de corrida muito estreita**
  entre duas chamadas concorrentes a `enqueuePending()` quando o bloqueio
  principal já está ocupado por uma terceira escrita — na pior das
  hipóteses, uma das duas pode perder a alteração da outra. Preferiu-se isto
  a bloquear um pedido web indefinidamente à espera de outro processo.
- **A pesquisa, `/categories/{id}` e as páginas de conteúdo estático não têm
  rede de segurança própria.** Durante uma falha, respondem 503.

## Desfazer

**Desligar só o disjuntor** (volta a tentar-se a ligação em todos os
pedidos; a cache de dados e o snapshot continuam a funcionar):

```dotenv
RESILIENCE_BREAKER=false
```

Depois: `php artisan config:clear`.

**Repor o disjuntor à mão**, se ficar aberto e quiser forçar uma tentativa:

```bash
php artisan resilience:status --reset
```

**Reverter tudo**, por git:

```bash
git revert <commit>
php artisan config:clear
```

O snapshot (`storage/app/emergency/`) e a cache de listas
(`storage/framework/cache/resilience/`) não estão versionados (ver
`.gitignore`); podem ser apagados à mão sem afectar o repositório, se alguma
vez for preciso.

## Os ficheiros

| Ficheiro | O que é |
| --- | --- |
| `config/resilience.php` | Todos os valores afináveis |
| `config/cache.php` | Store `resilience` (pasta própria, à prova de `cache:clear`) |
| `app/Support/DatabaseAvailability.php` | Distingue "MySQL em baixo" de "erro nosso" |
| `app/Exceptions/DatabaseUnavailableException.php` | A excepção que o Handler reconhece |
| `app/Services/DatabaseCircuitBreaker.php` | O disjuntor |
| `app/Services/DatabaseFallbackCache.php` | Cache de listas/contagens com cópia de emergência |
| `app/Services/EmergencySnapshotService.php` | O snapshot único de vagas/artigos/categorias |
| `app/Services/DatabaseUnavailableResponder.php` | O que responder numa falha (503, ou JSON) |
| `app/Providers/ResilienceServiceProvider.php` | Liga as peças; trava consultas com o circuito aberto |
| `app/Console/Commands/RebuildEmergencySnapshot.php` | `resilience:snapshot:rebuild` (manual) |
| `app/Console/Commands/ResilienceStatus.php` | `resilience:status` |
| `app/Console/Commands/CleanupLegacyResilienceCache.php` | Relata/remove os ficheiros do mecanismo antigo |
| `public/database-unavailable.html` | A página de 503 |
