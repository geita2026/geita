import type { Metadata } from "next";
import type { Allocation, Paginated } from "@/lib/types";

import Link from "next/link";

import { api } from "@/lib/api";
import { requirePermission } from "@/lib/auth/session";
import { currentExhibition } from "@/lib/exhibition";
import { apiQuery, toParams } from "@/lib/params";
import { fmtDateTime } from "@/lib/domain/format";
import { APPLICATION_STATUS, OCCUPANCY_STATUS } from "@/lib/domain/labels";
import { PageHeader } from "@/components/ui/page-header";
import { FilterBar } from "@/components/ui/filter-bar";
import { DataTable, Surface } from "@/components/ui/table";
import { StatusChip } from "@/components/ui/chips";
import { Pagination } from "@/components/ui/pagination";
import { ActionButton } from "@/components/ui/action-button";

export const metadata: Metadata = { title: "Check-in" };

const NEXT: Record<string, { to: string; label: string; variant?: "primary" | "secondary" }[]> = {
  not_occupied: [{ to: "checked_in", label: "Check in" }],
  checked_in: [
    { to: "occupied", label: "Mark occupied" },
    { to: "vacant", label: "Vacant", variant: "secondary" },
  ],
  occupied: [{ to: "vacant", label: "Mark vacant", variant: "secondary" }],
  vacant: [{ to: "occupied", label: "Occupied again" }],
};

/** FR-040..042 */
export default async function CheckinPage({
  searchParams,
}: {
  searchParams: Promise<Record<string, string | undefined>>;
}) {
  const sp = await searchParams;

  await requirePermission("checkin.manage");
  const exhibition = await currentExhibition();
  const list = await api<Paginated<Allocation>>("/allocations", {
    query: apiQuery(sp, ["q", "occupancy"], { exhibition_id: exhibition?.id, status: "allocated", per_page: 50 }),
  });

  return (
    <div className="grid gap-6">
      <PageHeader
        description="Record arrival at the gate and whether each allocated space is occupied. Only confirmed participants can be checked in."
        eyebrow={exhibition?.code}
        title="Check-in and occupancy"
      />
      <FilterBar
        facets={[
          {
            key: "occupancy",
            label: "Occupancy",
            options: Object.entries(OCCUPANCY_STATUS)
              .filter(([k]) => k !== "cancelled")
              .map(([value, s]) => ({ value, label: s.label })),
          },
        ]}
        placeholder="Search space or exhibitor…"
      />
      <Surface>
        <DataTable
          columns={[
            {
              key: "space",
              label: "Space",
              render: (a) => <span className="font-display text-lg font-extrabold">{a.space?.number}</span>,
            },
            { key: "hall", label: "Hall", render: (a) => a.space?.hall?.name },
            {
              key: "exhibitor",
              label: "Exhibitor",
              render: (a) => (
                <Link className="font-semibold hover:underline" href={`/applications/${a.application_id}`}>
                  {a.exhibitor?.organization_name}
                  <span className="block text-xs font-normal text-muted">{a.exhibitor?.phone}</span>
                </Link>
              ),
            },
            {
              key: "participation",
              label: "Participation",
              render: (a) => a.application && <StatusChip map={APPLICATION_STATUS} value={a.application.status} />,
            },
            {
              key: "occupancy",
              label: "Occupancy",
              render: (a) => <StatusChip map={OCCUPANCY_STATUS} value={a.occupancy_status} />,
            },
            {
              key: "checkin",
              label: "Checked in",
              render: (a) =>
                a.checked_in_at ? (
                  <span>
                    {fmtDateTime(a.checked_in_at)}
                    <span className="block font-mono text-xs text-muted">{a.checkin_reference}</span>
                  </span>
                ) : (
                  <span className="text-muted">—</span>
                ),
            },
            {
              key: "actions",
              label: "",
              align: "right",
              render: (a) =>
                a.application?.status === "confirmed" ? (
                  <span className="flex justify-end gap-1.5">
                    {(NEXT[a.occupancy_status] ?? []).map((n) => (
                      <ActionButton
                        key={n.to}
                        size="sm"
                        spec={{
                          method: "POST",
                          path: `/allocations/${a.id}/occupancy`,
                          extra: { occupancy_status: n.to },
                          success: `Space ${a.space?.number}: ${OCCUPANCY_STATUS[n.to].label.toLowerCase()}`,
                        }}
                        variant={n.variant ?? "primary"}
                      >
                        {n.label}
                      </ActionButton>
                    ))}
                  </span>
                ) : (
                  <span className="text-xs text-muted">Payment outstanding</span>
                ),
            },
          ]}
          empty="No allocated spaces"
          minWidth={940}
          rows={list.data}
        />
      </Surface>
      <Pagination page={list.current_page} pages={list.last_page} params={toParams(sp)} />
    </div>
  );
}
