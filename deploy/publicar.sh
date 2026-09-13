#!/usr/bin/env bash
#
# Faz o git pull na cópia que está na hospedagem — o mesmo que se faria à mão no
# Terminal do cPanel, sem mais nada. Não instala dependências, não corre
# migrações e não mexe em caches: isso, quando for preciso, continua a ser um
# passo consciente de quem publica.
#
# O GitHub Actions não copia este ficheiro para o servidor: manda-o pelo canal do
# SSH e o servidor lê-o do stdin (ver .github/workflows/publicar.yml), por isso
# corre sempre a versão que está no commit que se está a publicar.
#
# À mão, no Terminal do cPanel:
#
#   cd ~/caminho/da/aplicacao && APP_DIR="$PWD" bash deploy/publicar.sh
#
set -euo pipefail

APP_DIR="${APP_DIR:-}"
DEPLOY_COMMIT="${DEPLOY_COMMIT:-}"

# Do outro lado não há terminal nenhum. Sem isto, um repositório privado sem
# credenciais guardadas não falhava: ficava à espera de um utilizador e senha que
# ninguém ia escrever, até ao timeout do workflow.
export GIT_TERMINAL_PROMPT=0
export GIT_PAGER=cat

titulo() { printf '\n==> %s\n' "$1"; }
aviso()  { printf 'AVISO: %s\n' "$1"; }
erro()   { printf 'ERRO: %s\n' "$1" >&2; exit 1; }

# ---------------------------------------------------------------------------
# Onde estamos
# ---------------------------------------------------------------------------
titulo "Pasta da aplicação"

[ -n "$APP_DIR" ] || erro "falta APP_DIR (o segredo DEPLOY_PATH)."
cd "$APP_DIR" 2>/dev/null || erro "a pasta '$APP_DIR' não existe ou não é acessível a este utilizador."

[ -d .git ] || erro "'$APP_DIR' não é um repositório git. Em cPanel, crie-o em Git Version Control."
[ -f artisan ] || erro "'$APP_DIR' não parece a raiz da aplicação (não há ficheiro artisan)."

echo "$PWD"

# ---------------------------------------------------------------------------
# Como estava antes
# ---------------------------------------------------------------------------
titulo "Antes"

RAMO="$(git rev-parse --abbrev-ref HEAD)"
[ "$RAMO" != "HEAD" ] || erro "o repositório está com o HEAD destacado. Ponha-o num ramo: git checkout main"

ANTES="$(git rev-parse HEAD)"
echo "ramo:   $RAMO"
echo "commit: $ANTES"

# Ficheiros seguidos pelo git que foram mexidos à mão no servidor. Não é motivo
# para parar, mas se o pull a seguir falhar é quase de certeza por causa destes.
SUJOS="$(git status --porcelain --untracked-files=no)"
if [ -n "$SUJOS" ]; then
  aviso "há alterações locais por commitar no servidor:"
  printf '%s\n' "$SUJOS"
fi

# ---------------------------------------------------------------------------
# O git pull
# ---------------------------------------------------------------------------
titulo "git pull"

git fetch origin "$RAMO"
ALVO="$(git rev-parse FETCH_HEAD)"

if [ "$ANTES" = "$ALVO" ]; then
  echo "nada a trazer — o servidor já está no $(git rev-parse --short HEAD)"

elif git merge-base --is-ancestor "$ANTES" "$ALVO"; then
  # O caso normal: o servidor está atrás e avança em linha recta. --ff-only para
  # a publicação nunca inventar um merge por sua conta.
  git merge --ff-only FETCH_HEAD

elif git merge-base --is-ancestor "$ALVO" "$ANTES"; then
  # O servidor está À FRENTE do GitHub. Um 'git pull' aqui diria "Already up to
  # date" e saía com sucesso — o workflow ficava verde enquanto o site continuava
  # a correr commits que mais ninguém tem, e que o próximo push pode desfazer.
  echo "commits que só existem no servidor:"
  git --no-pager log --oneline "$ALVO..$ANTES"
  erro "o servidor está à frente do GitHub. Traga estes commits para o repositório (git push) ou deite-os fora (git reset --hard origin/$RAMO) antes de voltar a publicar."

else
  echo "só no servidor:"
  git --no-pager log --oneline "$ALVO..$ANTES"
  echo "só no GitHub:"
  git --no-pager log --oneline "$ANTES..$ALVO"
  erro "as duas cópias divergiram e não dá para avançar em linha recta. Isto resolve-se à mão, com calma, e não a meio de uma publicação automática."
fi

DEPOIS="$(git rev-parse HEAD)"

# ---------------------------------------------------------------------------
# Como ficou
# ---------------------------------------------------------------------------
titulo "Depois"

if [ "$ANTES" = "$DEPOIS" ]; then
  echo "nada mudou"
else
  git --no-pager log --oneline "$ANTES..$DEPOIS"
fi

# O commit que o GitHub mandou publicar pode já não ser o topo, se entretanto
# entrou outro push. Só interessa saber se ficou mesmo lá dentro.
if [ -n "$DEPLOY_COMMIT" ] && ! git merge-base --is-ancestor "$DEPLOY_COMMIT" HEAD 2>/dev/null; then
  aviso "o commit $DEPLOY_COMMIT não está nesta cópia. O servidor pode estar a puxar de outro ramo ou de outro repositório."
fi

# ---------------------------------------------------------------------------
# O que o git pull não faz
# ---------------------------------------------------------------------------
# Um pull traz ficheiros e mais nada. Quando o que mudou precisa de um comando a
# seguir, é melhor lê-lo aqui do que descobri-lo pelo erro 500 no site.
if [ "$ANTES" != "$DEPOIS" ]; then
  ALTERADOS="$(git diff --name-only "$ANTES" "$DEPOIS")"
  mudou() { printf '%s\n' "$ALTERADOS" | grep -q "$1"; }

  FALTA=""
  # O vendor/ não vem no repositório: se as dependências mudaram e o composer não
  # correr, o site fica em branco com um "class not found".
  mudou '^composer\.lock$'        && FALTA="$FALTA\n  composer install --no-dev --optimize-autoloader"
  mudou '^database/migrations/'   && FALTA="$FALTA\n  php artisan migrate --force"
  mudou '^config/'                && FALTA="$FALTA\n  php artisan config:clear   (se o config estiver em cache)"

  if [ -n "$FALTA" ]; then
    titulo "A fazer à mão no Terminal do cPanel"
    # shellcheck disable=SC2059
    printf "$FALTA\n"
  fi
fi

titulo "Feito"
git --no-pager log -1 --format='%h %s (%an, %ar)'
