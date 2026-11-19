import {
  Briefcase,
  ChartAreaStacked,
  ChartColumn,
  CircleCheck,
  ClockArrowRotateLeft,
  CreditCard,
  FileCheck,
  Gear,
  Gem,
  LayoutCells,
  MapPin,
  PlugConnection,
  Persons,
  Receipt,
  SealCheck,
  ShieldCheck,
} from "@gravity-ui/icons";

import type { NavItem } from "@/config/site";

const ICONS: Record<NavItem["icon"], typeof Gear> = {
  dashboard: ChartAreaStacked,
  exhibitions: Gem,
  map: MapPin,
  spaces: LayoutCells,
  applications: FileCheck,
  allocations: SealCheck,
  invoices: Receipt,
  payments: CreditCard,
  gateway: PlugConnection,
  checkin: CircleCheck,
  exhibitors: Persons,
  reports: ChartColumn,
  users: ShieldCheck,
  audit: ClockArrowRotateLeft,
  profile: Briefcase,
  settings: Gear,
};

export function NavIcon({ icon, className = "size-[18px]" }: { icon: NavItem["icon"]; className?: string }) {
  const Icon = ICONS[icon];

  return <Icon aria-hidden className={className} />;
}
