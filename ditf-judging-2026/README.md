# DITF 2026 Judging System

Judging platform for the **50th Dar es Salaam International Trade Fair (DITF) 2026** exhibitor stand/pavilion awards.

- **Frontend:** Next.js (App Router) + HeroUI + daisyUI + Tailwind — `apps/web`
- **Backend:** Node.js (Express) + Prisma + JWT — `apps/api`
- **Database:** MySQL / MariaDB (works with your XAMPP install)
- **Seed data:** `data/criteria.json` (rubrics) and `data/categories.json` (award categories), extracted from the official 2026 docs.

See **`SPEC.md`** for the full flow, data model, and design rationale.

---

## Quick start (one script)

With Node 18+ installed and XAMPP's **MySQL Database** running:

```bash
cd ditf-judging-2026
./run.sh setup     # create DB, env files, install, build schema, seed
./run.sh dev       # start API (:4000) + Web (:3000)
```

Then open http://localhost:3000. Other commands:

```bash
./run.sh test      # boot the API, run the end-to-end smoke test, stop it
./run.sh reset     # drop + recreate the DB and re-seed
./run.sh all       # setup, then dev
```

If your MySQL isn't the default root/no-password, pass it in:
`DB_USER=root DB_PASS=secret ./run.sh setup`

The manual steps below are equivalent if you'd rather run them yourself.

---

## Prerequisites
- Node.js 18+ and npm 9+
- A running MySQL/MariaDB (e.g. XAMPP — start *MySQL Database*)

## 1. Install
```bash
cd ditf-judging-2026
npm install
```

## 2. Create the database
With XAMPP MySQL running (root, no password by default):
```bash
/Applications/XAMPP/xamppfiles/bin/mysql -u root -e "CREATE DATABASE ditf_judging_2026 CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;"
```

## 3. Configure env
```bash
cp apps/api/.env.example apps/api/.env
cp apps/web/.env.local.example apps/web/.env.local
```
Edit `apps/api/.env` if your DB credentials differ. Default:
`mysql://root:@127.0.0.1:3306/ditf_judging_2026`

## 4. Create tables + seed
```bash
npm run db:generate      # prisma client
npm run db:migrate       # create tables (prisma migrate dev)
npm run db:seed          # rubrics, categories, demo admin + 6 judges
```

## 5. Run (both apps)
```bash
npm run dev
```
- API → http://localhost:4000  (health: `/health`)
- Web → http://localhost:3000

### Demo logins (from seed)
| Role | Email | Password |
|------|-------|----------|
| Admin | admin@tantrade.go.tz | admin123 |
| Judge 1–6 | judge1@tantrade.go.tz … judge6@… | judge123 |

---

## Typical flow to try
1. **Register exhibitors** at `/register` (public; pick a category).
2. **Admin** logs in → assigns judges to categories (`POST /admin/assignments`) and sets the event phase to `SCORING` (`PATCH /admin/events/ditf-2026/phase`).
3. **Judge** logs in → `/judge` → opens an exhibitor → fills the rubric on one page → submits.
4. **Admin** → `/admin/results` → **Compute results** → category winners (1st/2nd/3rd) appear.
5. For the Grand award, run compute with `?round=2` after re-scoring first-winners.

> Note: assignment + phase management UIs are minimal in this scaffold; the endpoints exist and are documented in `SPEC.md`. They're the natural next screens to build out.

## API quick reference
```
POST   /auth/login
GET    /categories                 GET /categories/rubric/:id
POST   /exhibitors                 GET /exhibitors (admin/judge)   PUT /exhibitors/:id (admin)
GET    /judge/assignments
GET    /judge/exhibitors/:id/sheet
POST   /judge/exhibitors/:id/scores   { items: [{ rubricItemId, value }] }
POST   /admin/judges               POST /admin/assignments
PATCH  /admin/events/:id/phase     POST /admin/results/compute?round=1
GET    /results?round=1
```

## Project layout
```
ditf-judging-2026/
├── data/                 # criteria.json, categories.json (seed source of truth)
├── apps/
│   ├── api/              # Express + Prisma backend
│   │   ├── prisma/       # schema.prisma, seed.ts
│   │   └── src/          # index.ts, auth.ts, routes/, services/
│   └── web/              # Next.js + HeroUI + daisyUI frontend
│       ├── app/          # /, /login, /register, /judge, /judge/score/[id], /admin/results
│       └── lib/api.ts
├── SPEC.md
└── README.md
```
