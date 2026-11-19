import type { Metadata } from "next";
import type { Paginated, Payment } from "@/lib/types";

import Link from "next/link";

import { api } from "@/lib/api";
import { can, requirePermission } from "@/lib/auth/session";
import { currentExhibition } from "@/lib/exhibition";
import { apiQuery, toParams } from "@/lib/params";
import { fmtDate } from "@/lib/domain/format";
import { PAYMENT_METHODS, PAYMENT_STATUS } from "@/lib/domain/labels";
import { PageHeader } from "@/components/ui/page-header";
import { FilterBar } from "@/components/ui/filter-bar";
import { DataTable, Surface } from "@/components/ui/table";
import { StatusChip } from "@/components/ui/chips";
import { Pagination } from "@/components/ui/pagination";
import { ActionButton } from "@/components/ui/action-button";
import { MorphForm } from "@/components/ui/morph-form";
import { AreaF } from "@/components/ui/action-form";

export const metadata: Metadata = { title: "Payments" };

/** FR-033: Finance's verification queue. */
export default async function PaymentsPage({
  searchParams,
}: {
  searchParams: Promise<Record<string, string | undefined>>;
}) {
  const sp = await searchParams;
  const session = await requirePermission("finance.view");
  const exhibition = await currentExhibition();
  const finance = can(session, "finance.manage");
  const list = await api<Paginated<Payment>>("/payments", {
    query: apiQuery(sp, ["q", "status", "channel"], { exhibition_id: exhibition?.id }),
  });

  return (
    <div className="grid gap-6">
      <PageHeader
        description="Match each payment to the bank statement, then verify it. Verifying issues the receipt and, once the invoice is settled, confirms participation."
        eyebrow={exhibition?.code}
        title="Payment verification"
      />
      <FilterBar
        facets={[
          {
            key: "status",
            label: "Verification",
            options: Object.entries(PAYMENT_STATUS).map(([value, s]) => ({ value, label: s.label })),
          },
          {
            key: "channel",
            label: "How it came in",
            options: [
              { value: "gateway", label: "GePG" },
              { value: "manual", label: "Recorded by hand" },
            ],
          },
        ]}
        placeholder="Search payment, transaction, receipt, invoice…"
      />
      <Surface>
        <DataTable
          columns={[
            {
              key: "ref",
              label: "Payment",
              render: (p) => <span className="font-mono text-xs font-semibold">{p.receipt_number ?? p.reference}</span>,
            },
            {
              key: "invoice",
              label: "Invoice · exhibitor",
              render: (p) => (
                <Link className="hover:underline" href={`/invoices/${p.invoice_id}`}>
                  <span className="font-semibold">{p.invoice?.exhibitor?.organization_name}</span>
                  <span className="block font-mono text-xs text-muted">{p.invoice?.number}</span>
                </Link>
              ),
            },
            {
              key: "method",
              label: "Method · reference",
              render: (p) => (
                <span>
                  {p.psp_name ? `GePG · ${p.psp_name}` : (PAYMENT_METHODS[p.method] ?? p.method)}
                  {p.manual_reason && <span className="badge badge-xs badge-warning badge-soft ms-1.5">Manual</span>}
                  <span className="block font-mono text-xs text-muted">{p.payer_reference}</span>
                  {p.submitter && p.channel !== "gateway" && (
                    <span className="block text-xs text-muted">Recorded by {p.submitter.name}</span>
                  )}
                </span>
              ),
            },
            { key: "date", label: "Paid on", render: (p) => fmtDate(p.paid_on) },
            {
              key: "amount",
              label: "Amount (TZS)",
              align: "right",
              render: (p) => <span className="font-semibold">{p.amount.toLocaleString("en-TZ")}</span>,
            },
            {
              key: "status",
              label: "Status",
              render: (p) => <StatusChip map={PAYMENT_STATUS} value={p.verification_status} />,
            },
            {
              key: "actions",
              label: "",
              align: "right",
              render: (p) => (
                <span className="flex items-center justify-end gap-1.5">
                  {p.has_evidence && (
                    <a
                      className="text-xs font-semibold text-brand-ink hover:underline"
                      href={`/api/files/payments/${p.id}/evidence`}
                      rel="noopener"
                      target="_blank"
                    >
                      Evidence
                    </a>
                  )}
                  {finance && p.verification_status === "pending" && p.submitted_by === session.user.id && (
                    <span className="text-xs text-warning">Needs another Finance Officer</span>
                  )}
                  {finance && p.verification_status === "pending" && p.submitted_by !== session.user.id && (
                    <>
                      <ActionButton
                        pendingLabel="Verifying…"
                        size="sm"
                        spec={{
                          method: "POST",
                          path: `/payments/${p.id}/verify`,
                          extra: { decision: "verify" },
                          success: "Payment verified and receipt issued",
                        }}
                        successLabel="Verified"
                      >
                        Verify
                      </ActionButton>
                      <MorphForm
                        label="Reject"
                        size="sm"
                        spec={{
                          method: "POST",
                          path: `/payments/${p.id}/verify`,
                          extra: { decision: "reject" },
                          success: "Payment rejected",
                        }}
                        submitLabel="Reject payment"
                        title={`Reject ${p.reference}`}
                        variant="danger"
                      >
                        <AreaF isRequired label="Why it could not be verified" name="notes" rows={3} />
                      </MorphForm>
                    </>
                  )}
                  {p.verification_status === "verified" && (
                    <a
                      className="text-xs font-semibold text-brand-ink hover:underline"
                      href={`/api/files/payments/${p.id}/receipt`}
                      rel="noopener"
                      target="_blank"
                    >
                      Receipt
                    </a>
                  )}
                </span>
              ),
            },
          ]}
          empty="No payments"
          emptyDescription="Payments appear here when exhibitors record them."
          minWidth={980}
          rowHref={(p) => `/invoices/${p.invoice_id}`}
          rowLabel={(p) => `Open invoice for payment ${p.reference}`}
          rows={list.data}
        />
      </Surface>
      <Pagination page={list.current_page} pages={list.last_page} params={toParams(sp)} />
    </div>
  );
}
