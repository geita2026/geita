/** Status vocabularies from the FRD, with the chip colour each one wears. */
type Tone = "default" | "accent" | "success" | "warning" | "danger";
type Meta = { label: string; color: Tone };

export const SPACE_STATUS: Record<string, Meta & { fill: string }> = {
  available: { label: "Available", color: "success", fill: "var(--success)" },
  reserved: { label: "Reserved", color: "warning", fill: "var(--warning)" },
  pending_approval: { label: "Pending approval", color: "warning", fill: "#f97316" },
  allocated: { label: "Allocated", color: "accent", fill: "#3b82f6" },
  occupied: { label: "Occupied", color: "accent", fill: "#8b5cf6" },
  blocked: { label: "Blocked", color: "default", fill: "#6b7280" },
  cancelled: { label: "Cancelled", color: "default", fill: "#9ca3af" },
  maintenance: { label: "Under maintenance", color: "danger", fill: "var(--danger)" },
};

export const APPLICATION_STATUS: Record<string, Meta> = {
  draft: { label: "Draft", color: "default" },
  submitted: { label: "Submitted", color: "accent" },
  returned: { label: "Returned", color: "warning" },
  approved: { label: "Approved", color: "success" },
  rejected: { label: "Rejected", color: "danger" },
  pending_payment: { label: "Pending payment", color: "warning" },
  confirmed: { label: "Confirmed", color: "success" },
  cancelled: { label: "Cancelled", color: "default" },
};

export const INVOICE_STATUS: Record<string, Meta> = {
  unpaid: { label: "Unpaid", color: "warning" },
  partially_paid: { label: "Partially paid", color: "accent" },
  paid: { label: "Paid", color: "success" },
  overdue: { label: "Overdue", color: "danger" },
  cancelled: { label: "Cancelled", color: "default" },
  refunded: { label: "Refunded", color: "default" },
};

export const PAYMENT_STATUS: Record<string, Meta> = {
  pending: { label: "Awaiting verification", color: "warning" },
  verified: { label: "Verified", color: "success" },
  rejected: { label: "Rejected", color: "danger" },
};

/** GePG bill (control number) statuses. */
export const GEPG_BILL_STATUS: Record<string, Meta> = {
  requesting: { label: "Requesting control number", color: "warning" },
  issued: { label: "Control number issued", color: "success" },
  failed: { label: "Not issued", color: "danger" },
  cancelled: { label: "Cancelled", color: "default" },
  superseded: { label: "Replaced", color: "default" },
  paid: { label: "Paid", color: "success" },
  expired: { label: "Expired", color: "warning" },
};

export const GEPG_MODE: Record<string, Meta> = {
  off: { label: "Off", color: "default" },
  simulator: { label: "Simulator", color: "warning" },
  live: { label: "Live", color: "success" },
};

export const ALLOCATION_STATUS: Record<string, Meta> = {
  pending: { label: "Awaiting confirmation", color: "warning" },
  allocated: { label: "Allocated", color: "success" },
  cancelled: { label: "Cancelled", color: "default" },
  reallocated: { label: "Reallocated", color: "default" },
  transferred: { label: "Transferred", color: "default" },
};

export const OCCUPANCY_STATUS: Record<string, Meta> = {
  not_occupied: { label: "Not occupied", color: "default" },
  checked_in: { label: "Checked in", color: "accent" },
  occupied: { label: "Occupied", color: "success" },
  vacant: { label: "Vacant", color: "warning" },
  cancelled: { label: "Cancelled", color: "default" },
};

export const EXHIBITION_STATUS: Record<string, Meta> = {
  draft: { label: "Draft", color: "default" },
  published: { label: "Open for registration", color: "success" },
  registration_closed: { label: "Registration closed", color: "warning" },
  ongoing: { label: "Ongoing", color: "accent" },
  completed: { label: "Completed", color: "default" },
  cancelled: { label: "Cancelled", color: "danger" },
};

export const ROLES: Record<string, string> = {
  system_admin: "System Administrator",
  exhibition_admin: "Exhibition Administrator",
  space_officer: "Space Allocation Officer",
  registration_officer: "Registration Officer",
  finance_officer: "Finance Officer",
  management: "Management",
  customer_service: "Customer Service Officer",
  auditor: "Auditor",
  exhibitor: "Exhibitor",
};

export const EXHIBITOR_TYPES: Record<string, string> = {
  local: "Local company",
  international: "International",
  government: "Government",
  sme: "SME / small-scale miner",
};

/** Who an exhibitor is (exhibitors.entity_type). */
export const ENTITY_TYPES: Record<string, string> = {
  individual: "Individual",
  business: "Registered business",
};

/** Every method a payment may carry, including older records. */
export const PAYMENT_METHODS: Record<string, string> = {
  bank: "Bank deposit / transfer",
  mobile_money: "Mobile money",
  control_number: "Control number",
  cash: "Cash",
};

/** What may be recorded by hand: no cash (no evidence trail), no control number (GePG reports those). */
export const MANUAL_PAYMENT_METHODS: Record<string, string> = {
  bank: PAYMENT_METHODS.bank,
  mobile_money: PAYMENT_METHODS.mobile_money,
};

export const humanize = (s: string) => s.replaceAll("_", " ").replace(/^./, (c) => c.toUpperCase());

export const options = (map: Record<string, string | { label: string }>) =>
  Object.entries(map).map(([id, v]) => ({ id, label: typeof v === "string" ? v : v.label }));
