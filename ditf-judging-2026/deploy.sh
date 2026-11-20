#!/usr/bin/env bash
#
# Deploy the DITF Judging app from this Mac to the server and serve it at
#   https://tradefair.tantrade.go.tz/award/
#
# It rsyncs the project, then on the server: installs deps, applies the DB
# schema, builds the API and the Next.js app (with the /award basePath), and
# (re)starts both processes under pm2.
#
# Usage:
#   ./deploy.sh            normal deploy
#   SEED=1 ./deploy.sh     first deploy — also seed rubrics/categories/users
#
# Config (override by exporting before running):
SSH_USER="${SSH_USER:-tantrade-admin}"
SSH_HOST="${SSH_HOST:-ttrade-sams-stg}"
REMOTE_DIR="${REMOTE_DIR:-/var/www/tradefair.tantrade.go.tz/award-app}"
APP_URL="${APP_URL:-https://tradefair.tantrade.go.tz}"
BASE_PATH="/award"
SEED="${SEED:-0}"

set -euo pipefail
cd "$(dirname "$0")"

c_green=$'\033[0;32m'; c_blue=$'\033[0;34m'; c_red=$'\033[0;31m'; c_off=$'\033[0m'
info() { echo "${c_blue}▶ $*${c_off}"; }
ok()   { echo "${c_green}✓ $*${c_off}"; }
die()  { echo "${c_red}✗ $*${c_off}" >&2; exit 1; }

command -v rsync >/dev/null || die "rsync is required on your Mac."
command -v ssh   >/dev/null || die "ssh is required on your Mac."

info "Syncing project to ${SSH_USER}@${SSH_HOST}:${REMOTE_DIR}"
rsync -az --delete \
  --exclude '.git' \
  --exclude 'node_modules' \
  --exclude '.next' \
  --exclude 'dist' \
  --exclude 'apps/api/.env' \
  --exclude 'apps/web/.env.local' \
  --exclude 'apps/web/.env.production' \
  ./ "${SSH_USER}@${SSH_HOST}:${REMOTE_DIR}/"
ok "Files synced"

info "Building & (re)starting on the server"
ssh "${SSH_USER}@${SSH_HOST}" \
  SEED="${SEED}" APP_URL="${APP_URL}" BASE_PATH="${BASE_PATH}" REMOTE_DIR="${REMOTE_DIR}" \
  'bash -s' <<'REMOTE'
set -euo pipefail
cd "$REMOTE_DIR"

command -v node >/dev/null || { echo "✗ Node.js not installed on the server. Install Node 18+ first."; exit 1; }
command -v pm2  >/dev/null || { echo "▶ Installing pm2 globally"; npm install -g pm2; }

# First deploy: seed the API env from the template, then stop so creds can be filled.
if [ ! -f apps/api/.env ]; then
  cp apps/api/.env.production.example apps/api/.env
  echo "! Created apps/api/.env from the template."
  echo "! Edit it with the real DATABASE_URL / SAMS_DATABASE_URL / JWT_SECRET, then re-run ./deploy.sh"
  exit 2
fi

echo "▶ Installing dependencies"
npm ci --include=dev

echo "▶ Applying database schema (prisma)"
npm run prisma:generate --workspace api
npm run prisma:push --workspace api

echo "▶ Building API"
npm run build --workspace api

if [ "$SEED" = "1" ]; then
  echo "▶ Seeding rubrics / categories / users"
  npm run seed --workspace api
fi

echo "▶ Building web (basePath=$BASE_PATH)"
export NEXT_PUBLIC_BASE_PATH="$BASE_PATH"
export NEXT_PUBLIC_API_URL="${APP_URL}${BASE_PATH}/api"
npm run build --workspace web

echo "▶ Restarting pm2 processes"
pm2 startOrReload ecosystem.config.cjs --update-env
pm2 save
echo "✓ Node API on :4000, Web on :3000 (served under $BASE_PATH)"
REMOTE

ok "Deploy finished."
echo
echo "If this is the first time, also do ONCE on the server:"
echo "  1. Create the DB + user:  CREATE DATABASE ditf_judging_2026 ...; GRANT ... TO 'ditf_user'@'127.0.0.1';"
echo "  2. Add the Apache proxy:  paste deploy/award-apache.conf into the vhost, a2enmod proxy proxy_http, reload apache2"
echo "  3. Persist pm2 on reboot: pm2 startup  (run the line it prints)  &&  pm2 save"
echo
echo "Then open: ${APP_URL}${BASE_PATH}/"
