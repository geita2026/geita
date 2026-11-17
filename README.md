# GeMEMIS — Geita Mineral Exhibition Management Information System

One system for the whole exhibition-space process: create an exhibition, lay out its halls and spaces, publish it, register exhibitors, review applications, allocate space on a floor plan, invoice, verify payments, confirm participation, check exhibitors in, and report on all of it.

Built from the *Functional Requirements Document* (FRD) for GeMEMIS. Every requirement number used in the code, tests and documents (`FR-010`, `BR-001`, …) is the FRD's own.

| Part | Stack | Folder |
|---|---|---|
| API | Laravel 12 (PHP 8.2+), Sanctum bearer tokens, DomPDF | `backend/` |
| Web client | Next.js 16 (App Router), HeroUI v3, daisyUI 5, Tailwind 4, Motion | `frontend/` |
| Documentation | Markdown | `docs/` |

The browser only ever talks to the Next.js server. The Next.js server calls the Laravel API with the user's token, which is kept in an httpOnly cookie.

## Run it locally

Requirements: PHP 8.2+, Composer, Node 20.12+.

```bash
cd backend && composer install && cp -n .env.example .env && php artisan key:generate
```

```bash
cd backend && php artisan migrate:fresh --seed && php artisan serve --port=8000
```

```bash
cd frontend && npm install && cp -n .env.example .env && npm run dev
```

Open http://localhost:3000. If the API is not on port 8000, set `API_URL` in `frontend/.env`.

### Demo accounts

`php artisan migrate:fresh --seed` creates one exhibition with 106 spaces, one staff account per role and 15 exhibitors at every stage of the process.

| Role | Email |
|---|---|
| System Administrator | `admin@gememis.test` |
| Exhibition Administrator | `exhibitions@gememis.test` |
| Space Allocation Officer | `spaces@gememis.test` |
| Registration Officer | `registration@gememis.test` |
| Finance Officer | `finance@gememis.test` |
| Management | `management@gememis.test` |
| Customer Service Officer | `service@gememis.test` |
| Auditor | `audit@gememis.test` |
| Exhibitor (confirmed) | `kahama@exhibitor.test` |
| Exhibitor (approved, no space yet) | `benki@exhibitor.test` |

All demo accounts share one password: the `DEMO_PASSWORD` constant in `backend/database/seeders/DatabaseSeeder.php`. These are development credentials. Never run that seeder in production — use `ProductionSeeder` (see `docs/09-operations.md`).

## Checks

```bash
cd backend && php artisan test
```

```bash
cd frontend && npm run check && npm run build
```

## Documentation

Every document is in Markdown (to edit) and PDF (to read and share).

| # | Document | What it answers | PDF |
|---|---|---|---|
| 00 | This README | Overview, setup, demo accounts | [PDF](docs/pdf/00-readme.pdf) |
| 01 | [Concept note](docs/01-concept-note.md) | Why the system exists, scope, stakeholders | [PDF](docs/pdf/01-concept-note.pdf) |
| 02 | [Software requirements specification](docs/02-srs.md) | What it must do; status of every FRD requirement | [PDF](docs/pdf/02-srs.pdf) |
| 03 | [Software design document](docs/03-sdd.md) | Architecture, state machines, key design decisions | [PDF](docs/pdf/03-sdd.pdf) |
| 04 | [Data dictionary](docs/04-data-dictionary.md) | Every table and column | [PDF](docs/pdf/04-data-dictionary.pdf) |
| 05 | [API reference](docs/05-api.md) | Every endpoint | [PDF](docs/pdf/05-api.pdf) |
| 06 | [Roles and permissions](docs/06-rbac.md) | Who may do what | [PDF](docs/pdf/06-rbac.pdf) |
| 07 | [Test plan and UAT](docs/07-test-plan.md) | Automated tests, UAT scripts, traceability | [PDF](docs/pdf/07-test-plan.pdf) |
| 08 | [User manual](docs/08-user-manual.md) | Step-by-step for each role | [PDF](docs/pdf/08-user-manual.pdf) |
| 09 | [Operations manual](docs/09-operations.md) | Deployment, configuration, backup, security | [PDF](docs/pdf/09-operations.pdf) |
| 10 | [Release notes](docs/10-release-notes.md) | What version 1.0.0 contains and what it does not | [PDF](docs/pdf/10-release-notes.pdf) |

The PDFs are generated from the Markdown. After changing a document, rebuild them (needs Google Chrome):

```bash
cd docs/tools && npm install && npm run pdf
```

## Known limits of version 1.0.0

Stated plainly so nobody discovers them in production:

- **Email and SMS** are sent through Laravel's mailer and an SMS driver that both default to the application log. Real delivery needs SMTP settings and an SMS gateway adapter (`backend/app/Services/Notifier.php`).
- **Payment platform integration (FR-059)** is a signed callback endpoint, tested with a simulated gateway. It has not been connected to a real provider (e.g. GePG).
- **Financial system integration (FR-061)** is not built; the payments report exports to CSV for import elsewhere.
- **Self-service "forgot password"** is not built. A System Administrator resets passwords (FR-046).
- **The floor plan** is a grid per hall, not a to-scale drawing or uploaded CAD plan.
- **Database**: developed and tested on SQLite. MySQL/MariaDB is the intended production database and uses only portable schema features, but has not been exercised yet.
