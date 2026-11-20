#!/usr/bin/env bash
#
# Build the Next.js timetable (static export) and stage it at the REPO ROOT /timetable/
# (the nginx docroot is the app root, so static files must live there — not in public/).
# Run on your Mac:  bash build_and_deploy.sh
#
set -euo pipefail

# Make Homebrew/nvm node visible even when run via plain bash
export PATH="/opt/homebrew/bin:/usr/local/bin:$PATH"
command -v node >/dev/null || { echo "node not found on PATH"; exit 1; }

cd "$(dirname "$0")"                      # ditf-timetable-next/
REPO_ROOT="$(cd .. && pwd)"
TARGET="$REPO_ROOT/timetable"

echo "==> Installing dependencies..."
npm install --legacy-peer-deps

echo "==> Building static export..."
npm run build                             # output:export -> ./out

echo "==> Copying export into $TARGET ..."
rm -rf "$TARGET"
mkdir -p "$TARGET"
cp -R out/* "$TARGET"/

echo
echo "Done. Static site staged at repo-root/timetable/"
echo "Next steps:"
echo "  cd \"$REPO_ROOT\""
echo "  git add ditf-timetable-next timetable"
echo "  git commit -m 'Update DITF timetable (gold theme)'"
echo "  git push origin main"
echo "Then on the server:  cd /var/www/tradefair.tantrade.go.tz && git pull origin main"
echo "Live URL:  https://tradefair.tantrade.go.tz/timetable/index.html"
