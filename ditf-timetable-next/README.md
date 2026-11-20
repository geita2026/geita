# DITF 2026 Timetable — Next.js + Tailwind + daisyUI + HeroUI

Live countdown timetable for the 50th DITF (SabaSaba) Official Opening Ceremony,
3 July 2026. Bilingual (English / Kiswahili), styled with **daisyUI** (Tailwind
component classes) and **HeroUI** (React components: Button, Chip).

## Stack
- Next.js 14 (App Router), static export (`output: "export"`)
- React 18
- Tailwind CSS 3 + daisyUI 4
- HeroUI 2 (`@heroui/button`, `@heroui/chip`, `@heroui/system`, `@heroui/theme`)
- Served under the base path `/timetable`

## Build & deploy (run on a machine with Node 18+, e.g. your Mac)

```bash
cd ditf-timetable-next
bash build_and_deploy.sh
```

This installs deps, builds the static export to `./out`, and copies it to
`../public/timetable/`. Then commit `ditf-timetable-next` + `public/timetable`,
push, and `git pull` on the server. Final URL:

```
https://tradefair.tantrade.go.tz/timetable/
```

No Node runtime is needed on the server — it serves plain static files.

## Local preview
```bash
npm install --legacy-peer-deps
npm run dev      # http://localhost:3000/timetable
```

## Editing the schedule
All event rows and the English/Kiswahili strings live in `app/data.js`.

## Note on dependencies
Install uses `--legacy-peer-deps` because `@heroui/theme@2.4.15` (the last
Tailwind-3-compatible theme) is pinned alongside slightly newer HeroUI
component packages. This is intentional and builds correctly.
