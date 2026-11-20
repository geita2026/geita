# eTAC Scanner — Developer Notes
> Last updated: March 2026

---

## Projects Overview

| Project | Path | Purpose |
|---|---|---|
| Laravel backend (SAMS) | `/Users/macbook/tradefair.tantrade.go.tz/` | Ticket creation, badge management, validation API |
| Flutter scanner app | `/Users/macbook/Documents/apps/eTAC-scanner/` | Gate scanner app (Android) |
| React frontend | `/Users/macbook/react.projects.tantrade.go.tz/` | Public-facing site |
| Staging server | `tantrade-admin@ttrade-sams-stg` | Linux, 15GB RAM |

---

## Badge Preview System (NEW — March 2026)

### URLs
```
Browser preview:  /badgesManagement/badge_preview/{member_id}
PDF download:     /badgesManagement/badge_pdf/{member_id}
```

### Sample member IDs for testing
```
000280f8-d518-44e7-8ff7-380296a78614  — Rehema Masija   (EXHIBITOR)
00057df7-017b-4a09-9879-4cbe453db43c  — Geoffrey Manace (PRINTED)
```

### Files created/modified
| File | Purpose |
|---|---|
| `app/Http/Controllers/Badges/BadgePreviewController.php` | New controller — bypasses all old steps |
| `resources/views/Badges/NewBadge.blade.php` | Badge preview blade — PNG bg + JS overlays |
| `public/ditf26.png` | DITF 2026 badge background (copy from react project) |
| `routes/web.php` | Added badge_preview and badge_pdf routes |

### How the badge renders
```
PNG background (/public/ditf26.png)
    + CSS div   #badge-strip    — group color strip (dynamic color from DB)
    + div       #badge-name     — member name (Times New Roman Bold)
    + div       #badge-company  — company name (Arial)
    + div       #badge-group    — group label on strip (white/dynamic color)
    + div       #qr-canvas      — QR code via qr-code-styling JS library
```

All overlays are positioned and sized via **ResizeObserver JS** using SVG coordinate mapping:
- SVG reference dimensions: `576 × 864.16`
- All positions recalculate on window resize automatically

### JS tuning constants (in NewBadge.blade.php)
```js
const QR_X     = 306,    QR_Y    = 298.46, QR_SIZE = 156.49;
const PADDING  = 4;       // white space inside QR box (px)
const QR_SCALE = 1.3;     // 1.0 = exact fit, increase to grow QR

const NAME_CX  = 379.8,  NAME_BY = 184.31, NAME_FS = 15.65;
const CO_CX    = 379.8,  CO_BY   = 193.5,  CO_FS   = 8;
const STRIP_X  = 267.89, STRIP_Y = 195.91, STRIP_W = 223.86, STRIP_H = 38.8;
const GRP_FS   = 21.44;
```

### Controller variables passed to blade
```php
'memberId'        => $badge->member_id,
'badgeName'       => first_name + last_name,
'badgeCompany'    => associateCompany ?? companyName,
'badgeGroup'      => group_name ?? 'EXHIBITOR',
'badgeGroupColor' => group_background_color ?? '#13487e',
'badgeTextColor'  => text_color ?? '#ffffff',
```

### QR code library
- **Browser preview**: `qr-code-styling@1.5.0` (JS, CDN)
  - Black dots, rounded style, extra-rounded corners, white background
  - `data: "SAMS|{member_id}"`
- **PDF**: server-side SVG QR via `SimpleSoftwareIO\QrCode` (no Imagick needed, use SVG format)

### Known issue: Imagick not installed
```
BaconQrCode\Exception\RuntimeException: You need to install the imagick extension
```
**Fix**: Always use `QrCode::format('svg')` not `format('png')` on this server.

### APP_URL mismatch
`.env` has `APP_URL=http://localhost` but dev server runs at `http://127.0.0.1:8000`
**Fix**: Use root-relative paths `/ditf26.png` instead of `asset()` in blade.

---

## Validation API — Known Bugs & Performance Issues

File: `app/Http/Controllers/APIs/ValidationApiController.php`

### Bug 1 — CRITICAL: Race condition on scanCount (lines ~90-100)
```php
// BAD — two separate operations allow duplicate scans
$ticket = ProducedTicket::where('ticketId', $id)->first(); // READ
if ($ticket->scanCount == 0) {                             // CHECK
    $ticket->update(['scanCount' => 1]);                   // WRITE (too late)
}

// FIX — atomic single operation
$updated = ProducedTicket::where('ticketId', $id)
    ->where('scanCount', 0)
    ->update(['scanCount' => 1, 'lastScanTime' => now()]);
if ($updated === 0) { /* already used — reject */ }
```

### Bug 2 — Dead code in VIP block (lines 94–97)
```php
if ($ticket->ticketAmount >= 1500000) {
    if ($ticket->ticketAmount == 1000) {    // DEAD — can never be true
        $this->groundCount(2);
    }
    if ($ticket->ticketAmount == 3000 ...) { // DEAD — can never be true
        $this->groundCount(1);
    }
}
// Result: groundCount() never called for VIP → stats are wrong
```

### Bug 3 — Double queries (exists + first)
```php
// BAD
elseif (ProducedTicket::where('ticketId', $id)->exists()) {   // query 1
    $ticket = ProducedTicket::where('ticketId', $id)->first(); // query 2 — same!
// FIX
$ticket = ProducedTicket::where('ticketId', $id)->first();
if ($ticket) { ... }
```

### Bug 4 — badge_status mixed types
Column has both `1` (integer) and `"PRINTED"` (string). Blade checks `== 'UNPRINTED'` so integer rows are invisible to filters.

### Performance Issues
| Issue | Impact |
|---|---|
| Up to 6 sequential DB queries per scan | 6x DB load |
| No Redis/Memcached caching | Every scan hits DB |
| `groundCount()` called synchronously | Adds latency to every scan |
| Base64 images in `company_members.member_image` | Memory exhaustion (crashed at /print_badges/create) |
| Rate limit `throttle:6000,1` (100 req/sec) | Will reject valid scans at peak |
| No confirmed DB indexes on ticketId, ticketNo | Full table scans as data grows |

---

## Redis — Ready to Install
Server has **13 GB available RAM**. Redis for ticket caching needs < 1 MB.
```bash
sudo apt install redis-server -y
sudo systemctl enable redis-server
sudo systemctl start redis-server
redis-cli ping   # → PONG
```

---

## Proposed QR Code Format (Smart Tickets)
Embed date + type into QR content so scanner can reject expired tickets **without hitting DB**:
```
TYPE|DATE|UUID
TICKET|20260724|550e8400-...       ← one-time daily
VIP|20260701-20260831|550e8400-... ← season range
CAR|20260724|550e8400-...
BADGE|20260701-20260831|550e8400-...
```
Frontend (Flutter) checks date before sending to server → 0 network calls for expired tickets.
Server checks date again as security layer.

---

## Load Testing Plan
- Tool: **k6 v1.6.1** (installed via Homebrew)
- Target: 1,000 scans/second
- Two test modes planned:
  1. Agent mode — sequential scans like a real gate agent
  2. Scroll/list mode — concurrent scans simulating multiple gates

---

## Badge Groups — DB table: `badge_groups`
Columns: `group_id`, `group_name`, `group_background_color`, `text_color`
Used to color the strip on the badge dynamically.

---

## Misc Notes
- `ditf26.svg` → `/Users/macbook/react.projects.tantrade.go.tz/public/ditf26.svg`
- Export badge background as **PNG** (not SVG) from Illustrator — SVG clips elements outside artboard
- When re-exporting PNG, copy to Laravel: `php -r "copy('...react.../public/ditf26.png', '...tantrade.go.tz/public/ditf26.png');"`
- The strip rectangle should be **removed from the PNG** — it's now rendered as a CSS div
- The name/group sample text should be **removed from the PNG** — rendered as HTML overlays
