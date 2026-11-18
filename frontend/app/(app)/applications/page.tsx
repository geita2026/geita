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
import { CategoryChip, StatusChip } from "@/components/ui/chips";
import { Pagination } from "@/components/ui/pagination";
import { PendingLink } from "@/components/ui/pending-link";

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
              render: (a) => (
                <PendingLink
                  className="font-mono text-xs font-semibold hover:underline"
                  href={`/applications/${a.id}`}
                  label={`Opening ${a.reference}…`}
                >
                  {a.reference}
                </PendingLink>
              ),
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
              key: "category",
              label: "Wants",
              render: (a) =>
                a.category ? (
                  <CategoryChip color={a.category.color} name={a.category.name} />
                ) : (
                  <span className="text-muted">Any</span>
                ),
            },
            {
              key: "product",
              label: "Products",
              className: "max-w-[220px] truncate",
              render: (a) => a.product_category,
            },
            {
              key: "spaces",
              label: "Spaces",
              align: "right",
              render: (a) => `${a.active_allocations_count ?? 0}/${a.spaces_requested}`,
            },
            { key: "submitted", label: "Submitted", render: (a) => fmtDate(a.submitted_at) },
            { key: "status", label: "Status", render: (a) => <StatusChip map={APPLICATION_STATUS} value={a.status} /> },
          ]}
          empty="No applications"
          emptyDescription={
            staff ? "Nothing matches these filters." : "Open a published exhibition and apply for space."
          }
          rows={list.data}
        />
      </Surface>
      <Pagination page={list.current_page} pages={list.last_page} params={toParams(sp)} />
    </div>
  );
}
