# 06 — Roles and permissions

Defined in one place: `backend/config/gememis.php`. Controllers call `permit('…')`; the web client receives the signed-in user's permission list from `GET /auth/me` and uses it only to hide controls.

## Permission matrix

| Permission | What it allows | Sys Admin | Exhibition Admin | Space Officer | Registration Officer | Finance Officer | Management | Customer Service | Auditor | Exhibitor |
|---|---|:-:|:-:|:-:|:-:|:-:|:-:|:-:|:-:|:-:|
| `exhibitions.manage` | Create, edit, publish, close exhibitions | ● | ● | | | | | | | |
| `spaces.manage` | Halls, categories, spaces, prices, manual status | ● | ● | ● | | | | | | |
| `applications.review` | Approve, reject, return | ● | ● | | ● | | | | | |
| `allocations.manage` | Allocate, confirm, auto-allocate, reallocate, cancel | ● | ● | ● | | | | | | |
| `allocations.authorize` | Change confirmed allocations; transfer; allocate before approval (BR-002, BR-006) | ● | ● | | | | | | | |
| `finance.manage` | Verify or reject payments; record a payment for an exhibitor; retry a control number; run a GePG reconciliation; simulate a GePG payment (simulator only) | ● | | | | ● | | | | |
| `finance.view` | Payments list, payments report, Payment gateway page (GePG settings, message log, reconciliation) | ● | ● | | | ● | ● | | ● | |
| `checkin.manage` | Check-in and occupancy | ● | ● | ● | | | | ● | | |
| `exhibitors.view` | Exhibitor directory | ● | ● | ● | ● | ● | ● | ● | ● | |
| `exhibitors.manage` | Classify or suspend an exhibitor; apply on an exhibitor's behalf | ● | ● | | ● | | | ● | | |
| `reports.view` | Reports and allocation schedule | ● | ● | ● | ● | ● | ● | | ● | |
| `users.manage` | Users, roles, activation, password reset | ● | | | | | | | | |
| `audit.view` | Audit trail | ● | | | | | ● | | ● | |
| `staff` | See all records of all exhibitors (read) | ● | ● | ● | ● | ● | ● | ● | ● | |

## What an exhibitor can do

No permission in the matrix, but on **their own organisation's records only**: maintain the profile; create, edit, submit and cancel applications; upload and remove documents (while draft or returned); select an available space when the exhibition allows it; withdraw an unconfirmed selection; record payments with evidence; download their invoices, receipts and allocation letter.

## Separation of duties

- The officer who allocates space cannot verify the payment for it (`allocations.manage` and `finance.manage` share no role except System Administrator).
- Auditors and Management read everything and change nothing.
- A user cannot deactivate themselves or change their own role.
- The System Administrator role holds every permission. In production it should be held by few people and used only for administration; day-to-day work belongs to the functional roles.

## Changing a role's permissions

Edit the `permissions` array in `config/gememis.php`, update this matrix, and add a test. Permissions are not editable from the user interface in version 1.0.0.
