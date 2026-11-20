#!/usr/bin/env bash
#
# DITF 2026 Judging — one script to rule them all.
#
# Usage:
#   ./run.sh setup     One-time: create DB, env files, install, build schema, seed
#   ./run.sh dev       Start API (:4000) and Web (:3000) together
#   ./run.sh test      Start API in the background, run the smoke test, stop it
#   ./run.sh reset     Drop & recreate the database, then re-seed
#   ./run.sh all       setup, then dev   (default if no command given)
#
# Config (override by exporting before running, e.g.  DB_PASS=secret ./run.sh setup):
DB_NAME="${DB_NAME:-ditf_judging_2026}"
DB_USER="${DB_USER:-root}"
DB_PASS="${DB_PASS:-}"
DB_HOST="${DB_HOST:-127.0.0.1}"
DB_PORT="${DB_PORT:-3306}"

set -euo pipefail
cd "$(dirname "$0")"
ROOT="$(pwd)"

# ---------- pretty output ----------
c_green=$'\033[0;32m'; c_yellow=$'\033[0;33m'; c_red=$'\033[0;31m'; c_blue=$'\033[0;34m'; c_off=$'\033[0m'
info()  { echo "${c_blue}▶ $*${c_off}"; }
ok()    { echo "${c_green}✓ $*${c_off}"; }
warn()  { echo "${c_yellow}! $*${c_off}"; }
die()   { echo "${c_red}✗ $*${c_off}" >&2; exit 1; }

# ---------- locate tools ----------
find_mysql() {
  if [ -x "/Applications/XAMPP/xamppfiles/bin/mysql" ]; then
    echo "/Applications/XAMPP/xamppfiles/bin/mysql"
  elif command -v mysql >/dev/null 2>&1; then
    command -v mysql
  else
    echo ""
  fi
}

mysql_cmd() {
  local bin; bin="$(find_mysql)"
  [ -n "$bin" ] || die "Could not find the 'mysql' client. Start XAMPP (MySQL) or install MySQL/MariaDB."
  if [ -n "$DB_PASS" ]; then
    "$bin" -u "$DB_USER" -p"$DB_PASS" -h "$DB_HOST" -P "$DB_PORT" "$@"
  else
    "$bin" -u "$DB_USER" -h "$DB_HOST" -P "$DB_PORT" "$@"
  fi
}

require_node() {
  command -v node >/dev/null 2>&1 || die "Node.js is not installed. Install Node 18+ from https://nodejs.org"
  local major; major="$(node -p 'process.versions.node.split(".")[0]')"
  [ "$major" -ge 18 ] || die "Node 18+ required (found $(node -v))."
  ok "Node $(node -v)"
}

# ---------- steps ----------
check_db_connection() {
  info "Checking MySQL connection ($DB_USER@$DB_HOST:$DB_PORT)…"
  echo "SELECT 1;" | mysql_cmd >/dev/null 2>&1 \
    || die "Cannot connect to MySQL. Is XAMPP's MySQL running? Check DB_USER/DB_PASS."
  ok "MySQL reachable"
}

create_db() {
  info "Creating database '$DB_NAME' (if it doesn't exist)…"
  echo "CREATE DATABASE IF NOT EXISTS \`$DB_NAME\` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;" | mysql_cmd
  ok "Database ready"
}

drop_db() {
  warn "Dropping database '$DB_NAME'…"
  echo "DROP DATABASE IF EXISTS \`$DB_NAME\`;" | mysql_cmd
  create_db
}

write_env() {
  local enc_pass="$DB_PASS"
  local db_url="mysql://${DB_USER}:${enc_pass}@${DB_HOST}:${DB_PORT}/${DB_NAME}"

  if [ ! -f "$ROOT/apps/api/.env" ]; then
    info "Writing apps/api/.env"
    cat > "$ROOT/apps/api/.env" <<EOF
DATABASE_URL="$db_url"
JWT_SECRET="$(node -e 'console.log(require("crypto").randomBytes(32).toString("hex"))')"
PORT=4000
CORS_ORIGIN="http://localhost:3000"
EOF
    ok "apps/api/.env created"
  else
    warn "apps/api/.env already exists — leaving it untouched"
  fi

  if [ ! -f "$ROOT/apps/web/.env.local" ]; then
    info "Writing apps/web/.env.local"
    echo 'NEXT_PUBLIC_API_URL="http://localhost:4000"' > "$ROOT/apps/web/.env.local"
    ok "apps/web/.env.local created"
  else
    warn "apps/web/.env.local already exists — leaving it untouched"
  fi
}

install_deps() {
  info "Installing npm dependencies (this can take a few minutes the first time)…"
  npm install
  ok "Dependencies installed"
}

build_schema() {
  info "Generating Prisma client + creating tables…"
  npm run prisma:generate --workspace api
  npm run prisma:push --workspace api
  ok "Schema applied"
}

seed_db() {
  info "Seeding rubrics, categories, admin + 6 judges…"
  npm run seed --workspace api
  ok "Seed complete"
}

start_dev() {
  info "Starting API (:4000) and Web (:3000). Press Ctrl+C to stop."
  npm run dev
}

run_smoke() {
  require_node
  info "Starting API in the background for the smoke test…"
  ( npm run dev:api >/tmp/ditf-api.log 2>&1 & echo $! > /tmp/ditf-api.pid )
  local pid; pid="$(cat /tmp/ditf-api.pid)"
  # wait for health (max ~30s)
  local i=0
  until curl -sf http://localhost:4000/health >/dev/null 2>&1; do
    i=$((i+1)); [ "$i" -gt 30 ] && { kill "$pid" 2>/dev/null || true; cat /tmp/ditf-api.log; die "API did not start in time"; }
    sleep 1
  done
  ok "API is up"
  set +e
  npm run test:smoke --workspace api
  local rc=$?
  set -e
  info "Stopping background API (pid $pid)…"
  kill "$pid" 2>/dev/null || true
  [ "$rc" -eq 0 ] && ok "Smoke test passed" || die "Smoke test failed (see output above)"
}

do_setup() {
  require_node
  check_db_connection
  create_db
  write_env
  install_deps
  build_schema
  seed_db
  echo
  ok "Setup complete."
  echo "  Next:  ${c_blue}./run.sh dev${c_off}   then open http://localhost:3000"
  echo "  Logins: admin@tantrade.go.tz / admin123  ·  judge1@tantrade.go.tz / judge123"
}

# ---------- dispatch ----------
cmd="${1:-all}"
case "$cmd" in
  setup) do_setup ;;
  dev)   start_dev ;;
  test)  run_smoke ;;
  reset) require_node; check_db_connection; drop_db; build_schema; seed_db; ok "Database reset + reseeded" ;;
  all)   do_setup; echo; start_dev ;;
  *)     die "Unknown command '$cmd'. Use: setup | dev | test | reset | all" ;;
esac
