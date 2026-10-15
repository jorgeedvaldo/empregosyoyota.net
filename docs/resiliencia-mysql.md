# Quando o MySQL fica sem ligações

O alojamento permite **30 ligações simultâneas** a este utilizador do MySQL.
Ao chegar lá, o servidor recusa as seguintes e o Laravel atira:

```text
SQLSTATE[42000] [1226] User 'yqqnyhrgnt_edv' has exceeded the 'max_user_connections' resource (current value: 30)
```

Antes, isso era um 500 para quem estivesse a visitar o site. Agora não é: as
páginas públicas continuam a abrir com o conteúdo que já estava guardado, e só
quando não há mesmo nada guardado é que aparece uma página de indisponibilidade
— com 503, nunca com um erro técnico à vista.

O resto deste documento é o que foi feito, como se confirma que está a
funcionar, e como se desfaz.

## As três camadas

Vistas de fora para dentro, cada uma apanha o que a anterior deixou passar:

| Camada | Onde vive | O que faz |
| --- | --- | --- |
| HTML das páginas | `App\Services\PublicPageCache` + middleware | Com uma cópia válida, o pedido nem chega ao controlador — logo, não abre ligação nenhuma |
| Dados | `App\Services\DatabaseFallbackCache` | Guarda duas cópias de cada consulta: a normal e a de emergência |
| Disjuntor | `App\Services\DatabaseCircuitBreaker` | Depois de 3 falhas, ninguém mais tenta ligar-se durante 30 s |

E, por baixo de tudo, `public/database-unavailable.html`: ficheiro estático,
servido com 503, para quando não há cópia nenhuma da página pedida.

### A cópia de emergência

É a ideia central e a única coisa aqui que não é cache normal.

Cada valor é guardado **duas vezes**. A cópia normal é curta e serve o
dia-a-dia. A cópia de emergência dura sete dias, só é lida quando o MySQL não
responde, e **não é apagada quando se publica uma vaga** — se fosse, o site
ficava sem rede de segurança exactamente no momento com mais visitas, logo a
seguir a publicar.

Vive em `storage/framework/cache/resilience/`, numa pasta separada da cache
normal, para que um `php artisan cache:clear` não a leve à frente.

### O disjuntor

Um MySQL em baixo é pior do que parece: cada pedido que chega tenta abrir a sua
ligação, espera pelo erro e só depois desiste. Com o tecto nas 30, as
tentativas falhadas passam a ser parte do problema.

À terceira falha o circuito abre e, durante 30 segundos, nenhum pedido tenta
ligar-se — excepto **uma sonda de 5 em 5 segundos**, que serve para dar pela
recuperação. Quando a sonda passa, fecha-se tudo e a vida continua, sem
ninguém ter de reiniciar nada.

Enquanto o circuito está aberto, qualquer consulta falha de imediato
(`DB::beforeExecuting`), incluindo as que não passam pela cache — paginação,
pesquisa, painel. Na consola isto nunca se aplica, para o `artisan` continuar
utilizável durante uma falha.

## O que está protegido

Páginas públicas, com cópia de emergência dos dados **e** do HTML:

- `/` — página inicial
- `/empregos` e `/empregos/{slug}`
- `/articles` e `/articles/{slug}`
- `/{país}/empregos`, `/vagas-de-emprego-em-angola` e as landings de
  `config/landings.php`
- `/categories/{id}`
- `/modelos-de-curriculos` e `/modelos-de-curriculos/{slug}`
- `/about`, `/terms`, `/biblioteca-de-documentos-legais`
- `/feed` e os sitemaps

Fora, de propósito:

- `/pesquisar` — os resultados mudam com cada termo escrito; guardá-los enchia
  o disco de cópias que ninguém voltava a ler. Continua a aproveitar a cache de
  dados das categorias, mas numa falha responde 503.
- `/admin` e o painel Filament — tem dados de quem lá está dentro.
- `/api/*`.
- Qualquer pedido de uma sessão com login, qualquer coisa que não seja `GET`, e
  qualquer resposta que traga cookies ou um token CSRF.

## Operações que escrevem

Login, registo, recuperação de palavra-passe, publicação de conteúdos,
candidaturas, pagamentos, perfis, painel: **nada disto é fingido**. Com o MySQL
em baixo a resposta é 503 com `Retry-After`, para a pessoa voltar a tentar.
Nunca um sucesso inventado, nunca um formulário que parece ter sido gravado e
não foi.

## Tempos de cache

| O quê | Cópia normal | Cópia de emergência |
| --- | --- | --- |
| Últimas vagas, últimos artigos, categorias | 1440 s (24 min) | 7 dias |
| Vaga ou artigo individual | 1440 s (24 min) | 7 dias |
| Totais para a paginação | 1440 s (24 min) | 7 dias |
| Feed RSS | 1800 s (30 min) | 7 dias |
| Definições (`settings`) | 1440 s | 7 dias |
| HTML das páginas públicas | 600 s (10 min) | 7 dias |
| Sitemaps | 6 h (como antes) | — |

Os tempos das cópias normais são os que o site já usava: isto não mexeu na
frescura do conteúdo, só lhe acrescentou uma rede por baixo. Todos se mudam em
`config/resilience.php` ou pelo `.env`.

A invalidação continua a ser imediata: ao gravar uma vaga, artigo, categoria ou
currículo, as cópias normais caem e o HTML guardado passa a estar numa versão
antiga (a chave leva a versão dentro). As de emergência ficam.

## Alterações no `.env`

**Nenhuma é obrigatória.** O projecto já usava `CACHE_DRIVER=file` e
`SESSION_DRIVER=file`, que é o que esta solução precisa — nada depende do
MySQL para guardar cache ou sessões, e a configuração das filas não foi
tocada.

Confirme, no `.env` do servidor, que estas duas linhas estão assim:

```dotenv
CACHE_DRIVER=file
SESSION_DRIVER=file
```

> Em Laravel 9 a variável é `CACHE_DRIVER`. O `CACHE_STORE` só aparece no
> Laravel 11 — aqui não faz nada.

O resto é afinação opcional, com estes valores por omissão:

```dotenv
RESILIENCE_BREAKER=true              # disjuntor ligado
RESILIENCE_BREAKER_FAILURES=3        # falhas até abrir o circuito
RESILIENCE_BREAKER_COOLDOWN=30       # segundos de arrefecimento
RESILIENCE_PAGE_CACHE=true           # cache de HTML das páginas públicas
RESILIENCE_PAGE_TTL=600              # 10 minutos
RESILIENCE_EMERGENCY_TTL=604800      # 7 dias
RESILIENCE_LITESPEED_HEADER=false    # ver a secção do LiteSpeed
```

## Depois de publicar

O `git pull` traz ficheiros e mais nada. Esta alteração traz um **provider**
(`config/app.php`) e um **ficheiro de configuração novo**, por isso, no
Terminal do cPanel, uma vez:

```bash
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

Se a configuração estiver em cache (`php artisan config:cache`), **sem o
`config:clear` nada disto entra em funcionamento** — o `config/resilience.php`
novo nem sequer é lido.

### Índices (opcional, mas recomendado)

Há uma migração que acrescenta índices a `jobs.country_id`, `jobs.slug`,
`articles.country_id`, `articles.slug` e `curriculos.slug` — as colunas por que
as páginas públicas filtram a toda a hora. Uma consulta mais rápida segura a
ligação menos tempo, e são menos ligações a acumular-se.

```bash
php artisan migrate
```

A migração é defensiva: salta a tabela que não existir, a coluna que não
existir e o índice que já lá estiver. Se preferir não correr migrações no
servidor, nada do resto deixa de funcionar — fica só mais lento do que podia.

### Cron (opcional, mas recomendado)

A rede de segurança só apanha o que já lá estava: uma página que nunca foi
visitada não tem cópia para servir. Com o agendador do Laravel a correr, o
essencial está sempre guardado antes de ser preciso.

Em cPanel → **Cron Jobs**, de minuto a minuto:

```
* * * * * cd /home/UTILIZADOR/empregosyoyota.net && php artisan schedule:run >> /dev/null 2>&1
```

Ficam agendados:

- `resilience:warm --pages`, de 30 em 30 minutos — actualiza as cópias;
- `resilience:prune`, às 04:10 — apaga o que expirou e mantém a pasta abaixo
  de 64 MB.

Os dois com `withoutOverlapping()`, que aqui conta mais do que o costume: uma
execução que demore mais do que o intervalo não pode acumular cópias a correr
ao mesmo tempo, porque cada uma ocupa ligações das 30. O bloqueio vive na cache
de ficheiros, não no MySQL, portanto funciona durante uma falha.

Sem cron nada se parte: as cópias passam a encher-se com as visitas reais.

## Confirmar que está a funcionar

### Pelo terminal

```bash
php artisan resilience:status
```

Mostra se o MySQL responde e em quanto tempo, o estado do disjuntor, que chaves
têm cópia normal e cópia de emergência, e o tamanho da pasta. A coluna **cópia
de emergência** é a que interessa: com `NAO` em todas, uma falha agora não tem
nada para servir — corra `php artisan resilience:warm --pages`.

### Pelo browser

As respostas trazem um cabeçalho:

```bash
curl -sI https://empregosyoyota.net/ | grep -i x-resilience
```

| Valor | O que diz |
| --- | --- |
| (ausente) | Página gerada agora, com o MySQL a responder |
| `X-Resilience-Cache: hit` | Servida da cópia guardada, sem tocar na base de dados |
| `X-Resilience-Cache: emergency` | MySQL em baixo; é a última cópia válida |
| `X-Resilience-Cache: offline` | Não havia cópia; é a página de indisponibilidade (503) |

Visite a mesma página duas vezes seguidas: a segunda deve trazer `hit`.

### No log

Durante uma falha, `storage/logs/laravel.log` leva linhas `[resiliencia]`, no
máximo uma por minuto para não encher o disco:

```bash
grep resiliencia storage/logs/laravel.log | tail
```

## LiteSpeed

O alojamento corre LiteSpeed, que sabe servir páginas de uma cache própria sem
sequer acordar o PHP. Isso depende do módulo **LSCache** estar ligado na conta
— coisa que não se consegue confirmar a partir do código, e que pode não estar.

Por isso, a cache de HTML descrita aqui é **toda do lado do Laravel** e não
depende de nada disso. O suporte ao LSCache está preparado mas desligado:

```dotenv
RESILIENCE_LITESPEED_HEADER=true
```

Com isto, as páginas públicas passam a sair com
`X-LiteSpeed-Cache-Control: public,max-age=600`. Se o módulo estiver ligado,
o LiteSpeed guarda-as; se não estiver, o cabeçalho é ignorado e nada muda.

Antes de ligar, confirme com o suporte da Spaceship que o LSCache está activo
na conta. E note que a partir daí há **duas** caches à frente do site: uma
página errada guardada pelo LiteSpeed sai de lá com `/usr/local/lsws/admin` ou
com um restart, não com `php artisan cache:clear`.

## Limitações do alojamento

- **As 30 ligações não aumentam.** Isto não contorna o limite: reduz a
  frequência com que se lá chega e evita que o site caia quando se lá chega.
- **Sem Redis nem memcached** garantidos num alojamento partilhado. A cache é
  de ficheiros, que no cPanel é o que há — mais lenta do que memória, e
  partilhada entre processos PHP, que é o que aqui interessa.
- **Sem acesso root**: nada disto mexe em configurações globais, nem reinicia
  processos, nem toca em ficheiros de outros sites.
- **O `add()` da cache de ficheiros não é perfeitamente atómico.** Na pior das
  hipóteses passam duas sondas de recuperação em vez de uma — o que é
  inofensivo, e muito longe das 30.
- **A pesquisa não tem rede de segurança.** Durante uma falha responde 503.
  Depende do índice TNTSearch e da base de dados, e guardar resultados por
  termo não era comportável.
- **Cópias de emergência ocupam disco.** O `resilience:prune` mantém a pasta
  abaixo de 64 MB (`--max-mb` muda o limite).

## Desfazer

Por ordem, do menos ao mais drástico.

**Desligar só a cache de HTML** (o fallback de dados continua):

```dotenv
RESILIENCE_PAGE_CACHE=false
```

**Desligar o disjuntor** (volta a tentar-se a ligação em todos os pedidos):

```dotenv
RESILIENCE_BREAKER=false
```

Depois de qualquer um dos dois: `php artisan config:clear`.

**Repor o disjuntor à mão**, se ficar aberto e se quiser forçar uma tentativa:

```bash
php artisan resilience:status --reset
```

**Reverter tudo**, por git:

```bash
git revert <commit>
php artisan config:clear
```

Os índices da migração ficam — não fazem mal nenhum e não dependem de nada
disto. Para os tirar também: `php artisan migrate:rollback --step=1`.

## Os ficheiros

| Ficheiro | O que é |
| --- | --- |
| `config/resilience.php` | Todos os valores afináveis |
| `config/cache.php` | Store `resilience` (pasta própria, à prova de `cache:clear`) |
| `app/Support/DatabaseAvailability.php` | Distingue "MySQL em baixo" de "erro nosso" |
| `app/Exceptions/DatabaseUnavailableException.php` | A excepção que o Handler reconhece |
| `app/Services/DatabaseCircuitBreaker.php` | O disjuntor |
| `app/Services/DatabaseFallbackCache.php` | Cache de dados com cópia de emergência |
| `app/Services/PublicPageCache.php` | Cache de HTML das páginas públicas |
| `app/Services/DatabaseUnavailableResponder.php` | O que responder numa falha |
| `app/Http/Middleware/ServePublicPageFromCache.php` | Serve e guarda o HTML |
| `app/Providers/ResilienceServiceProvider.php` | Liga as peças; trava consultas com o circuito aberto |
| `app/Console/Commands/WarmResilienceCache.php` | `resilience:warm` |
| `app/Console/Commands/PruneResilienceCache.php` | `resilience:prune` |
| `app/Console/Commands/ResilienceStatus.php` | `resilience:status` |
| `public/database-unavailable.html` | A página de 503 |
