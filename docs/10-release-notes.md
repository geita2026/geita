# 10 — Release notes

## Version 1.0.0

First release. Implements the GeMEMIS Functional Requirements Document.

### Included

**Updates in this release candidate**
- Sign-up as on SAMS (name, phone, email, password, terms); whether the exhibitor is an **individual** or a **registered business** is asked on the first application, and each is treated as such — individuals are invoiced in their own name and have their own mandatory documents.
- **Payment safeguards**: with GePG on, exhibitors pay only by control number; Finance may record a payment outside GePG as an exception — slip and reason required, no cash — and a *different* Finance Officer must verify it (two-person rule). Management and the Auditor are notified of every manual acceptance. The System Administrator no longer holds `finance.manage`. `MANUAL_PAYMENTS=off` turns manual recording off entirely.
- Countdown to the GePG control number's expiry on the invoice and beside **Pay invoice**.
- Optional distortion-wave transition (theme and accent changes), switchable in Settings.
- Fixes: forms no longer clear what was typed when the server refuses a submission; the Manrope typeface now loads (pages previously fell back to the system font).


- Exhibition life cycle: draft, publish, close and reopen registration, ongoing, completed.
- Halls, space categories with per-m² pricing, spaces, block generation, and an interactive floor plan.
- Exhibitor self-registration and organisation profile.
- Applications with mandatory documents; approve, reject, return for correction.
- Space selection by exhibitors (with officer confirmation), allocation by officers, automatic allocation, reallocation, transfer and cancellation, each with a recorded reason.
- Double allocation prevented at database level.
- Automatic invoices with VAT and a control number; payments with evidence; verification; numbered receipts; automatic confirmation of participation.
- Check-in and occupancy tracking.
- In-system notifications with email and SMS dispatch points; daily reminders.
- Dashboard with the FRD's KPIs; five reports with CSV export; global search; list filters.
- PDF invoice, receipt, allocation letter and allocation schedule.
- Nine roles; user administration; account lockout; forced change of temporary passwords.
- Append-only audit trail with old and new values and IP address.
- Versioned REST API.
- **GePG integration:** signed bills, control numbers by SMS and email, payment notifications that issue the receipt and confirm participation, automatic cancel-and-reissue when an amount changes, control-number reuse, daily reconciliation, and a Payment gateway page with the full message log. Runs against a built-in simulator until the institution is registered with GePG.

### Verified

- 13 automated API tests (115 assertions) covering the critical requirements and business rules.
- Web client type-checks, lints and builds.
- System test in a browser on demo data as Exhibition Administrator (`07-test-plan.md` §4).

### Not yet verified

- Pages were smoke-tested (load without error, role redirects correct) as an exhibitor, Finance Officer and System Administrator, but the exhibitor journey and the other staff roles have not been walked through step by step in a browser. Light theme and phone layout have not been checked.
- Not run against MySQL/MariaDB.
- No load testing.
- User acceptance testing has not been carried out.

### Known limits

| Area | Limit |
|---|---|
| FR-035..039, FR-060 | Email and SMS go to the log until providers are configured (SMS also needs a small adapter) |
| FR-046 | No self-service "forgot password"; administrators reset passwords |
| FR-049 | The audit trail is a screen; it has no CSV export |
| FR-059 | GePG integration tested against the built-in simulator only. The message format follows the published GePG Service Provider API and must be checked against the Ministry of Finance's specification at onboarding (`docs/09-operations.md` §9) |
| FR-061 | No financial-system integration; CSV export only |
| FR-029 | Floor plan is a grid per hall, not a scaled drawing |
| FR-032 | `refunded` status exists but there is no refund workflow; overpayment after a change is held as credit |
| FR-044 | Roles are fixed permission sets; not editable in the interface |
| Language | English only |
| Front-end tests | None automated |

### Suggested for the next release

1. Register with GePG, check the message format against MoF's specification, and run UAT-19 in GePG's test environment; connect the SMS gateway.
2. Self-service password reset by email.
3. Refund and credit-note workflow.
4. Scaled floor plan with drag-to-place editing, or import from a drawing.
5. Swahili interface.
6. Automated browser tests for the main journeys.
7. Audit trail export and scheduled report emails.
