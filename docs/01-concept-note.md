# 01 — Concept note

| | |
|---|---|
| System | Geita Mineral Exhibition Management Information System (GeMEMIS) |
| Version | 1.0.0 |
| Source | Functional Requirements Document (FRD), GeMEMIS |
| Lineage | Modelled on the space-management module of TANTRADE's SAMS, rebuilt as a standalone product |

## 1. Problem

Exhibition space is allocated with spreadsheets, letters and phone calls. The same booth can be promised twice, nobody can say at a given moment which spaces are free, invoices are typed by hand, payment is confirmed by walking a bank slip between offices, and there is no record of who changed an allocation or why.

## 2. What GeMEMIS does

It runs the FRD's process flow end to end, in one place:

```mermaid
flowchart LR
  A[Create exhibition] --> B[Configure spaces] --> C[Publish]
  C --> D[Exhibitor registers] --> E[Applies + documents]
  E --> F[Review and approval] --> G[Space selection / allocation]
  G --> H[Invoice] --> I[Payment + verification]
  I --> J[Participation confirmed] --> K[Check-in and occupancy]
  K --> L[Reports and evaluation]
```

## 3. Objectives (FRD §1.3) and how each is met

| Objective | Met by |
|---|---|
| Automate space allocation | Allocation service: officer allocation, exhibitor self-selection, automatic allocation |
| Central database of available and occupied spaces | `spaces` table with a live status; floor plan and Spaces list |
| Exhibitors register and apply online | Public registration page; application form with document upload |
| Prevent double allocation | Unique database index on the live allocation of a space (BR-001) |
| Transparency | Floor plan visible to exhibitors; every change audited with a reason |
| Automate invoicing and payment tracking | Invoice generated on allocation; payment verification issues the receipt |
| Real-time availability | Space status changes in the same transaction as the allocation |
| Monitoring and reporting | Dashboard KPIs and five reports with CSV export |
| Audit trail | Append-only `audit_logs` |
| Communication | In-system notifications, with email and SMS channels |
| Planning and utilisation | Utilisation by hall and by category; performance report |

## 4. Scope

**In scope (FRD §2):** exhibition creation; space planning, configuration and categorisation; exhibitor registration; application; approval; space selection, allocation, transfer and reallocation; billing and invoicing; payment verification; registration confirmation; check-in and occupancy; reporting and dashboards; notifications; user and role management; audit trail; system administration.

**Out of scope for 1.0.0:** ticketing and visitor management, stand construction orders, an online card-payment checkout, accounting ledgers, and a mobile app. See `10-release-notes.md` for partial items.

## 5. Stakeholders (FRD §3)

| Stakeholder | Interest | Role in the system |
|---|---|---|
| Management | Oversight, decisions | Management |
| Exhibition Management Unit | Plan and run exhibitions | Exhibition Administrator |
| Space Allocation Officers | Configure and allocate spaces | Space Allocation Officer |
| Registration | Review applications | Registration Officer |
| Finance Unit | Billing, verification, financial reports | Finance Officer |
| Customer Service | Help exhibitors | Customer Service Officer |
| Security / Access Control | Verify exhibitors at the gate | Customer Service Officer or Space Allocation Officer (check-in permission) |
| Auditors | Review transactions | Auditor |
| ICT Unit | Administration and support | System Administrator |
| Exhibitors | Register, apply, pay, exhibit | Exhibitor |

## 6. Success measures

- No space is ever held by two exhibitors (enforced, and proven by test TC-010).
- An exhibitor can go from registration to a confirmed space without visiting an office.
- Management can read occupancy, revenue and outstanding payments at any moment without asking anyone.
- Every allocation, reallocation and cancellation can be traced to a named user, a time and a reason.

## 7. Assumptions and constraints

- Exhibitors have internet access and an email address.
- Prices are per square metre by space category, with per-space overrides.
- One currency per exhibition (TZS by default); VAT is a single rate per exhibition.
- An SMS gateway and a payment platform will be contracted separately; the system provides the integration points.
