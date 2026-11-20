# Deploying the DITF Judging app → tradefair.tantrade.go.tz/award/

The frontend is a **Next.js static export** served directly by **nginx**; the API is a
dynamic Express + Prisma/MySQL service under **pm2**. nginx serves the static files and
reverse-proxies `/award/api/` to the API.

```
Browser ──https──> nginx (tradefair.tantrade.go.tz, docroot = /var/www/tradefair.tantrade.go.tz)
                     ├─ /award/api/*  ──proxy──> 127.0.0.1:4000   (Express API, pm2: award-api)
                     └─ /award/*      ──static─> <docroot>/award/  (Next.js static export)
                                                 │
                                          MariaDB: ditf_judging_2026 (own DB)
                                          MariaDB: tradefair_live   (SAMS, read-only)
```

## Deploy (from your Mac)

```bash
cd ditf-judging-2026
SEED=1 ./deploy-prod.sh      # first time (also seeds rubrics/categories/users)
./deploy-prod.sh             # every deploy after that
```

`deploy-prod.sh` does all the **local** work first, then shifts to prod:

1. **Local backend** — `npm ci` (all workspaces) + `prisma generate` + `prisma migrate dev`
   (applies pending migrations to your local DB, and creates+names a new migration if
   `schema.prisma` changed). If this fails, it stops before touching prod.
2. **Local frontend** — builds the static export with the prod env baked in.
3. **Connect to prod** — prompts once for the SSH password.
4. **Deploy** — syncs the API source, runs `prisma migrate deploy` + builds + (re)starts pm2,
   deploys the static files into the nginx docroot, and inserts the nginx routing (guarded —
   backs up the vhost, runs `nginx -t`, rolls back if the test fails), then verifies.

See [`award-nginx.conf`](./award-nginx.conf) for the exact location blocks it adds.

> The old `deploy.sh` + `award-apache.conf` (dynamic `next start` behind Apache) are
> **legacy**, kept only for reference. Use `deploy-prod.sh` + `award-nginx.conf`.

## One-time server setup

1. **Node 18+** on the server (`node -v`). If missing, install it (e.g. via `nvm` or NodeSource).

2. **Create the app's database + user** (the judging app keeps its own DB, separate from SAMS).
   The deploy script prints this exact SQL if the DB is missing — run it once as a MySQL admin:
   ```sql
   CREATE DATABASE ditf_judging_2026 CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
   CREATE USER 'ditf_user'@'127.0.0.1' IDENTIFIED BY 'a-strong-password';
   GRANT ALL PRIVILEGES ON ditf_judging_2026.* TO 'ditf_user'@'127.0.0.1';
   GRANT SELECT ON tradefair_live.* TO 'ditf_user'@'127.0.0.1';   -- read-only SAMS for sync
   FLUSH PRIVILEGES;
   ```

3. **API env**: the first `deploy-prod.sh` run creates `apps/api/.env` on the server from the
   template and stops. Fill in the real `DATABASE_URL` (the `ditf_user` above), a random
   `JWT_SECRET` (`openssl rand -hex 32`), `SAMS_DATABASE_URL`, and
   `CORS_ORIGIN=https://tradefair.tantrade.go.tz`, then re-run.

4. **Persist pm2 across reboots** (once, on the server): `pm2 startup` (run the line it prints), then `pm2 save`.

5. **Point SAMS at the category feed** (SAMS `.env` on the server), then `php artisan config:clear`:
   ```
   JUDGING_CATEGORY_API="https://tradefair.tantrade.go.tz/award/api/categories/feed"
   ```

## Database migrations

The schema is managed with **Prisma migrations** (committed under
`apps/api/prisma/migrations/`). The deploy runs `prisma migrate deploy`, which applies any
new committed migrations non-interactively — it never prompts and never auto-drops data.

**To change the schema:** edit `apps/api/prisma/schema.prisma`, then locally run
`npm run prisma:migrate --workspace api` (generates a new migration in `prisma/migrations/`),
commit the generated folder, and the next `./deploy-prod.sh` applies it on the server. Do
**not** hand-edit the live DB or use `prisma db push` in production — that bypasses the
migration history and risks silent data loss.

## Verify
- `https://tradefair.tantrade.go.tz/award/` loads the app.
- `https://tradefair.tantrade.go.tz/award/api/health` returns `{ok:true}`.
- `pm2 ls` shows `award-api` online; `pm2 logs award-api` for troubleshooting.

## Config knobs (env vars for deploy-prod.sh)
| var | default |
|-----|---------|
| `SSH_USER` | `tantrade-admin` |
| `SSH_HOST` | `10.1.79.139` |
| `DOMAIN` | `tradefair.tantrade.go.tz` |
| `BASE_PATH` | `/award` |
| `APP_URL` | `https://tradefair.tantrade.go.tz` |
| `REMOTE_DIR` | `/var/www/tradefair.tantrade.go.tz/award-app` (API source) |
| `DOCROOT` | auto-detected (e.g. `/var/www/tradefair.tantrade.go.tz`) |
| `SEED` | `0` (set `1` to seed) |
