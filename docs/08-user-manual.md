# 08 — User manual

## Getting around

- **Menu:** the icon rail on the left opens when you point at it. On a phone, use the menu button in the top bar.
- **Exhibition switcher:** the gem button in the top bar chooses which exhibition the map, lists and reports show.
- **Search:** the box in the top bar finds exhibitors, applications, spaces, invoices, payments and exhibitions.
- **Bell:** your notifications. A copy also goes to your email and phone where those services are switched on.
- **Dialogs:** most buttons open out into a form and fold back when you save. Press Esc or the ✕ to close without saving.
- **Filters:** every list has a search box and filter chips. The filtered view is in the page address, so you can bookmark or share it.
- **Settings:** accent colour, reduced motion, and changing your password.

---

## For exhibitors

### 1. Create your account
On the sign-in page choose **Create an exhibitor account**. Enter your organisation's details, your email (this is what you sign in with) and a password of at least 10 characters with upper and lower case letters and a number.

### 2. Apply for space
1. **Exhibitions** → open the exhibition → **Apply for space**.
2. Choose your preferred category and size, the number of spaces and what you will exhibit. **Save and continue.** This is a draft.
3. On the application page, **Upload document** for each mandatory document shown in red.
4. **Submit application.** You cannot submit until every mandatory document is uploaded.

You will be notified when it is approved, rejected, or returned for correction. If it is returned, read the reason at the top of the application, fix it, and submit again.

### 3. Choose your space
Once approved (and if the organisers allow it): **Space map** → press a green (available) space to see its size, price and facilities → **Select this space**. It shows as "Yours" and waits for an officer to confirm. You can withdraw it from your application page until then. If selection is switched off, an officer allocates your space.

### 4. Pay
When your space is confirmed you receive an invoice. **Invoices** → open it:
- The **control number** and amount are at the top. **Invoice PDF** downloads it.
- After paying at the bank or by mobile money, press **Record a payment**, enter the amount, date and transaction reference, and attach the slip.

Finance checks it. You are notified when it is verified, and the receipt appears on the invoice. You may pay in parts; participation is confirmed when the invoice is fully paid.

### 5. Before the exhibition
When your application says **Confirmed**, download the **Allocation letter** from the application page and bring it to check-in.

### Your dashboard
The dashboard always shows which step you are on and what to do next.

---

## For Exhibition Administrators

### Create an exhibition
**Exhibitions → New exhibition.** Fill in the dates carefully: registration must close on or before the start date, and the payment deadline is the default due date on invoices. List the **mandatory documents** one per line — exhibitors cannot submit without them. It is saved as a **Draft**, invisible to exhibitors.

### Lay it out
On the exhibition page:
1. **Add hall or area** for each hall, yard or pavilion. "Map columns" is how many spaces sit in a row on the floor plan.
2. **Add category** for each kind of space, with its price per m² and a map colour.
3. Go to **Spaces** → **Generate a block** to create many identical spaces at once, or **New space** for one.

### Publish and close
**Publish for registration** when the layout is ready. Registration closes by itself the day after the closing date; you can also **Close registration** by hand. To reopen after the deadline, first **Edit** the closing date, then **Reopen registration**. Later, **Mark as ongoing** and **Mark as completed**.

### Authorise exceptions
Only you (and the System Administrator) can: change or cancel the space of a **confirmed** participant, **transfer** a space from one exhibitor to another, and allocate space to an application that is not yet approved (tick *Authorise as an exception*).

---

## For Registration Officers

**Applications** lists everything submitted; filter by **Status → Submitted** for your queue (the dashboard shows the count). Open an application, read the details and open each document, then:
- **Approve** — refused if a mandatory document is missing.
- **Return** — say exactly what to correct; the exhibitor can edit and resubmit.
- **Reject** — give the reason; it is sent to the exhibitor.

---

## For Space Allocation Officers

### Allocate from the map
**Space map** → press a space → choose the approved application → **Allocate**. The invoice is created at once. Colours: green available, amber reserved, orange pending approval, blue allocated, purple occupied, grey blocked, red under maintenance. The stripe on the left of each cell is the category.

### Confirm exhibitors' selections
**Allocations → Status: Awaiting confirmation → Confirm.** This invoices the exhibitor.

### Allocate automatically
**Auto-allocate** (on the map or Allocations page) gives every approved application the first available space in its preferred category, closest to its preferred size, highest priority first. The result lists any application for which nothing matched.

### Change an allocation
Open the application. Under **Space**:
- **Reallocate** — pick the new space and give the reason. The invoice follows.
- **Cancel allocation** — give the reason. The space becomes available and comes off the invoice.

A reason is always required and is kept in the audit trail.

### Take a space out of use
On the map, press a space with no holder → **Change status** → Blocked, Under maintenance, Reserved or Cancelled.

---

## For Finance Officers

**Payments** is your verification queue (**Verification → Awaiting verification**). For each payment: open the **Evidence**, match it to the bank statement, then:
- **Verify** — issues a numbered receipt, updates the invoice, and confirms the exhibitor's participation once the invoice is fully paid.
- **Reject** — give the reason; the exhibitor is told.

You can also **Record a payment** on an invoice for an exhibitor who paid at the counter. **Reports → Payments** shows paid, unpaid, partially paid and overdue invoices and revenue; **Download CSV** for your records.

---

## For Customer Service and gate staff

**Check-in** lists every allocated space. Search by organisation or space number.
- **Check in** when the exhibitor arrives (only possible when participation is confirmed — otherwise the row says "Payment outstanding").
- **Mark occupied** when they have set up; **Mark vacant** if the space is found empty.

Customer Service can also start an application on an exhibitor's behalf and upload their documents.

---

## For Management and Auditors

- **Dashboard** — spaces, occupancy, exhibitors, applications, billed, collected and outstanding for the selected exhibition.
- **Reports** — space availability, space allocation, exhibitors, payments, exhibition performance; each downloads as CSV. The allocation schedule downloads as PDF.
- **Audit trail** — every significant action with user, time, reference, what changed and the IP address. Filter by area, date or free text. Entries cannot be edited or deleted.

---

## For System Administrators

**Users**:
- **Add a staff user** — a one-time password is displayed once; pass it on securely. They must change it at first sign-in.
- **Edit** — name, phone, role. Changing a role signs the user out.
- **Deactivate / Activate** — a deactivated user is signed out immediately and cannot sign in.
- **Unlock** — after five wrong passwords an account locks.
- **Reset password** — issues a new one-time password.

Exhibitors register themselves; you can deactivate or reset their accounts the same way.

---

## Common questions

**"Registration is not open."** The exhibition is not published, or today is outside the registration dates.

**"Space … has just been taken by another exhibitor."** Someone else got it a moment earlier. Choose another.

**"This participation is confirmed. Changes need authorisation."** Ask an Exhibition Administrator.

**I cannot see a page a colleague can.** Pages depend on your role. Ask the System Administrator if your role is wrong.

**I was signed out.** Sessions end after 12 hours, or at once if your account or role was changed.
