import type { Metadata } from "next";
import type { Application, Paginated } from "@/lib/types";

import Link from "next/link";

import { api } from "@/lib/api";
import { requireSession } from "@/lib/auth/session";
import { currentExhibition } from "@/lib/exhibition";
import { apiQuery, toParams } from "@/lib/params";
import { fmtDate } from "@/lib/domain/format";
import { APPLICATION_STATUS } from "@/lib/domain/labels";
import { PageHeader } from "@/components/ui/page-header";
import { FilterBar } from "@/components/ui/filter-bar";
import { DataTable, Surface } from "@/components/ui/table";
import { StatusChip } from "@/components/ui/chips";
import { Pagination } from "@/components/ui/pagination";
import { PendingLink } from "@/components/ui/pending-link";
import { RefLink } from "@/components/ui/ref-link";

export const metadata: Metadata = { title: "Applications" };

export default async function ApplicationsPage({
  searchParams,
}: {
  searchParams: Promise<Record<string, string | undefined>>;
}) {
  const sp = await searchParams;
  const session = await requireSession();
  const exhibition = await currentExhibition();
  const staff = session.user.role !== "exhibitor";
  const list = await api<Paginated<Application>>("/applications", {
    query: apiQuery(sp, ["q", "status", "from", "to"], { exhibition_id: staff ? exhibition?.id : undefined }),
  });

  return (
    <div className="grid gap-6">
      <PageHeader
        actions={
          !staff &&
          exhibition && (
            <Link className="button button--primary button--md" href={`/exhibitions/${exhibition.id}`}>
              Apply for space
            </Link>
          )
        }
        description={
          staff
            ? `${list.total} applications · open one to approve, return or reject it`
            : "Track the status of your applications."
        }
        eyebrow={staff ? exhibition?.code : undefined}
        title={staff ? "Applications" : "My applications"}
      />
      <FilterBar
        dates={staff}
        facets={[
          {
            key: "status",
            label: "Status",
            options: Object.entries(APPLICATION_STATUS)
              .filter(([k]) => !staff || k !== "draft")
              .map(([value, s]) => ({ value, label: s.label })),
          },
        ]}
        placeholder="Search reference, exhibitor, product…"
      />
      <Surface>
        <DataTable
          columns={[
            {
              key: "ref",
              label: "Reference",
              render: (a) => <RefLink href={`/applications/${a.id}`}>{a.reference}</RefLink>,
            },
            ...(staff
              ? [
                  {
                    key: "exhibitor",
                    label: "Exhibitor",
                    render: (a: Application) => (
                      <span>
                        <PendingLink
                          className="font-semibold hover:underline"
                          href={`/applications/${a.id}`}
                          label={`Opening ${a.reference}…`}
                        >
                          {a.exhibitor?.organization_name}
                        </PendingLink>
                        <span className="block text-xs text-muted">{a.exhibitor?.country}</span>
                      </span>
                    ),
                  },
                ]
              : [{ key: "exhibition", label: "Exhibition", render: (a: Application) => a.exhibition?.name }]),
            {
              key: "space",
              label: "Space",
              render: (a) => {
                const held = a.active_allocations ?? [];

                // Once a space is held, show it — the category asked for no longer matters.
                if (held.length) {
                  return (
                    <span className="grid gap-0.5">
                      <span className="flex flex-wrap items-center gap-1.5">
                        {held.map((x) => (
                          <span key={x.id} className="font-display text-base font-extrabold">
                            {x.space?.number}
                          </span>
                        ))}
                        {held.some((x) => x.status === "pending") && (
                          <span className="text-xs text-warning">awaiting confirmation</span>
                        )}
                      </span>
                      <span className="text-xs text-muted">
                        {[held[0].space?.hall?.name, held[0].space?.size_label].filter(Boolean).join(" · ")}
                        {a.spaces_requested > held.length ? ` · ${held.length} of ${a.spaces_requested}` : ""}
                      </span>
                    </span>
                  );
                }

                return (
                  <span className="grid gap-0.5">
                    <span className="text-muted">Not chosen yet</span>
                    <span className="text-xs text-muted">
                      {a.spaces_requested > 1 ? `${a.spaces_requested} spaces · ` : ""}
                      {a.category ? a.category.name : "any category"}
                    </span>
                  </span>
                );
              },
            },
            {
              key: "product",
              label: "Products",
              className: "max-w-[220px] truncate",
              render: (a) => a.product_category,
            },
            {
              key: "control",
              label: "Control number",
              render: (a) => {
                const inv = a.invoices?.[0];

                if (!inv) return <span className="text-muted">—</span>;
                if (inv.status === "paid") return <span className="text-success">Paid</span>;

                return inv.control_number ? (
                  <Link
                    className="font-mono text-xs font-semibold tracking-wide text-brand-ink hover:underline"
                    href={`/invoices/${inv.id}`}
                    title={`Invoice ${inv.number}`}
                  >
                    {inv.control_number}
                  </Link>
                ) : (
                  <span className="text-xs text-muted">Being issued…</span>
                );
              },
            },
            { key: "submitted", label: "Submitted", render: (a) => fmtDate(a.submitted_at) },
            { key: "status", label: "Status", render: (a) => <StatusChip map={APPLICATION_STATUS} value={a.status} /> },
          ]}
          empty="No applications"
          emptyDescription={
            staff ? "Nothing matches these filters." : "Open a published exhibition and apply for space."
          }
          rowHref={(a) => `/applications/${a.id}`}
          rowLabel={(a) => `Open application ${a.reference}`}
          rows={list.data}
        />
      </Surface>
      <Pagination page={list.current_page} pages={list.last_page} params={toParams(sp)} />
    </div>
  );
}
