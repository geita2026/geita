# 10 — Release notes

## Version 1.0.0

First release. Implements the GeMEMIS Functional Requirements Document.

### Included

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
- Versioned REST API and a signed payment callback.

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
| FR-059 | Payment callback tested with a simulated gateway only |
| FR-061 | No financial-system integration; CSV export only |
| FR-029 | Floor plan is a grid per hall, not a scaled drawing |
| FR-032 | `refunded` status exists but there is no refund workflow; overpayment after a change is held as credit |
| FR-044 | Roles are fixed permission sets; not editable in the interface |
| Language | English only |
| Front-end tests | None automated |

### Suggested for the next release

1. Connect the contracted payment platform and SMS gateway.
2. Self-service password reset by email.
3. Refund and credit-note workflow.
4. Scaled floor plan with drag-to-place editing, or import from a drawing.
5. Swahili interface.
6. Automated browser tests for the main journeys.
7. Audit trail export and scheduled report emails.
