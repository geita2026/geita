import type { Metadata } from "next";
import type { Invoice, Paginated } from "@/lib/types";

import { api } from "@/lib/api";
import { requireSession } from "@/lib/auth/session";
import { currentExhibition } from "@/lib/exhibition";
import { apiQuery, toParams } from "@/lib/params";
import { fmtDate } from "@/lib/domain/format";
import { INVOICE_STATUS } from "@/lib/domain/labels";
import { PageHeader } from "@/components/ui/page-header";
import { FilterBar } from "@/components/ui/filter-bar";
import { DataTable, Surface } from "@/components/ui/table";
import { StatusChip } from "@/components/ui/chips";
import { Pagination } from "@/components/ui/pagination";
import { RefLink } from "@/components/ui/ref-link";

export const metadata: Metadata = { title: "Invoices" };

export default async function InvoicesPage({
  searchParams,
}: {
  searchParams: Promise<Record<string, string | undefined>>;
}) {
  const sp = await searchParams;
  const session = await requireSession();
  const staff = session.user.role !== "exhibitor";
  const exhibition = await currentExhibition();
  const list = await api<Paginated<Invoice>>("/invoices", {
    query: apiQuery(sp, ["q", "status", "from", "to"], { exhibition_id: staff ? exhibition?.id : undefined }),
  });
  const money = (n: number) => n.toLocaleString("en-TZ");

  return (
    <div className="grid gap-6">
      <PageHeader
        description={
          staff
            ? `${list.total} invoices · generated automatically when space is allocated`
            : "Pay an invoice and upload the evidence to confirm your participation."
        }
        eyebrow={staff ? exhibition?.code : undefined}
        title={staff ? "Invoices" : "My invoices"}
      />
      <FilterBar
        dates={staff}
        facets={[
          {
            key: "status",
            label: "Payment status",
            options: Object.entries(INVOICE_STATUS).map(([value, s]) => ({ value, label: s.label })),
          },
        ]}
        placeholder="Search invoice, control number, exhibitor…"
      />
      <Surface>
        <DataTable
          columns={[
            {
              key: "number",
              label: "Invoice",
              render: (i) => <RefLink href={`/invoices/${i.id}`}>{i.number}</RefLink>,
            },
            {
              key: "exhibitor",
              label: staff ? "Exhibitor" : "Exhibition",
              render: (i) => (
                <span className="font-semibold">{staff ? i.exhibitor?.organization_name : i.exhibition?.name}</span>
              ),
            },
            {
              key: "control",
              label: "Control no.",
              render: (i) => <span className="font-mono text-xs">{i.control_number}</span>,
            },
            { key: "due", label: "Due", render: (i) => fmtDate(i.due_on) },
            { key: "total", label: "Total (TZS)", align: "right", render: (i) => money(i.total) },
            { key: "paid", label: "Paid", align: "right", render: (i) => money(i.amount_paid) },
            {
              key: "balance",
              label: "Balance",
              align: "right",
              render: (i) => <span className={i.balance > 0 ? "font-semibold" : "text-muted"}>{money(i.balance)}</span>,
            },
            { key: "status", label: "Status", render: (i) => <StatusChip map={INVOICE_STATUS} value={i.status} /> },
          ]}
          empty="No invoices"
          emptyDescription="An invoice is generated when a space is allocated."
          minWidth={900}
          rows={list.data}
        />
      </Surface>
      <Pagination page={list.current_page} pages={list.last_page} params={toParams(sp)} />
    </div>
  );
}
