#!/usr/bin/env bash
# Start the Next.js dev server for the DITF timetable.
# Usage:  bash dev.sh   then open http://localhost:3000/timetable
export PATH="/opt/homebrew/bin:/usr/local/bin:$PATH"
cd "$(dirname "$0")"
command -v npm >/dev/null || { echo "npm not found (install Node / check PATH)"; exit 1; }
[ -d node_modules ] || npm install --legacy-peer-deps
echo "Dev server starting → open http://localhost:3000/timetable (or the port shown below)"
npm run dev
