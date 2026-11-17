export const siteConfig = {
  name: process.env.NEXT_PUBLIC_APP_NAME ?? "GeMEMIS",
  shortName: "GeMEMIS",
  longName: "Geita Mineral Exhibition Management Information System",
  description:
    "Space planning, exhibitor registration, allocation, billing and occupancy for the Geita mineral exhibitions.",
  organisation: "Geita Mineral Exhibition Secretariat",
};

export type NavItem = {
  label: string;
  href: string;
  icon:
    | "dashboard"
    | "exhibitions"
    | "map"
    | "spaces"
    | "applications"
    | "allocations"
    | "invoices"
    | "payments"
    | "checkin"
    | "exhibitors"
    | "reports"
    | "users"
    | "audit"
    | "profile"
    | "settings";
  /** Shown when the user holds any of these permissions; omitted = everyone. */
  needs?: string[];
  /** Hide from staff (exhibitor-only pages). */
  exhibitorOnly?: boolean;
};

export const navItems: NavItem[] = [
  { label: "Dashboard", href: "/dashboard", icon: "dashboard" },
  { label: "Exhibitions", href: "/exhibitions", icon: "exhibitions" },
  { label: "Space map", href: "/map", icon: "map" },
  { label: "Spaces", href: "/spaces", icon: "spaces", needs: ["staff"] },
  { label: "Applications", href: "/applications", icon: "applications" },
  { label: "Allocations", href: "/allocations", icon: "allocations", needs: ["staff"] },
  { label: "Invoices", href: "/invoices", icon: "invoices" },
  { label: "Payments", href: "/payments", icon: "payments", needs: ["finance.view"] },
  { label: "Check-in", href: "/checkin", icon: "checkin", needs: ["checkin.manage"] },
  { label: "Exhibitors", href: "/exhibitors", icon: "exhibitors", needs: ["exhibitors.view"] },
  { label: "Reports", href: "/reports", icon: "reports", needs: ["reports.view"] },
  { label: "Users", href: "/admin/users", icon: "users", needs: ["users.manage"] },
  { label: "Audit trail", href: "/admin/audit", icon: "audit", needs: ["audit.view"] },
  { label: "My organisation", href: "/profile", icon: "profile", exhibitorOnly: true },
  { label: "Settings", href: "/settings", icon: "settings" },
];
