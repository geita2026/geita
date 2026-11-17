import type { Metadata } from "next";
import type { Allocation, Paginated } from "@/lib/types";

import Link from "next/link";

import { api } from "@/lib/api";
import { can, requirePermission } from "@/lib/auth/session";
import { currentExhibition } from "@/lib/exhibition";
import { apiQuery, toParams } from "@/lib/params";
import { fmtDate } from "@/lib/domain/format";
import { ALLOCATION_STATUS, APPLICATION_STATUS } from "@/lib/domain/labels";
import { PageHeader } from "@/components/ui/page-header";
import { FilterBar } from "@/components/ui/filter-bar";
import { DataTable, Surface } from "@/components/ui/table";
import { CategoryChip, StatusChip } from "@/components/ui/chips";
import { Pagination } from "@/components/ui/pagination";
import { ActionButton } from "@/components/ui/action-button";
import { DownloadLink } from "@/components/ui/download-link";

export const metadata: Metadata = { title: "Allocations" };

export default async function AllocationsPage({
  searchParams,
}: {
  searchParams: Promise<Record<string, string | undefined>>;
}) {
  const sp = await searchParams;
  const session = await requirePermission("staff");
  const exhibition = await currentExhibition();
  const manage = can(session, "allocations.manage");
  const list = await api<Paginated<Allocation>>("/allocations", {
    query: apiQuery(sp, ["q", "status"], { exhibition_id: exhibition?.id }),
  });

  return (
    <div className="grid gap-6">
      <PageHeader
        actions={
          exhibition && (
            <>
              {can(session, "reports.view") && (
                <DownloadLink path={`exhibitions/${exhibition.id}/schedule`}>Allocation schedule (PDF)</DownloadLink>
              )}
              {manage && (
                <ActionButton
                  pendingLabel="Allocating…"
                  spec={{
                    method: "POST",
                    path: `/exhibitions/${exhibition.id}/auto-allocate`,
                    success: "Automatic allocation finished",
                  }}
                  variant="secondary"
                >
                  Auto-allocate approved
                </ActionButton>
              )}
            </>
          )
        }
        description="Every allocation, reallocation, transfer and cancellation. Open the application to change one."
        eyebrow={exhibition?.code}
        title="Allocations"
      />
      <FilterBar
        facets={[
          {
            key: "status",
            label: "Status",
            options: Object.entries(ALLOCATION_STATUS).map(([value, s]) => ({ value, label: s.label })),
          },
        ]}
        placeholder="Search space, exhibitor, reference…"
      />
      <Surface>
        <DataTable
          columns={[
            {
              key: "space",
              label: "Space",
              render: (a) => <span className="font-display font-extrabold">{a.space?.number}</span>,
            },
            {
              key: "where",
              label: "Hall · category",
              render: (a) => (
                <span className="grid justify-items-start gap-1">
                  {a.space?.hall?.name}
                  {a.space?.category && <CategoryChip color={a.space.category.color} name={a.space.category.name} />}
                </span>
              ),
            },
            {
              key: "exhibitor",
              label: "Exhibitor",
              render: (a) => (
                <Link className="font-semibold hover:underline" href={`/applications/${a.application_id}`}>
                  {a.exhibitor?.organization_name}
                  <span className="block font-mono text-xs font-normal text-muted">{a.application?.reference}</span>
                </Link>
              ),
            },
            { key: "date", label: "Allocated", render: (a) => fmtDate(a.allocated_at) },
            {
              key: "by",
              label: "By",
              render: (a) =>
                a.source === "self"
                  ? "Exhibitor"
                  : a.source === "auto"
                    ? `Auto · ${a.allocator?.name ?? ""}`
                    : a.allocator?.name,
            },
            {
              key: "status",
              label: "Allocation",
              render: (a) => <StatusChip map={ALLOCATION_STATUS} value={a.status} />,
            },
            {
              key: "participation",
              label: "Participation",
              render: (a) => a.application && <StatusChip map={APPLICATION_STATUS} value={a.application.status} />,
            },
            {
              key: "actions",
              label: "",
              align: "right",
              render: (a) =>
                manage && a.status === "pending" ? (
                  <ActionButton
                    pendingLabel="Confirming…"
                    size="sm"
                    spec={{
                      method: "POST",
                      path: `/allocations/${a.id}/confirm`,
                      success: `Space ${a.space?.number} confirmed and invoiced`,
                    }}
                    successLabel="Confirmed"
                  >
                    Confirm
                  </ActionButton>
                ) : (
                  <Link
                    className="text-xs font-semibold text-brand-ink hover:underline"
                    href={`/applications/${a.application_id}`}
                  >
                    Open
                  </Link>
                ),
            },
          ]}
          empty="No allocations"
          emptyDescription="Allocate space from the floor plan or from an approved application."
          minWidth={980}
          rows={list.data}
        />
      </Surface>
      <Pagination page={list.current_page} pages={list.last_page} params={toParams(sp)} />
    </div>
  );
}
