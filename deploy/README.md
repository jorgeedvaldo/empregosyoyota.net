# Publicar na hospedagem

Aquilo que se fazia à mão a cada alteração:

1. `git push` para a `main`
2. entrar no cPanel, abrir o Terminal
3. `cd` para a pasta da aplicação e `git pull`

O passo 2 e o 3 passam a ser feitos pelo GitHub Actions, sozinho, sempre que a
`main` muda. É o mesmo `git pull`, na mesma pasta, com o mesmo utilizador — só
que pelo SSH em vez do Terminal do cPanel.

| Ficheiro | O que é |
| --- | --- |
| `.github/workflows/publicar.yml` | corre no GitHub: prepara a chave, liga-se por SSH |
| `deploy/publicar.sh` | corre no servidor: faz o `git pull` e diz como ficou |

O script não é copiado para o servidor — vai pelo canal do SSH e é lido do
stdin. Corre sempre a versão que está no commit que se está a publicar, nunca
uma cópia esquecida lá dentro.

## Preparar, uma vez

### 1. Ligar o acesso SSH na conta

No cPanel, **SSH Access**. Se o alojamento tiver o SSH desligado, é preciso
pedir ao suporte para o ligar — sem isso nada disto funciona. Aproveite para
confirmar a porta: a maior parte usa a 22, alguns usam outra.

### 2. Criar uma chave só para isto

No seu computador:

```bash
ssh-keygen -t ed25519 -N "" -f ~/.ssh/publicar_yoyota -C "github actions"
```

O `-N ""` é uma chave **sem palavra-passe**. Não é descuido: do lado do GitHub
não há ninguém para a escrever. Uma chave só para publicar, que se pode revogar
sozinha sem mexer nas outras.

### 3. Autorizar a chave no servidor

cPanel → **SSH Access** → **Manage SSH Keys** → **Import Key**. Cole o conteúdo
de `~/.ssh/publicar_yoyota.pub` (o `.pub`, o ficheiro que acaba em `.pub`) e, já
na lista, carregue em **Manage** → **Authorize**.

Uma chave importada e não autorizada é a causa mais comum de um
`Permission denied (publickey)` que parece não fazer sentido.

Confirme do seu computador antes de ir para o GitHub:

```bash
ssh -i ~/.ssh/publicar_yoyota utilizador@servidor.pt 'pwd && git --version'
```

### 4. Descobrir o caminho da aplicação

No Terminal do cPanel, na pasta onde costuma fazer o `git pull`:

```bash
pwd
```

O que sair daqui é o `DEPLOY_PATH`. Costuma ser algo como
`/home/utilizador/empregosyoyota.net`. Use o caminho absoluto, não o `~`.

### 5. Guardar a impressão digital do servidor (opcional, mas faça-o)

```bash
ssh-keyscan -p 22 servidor.pt
```

Cole as linhas todas no segredo `DEPLOY_KNOWN_HOSTS`. Para ter a certeza de que
o `ssh-keyscan` falou mesmo com o seu servidor e não com outra coisa qualquer,
compare a impressão digital com a que o próprio servidor diz, no Terminal do
cPanel:

```bash
ssh-keygen -lf /etc/ssh/ssh_host_ed25519_key.pub
```

Sem este segredo a publicação continua a funcionar, com um aviso: aceita-se o
servidor que responder. A chave privada continua a não sair do runner — o SSH
assina um desafio ligado à sessão, que um servidor falso não consegue reutilizar
no verdadeiro. O que se perde é a garantia de se ter falado com o seu servidor.

### 6. Pôr os segredos no GitHub

**Settings** → **Secrets and variables** → **Actions** → **New repository secret**.

| Segredo | Obrigatório | O que é |
| --- | --- | --- |
| `DEPLOY_SSH_KEY` | sim | a chave **privada** inteira (`~/.ssh/publicar_yoyota`), da linha `BEGIN` à linha `END` |
| `DEPLOY_HOST` | sim | `servidor.pt` ou o IP |
| `DEPLOY_USER` | sim | o utilizador do cPanel |
| `DEPLOY_PATH` | sim | o caminho do ponto 4 |
| `DEPLOY_PORT` | não | só se o SSH não estiver na 22 |
| `DEPLOY_KNOWN_HOSTS` | não | o do ponto 5 |

## Experimentar

**Actions** → **Publicar na hospedagem** → **Run workflow**. Serve para testar a
configuração sem ter de inventar um commit, e para repetir uma publicação que
tenha falhado.

A partir daqui, cada `push` para a `main` publica sozinho. Duas publicações ao
mesmo tempo não se atropelam: a segunda espera pela primeira.

## O que o `git pull` não faz

O mesmo que não fazia quando era à mão. O pull traz ficheiros; não instala
dependências, não corre migrações e não limpa caches.

Quando o que veio no pull precisar de um desses comandos, o registo do workflow
diz quais, no fim, por baixo de **A fazer à mão no Terminal do cPanel** — o
`vendor/` e o `.env` não estão no repositório, por isso um `composer.lock`
alterado sem `composer install` deixa o site em branco com um *class not found*.
Mais vale ler isso no registo do que descobri-lo pelo erro 500.

## Quando corre mal

O passo **Diagnóstico da ligação** só corre quando a publicação falha, e mostra
a ligação SSH em detalhe, que chave foi usada e se a porta sequer responde. Sem
isso o registo diria apenas `Connection closed`, sem se perceber em que ponto do
aperto de mão é que o servidor desistiu.

**`Permission denied (publickey)`** — a chave não está autorizada (ponto 3), ou
foi colada a pública em vez da privada no `DEPLOY_SSH_KEY`.

**A chave tem palavra-passe** — o workflow pára logo no início e diz isso. Gere
outra com `-N ""`.

**Porta fechada ou filtrada** — a firewall do alojamento está a bloquear os
endereços do GitHub. É um pedido para o suporte.

**O git ficou pendurado ou queixou-se de credenciais** — o repositório é privado
e a cópia do servidor não tem credenciais guardadas. No Terminal do cPanel o
utilizador e a senha eram escritos por si; pelo SSH não há ninguém para os
escrever, por isso o script falha de imediato em vez de ficar à espera. Resolve-se
pondo a origem com um token, no servidor:

```bash
git remote set-url origin https://UTILIZADOR:TOKEN@github.com/jorgeedvaldo/empregosyoyota.net.git
```

**`o servidor está à frente do GitHub`** — há commits feitos directamente na
cópia do servidor. Um `git pull` normal diria *Already up to date* e ficava tudo
verde, com o site a correr código que mais ninguém tem e que o próximo push
desfaz. O script pára e mostra quais são: leve-os para o repositório com um
`git push`, ou deite-os fora com `git reset --hard origin/main`.

**`as duas cópias divergiram`** — o mesmo, mas com alterações dos dois lados. O
script mostra o que há de cada lado e não mexe em nada: isto resolve-se à mão,
com calma, e não a meio de uma publicação automática.
