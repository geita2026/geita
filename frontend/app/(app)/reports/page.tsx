import type { Metadata } from "next";
import type { Report } from "@/lib/types";

import Link from "next/link";
import clsx from "clsx";

import { api } from "@/lib/api";
import { can, requirePermission } from "@/lib/auth/session";
import { currentExhibition } from "@/lib/exhibition";
import { fmtDateTime, tzs } from "@/lib/domain/format";
import { humanize } from "@/lib/domain/labels";
import { PageHeader } from "@/components/ui/page-header";
import { EmptyState } from "@/components/ui/empty-state";
import { Surface } from "@/components/ui/table";
import { DownloadLink } from "@/components/ui/download-link";

export const metadata: Metadata = { title: "Reports" };

const REPORTS = [
  { type: "space-availability", label: "Space availability", req: "FR-050" },
  { type: "space-allocation", label: "Space allocation", req: "FR-051" },
  { type: "exhibitors", label: "Exhibitors", req: "FR-052" },
  { type: "payments", label: "Payments", req: "FR-053", needs: "finance.view" },
  { type: "performance", label: "Exhibition performance", req: "FR-054" },
];

const STATUS_COLUMNS = new Set(["status", "participation", "occupancy", "type"]);

export default async function ReportsPage({ searchParams }: { searchParams: Promise<{ type?: string }> }) {
  const sp = await searchParams;
  const session = await requirePermission("reports.view");
  const exhibition = await currentExhibition();

  if (!exhibition) return <EmptyState title="No exhibition" />;

  const available = REPORTS.filter((r) => !r.needs || can(session, r.needs));
  const active = available.find((r) => r.type === sp.type) ?? available[0];
  const report = await api<Report>(`/exhibitions/${exhibition.id}/reports/${active.type}`);
  const cell = (key: string, value: string | number | null, money?: boolean) =>
    value === null || value === ""
      ? "—"
      : typeof value === "number"
        ? money
          ? value.toLocaleString("en-TZ")
          : value
        : STATUS_COLUMNS.has(key)
          ? humanize(value)
          : value;

  return (
    <div className="grid gap-6">
      <PageHeader
        actions={
          <>
            <DownloadLink path={`exhibitions/${exhibition.id}/reports/${active.type}?format=csv`}>
              Download CSV
            </DownloadLink>
            {active.type === "space-allocation" && (
              <DownloadLink path={`exhibitions/${exhibition.id}/schedule`}>Schedule PDF</DownloadLink>
            )}
          </>
        }
        description={`${exhibition.name} · generated ${fmtDateTime(report.generated_at)}`}
        eyebrow={`${exhibition.code} · ${active.req}`}
        title="Reports"
      />

      <nav aria-label="Report" className="flex flex-wrap gap-2">
        {available.map((r) => (
          <Link
            key={r.type}
            aria-current={r.type === active.type ? "page" : undefined}
            className={clsx(
              "rounded-full px-4 py-2 text-sm font-semibold transition-colors",
              r.type === active.type
                ? "bg-brand text-brand-foreground"
                : "bg-surface text-muted soft-shadow hover:text-foreground",
            )}
            href={`?type=${r.type}`}
          >
            {r.label}
          </Link>
        ))}
      </nav>

      <div className="grid grid-cols-2 gap-3 md:grid-cols-3 xl:grid-cols-6">
        {Object.entries(report.summary).map(([label, value]) => (
          <div key={label} className="soft-shadow rounded-2xl bg-surface p-4">
            <p className="text-xs font-bold uppercase tracking-[0.12em] text-muted">{label}</p>
            <p className="mt-1 font-display text-[22px] font-extrabold leading-tight tabular">
              {typeof value === "number"
                ? report.money_summary?.includes(label)
                  ? tzs(value)
                  : value.toLocaleString("en-TZ")
                : value}
            </p>
          </div>
        ))}
      </div>

      <Surface>
        {report.rows.length === 0 ? (
          <EmptyState description="There is nothing to report for this exhibition yet." title="No rows" />
        ) : (
          <div className="overflow-x-auto">
            <table className="table data-table" style={{ minWidth: Math.max(640, report.columns.length * 120) }}>
              <thead>
                <tr>
                  {report.columns.map((c) => (
                    <th key={c.key} className={`px-3 py-2.5 ${c.num ? "text-right" : "text-left"}`}>
                      {c.label}
                    </th>
                  ))}
                </tr>
              </thead>
              <tbody>
                {report.rows.map((row, n) => (
                  <tr key={n} className="border-t border-separator text-sm">
                    {report.columns.map((c) => (
                      <td key={c.key} className={`px-3 py-2.5 ${c.num ? "text-right tabular" : ""}`}>
                        {cell(c.key, row[c.key], c.money)}
                      </td>
                    ))}
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        )}
      </Surface>
    </div>
  );
}
