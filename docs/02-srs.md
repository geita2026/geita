# 02 — Software requirements specification

This document restates the FRD's requirements as built, and records the status of each one. Where the FRD left a choice open, the decision taken is written in the **Notes** column. The FRD remains the source of the requirement text.

**Status key:** ✅ implemented and verified by an automated test or in the running app · 🟡 implemented with a stated limit · ⬜ not in version 1.0.0

## 1. Users and roles (FRD §4)

Nine roles; permissions are in `06-rbac.md`. Exhibitors create their own accounts; staff accounts are created by the System Administrator.

## 2. Functional requirements

### Exhibition management
| ID | Requirement | Status | Notes |
|---|---|---|---|
| FR-001 | Create exhibition with name, code, theme, objectives, venue, dates, registration dates, payment deadline, categories, terms, contact, status | ✅ | Also captures mandatory document types, VAT rate, and whether exhibitors may select their own space |
| FR-002 | Edit exhibition until finalised | ✅ | Locked once `completed` or `cancelled` |
| FR-003 | Publish for registration | ✅ | Refused if no spaces are configured |
| FR-004 | Close registration automatically or manually | ✅ | Manual: status change. Automatic: daily housekeeping job (BR-010) |

### Space management
| ID | Requirement | Status | Notes |
|---|---|---|---|
| FR-005 | Create space with number, hall, category, size, location, price, status, facilities, special conditions | ✅ | Plus a block generator for many identical spaces |
| FR-006 | Space categories | ✅ | Defined per exhibition, each with a colour for the map |
| FR-007 | Space dimensions | ✅ | Width × length in metres; area is derived |
| FR-008 | Price per category | ✅ | Price per m²; space price = area × rate unless overridden |
| FR-009 | Space status | ✅ | All eight FRD statuses. Officers set available / reserved / blocked / cancelled / maintenance by hand; pending approval / allocated / occupied follow from allocations |
| FR-010 | Prevent double allocation | ✅ | Database unique index, plus application checks |

### Exhibitor registration and application
| ID | Requirement | Status | Notes |
|---|---|---|---|
| FR-011 | Exhibitor account creation | ✅ | As on SAMS: first and last name, phone, email, password and acceptance of the terms. Nothing about the organisation — that is asked when applying |
| FR-012 | Secure login | ✅ | Account locks after 5 failed attempts |
| FR-013 | Exhibitor profile | ✅ | The exhibitor says whether they exhibit as an **individual** (no registered business) or a **registered business**, and gives only the details that apply. Individuals are invoiced in their own name |
| FR-014 | Submit space application | ✅ | One live application per exhibitor per exhibition; saved as draft, then submitted. The first application collects the applicant details (individual or business); later ones confirm them |
| FR-015 | Application tracking | ✅ | FRD statuses plus `returned` (needed by FR-021) |
| FR-016 | Download documents | ✅ | Supporting documents, payment evidence, invoices, receipts, allocation letter |
| FR-017 | — | — | Not defined in the FRD |

### Review and approval
| ID | Requirement | Status | Notes |
|---|---|---|---|
| FR-018 | Review application | ✅ | |
| FR-019 | Approve | ✅ | Refused if mandatory documents are missing (BR-003) |
| FR-020 | Reject with reasons | ✅ | Reason is mandatory |
| FR-021 | Return for correction | ✅ | Reason is mandatory; exhibitor edits and resubmits |

### Selection and allocation
| ID | Requirement | Status | Notes |
|---|---|---|---|
| FR-022 | Display available spaces by exhibition, hall, category, size, location, price, status | ✅ | Floor plan and Spaces list |
| FR-023 | Space selection by exhibitors | ✅ | Per-exhibition switch. A selection is allocated and invoiced at once, without officer confirmation; the exhibitor may give it back until something is paid |
| FR-024 | Allocation by officers | ✅ | |
| FR-025 | Automatic allocation | ✅ | By category, closest size, application priority then submission time, availability. Payment status is not used as a rule because payment follows allocation in this process |
| FR-026 | Reallocation with reason | ✅ | |
| FR-027 | Transfer between exhibitors | ✅ | Needs the authorisation permission |
| FR-028 | Cancellation with date, reason, user | ✅ | "Approval where required" = BR-006 authorisation for confirmed allocations |

### Space map
| ID | Requirement | Status | Notes |
|---|---|---|---|
| FR-029 | Digital map showing number, location, size, category, status, exhibitor | 🟡 | A grid per hall, not a scaled drawing. Exhibitors do not see who holds other spaces |
| FR-030 | Interactive map | ✅ | Press a space for details and actions |

### Billing
| ID | Requirement | Status | Notes |
|---|---|---|---|
| FR-031 | Generate invoice automatically | ✅ | On allocation (or on confirmation of a self-selection). One open invoice per application; each space is a line. A GePG bill for the balance follows automatically (FR-059) |
| FR-032 | Payment status | ✅ | All six FRD statuses. `refunded` exists as a status but there is no refund workflow |
| FR-033 | Payment verification | ✅ | GePG payments verify themselves. A payment recorded by hand needs evidence and (by staff) a reason, cannot be cash, and is verified by a different Finance Officer (two-person rule); Management and the Auditor are told. `MANUAL_PAYMENTS=off` disables recording by hand |
| FR-034 | Payment receipt | ✅ | Numbered receipt and PDF on verification |

### Notifications
| ID | Requirement | Status | Notes |
|---|---|---|---|
| FR-035..038 | Application, approval, payment, allocation notices | 🟡 | In-system: ✅. Email and SMS are dispatched through drivers that default to the log |
| FR-039 | Reminders | 🟡 | Daily job: pending applications, payments due and overdue, registration deadline, check-in. Same channel limit |

### Check-in
| ID | Requirement | Status | Notes |
|---|---|---|---|
| FR-040 | Record check-in | ✅ | Only for confirmed participants |
| FR-041 | Occupancy status | ✅ | |
| FR-042 | Check-in confirmation | ✅ | Reference number and notification |

### Users, audit, reports
| ID | Requirement | Status | Notes |
|---|---|---|---|
| FR-043 | Create user | ✅ | One-time temporary password |
| FR-044 | Assign roles | ✅ | Roles are fixed sets of permissions; custom permission editing is not offered |
| FR-045 | Activate / deactivate | ✅ | Ends the user's sessions immediately |
| FR-046 | Password reset | 🟡 | By administrator. No self-service email reset |
| FR-047 | Role-based access | ✅ | |
| FR-048 | Record user activities | ✅ | User, activity, date/time, reference, old and new values, IP address |
| FR-049 | Audit report | 🟡 | Searchable, filterable screen. No CSV export of the audit trail |
| FR-050..054 | Availability, allocation, exhibitor, payment, performance reports | ✅ | On screen and CSV |
| FR-055 | Management dashboard | ✅ | All nine FRD KPIs and more |
| FR-056 | Search | ✅ | Exhibitors, applications, spaces, invoices, payments, exhibitions |
| FR-057 | Filtering | ✅ | Date, exhibition, category, status, exhibitor type, payment status, application status |
| FR-058 | Generate documents | ✅ | Allocation letter, invoice, receipt, allocation schedule (PDF); exhibitor list and reports (CSV) |

### Integration
| ID | Requirement | Status | Notes |
|---|---|---|---|
| FR-059 | Payment platform | 🟡 | Direct GePG integration: bills, control numbers, payment notifications, cancellation, control-number reuse, daily reconciliation, all signed. Runs against a built-in GePG simulator until the institution is registered with the Ministry of Finance; the message format must then be checked against MoF's specification |
| FR-060 | SMS / email | 🟡 | Driver seams in place; log by default |
| FR-061 | Financial system | ⬜ | CSV export only |
| FR-062 | Secure APIs | ✅ | Versioned REST API with bearer tokens; see `05-api.md` |

## 3. Business rules (FRD §22)

| ID | Rule | Enforced by |
|---|---|---|
| BR-001 | One exhibitor per space per exhibition | Unique index `allocations.active_space_id` |
| BR-002 | Only approved applications are allocated, unless authorised as an exception | `AllocationService::guardApplication` |
| BR-003 | No confirmation with incomplete mandatory documents | Checked at submission, approval and confirmation. Each exhibition sets one list for businesses and one for individuals (who have no business licence or TIN) |
| BR-004 | Invoice follows category and pricing rules | `BillingService::billAllocation` |
| BR-005 | Allocation subject to availability | `AllocationService::guardSpace` |
| BR-006 | Changes to confirmed allocations need authorisation | `allocations.authorize` permission |
| BR-007 | Every allocation, reallocation, cancellation audited | `Audit::log` in the same transaction |
| BR-008 | Payment status updated by the integrated mechanism | A signed GePG payment notification records and verifies the payment, issues the receipt and confirms participation; reconciliation recovers lost notifications. Payments outside GePG are verified by Finance |
| BR-009 | Access by role only | `User::allowed` in every controller |
| BR-010 | Registration closes automatically after the deadline unless extended | `registration_open` is computed from the dates; housekeeping job changes the status |

## 4. Non-functional requirements

The FRD is a functional document; these are the qualities the build was held to.

| Area | Requirement | How |
|---|---|---|
| Security | Passwords hashed; ≥10 characters, mixed case and a digit | Laravel `Password` rule, bcrypt |
| Security | Tokens never exposed to browser scripts | httpOnly cookie on the Next server |
| Security | Brute-force protection | Lock after 5 failures; login rate limit 10/min |
| Security | Content Security Policy and standard headers | `frontend/next.config.ts` |
| Integrity | Allocation and billing are atomic | Database transactions |
| Auditability | Audit entries cannot be changed | Model refuses update/delete; DB triggers in production |
| Usability | Works on phone, tablet and desktop; light and dark; reduced-motion option | Responsive layout, theme switch, Settings |
| Accessibility | Keyboard reachable, labelled controls, status announced | HeroUI (React Aria) components |
| Maintainability | One place for permissions; one write path from the client | `config/gememis.php`; `server/actions.ts` |

## 5. Acceptance criteria (FRD §27)

All fifteen criteria are covered by UAT scripts in `07-test-plan.md`. Criterion 10 ("notifications are successfully generated") is met for in-system notifications; email/SMS delivery depends on the providers being configured.
