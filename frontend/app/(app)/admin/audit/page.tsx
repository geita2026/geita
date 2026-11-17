import type { Metadata } from "next";
import type { AuditLog, Paginated } from "@/lib/types";

import { api } from "@/lib/api";
import { requirePermission } from "@/lib/auth/session";
import { apiQuery, toParams } from "@/lib/params";
import { fmtDateTime } from "@/lib/domain/format";
import { PageHeader } from "@/components/ui/page-header";
import { FilterBar } from "@/components/ui/filter-bar";
import { DataTable, Surface } from "@/components/ui/table";
import { Pagination } from "@/components/ui/pagination";

export const metadata: Metadata = { title: "Audit trail" };

const AREAS = [
  "auth",
  "exhibition",
  "space",
  "application",
  "allocation",
  "invoice",
  "payment",
  "checkin",
  "user",
  "exhibitor",
  "document",
];

function Values({ values, tone }: { values: Record<string, unknown> | null; tone: "old" | "new" }) {
  if (!values || !Object.keys(values).length) return null;

  return (
    <span
      className={`block font-mono text-xs ${tone === "old" ? "text-hl-old line-through decoration-1" : "text-hl-new"}`}
    >
      {Object.entries(values)
        .slice(0, 4)
        .map(([k, v]) => `${k}: ${typeof v === "object" ? JSON.stringify(v) : String(v)}`)
        .join(" · ")}
    </span>
  );
}

/** FR-048/049. Append-only: nothing on this page can be edited or removed. */
export default async function AuditPage({
  searchParams,
}: {
  searchParams: Promise<Record<string, string | undefined>>;
}) {
  const sp = await searchParams;

  await requirePermission("audit.view");
  const list = await api<Paginated<AuditLog>>("/audit-logs", {
    query: apiQuery(sp, ["q", "action", "from", "to"], { per_page: 30 }),
  });

  return (
    <div className="grid gap-6">
      <PageHeader
        description={`${list.total.toLocaleString()} entries. Every significant action is recorded with who did it, when, from where, and what changed. Entries cannot be altered.`}
        title="Audit trail"
      />
      <FilterBar
        dates
        facets={[
          {
            key: "action",
            label: "Area",
            options: AREAS.map((a) => ({ value: a, label: a[0].toUpperCase() + a.slice(1) })),
          },
        ]}
        placeholder="Search summary, reference, user…"
      />
      <Surface>
        <DataTable
          columns={[
            {
              key: "when",
              label: "Date and time",
              className: "whitespace-nowrap",
              render: (l) => fmtDateTime(l.created_at),
            },
            { key: "who", label: "User", render: (l) => l.user_name },
            {
              key: "action",
              label: "Activity",
              render: (l) => (
                <span className="rounded-md bg-default px-1.5 py-0.5 font-mono text-xs text-hl-code">{l.action}</span>
              ),
            },
            {
              key: "ref",
              label: "Reference",
              render: (l) => <span className="font-mono text-xs">{l.reference ?? "—"}</span>,
            },
            {
              key: "what",
              label: "Details",
              render: (l) => (
                <span className="block max-w-[520px]">
                  {l.summary}
                  <Values tone="old" values={l.old_values} />
                  <Values tone="new" values={l.new_values} />
                </span>
              ),
            },
            {
              key: "ip",
              label: "IP address",
              render: (l) => <span className="font-mono text-xs text-hl-ip">{l.ip_address ?? "—"}</span>,
            },
          ]}
          empty="No entries match"
          minWidth={1080}
          rows={list.data}
        />
      </Surface>
      <Pagination page={list.current_page} pages={list.last_page} params={toParams(sp)} />
    </div>
  );
}
