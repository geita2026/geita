/** Shapes returned by the Laravel API (docs/api.md). Dates arrive as ISO strings. */
export type Role =
  | "system_admin"
  | "exhibition_admin"
  | "space_officer"
  | "registration_officer"
  | "finance_officer"
  | "management"
  | "customer_service"
  | "auditor"
  | "exhibitor";

export type Exhibitor = {
  id: number;
  organization_name: string;
  registration_number: string | null;
  tin: string | null;
  contact_person: string;
  phone: string;
  email: string;
  physical_address: string | null;
  country: string;
  business_sector: string | null;
  business_activity: string | null;
  exhibitor_type: string;
  status: string;
  applications_count?: number;
  live_allocations_count?: number;
  applications?: Application[];
  users?: User[];
};

export type User = {
  id: number;
  name: string;
  email: string;
  phone: string | null;
  role: Role;
  role_label: string;
  is_active: boolean;
  must_change_password: boolean;
  locked_at: string | null;
  last_login_at: string | null;
  exhibitor_id: number | null;
  exhibitor?: Exhibitor | null;
};

export type Session = { user: User; permissions: string[] };

export type Hall = {
  id: number;
  exhibition_id: number;
  code: string;
  name: string;
  kind: string;
  description: string | null;
  grid_cols: number;
  spaces_count?: number;
  held_count?: number;
};

export type SpaceCategory = {
  id: number;
  code: string;
  name: string;
  description: string | null;
  price_per_sqm: number;
  color: string;
  gfs_code: string | null;
  spaces_count?: number;
};

export type Exhibition = {
  id: number;
  code: string;
  name: string;
  theme: string | null;
  objectives: string | null;
  venue: string;
  start_date: string;
  end_date: string;
  registration_opens_on: string;
  registration_closes_on: string;
  payment_deadline: string;
  categories: string[] | null;
  required_documents: string[] | null;
  terms: string | null;
  contact_email: string | null;
  contact_phone: string | null;
  currency: string;
  vat_rate: number;
  allow_self_selection: boolean;
  status: string;
  registration_open: boolean;
  spaces_count?: number;
  applications_count?: number;
  available_spaces_count?: number;
  halls?: Hall[];
  space_categories?: SpaceCategory[];
};

export type Space = {
  id: number;
  exhibition_id: number;
  hall_id: number;
  space_category_id: number;
  number: string;
  width_m: number;
  length_m: number;
  area_sqm: number;
  price: number;
  status: string;
  size_label: string;
  facilities: string[] | null;
  location_note: string | null;
  special_conditions: string | null;
  map_col: number;
  map_row: number;
  map_w: number;
  map_h: number;
  hall?: Pick<Hall, "id" | "code" | "name">;
  category?: Pick<SpaceCategory, "id" | "code" | "name" | "color">;
  active_allocation?:
    | (Pick<Allocation, "id" | "reference" | "status" | "occupancy_status" | "application_id"> & {
        exhibitor?: Pick<Exhibitor, "id" | "organization_name">;
      })
    | null;
  /** Exhibitor view only: the status of their own hold on this space. */
  mine?: string | null;
};

export type AppDocument = {
  id: number;
  type: string;
  original_name: string;
  mime: string | null;
  size: number;
  created_at: string;
};

export type Allocation = {
  id: number;
  reference: string;
  application_id: number;
  exhibition_id: number;
  exhibitor_id: number;
  space_id: number;
  status: string;
  source: string;
  allocated_at: string | null;
  change_reason: string | null;
  ended_at: string | null;
  occupancy_status: string;
  checkin_reference: string | null;
  checked_in_at: string | null;
  checkin_notes: string | null;
  space?: Space;
  exhibitor?: Pick<Exhibitor, "id" | "organization_name" | "phone">;
  application?: Pick<Application, "id" | "reference" | "status">;
  allocator?: { id: number; name: string } | null;
};

export type Application = {
  id: number;
  reference: string;
  exhibition_id: number;
  exhibitor_id: number;
  space_category_id: number | null;
  preferred_size: string | null;
  spaces_requested: number;
  product_category: string | null;
  products_description: string | null;
  special_requirements: string | null;
  status: string;
  priority: number;
  submitted_at: string | null;
  reviewed_at: string | null;
  review_notes: string | null;
  confirmed_at: string | null;
  updated_at: string;
  active_allocations_count?: number;
  documents_count?: number;
  exhibitor?: Exhibitor;
  exhibition?: Exhibition;
  category?: SpaceCategory | null;
  documents?: AppDocument[];
  allocations?: Allocation[];
  active_allocations?: Allocation[];
  invoices?: Invoice[];
  reviewer?: { id: number; name: string } | null;
  missing_documents?: string[];
};

export type InvoiceItem = {
  id: number;
  description: string;
  space_number: string | null;
  space_size: string | null;
  space_category: string | null;
  unit_price: number;
  quantity: number;
  amount: number;
};

export type Payment = {
  id: number;
  reference: string;
  invoice_id: number;
  amount: number;
  paid_on: string;
  method: string;
  payer_reference: string | null;
  evidence_name: string | null;
  has_evidence: boolean;
  verification_status: string;
  channel: string;
  receipt_number: string | null;
  verified_at: string | null;
  verification_notes: string | null;
  psp_name: string | null;
  psp_receipt: string | null;
  payment_channel: string | null;
  created_at: string;
  verifier?: { id: number; name: string } | null;
  invoice?: Invoice;
};

export type Invoice = {
  id: number;
  number: string;
  control_number: string | null;
  exhibition_id: number;
  exhibitor_id: number;
  application_id: number;
  subtotal: number;
  vat_rate: number;
  vat_amount: number;
  total: number;
  amount_paid: number;
  balance: number;
  currency: string;
  status: string;
  issued_on: string;
  due_on: string;
  /** requesting | issued | failed | cancelled | paid; null when GePG is off. */
  gepg_status: string | null;
  gepg_bills?: GepgBill[];
  items_count?: number;
  items?: InvoiceItem[];
  payments?: Payment[];
  exhibitor?: Exhibitor;
  exhibition?: Exhibition;
  application?: Pick<Application, "id" | "reference" | "status">;
};

export type AppNotification = {
  id: number;
  kind: string;
  title: string;
  body: string;
  href: string | null;
  read_at: string | null;
  created_at: string;
};

export type AuditLog = {
  id: number;
  user_name: string | null;
  action: string;
  reference: string | null;
  entity_type: string | null;
  summary: string;
  old_values: Record<string, unknown> | null;
  new_values: Record<string, unknown> | null;
  ip_address: string | null;
  created_at: string;
};

export type Paginated<T> = { data: T[]; current_page: number; last_page: number; total: number; per_page: number };

export type Report = {
  title: string;
  columns: { key: string; label: string; num?: boolean; money?: boolean }[];
  rows: Record<string, string | number | null>[];
  summary: Record<string, string | number>;
  money_summary?: string[];
  generated_at: string;
};

export type StaffDashboard = {
  exhibition: Exhibition;
  kpis: Record<string, number>;
  spaces_by_status: { status: string; count: number }[];
  applications_by_status: { status: string; count: number }[];
  by_category: {
    id: number;
    name: string;
    color: string;
    price_per_sqm: number;
    spaces: number;
    held: number;
    billed: number;
  }[];
  by_hall: (Hall & { spaces_count: number; held_count: number })[];
  recent_activity: AuditLog[];
};

export type ExhibitorDashboard = {
  exhibition: Exhibition;
  application: Application | null;
  missing_documents: string[];
  invoices: Invoice[];
  billed: number;
  paid: number;
  available_spaces: number;
};

export type GepgBill = {
  id: number;
  bill_id: string;
  amount: number;
  paid_amount: number;
  currency: string;
  expires_at: string;
  status: string;
  control_number: string | null;
  status_code: string | null;
  error: string | null;
  requested_at: string | null;
  issued_at: string | null;
  ended_at: string | null;
};

export type GepgStatus = {
  mode: "off" | "simulator" | "live";
  sp_code: string;
  sub_sp_code: string;
  sp_sys_id: string;
  base_url: string | null;
  payment_option: number;
  default_gfs_code: string | null;
  simulator_keys: boolean;
  callback_url: string;
  bills: Record<string, number>;
  last_message_at: string | null;
  last_failure: { id: number; type: string; reference: string | null; error: string; created_at: string } | null;
};

export type GepgMessage = {
  id: number;
  direction: "in" | "out";
  type: string;
  reference: string | null;
  signature_valid: boolean | null;
  status_code: string | null;
  http_status: number | null;
  error: string | null;
  created_at: string;
  payload?: string;
};

export type GepgReconciliation = {
  id: number;
  request_id: string;
  transaction_date: string;
  status: string;
  gepg_count: number;
  gepg_total: number;
  matched: number;
  recovered: { trx: string; control_number: string | null; amount: number }[] | null;
  missing_at_gepg: { trx: string; amount: number; payment: string }[] | null;
  amount_mismatches: { trx: string; ours: number; gepg: number; payment: string }[] | null;
  error: string | null;
  requester?: { id: number; name: string } | null;
  received_at: string | null;
  created_at: string;
};
