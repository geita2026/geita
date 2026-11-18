# 05 — API reference

Base URL: `/api/v1`. JSON in, JSON out. Route definitions: `backend/routes/api.php`.

## Conventions

- **Authentication:** `Authorization: Bearer <token>` on everything except the public endpoints. Tokens come from `POST /auth/login` or `/auth/register` and expire after `SANCTUM_TOKEN_MINUTES` (default 720).
- **Errors:**

| Status | Meaning | Body |
|---|---|---|
| 401 | No or invalid token; account deactivated or locked | `{message}` |
| 403 | Role may not do this, or record belongs to another exhibitor | `{message}`; with `code: "password_change_required"` when a temporary password must be changed first |
| 404 | No such record | `{message}` |
| 422 | Validation failed | `{message, errors: {field: [..]}}` |
| 422 | Business rule refused | `{message, rule: "BR-00x" \| null}` |

- **Lists** marked *paginated* return Laravel pagination: `{data, current_page, last_page, total, per_page}`; `?page=` and `?per_page=` (5–100).
- **Filters** taking several values use commas: `?status=approved,confirmed`.

## Public

| Method | Path | Purpose |
|---|---|---|
| GET | `/health` | Liveness |
| GET | `/meta` | Role and status vocabularies, and the country list registration accepts |
| POST | `/auth/login` | `{email, password}` → `{token, user, permissions}`. 10/min |
| POST | `/auth/register` | Exhibitor self-registration (FR-011) → `{token, user, permissions}` |
| POST | `/integrations/gepg` (also `/control-number`, `/payment`, `/reconciliation`) | GePG callbacks (FR-059), see below |

## Session

| Method | Path | Purpose |
|---|---|---|
| GET | `/auth/me` | Current user, exhibitor profile, permission list |
| POST | `/auth/logout` | Revoke the current token |
| PUT | `/auth/password` | `{current_password, password, password_confirmation}`; revokes other sessions |
| PUT | `/auth/profile` | Exhibitor updates their organisation (FR-013) |
| GET | `/search?q=` | Global search (FR-056) |
| GET | `/notifications` | Latest 30 |
| POST | `/notifications/read` | `{id?}`; all when omitted |

## Exhibitions, halls, categories

| Method | Path | Permission | Purpose |
|---|---|---|---|
| GET | `/exhibitions` | any | List; exhibitors see published onward. `?status`, `?q` |
| POST | `/exhibitions` | `exhibitions.manage` | FR-001 |
| GET | `/exhibitions/{id}` | any | With halls and categories |
| PUT | `/exhibitions/{id}` | `exhibitions.manage` | FR-002 |
| POST | `/exhibitions/{id}/transition` | `exhibitions.manage` | `{status}` — publish, close, reopen, ongoing, completed, cancelled |
| POST | `/exhibitions/{id}/halls` | `spaces.manage` | |
| PUT / DELETE | `/halls/{id}` | `spaces.manage` | Delete only when empty |
| POST | `/exhibitions/{id}/categories` | `spaces.manage` | FR-006, FR-008 |
| PUT / DELETE | `/categories/{id}` | `spaces.manage` | `reprice: true` re-prices unheld spaces |

## Spaces

| Method | Path | Permission | Purpose |
|---|---|---|---|
| GET | `/exhibitions/{id}/spaces` | any | All spaces with hall and category. Staff also get the holder; exhibitors get `mine`. `?hall_id`, `?space_category_id`, `?status`, `?q`, `?min_area`, `?max_price` |
| POST | `/exhibitions/{id}/spaces` | `spaces.manage` | FR-005; price defaults to area × category rate |
| POST | `/exhibitions/{id}/spaces/generate` | `spaces.manage` | `{hall_id, space_category_id, prefix, count, width_m, length_m}` |
| PUT | `/spaces/{id}` | `spaces.manage` | Size, category and price are locked while the space is held |
| POST | `/spaces/{id}/status` | `spaces.manage` | `{status, reason?}` — manual statuses only (FR-009) |
| DELETE | `/spaces/{id}` | `spaces.manage` | Only with no allocation history |

## Applications and documents

| Method | Path | Permission | Purpose |
|---|---|---|---|
| GET | `/applications` | any | *Paginated.* Exhibitors: own. `?exhibition_id`, `?status`, `?from`, `?to`, `?q` |
| POST | `/applications` | exhibitor, or `exhibitors.manage` with `exhibitor_id` | Draft (FR-014) |
| GET | `/applications/{id}` | owner or staff | With documents, allocations, invoices, `missing_documents` |
| PUT | `/applications/{id}` | owner or staff | Draft or returned only |
| POST | `/applications/{id}/submit` | owner or staff | Refused if documents are missing or registration is closed |
| POST | `/applications/{id}/review` | `applications.review` | `{decision: approve\|reject\|return, notes}` (FR-018..021) |
| POST | `/applications/{id}/cancel` | owner or staff | `{reason}` |
| POST | `/applications/{id}/documents` | owner or staff | multipart `{type, file}`; pdf, jpg, png, doc, docx ≤ 10 MB |
| GET / DELETE | `/documents/{id}` | owner or staff | Download / remove |
| GET | `/applications/{id}/allocation-letter` | owner or staff | PDF; confirmed applications only |

## Allocations and check-in

| Method | Path | Permission | Purpose |
|---|---|---|---|
| GET | `/allocations` | any | *Paginated.* `?exhibition_id`, `?status`, `?occupancy`, `?q` |
| POST | `/allocations` | `allocations.manage`, or the owning exhibitor | `{application_id, space_id, exception?}`. Exhibitor → `pending` (FR-023); officer → `allocated` + invoice (FR-024) |
| POST | `/allocations/{id}/confirm` | `allocations.manage` | Confirms a self-selection and invoices it |
| POST | `/applications/{id}/auto-allocate` | `allocations.manage` | FR-025, one application |
| POST | `/exhibitions/{id}/auto-allocate` | `allocations.manage` | FR-025, all approved applications → `{allocated, skipped}` |
| POST | `/allocations/{id}/reallocate` | `allocations.manage` | `{space_id, reason}` (FR-026) |
| POST | `/allocations/{id}/transfer` | `allocations.manage` + `allocations.authorize` | `{application_id, reason}` (FR-027) |
| POST | `/allocations/{id}/cancel` | `allocations.manage`, or owner for a pending selection | `{reason}` (FR-028) |
| POST | `/allocations/{id}/occupancy` | `checkin.manage` | `{occupancy_status, notes?}` (FR-040..042) |

## Billing

| Method | Path | Permission | Purpose |
|---|---|---|---|
| GET | `/invoices` | any | *Paginated.* Exhibitors: own. `?exhibition_id`, `?status`, `?from`, `?to`, `?q` |
| GET | `/invoices/{id}` | owner or staff | With lines and payments |
| GET | `/invoices/{id}/pdf` | owner or staff | PDF |
| POST | `/invoices/{id}/payments` | owner, or `finance.manage` | multipart `{amount, paid_on, method, payer_reference, evidence?}` |
| GET | `/payments` | `finance.view` | *Paginated.* `?status`, `?exhibition_id`, `?q` |
| POST | `/payments/{id}/verify` | `finance.manage` | `{decision: verify\|reject, notes}` (FR-033, FR-034) |
| GET | `/payments/{id}/evidence` | owner or staff | Uploaded evidence |
| GET | `/payments/{id}/receipt` | owner or staff | PDF; verified payments only |

## Dashboard, reports, documents

| Method | Path | Permission | Purpose |
|---|---|---|---|
| GET | `/exhibitions/{id}/dashboard` | any | Staff KPIs (FR-055), or the exhibitor's own progress |
| GET | `/exhibitions/{id}/reports/{type}` | `reports.view` (`payments` also needs `finance.view`) | `type`: `space-availability`, `space-allocation`, `exhibitors`, `payments`, `performance`. `?format=csv` downloads |
| GET | `/exhibitions/{id}/schedule` | `reports.view` | Allocation schedule PDF |

## Directory and administration

| Method | Path | Permission | Purpose |
|---|---|---|---|
| GET | `/exhibitors` | `exhibitors.view` | *Paginated.* `?q`, `?country`, `?exhibitor_type`, `?status`, `?exhibition_id` |
| GET | `/exhibitors/{id}` | owner or staff | With users and applications |
| PUT | `/exhibitors/{id}` | `exhibitors.manage` | `{exhibitor_type?, status?}` |
| GET | `/users` | `users.manage` | *Paginated.* `?role`, `?q` |
| POST | `/users` | `users.manage` | → `{user, temporary_password}` (shown once) |
| PUT | `/users/{id}` | `users.manage` | `{name?, phone?, role?, is_active?, unlock?}` |
| POST | `/users/{id}/reset-password` | `users.manage` | → `{temporary_password}` |
| GET | `/audit-logs` | `audit.view` | *Paginated.* `?q`, `?action` (prefix), `?user_id`, `?from`, `?to` |

## GePG (FR-059)

### Callbacks from GePG

`POST /integrations/gepg` — or `/integrations/gepg/control-number`, `/payment`, `/reconciliation` if GePG registers one address per message type; all go to the same handler, which reads the type from the message.

- Body: GePG XML, `<Gepg><content/><gepgSignature/></Gepg>`. The signature (RSA over the content element's exact text, base64) must verify against GePG's certificate.
- Reply: our signed acknowledgement, `application/xml`, e.g. `<Gepg><gepgPmtSpInfoAck><TrxStsCode>7101</TrxStsCode></gepgPmtSpInfoAck><gepgSignature>…</gepgSignature></Gepg>`.
- `7101` = processed (also for a repeated payment, which is not recorded twice); the failure and bad-signature codes are set in `config/gepg.php`.
- No bearer token: GePG's signature is the authentication. 503 when `GEPG_MODE=off`.

| Message received | Effect |
|---|---|
| `gepgBillSubResp` | Control number saved on the bill and invoice; exhibitor notified by SMS and email |
| `gepgPmtSpInfo` | Payment recorded and verified, receipt issued, invoice settled, participation confirmed when paid in full |
| `gepgSpReconcResp` | Day's payments compared; missing ones recorded, differences flagged to Finance |

### Requests GeMEMIS sends

To `GEPG_BASE_URL` + the paths in `config/gepg.php`, with the `Gepg-Com` and `Gepg-Code` headers: `gepgBillSubReq` (bill, and control-number reuse), `gepgBillCanclReq`, `gepgSpReconcReq`. Element names follow the published GePG Service Provider API; check them against the Ministry of Finance's specification during onboarding — they are all in `app/Services/Gepg/GepgXml.php`.

### Staff endpoints

| Method | Path | Permission | Purpose |
|---|---|---|---|
| GET | `/gepg/status` | `finance.view` | Mode, SP identity, callback address, bill counts, last problem |
| GET | `/gepg/messages` | `finance.view` | *Paginated* message log, verbatim XML. `?q` (reference), `?errors=1` |
| GET | `/gepg/messages/{id}` | `finance.view` | One message |
| GET | `/gepg/reconciliations` | `finance.view` | Last 30 runs |
| POST | `/gepg/reconciliations` | `finance.manage` | `{date}` — reconcile that day now |
| POST | `/invoices/{id}/gepg/retry` | `finance.manage` | Ask GePG again (after a failure) |
| POST | `/invoices/{id}/gepg/simulate-payment` | `finance.manage` | Simulator only: `{amount, silent?}` pays the control number; `silent` loses the notification for reconciliation to recover |

Invoices returned by `GET /invoices/{id}` include `gepg_status` and `gepg_bills` (newest first).
