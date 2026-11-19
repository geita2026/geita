# 07 — Test plan and user acceptance testing

## 1. Levels of testing

| Level | What | Tool | Where |
|---|---|---|---|
| Automated feature tests | Business rules through the real HTTP API, on an in-memory database | PHPUnit | `backend/tests/Feature/GememisTest.php` |
| Static checks | Types and lint for the web client | `tsc`, ESLint | `npm run check` |
| Build | Every page compiles | `next build` | `npm run build` |
| System test | The two parts together, in a browser, on demo data | Manual | This document §4 |
| User acceptance | The FRD's scenarios, by the people who will use it | Manual | This document §5 |

Run before every release:

```bash
cd backend && php artisan test
```

```bash
cd frontend && npm run check && npm run build
```

## 2. Automated tests

Test names carry the FRD's test-case numbers (FRD §25: TC-nnn proves FR-nnn).

| Test | Proves | What it does |
|---|---|---|
| TC-001 | FR-001, BR-009 | Finance Officer is refused; Exhibition Administrator creates a draft; audit entry written |
| TC-005 | FR-005, FR-007, FR-008 | A 3 × 6 m space in a 10,000/m² category is priced 180,000 |
| TC-010 | FR-010, BR-001, BR-005 | Second allocation of a held space is refused by the API; a direct database insert is refused by the unique index |
| TC-012 | FR-012 | Five wrong passwords lock the account; the right password is then refused |
| TC-011 | FR-011 | Sign-up needs only name, phone, email, password and the terms; the applicant type is not set yet |
| TC-014 | FR-014, BR-003 | Submission without the mandatory document is refused, naming the document |
| TC-014 (applicant) | FR-013, FR-014 | Applying requires saying individual or business; a business needs its name and registration number, the country must be on the list; an individual is billed in their own name with no TIN |
| TC-014 (individual documents) | BR-003 | An individual is asked for the individuals' documents (National ID), not the business licence |
| TC-023 | FR-023 | Exhibitor selection is pending, holds the space, raises no invoice; a second selection beyond the requested number is refused; officer confirmation invoices it |
| TC-024 | FR-024, BR-002, FR-031, BR-004 | A draft application cannot be allocated; an approved one is, with invoice 90,000 + 18% VAT = 106,200 |
| TC-026 | FR-026, FR-028, BR-007 | Reallocation without a reason is refused; with a reason the old space frees and the new one is held; cancellation frees the space, cancels the invoice, returns the application to approved |
| TC-034 | FR-032, FR-033, FR-034 | Duplicate transaction reference refused; exhibitor cannot verify; part payment → partially paid; full payment → paid, receipt PDF, participation confirmed |
| TC-033a | FR-033 | Two-person rule: the Finance Officer who recorded a payment gets 422; a second one verifies; Management and the Auditor are notified; audit `payment.verified_manual` |
| TC-033b | FR-033 | System Administrator cannot record or verify a payment (403) |
| TC-033c | FR-033 | GePG on: exhibitor cannot record (403); staff without evidence or reason → 422; cash → 422; `MANUAL_PAYMENTS=off` → 403 |
| TC-047 | FR-047, BR-009 | An exhibitor cannot read another's application, the audit trail or the user list |
| TC-048 | FR-048 | Status change is logged with the old value; the entry cannot be modified |
| BR-010 | FR-004, BR-010 | After the closing date an application is refused; the housekeeping job sets `registration_closed` |

GePG tests (`backend/tests/Feature/GepgTest.php`, against the simulator):

| Test | Proves |
|---|---|
| Allocation sends a signed bill | Bill amount incl. VAT, GFS code, our RSA signature over the exact content; control number reaches the invoice and the exhibitor |
| GePG payment | Payment verified, receipt, invoice paid, participation confirmed; GePG's retry of the same notification is acknowledged and not recorded twice |
| Unsigned message | A message not signed by GePG is refused (`7303`) and changes nothing |
| Part payment | The control number stays open for the rest; never re-billed |
| Amount change | Reallocation to a dearer space cancels the bill at GePG and issues a new control number with the new GFS code |
| Settled outside GePG | A bank transfer recorded by one Finance Officer and verified by another cancels the open control number |
| Reconciliation | A payment whose notification was lost is recovered; a "GePG" payment unknown to GePG is flagged |
| Live mode | Signed XML posted to the configured URL with `Gepg-Com` / `Gepg-Code` headers; a refusal code marks the bill failed and tells Finance |
| Off mode | No bill, no control number |

Current result: **24 tests, 193 assertions, all passing.**

### Not covered by automated tests

Stated so the gaps are tested by hand: GePG's real test environment (only the simulator is automated — see UAT-19); transfer between exhibitors (FR-027); automatic allocation (FR-025); check-in (FR-040..042); reports and CSV (FR-050..054); search (FR-056); user administration (FR-043..046); PDFs other than the receipt; email and SMS delivery; the web client's pages (no browser test suite).

## 3. Test environment

`php artisan migrate:fresh --seed` gives a repeatable starting state: one published exhibition (code `GMTE27`), 106 spaces in four halls, and exhibitors in every status. Accounts are listed in the README.

## 4. System test record (version 1.0.0)

Carried out in a browser against the demo data, signed in as the Exhibition Administrator.

| Check | Result |
|---|---|
| Sign in; dashboard KPIs and charts render | Pass |
| Floor plan renders four halls with status colours and category stripes | Pass |
| Space dialog morphs open from the cell; choosing from the select does not close it | Pass |
| Allocate space A05 to an approved application from the map; cell and dialog update | Pass |
| Application page: review actions, documents, allocate | Renders; "Return for correction" dialog opens |
| Invoice page: lines, VAT, part payment, receipt link | Renders correctly |
| All list and detail pages respond without error | Pass (25 routes) |
| Invoice, receipt, allocation letter, schedule PDFs; payments CSV; document download | Pass (all return a file) |
| Pages a role may not open redirect to the dashboard | Pass (checked as Exhibition Administrator, exhibitor, Finance Officer, System Administrator) |
| Every menu page loads without error as exhibitor, Finance Officer and System Administrator | Pass (smoke test only) |

Not exercised in this pass: the exhibitor's journey in the browser (registration through payment), light theme, phone width, and the roles other than Exhibition Administrator. These belong to UAT below.

## 5. User acceptance test scripts

Each script lists who performs it, the steps, and the expected result. Record Pass/Fail, the tester and the date. The fifteen FRD acceptance criteria (§27) map to these scripts in §6.

**UAT-01 User registration** — *Exhibitor.* Open the sign-in page → Create an exhibitor account → enter name, phone, email and password; submit once without accepting the terms (**expect** refused, nothing typed is lost), then accept → Create account. **Expect:** signed in on the dashboard; "Apply for space" offered.

**UAT-02 Exhibition creation** — *Exhibition Administrator.* Exhibitions → New exhibition → complete → Create. **Expect:** exhibition page opens, status Draft; exhibitors cannot see it.

**UAT-03 Space configuration** — *Space Allocation Officer.* On the exhibition: add a hall and two categories with prices. Spaces → Generate a block (10 spaces, 3 × 3). **Expect:** 10 spaces listed with price = 9 × rate; visible on the Space map.

**UAT-04 Publication** — *Exhibition Administrator.* Publish for registration. **Expect:** status "Open for registration"; exhibitors are notified and can see it.

**UAT-05 Exhibitor application** — *Exhibitor.* Open the exhibition → Apply for space → choose **An individual** (repeat with another account as **A registered business**) → Save. **Expect:** an individual is asked for no company details and for the individuals' documents only. Press Submit before uploading. **Expect:** refused, naming the missing documents. Upload them → Submit. **Expect:** status Submitted; notification received.

**UAT-06 Application approval** — *Registration Officer.* Applications → open it → Return with a reason. **Expect:** exhibitor sees the reason and can edit. After resubmission → Approve. **Expect:** status Approved; exhibitor notified. Also reject a different application; a reason is demanded.

**UAT-07 Space selection** — *Exhibitor.* Space map → press an available space → Select this space. **Expect:** cell marked "Yours"; status Pending approval; no invoice yet.

**UAT-08 Space allocation** — *Space Allocation Officer.* Allocations → Confirm the selection. For another approved application: Space map → press a space → choose the application → Allocate. **Expect:** spaces Allocated; applications Pending payment.

**UAT-09 Duplicate allocation** — *Two Space Allocation Officers at once.* Both open the same available space and allocate it to different applications. **Expect:** one succeeds; the other is told the space has just been taken.

**UAT-10 Invoice generation** — *Exhibitor / Finance.* Open the invoice. **Expect:** number, exhibitor, exhibition, space number, size, category, unit price, quantity, VAT, total, payment deadline, control number. Download the PDF.

**UAT-11 Payment and verification** — *Exhibitor, then Finance Officer (with `GEPG_MODE=off`).* Exhibitor: Record a payment with evidence. Finance: Payments → view evidence → Verify. **Expect:** invoice Paid; receipt PDF; application Confirmed; exhibitor notified. Repeat with a part payment → Partially paid, not confirmed. Reject a payment → reason reaches the exhibitor.

**UAT-12 Allocation letter and check-in** — *Exhibitor, then Customer Service.* Exhibitor downloads the allocation letter. Check-in → Check in → Mark occupied. **Expect:** check-in reference; space shows Occupied on the map; occupancy rate changes on the dashboard. An unpaid exhibitor cannot be checked in.

**UAT-13 Reallocation, transfer, cancellation** — *Space Allocation Officer, Exhibition Administrator.* Reallocate with a reason; attempt it on a confirmed participant as Space Officer (**expect** refused, BR-006) then as Exhibition Administrator (succeeds). Transfer a space to another approved exhibitor. Cancel an allocation. **Expect:** spaces and invoices follow each change; each appears in the audit trail with its reason.

**UAT-14 Reports and dashboard** — *Management.* Dashboard figures agree with the Reports pages; download each CSV. **Expect:** totals match the lists.

**UAT-15 Access control** — *Each role.* Confirm the menu shows only that role's pages; attempt an action outside the role by URL. **Expect:** redirected or refused.

**UAT-16 Audit trail** — *Auditor.* Find the entries for UAT-08, UAT-11 and UAT-13 by reference. **Expect:** user, date and time, activity, old and new values, IP address. No edit or delete control exists.

**UAT-17 User administration** — *System Administrator.* Create a user (one-time password shown); sign in as them and be forced to change it; change their role; deactivate (they are signed out); reset password; unlock a locked account.

**UAT-11b Manual payment safeguards** — *Two Finance Officers, Management.* With GePG on, Finance Officer A records a payment outside GePG with a slip and reason. **Expect:** A sees *Needs another Finance Officer*, not **Verify**; the exhibitor sees no **Record a payment**. Finance Officer B verifies. **Expect:** receipt issued; Management and the Auditor notified; audit entry *payment.verified_manual*. As System Administrator, the invoice shows no **Record a payment**.

**UAT-19 GePG (in GePG's test environment)** — *Finance Officer, Exhibitor.* With `GEPG_MODE=live` against GePG's test environment: allocate a space → a control number arrives within minutes and reaches the exhibitor by SMS. Pay part of it through GePG's test channel → payment, receipt and *Partially paid*; pay the rest → *Paid*, participation confirmed. Reallocate a confirmed exhibitor to a dearer space → the old control number is cancelled at GePG and a new one issued. Run **Reconcile a day** → *All matched*. In the message log every message from GePG shows *Signature valid*.

**UAT-18 Notifications** — *Exhibitor and officers.* Confirm the bell shows a notice for each of: application submitted, approved/rejected, invoice issued, payment verified, space allocated. Where email/SMS providers are configured, confirm delivery.

## 6. Acceptance criteria traceability (FRD §27)

| # | Criterion | Requirement | Automated | UAT |
|---|---|---|---|---|
| 1 | Authorised users can create exhibitions | FR-001 | TC-001 | UAT-02 |
| 2 | Spaces can be configured and categorised | FR-005..008 | TC-005 | UAT-03 |
| 3 | Exhibitors can register online | FR-011 | — | UAT-01 |
| 4 | Exhibitors can submit applications | FR-014 | TC-014 | UAT-05 |
| 5 | Officers can review and approve | FR-018..021 | (within TC-010, TC-024) | UAT-06 |
| 6 | Duplicate allocation prevented | FR-010 | TC-010 | UAT-09 |
| 7 | Allocated spaces reflected in real time | FR-009, FR-022 | TC-024, TC-026 | UAT-08 |
| 8 | Invoices generated accurately | FR-031 | TC-024 | UAT-10 |
| 9 | Payments recorded and verified | FR-033, FR-034, FR-059 | TC-034, GePG tests | UAT-11, UAT-19 |
| 10 | Notifications generated | FR-035..039 | — | UAT-18 |
| 11 | Reports generated | FR-050..055 | — | UAT-14 |
| 12 | Activities recorded in the audit trail | FR-048 | TC-048 | UAT-16 |
| 13 | Role-based access works | FR-047 | TC-001, TC-047 | UAT-15 |
| 14 | Accurate space utilisation information | FR-050, FR-055 | — | UAT-14 |
| 15 | Users complete UAT | — | — | Sign-off below |

## 7. Defect handling and sign-off

Log each defect with the script number, steps, expected and actual result, and severity (Critical: wrong money or allocation, or data exposed; High: a requirement cannot be completed; Medium: workaround exists; Low: cosmetic). Release needs no open Critical or High defects.

| Role | Name | Signature | Date |
|---|---|---|---|
| Exhibition Management Unit | | | |
| Finance Unit | | | |
| ICT Unit | | | |
| Management | | | |
