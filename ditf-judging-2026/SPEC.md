# DITF 2026 Judging System — Spec & Build Blueprint

**Separate project.** Stack: **Next.js + HeroUI + daisyUI** (frontend) · **Node.js** (backend API).
Source of truth: `CRITERIA 2026.doc` and `JUDGING CATEGORIES 2026.doc` (extracted into `data/criteria.json` and `data/categories.json`).

> This is the **Exhibitor Stand/Pavilion Awards** judging for the 50th DITF (Golden Jubilee), 2026 — NOT the TNB logo competition that the legacy `mis_update` database was about.

---

## 1. What the system does

Exhibitors at the 50th DITF register for **one** award category based on their product/service (online, before **27 June 2026**). A panel of judges visits stands and scores each registered exhibitor against a points rubric. Per category the system ranks exhibitors and produces **First / Second / Third** winners. A **second round** then re-judges the first-winners of each ordinary category to pick the **Overall Golden Winner** (1st/2nd/3rd).

### Award structure (see `data/categories.json` for the full list)
- **23 Ordinary (sectorial) categories** — scored with the **Ordinary rubric** (100 pts).
- **4 Special categories** — Best Foreign Exhibitor & Best Tanzanian Manufacturer use the **Best Exhibitor rubric** (100 pts); Best Tanzania Branded Pavilion & Best Foreign Country Pavilion use the **Best Branded Pavilion rubric** (100 pts).
- **Recognition awards** (long-standing exhibitor ×3, Popular/public-choice ×1, TanTrade Excellence) — committee-selected, not point-scored (Popular = public vote).
- **Grand award** (second round) — Overall Golden Winner 1st/2nd/3rd from ordinary first-winners.

---

## 2. Scoring rubrics (see `data/criteria.json`)

**Ordinary rubric — 100 pts**, five groups of weighted sub-criteria:

| Group | Max |
|---|---|
| Overall Appearance of the Stand/Pavilion | 35 |
| Professional Display of Exhibits & Stand Management | 25 |
| Quality of Products and Services | 25 |
| Customer Care | 10 |
| Alignment to the Theme | 5 |

Each group breaks into scored items (e.g. Appearance → signage 15, colour 5, cleanliness 5, signboard 5, lighting 5). A judge enters a score `0..maxMark` per **item**; group and overall totals are derived.

**Best Foreign/Tanzania Exhibitor — 100 pts** (flat list; Value-addition alone = 50).
**Best Branded Pavilion — 100 pts** (flat list; Overall appearance = 60).

**Aggregation:** an exhibitor's score from one judge = sum of that judge's item scores (≤100). Final exhibitor score = **average across judges**. Rank descending within category → 1st/2nd/3rd. (Confirm tie-break rule with TanTrade — suggest: higher Appearance group, then Quality.)

---

## 3. Roles & flow

```
APPLICANT (exhibitor)            JUDGE                         ADMIN (TanTrade)
   │ register for 1 category       │ see assigned exhibitors      │ open/close registration
   │ (before 27 Jun 2026)          │ score each on rubric items   │ manage judges & assignments
   │ edit until deadline           │ submit / revise own scores   │ lock scoring, compute results
   ▼                               ▼                              │ release winners, second round
 status: REGISTERED ─────────► being scored ──────────► RESULTS ◄─┘
```

1. **Registration** — exhibitor picks one category; system validates category group (e.g. only foreign companies → Best Foreign).
2. **Assignment** — admin assigns judges to categories/exhibitors (panel per category).
3. **Scoring** — each judge opens an exhibitor's **rubric sheet** (all items for that exhibitor's rubric), enters scores capped per item, sees a live running total vs 100, submits once. One score-set per (exhibitor × judge). Editable until admin locks.
4. **Results** — system averages judge scores, ranks per category, marks 1st/2nd/3rd.
5. **Second round** — ordinary first-winners re-scored (anonymous judges) → overall 1st/2nd/3rd.
6. **Recognition/Popular** — committee entries + public vote tally.

---

## 4. Data model (fresh — Prisma / PostgreSQL or MySQL)

```prisma
model Event        { id String @id; name String; theme String; registrationDeadline DateTime; phase String } // REGISTRATION|SCORING|R1_DONE|SECOND_ROUND|CLOSED

model Category     { id String @id; eventId String; order Int; name String; group String; rubricId String }
// group: ORDINARY | SPECIAL_EXHIBITOR | SPECIAL_PAVILION | RECOGNITION | GRAND

model Rubric       { id String @id; code String; name String; totalPoints Int; groups RubricGroup[] }
model RubricGroup  { id String @id; rubricId String; code String; title String; maxMark Int; items RubricItem[] }
model RubricItem   { id String @id; groupId String; code String; description String; maxMark Int }

model Exhibitor    { id String @id; eventId String; companyName String; standLocation String; isForeign Boolean; categoryId String; status String } // REGISTERED|SCORING|RANKED

model Judge        { id String @id; userId String; name String; code String; active Boolean }
model JudgeAssignment { id String @id; judgeId String; categoryId String }

model Score        { id String @id; exhibitorId String; judgeId String; rubricItemId String; value Float
                     @@unique([exhibitorId, judgeId, rubricItemId]) }   // one item-score per judge per exhibitor

model Result       { id String @id; exhibitorId String; categoryId String; round Int; average Float; rank Int } // rank 1/2/3
```

Seed `Rubric/RubricGroup/RubricItem` from `data/criteria.json` and `Category` from `data/categories.json`.

---

## 5. Node.js API surface

```
POST  /auth/login                                  → JWT (admin|judge|exhibitor)

# registration (exhibitor / admin)
GET   /categories?event=                           → categories + group + rubric
POST  /exhibitors                                  → register (one category)
PUT   /exhibitors/:id                              → edit until deadline
GET   /exhibitors?category=&status=                → admin/judge list

# scoring (judge)
GET   /judge/assignments                           → my categories + exhibitors
GET   /judge/exhibitors/:id/sheet                  → rubric items + my existing scores
POST  /judge/exhibitors/:id/scores                 → bulk { items:[{rubricItemId,value}] } (validated ≤ maxMark)

# admin
POST  /admin/judges, /admin/assignments
PATCH /events/:id/phase                            → REGISTRATION→SCORING→...
POST  /admin/results/compute?round=1|2             → average, rank, persist Result
GET   /results?category=&round=                    → leaderboard (avg + per-judge matrix)
```

---

## 6. Frontend — Next.js (App Router) + HeroUI + daisyUI

**Public / exhibitor**
- `/register` — category picker (grouped by sector) + stand details. HeroUI `Select`, `Input`; daisyUI `steps`, `alert`.

**Judge portal** (role: judge)
- `/judge` — assigned exhibitors, scored/unscored badges (daisyUI `table`, `badge`, `progress`).
- `/judge/score/:exhibitorId` — **single-page rubric**: groups as daisyUI `collapse`/`card`, each item a HeroUI `NumberInput`/`Slider` capped at `maxMark`, live group subtotal + grand total vs 100, one **Submit** button.

**Admin console** (role: admin)
- `/admin/exhibitors` — registrations by category, filters, lock toggle.
- `/admin/judges` — manage judges & panel assignments.
- `/admin/results` — per-category leaderboard, per-judge score matrix (HeroUI `Table`, daisyUI `stat` cards), "Compute results" + "Run second round" actions; export.

**UI kit:** HeroUI for form/data components; daisyUI for layout (`navbar`, `card`, `badge`, `stat`, `steps`, `tabs`, `alert`); Tailwind for spacing. Theme to TanTrade branding (logo at the Laravel app's `public/images/logo.jpg`).

---

## 7. Build order
1. Scaffold monorepo: `apps/web` (Next.js) + `apps/api` (Node) + shared `data/`.
2. Prisma schema (§4) + seed from the two JSON files.
3. Auth + scoring endpoints (core engine) → judge single-page rubric.
4. Registration flow.
5. Admin results compute + leaderboard + second round.
6. Recognition/Popular vote handling.

## 8. Open questions for TanTrade
- Tie-break rule within a category?
- How many judges per panel; is each exhibitor scored by all panel judges?
- Popular Award — public voting mechanism (per visitor? per device?).
- Eligibility checks for special categories (foreign vs Tanzanian, manufacturer, pavilion).
- Source-doc numbering slip: two "Category Twenty Three" entries — `categories.json` renumbers sequentially; confirm intended numbering.
