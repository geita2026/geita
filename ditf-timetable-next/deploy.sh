#!/usr/bin/env bash
#
# One-shot deploy for the DITF timetable (+ award-night page).
#
#   bash deploy.sh                       # default commit message
#   bash deploy.sh "your commit message" # custom message
#   SSH_KEY=~/.ssh/other bash deploy.sh  # different SSH key
#
# NOTE: the production server has NO outbound access to github.com (port 443 times
# out), so it can never `git pull`. Instead we build here and ship the static files
# straight to the server over SSH (rsync). GitHub is still used for version history.
#
set -euo pipefail

MSG="${1:-Update DITF timetable}"
SERVER="tantrade-admin@10.1.79.139"
APP_DIR="/var/www/tradefair.tantrade.go.tz"

# SSH key used to log into the server
SSH_KEY="${SSH_KEY:-$HOME/.ssh/id_ed25519}"
if [ -f "$SSH_KEY" ]; then
  SSH_CMD="ssh -i $SSH_KEY -o IdentitiesOnly=yes"
else
  SSH_CMD="ssh"
fi

# Homebrew/nvm node isn't on a plain bash PATH
export PATH="/opt/homebrew/bin:/usr/local/bin:$PATH"
command -v npm >/dev/null || { echo "npm not found — install Node or fix PATH"; exit 1; }

cd "$(dirname "$0")"                       # ditf-timetable-next/
REPO_ROOT="$(cd .. && pwd)"
TARGET="$REPO_ROOT/timetable"

echo "==> 1/4 Installing dependencies..."
npm install --legacy-peer-deps

echo "==> 2/4 Building static export..."
npm run build                              # output:export -> ./out

echo "==> 3/4 Staging into $TARGET ..."
rm -rf "$TARGET"
mkdir -p "$TARGET"
cp -R out/* "$TARGET"/

echo "==> 4/4 Shipping to the server over SSH (server can't reach GitHub)..."
if command -v rsync >/dev/null; then
  rsync -az --delete -e "$SSH_CMD" "$TARGET"/ "$SERVER:$APP_DIR/timetable/"
else
  # fallback: tar over ssh (wipes the remote folder first so stale files go away)
  $SSH_CMD "$SERVER" "rm -rf $APP_DIR/timetable && mkdir -p $APP_DIR/timetable"
  tar czf - -C "$TARGET" . | $SSH_CMD "$SERVER" "tar xzf - -C $APP_DIR/timetable"
fi

# Version history only — the server does NOT pull from here.
echo "==> Committing to GitHub (history only; deploy already done)..."
cd "$REPO_ROOT"
rm -f .git/index.lock
git add ditf-timetable-next timetable
if git diff --cached --quiet; then
  echo "    (nothing new to commit)"
else
  git commit -m "$MSG"
fi
git push origin main || echo "    (push failed — site is already live; fix git later)"

echo
echo "Done ✅  — files are live on the server."
echo "  Closing Ceremony : https://tradefair.tantrade.go.tz/timetable/"
echo "  Awards Night     : https://tradefair.tantrade.go.tz/timetable/award-night/index.html"
echo "                     (or /timetable/?event=award-night)"
echo
echo "Tip: hard-refresh (Cmd+Shift+R) — index.html isn't content-hashed and may be cached."
