import type { Metadata } from "next";
import type { Exhibitor, Paginated } from "@/lib/types";

import { api } from "@/lib/api";
import { requirePermission } from "@/lib/auth/session";
import { apiQuery, toParams } from "@/lib/params";
import { ENTITY_TYPES, EXHIBITOR_TYPES } from "@/lib/domain/labels";
import { PageHeader } from "@/components/ui/page-header";
import { FilterBar } from "@/components/ui/filter-bar";
import { DataTable, Surface } from "@/components/ui/table";
import { Pagination } from "@/components/ui/pagination";
import { PendingLink } from "@/components/ui/pending-link";
import { StatusChip } from "@/components/ui/chips";

export const metadata: Metadata = { title: "Exhibitors" };

const STATUS = {
  active: { label: "Active", color: "success" as const },
  suspended: { label: "Suspended", color: "danger" as const },
};

export default async function ExhibitorsPage({
  searchParams,
}: {
  searchParams: Promise<Record<string, string | undefined>>;
}) {
  const sp = await searchParams;

  await requirePermission("exhibitors.view");
  const list = await api<Paginated<Exhibitor>>("/exhibitors", {
    query: apiQuery(sp, ["q", "exhibitor_type", "status"]),
  });

  return (
    <div className="grid gap-6">
      <PageHeader description={`${list.total} registered organisations, across all exhibitions`} title="Exhibitors" />
      <FilterBar
        facets={[
          {
            key: "entity_type",
            label: "Exhibits as",
            options: Object.entries(ENTITY_TYPES).map(([value, label]) => ({ value, label })),
          },
          {
            key: "exhibitor_type",
            label: "Type",
            options: Object.entries(EXHIBITOR_TYPES).map(([value, label]) => ({ value, label })),
          },
          {
            key: "status",
            label: "Status",
            options: Object.entries(STATUS).map(([value, s]) => ({ value, label: s.label })),
          },
        ]}
        placeholder="Search organisation, contact, email, TIN…"
      />
      <Surface>
        <DataTable
          columns={[
            {
              key: "org",
              label: "Organisation",
              render: (e) => (
                <PendingLink
                  className="font-semibold hover:underline"
                  href={`/exhibitors/${e.id}`}
                  label={`Opening ${e.organization_name}…`}
                >
                  {e.organization_name}
                </PendingLink>
              ),
            },
            {
              key: "as",
              label: "Exhibits as",
              render: (e) =>
                e.entity_type ? ENTITY_TYPES[e.entity_type] : <span className="text-muted">Not stated yet</span>,
            },
            { key: "type", label: "Type", render: (e) => EXHIBITOR_TYPES[e.exhibitor_type] ?? e.exhibitor_type },
            { key: "country", label: "Country", render: (e) => e.country },
            { key: "sector", label: "Sector", render: (e) => e.business_sector },
            {
              key: "contact",
              label: "Contact",
              render: (e) => (
                <span>
                  {e.contact_person}
                  <span className="block text-xs text-muted">{e.phone}</span>
                </span>
              ),
            },
            { key: "apps", label: "Applications", align: "right", render: (e) => e.applications_count },
            { key: "spaces", label: "Spaces held", align: "right", render: (e) => e.live_allocations_count },
            { key: "status", label: "Status", render: (e) => <StatusChip map={STATUS} value={e.status} /> },
          ]}
          empty="No exhibitors match"
          minWidth={960}
          rowHref={(e) => `/exhibitors/${e.id}`}
          rowLabel={(e) => `Open ${e.organization_name}`}
          rows={list.data}
        />
      </Surface>
      <Pagination page={list.current_page} pages={list.last_page} params={toParams(sp)} />
    </div>
  );
}
