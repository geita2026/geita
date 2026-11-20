#!/usr/bin/env bash
#
# One-shot production deploy for the DITF 2026 Judging app.
#
#   Frontend : Next.js STATIC export (apps/web -> ./out) served by nginx at /award/
#   API      : Express + Prisma (apps/api) under pm2 on :4000, proxied at /award/api
#   Target   : https://tradefair.tantrade.go.tz/award/
#
# It prompts ONCE for the SSH password (no key needed) and reuses that one
# authenticated connection for every step (sshpass if installed, otherwise
# OpenSSH connection multiplexing). The password is never stored on disk.
#
# Usage:
#   ./deploy-prod.sh           # normal deploy
#   SEED=1 ./deploy-prod.sh    # first deploy — also seed rubrics/categories/users
#
# Override any of these by exporting before running:
SSH_USER="${SSH_USER:-tantrade-admin}"
SSH_HOST="${SSH_HOST:-10.1.79.139}"
DOMAIN="${DOMAIN:-tradefair.tantrade.go.tz}"
BASE_PATH="${BASE_PATH:-/award}"
SURVEY_PATH="${SURVEY_PATH:-/survey}"            # public feedback form (reuses the /award API)
APP_URL="${APP_URL:-https://tradefair.tantrade.go.tz}"
REMOTE_DIR="${REMOTE_DIR:-/var/www/tradefair.tantrade.go.tz/award-app}"   # API source on the server
DOCROOT="${DOCROOT:-}"                                                    # auto-detected if empty
SEED="${SEED:-0}"

set -euo pipefail
cd "$(dirname "$0")"
export PATH="/opt/homebrew/bin:/usr/local/bin:$PATH"   # find node/npm when run from a GUI shell

c_g=$'\033[0;32m'; c_b=$'\033[0;34m'; c_r=$'\033[0;31m'; c_y=$'\033[0;33m'; c_0=$'\033[0m'
info(){ echo "${c_b}▶ $*${c_0}"; }
ok(){   echo "${c_g}✓ $*${c_0}"; }
warn(){ echo "${c_y}! $*${c_0}"; }
die(){  echo "${c_r}✗ $*${c_0}" >&2; exit 1; }

command -v rsync >/dev/null || die "rsync is required on your Mac."
command -v ssh   >/dev/null || die "ssh is required on your Mac."
command -v npm   >/dev/null || die "node/npm not found on PATH."

# ============================================================================
# LOCAL SIDE — settle everything locally first; only touch prod once it's clean.
# ============================================================================

# ----------------------------------------------------------------------------
# 1) Local backend: install dependencies + settle Prisma migrations.
#    `migrate dev` applies any pending migrations to your local DB and, if
#    schema.prisma has changed, creates a new migration (it will ask you to name
#    it) — that file is then committed/synced and applied on prod via
#    `migrate deploy`. If this step fails, we stop before touching prod.
# ----------------------------------------------------------------------------
info "Installing dependencies (npm ci — all workspaces)"
npm ci
ok "Dependencies installed"

[ -f apps/api/.env ] || die "apps/api/.env not found — create it (with your local DATABASE_URL) before deploying."
info "Settling local backend migrations (apps/api)"
(
  cd apps/api
  npx prisma generate
  npx prisma migrate dev
)
ok "Local backend settled — migrations up to date"

# ----------------------------------------------------------------------------
# 2) Local frontend: build the static export (real prod env baked in).
# ----------------------------------------------------------------------------
info "Building static web export (basePath=${BASE_PATH})"
(
  cd apps/web
  export NEXT_PUBLIC_BASE_PATH="$BASE_PATH"
  export NEXT_PUBLIC_API_URL="${APP_URL}${BASE_PATH}/api"
  rm -rf .next out
  npm run build
)
[ -f apps/web/out/index.html ] || die "Static export failed — apps/web/out/index.html missing."
ok "Static export ready (apps/web/out)"

# Survey portal — static export at /survey, posting to the same /award API.
info "Building survey static export (basePath=${SURVEY_PATH})"
(
  cd apps/survey
  export NEXT_PUBLIC_BASE_PATH="$SURVEY_PATH"
  export NEXT_PUBLIC_API_URL="${APP_URL}${BASE_PATH}/api"
  rm -rf .next out
  npm run build
)
[ -f apps/survey/out/index.html ] || die "Survey export failed — apps/survey/out/index.html missing."
ok "Survey export ready (apps/survey/out)"

# ============================================================================
# PROD SIDE — local is clean; now connect and deploy.
# ============================================================================

# ----------------------------------------------------------------------------
# 3) Connect to prod — prompt for the SSH password ONCE, reuse for ssh/rsync.
# ----------------------------------------------------------------------------
info "Local side ready. Connecting to ${SSH_USER}@${SSH_HOST} to deploy…"
CTRL="$(mktemp -u "${TMPDIR:-/tmp}/ditf-ssh-XXXXXX")"
cleanup(){ ssh -o ControlPath="$CTRL" -O exit "${SSH_USER}@${SSH_HOST}" 2>/dev/null || true; rm -f "$CTRL"; }
trap cleanup EXIT

if command -v sshpass >/dev/null; then
  # sshpass present: read the password once and reuse it for every ssh/rsync.
  read -rsp "SSH password for ${SSH_USER}@${SSH_HOST}: " SSH_PW; echo
  [ -n "$SSH_PW" ] || die "No password entered."
  export SSHPASS="$SSH_PW"; unset SSH_PW
  SSH_OPTS=(-o StrictHostKeyChecking=accept-new)
  rsh()     { sshpass -e ssh    "${SSH_OPTS[@]}" "${SSH_USER}@${SSH_HOST}" "$@"; }
  rsh_tty() { sshpass -e ssh -t "${SSH_OPTS[@]}" "${SSH_USER}@${SSH_HOST}" "$@"; }
  RSYNC_E="sshpass -e ssh ${SSH_OPTS[*]}"
else
  # No sshpass: open ONE multiplexed master connection. ssh prompts for the password
  # exactly once here; every later ssh/rsync reuses the socket without re-auth.
  SSH_OPTS=(-o StrictHostKeyChecking=accept-new -o ControlMaster=auto -o ControlPath="$CTRL" -o ControlPersist=600)
  info "Enter the password for ${SSH_USER}@${SSH_HOST} when prompted (asked once):"
  ssh "${SSH_OPTS[@]}" "${SSH_USER}@${SSH_HOST}" true || die "SSH connection failed."
  rsh()     { ssh    "${SSH_OPTS[@]}" "${SSH_USER}@${SSH_HOST}" "$@"; }
  rsh_tty() { ssh -t "${SSH_OPTS[@]}" "${SSH_USER}@${SSH_HOST}" "$@"; }
  RSYNC_E="ssh ${SSH_OPTS[*]}"
fi

rsh true || die "Could not authenticate to ${SSH_USER}@${SSH_HOST}."
ok "SSH connection established"

# ----------------------------------------------------------------------------
# 4) Sync API source to the server (exclude build artifacts + secrets).
# ----------------------------------------------------------------------------
info "Syncing API source to ${SSH_USER}@${SSH_HOST}:${REMOTE_DIR}"
rsh "mkdir -p '$REMOTE_DIR'"
rsync -az --delete -e "$RSYNC_E" \
  --exclude '.git' --exclude 'node_modules' --exclude '.next' --exclude 'out' \
  --exclude 'dist' --exclude 'apps/web' --exclude 'apps/survey' \
  --exclude 'apps/api/.env' \
  ./ "${SSH_USER}@${SSH_HOST}:${REMOTE_DIR}/"
ok "API source synced"

# ----------------------------------------------------------------------------
# 5) Build + (re)start the API on the server.
# ----------------------------------------------------------------------------
info "Building API and (re)starting pm2 on the server"
set +e
rsh "SEED='$SEED' REMOTE_DIR='$REMOTE_DIR' bash -s" <<'REMOTE'
set -euo pipefail
cd "$REMOTE_DIR"
command -v node >/dev/null || { echo "✗ Node.js not installed on the server (need 18+)."; exit 10; }
command -v pm2  >/dev/null || { echo "▶ Installing pm2 globally"; npm install -g pm2; }

# First deploy: seed the API env from the template, then stop so creds can be filled.
if [ ! -f apps/api/.env ]; then
  cp apps/api/.env.production.example apps/api/.env
  echo "ENV_TEMPLATE_CREATED"
  exit 20
fi

echo "▶ Installing dependencies (npm ci)"
npm ci --include=dev

echo "▶ Prisma generate + migrate deploy"
npm run prisma:generate --workspace api
if ! npm run prisma:deploy --workspace api; then
  echo "MIGRATE_FAILED"
  exit 30
fi

echo "▶ Building API"
npm run build --workspace api

if [ "$SEED" = "1" ]; then
  echo "▶ Seeding rubrics / categories / users"
  npm run seed --workspace api
fi

echo "▶ pm2 startOrReload"
pm2 startOrReload ecosystem.config.cjs --update-env
pm2 save
echo "API_OK"
REMOTE
rc=$?
set -e
case "$rc" in
  0) ok "API built and running under pm2 (:4000)";;
  20) die "Created apps/api/.env on the server from the template.
   SSH in and fill the real values, then re-run this script:
     ssh ${SSH_USER}@${SSH_HOST}
     nano ${REMOTE_DIR}/apps/api/.env   # DATABASE_URL, JWT_SECRET, SAMS_DATABASE_URL, CORS_ORIGIN=${APP_URL}";;
  30) die "prisma migrate deploy failed — the app database/user likely doesn't exist yet.
   Create it ONCE on the server (as a MySQL root/admin user), matching DATABASE_URL in apps/api/.env:
     CREATE DATABASE ditf_judging_2026 CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
     CREATE USER 'ditf_user'@'127.0.0.1' IDENTIFIED BY 'a-strong-password';
     GRANT ALL PRIVILEGES ON ditf_judging_2026.* TO 'ditf_user'@'127.0.0.1';
     GRANT SELECT ON tradefair_live.* TO 'ditf_user'@'127.0.0.1';   -- read-only SAMS for sync
     FLUSH PRIVILEGES;
   Then re-run this script.";;
  *) die "Remote API build failed (exit $rc). See the output above.";;
esac

# ----------------------------------------------------------------------------
# 6) Deploy the static frontend into the nginx docroot at /award/.
# ----------------------------------------------------------------------------
info "Detecting nginx docroot"
if [ -z "$DOCROOT" ]; then
  DOCROOT="$(rsh "for d in /var/www/${DOMAIN} /var/www/${DOMAIN}/public /usr/share/nginx/html; do [ -d \"\$d\" ] && { echo \"\$d\"; break; }; done")"
fi
[ -n "$DOCROOT" ] || die "Could not find a docroot. Set DOCROOT=/path/to/docroot and re-run."
ok "Docroot: ${DOCROOT}  (static site -> ${DOCROOT}/award/)"

info "Uploading static export"
rsh "mkdir -p ~/.ditf-award-static"
rsync -az --delete -e "$RSYNC_E" apps/web/out/ "${SSH_USER}@${SSH_HOST}:~/.ditf-award-static/"
# Move into the docroot (sudo, since the docroot is usually root/www-data owned).
rsh_tty "sudo mkdir -p '${DOCROOT}/award' && sudo rsync -a --delete ~/.ditf-award-static/ '${DOCROOT}/award/' && rm -rf ~/.ditf-award-static && echo STATIC_DEPLOYED"
ok "Static frontend deployed to ${DOCROOT}/award/"

info "Uploading survey export"
rsh "mkdir -p ~/.ditf-survey-static"
rsync -az --delete -e "$RSYNC_E" apps/survey/out/ "${SSH_USER}@${SSH_HOST}:~/.ditf-survey-static/"
rsh_tty "sudo mkdir -p '${DOCROOT}/survey' && sudo rsync -a --delete ~/.ditf-survey-static/ '${DOCROOT}/survey/' && rm -rf ~/.ditf-survey-static && echo SURVEY_DEPLOYED"
ok "Survey portal deployed to ${DOCROOT}/survey/"

# ----------------------------------------------------------------------------
# 7) nginx: ensure /award/ + /award/api/ are configured (guarded, auto-rollback).
# ----------------------------------------------------------------------------
info "Checking nginx routing for ${BASE_PATH}"
if rsh "curl -sk -o /dev/null -w '%{http_code}' -H 'Host: ${DOMAIN}' https://localhost${BASE_PATH}/api/health" | grep -q '^200$'; then
  ok "nginx already proxies ${BASE_PATH}/api → API (skipping nginx edit)"
else
  warn "nginx not yet routing ${BASE_PATH}; attempting a guarded config insert"
  rsh_tty "DOMAIN='${DOMAIN}' bash -s" <<'REMOTE'
set -euo pipefail
BLOCKS=$(cat <<'NGINX'

    # --- DITF Judging app (/award) — inserted by deploy-prod.sh ---
    location /award/api/ {
        proxy_pass         http://127.0.0.1:4000/;
        proxy_http_version 1.1;
        proxy_set_header   Host              $host;
        proxy_set_header   X-Real-IP         $remote_addr;
        proxy_set_header   X-Forwarded-For   $proxy_add_x_forwarded_for;
        proxy_set_header   X-Forwarded-Proto $scheme;
    }
    location /award/ {
        index     index.html;
        try_files $uri $uri/ =404;
    }
NGINX
)
VHOST=$(sudo grep -rlE "server_name[^;]*${DOMAIN}" /etc/nginx/sites-enabled/ /etc/nginx/conf.d/ 2>/dev/null | head -1 || true)
[ -n "$VHOST" ] || { echo "VHOST_NOT_FOUND"; exit 41; }
if sudo grep -q "location /award/api/" "$VHOST"; then echo "ALREADY_PRESENT"; exit 0; fi

BAK="${VHOST}.bak.$(date +%s)"
sudo cp "$VHOST" "$BAK"
echo "▶ vhost: $VHOST  (backup: $BAK)"

# Insert the blocks right after the (first) matching server_name line — always inside server{}.
TMP=$(mktemp)
sudo awk -v ins="$BLOCKS" -v dom="$DOMAIN" '
  !done && $0 ~ "server_name" && index($0, dom) { print; print ins; done=1; next }
  { print }
' "$VHOST" > "$TMP"
sudo cp "$TMP" "$VHOST"; rm -f "$TMP"

if sudo nginx -t; then
  sudo systemctl reload nginx
  echo "NGINX_RELOADED"
else
  echo "✗ nginx -t failed — rolling back"
  sudo cp "$BAK" "$VHOST"
  exit 42
fi
REMOTE
  ok "nginx updated and reloaded"
fi

# ----------------------------------------------------------------------------
# 7b) nginx: ensure /survey/ serves the static portal (no API proxy — it reuses
#     /award/api). Independent + idempotent so it lands even if /award already exists.
# ----------------------------------------------------------------------------
info "Checking nginx routing for ${SURVEY_PATH}"
if rsh "curl -sk -o /dev/null -w '%{http_code}' -H 'Host: ${DOMAIN}' https://localhost${SURVEY_PATH}/index.html" | grep -q '^200$'; then
  ok "nginx already serves ${SURVEY_PATH} (skipping nginx edit)"
else
  warn "nginx not yet serving ${SURVEY_PATH}; attempting a guarded config insert"
  rsh_tty "DOMAIN='${DOMAIN}' bash -s" <<'REMOTE'
set -euo pipefail
BLOCKS=$(cat <<'NGINX'

    # --- DITF Survey portal (/survey) — inserted by deploy-prod.sh ---
    location /survey/ {
        index     index.html;
        try_files $uri $uri/ =404;
    }
NGINX
)
VHOST=$(sudo grep -rlE "server_name[^;]*${DOMAIN}" /etc/nginx/sites-enabled/ /etc/nginx/conf.d/ 2>/dev/null | head -1 || true)
[ -n "$VHOST" ] || { echo "VHOST_NOT_FOUND"; exit 41; }
if sudo grep -q "location /survey/" "$VHOST"; then echo "ALREADY_PRESENT"; exit 0; fi

BAK="${VHOST}.bak.$(date +%s)"
sudo cp "$VHOST" "$BAK"
echo "▶ vhost: $VHOST  (backup: $BAK)"

TMP=$(mktemp)
sudo awk -v ins="$BLOCKS" -v dom="$DOMAIN" '
  !done && $0 ~ "server_name" && index($0, dom) { print; print ins; done=1; next }
  { print }
' "$VHOST" > "$TMP"
sudo cp "$TMP" "$VHOST"; rm -f "$TMP"

if sudo nginx -t; then
  sudo systemctl reload nginx
  echo "NGINX_RELOADED"
else
  echo "✗ nginx -t failed — rolling back"
  sudo cp "$BAK" "$VHOST"
  exit 42
fi
REMOTE
  ok "nginx /survey/ block inserted and reloaded"
fi

# ----------------------------------------------------------------------------
# 8) Verify.
# ----------------------------------------------------------------------------
info "Verifying"
H_API=$(rsh "curl -sk -o /dev/null -w '%{http_code}' -H 'Host: ${DOMAIN}' https://localhost${BASE_PATH}/api/health" || true)
H_WEB=$(rsh "curl -sk -o /dev/null -w '%{http_code}' -H 'Host: ${DOMAIN}' https://localhost${BASE_PATH}/index.html" || true)
H_SURVEY=$(rsh "curl -sk -o /dev/null -w '%{http_code}' -H 'Host: ${DOMAIN}' https://localhost${SURVEY_PATH}/index.html" || true)
echo "   ${BASE_PATH}/api/health  -> HTTP ${H_API:-???}"
echo "   ${BASE_PATH}/index.html  -> HTTP ${H_WEB:-???}"
echo "   ${SURVEY_PATH}/index.html -> HTTP ${H_SURVEY:-???}"
[ "$H_API" = "200" ] || warn "API health did not return 200 — check: ssh ${SSH_USER}@${SSH_HOST} 'pm2 logs award-api'"
[ "$H_WEB" = "200" ] || warn "Static index did not return 200 — check the nginx /award/ block and ${DOCROOT}/award/index.html"
[ "$H_SURVEY" = "200" ] || warn "Survey index did not return 200 — check the nginx /survey/ block and ${DOCROOT}/survey/index.html"

echo
ok "Deploy finished."
echo "Open:   ${APP_URL}${BASE_PATH}/"
echo "Survey: ${APP_URL}${SURVEY_PATH}/"
if [ "$SEED" = "1" ]; then echo "Seeded demo logins: admin@tantrade.go.tz / admin123"; fi
