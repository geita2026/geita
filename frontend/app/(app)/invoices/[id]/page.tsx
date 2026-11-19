import type { Metadata } from "next";
import type { GepgStatus, Invoice } from "@/lib/types";

import Link from "next/link";
import { Plus } from "@gravity-ui/icons";

import { api } from "@/lib/api";
import { can, requireSession } from "@/lib/auth/session";
import { fmtDate, fmtDateTime, pct } from "@/lib/domain/format";
import { INVOICE_STATUS, MANUAL_PAYMENT_METHODS, PAYMENT_METHODS, PAYMENT_STATUS, options } from "@/lib/domain/labels";
import { PageHeader } from "@/components/ui/page-header";
import { Panel } from "@/components/ui/panel";
import { Fact, StatusChip } from "@/components/ui/chips";
import { MorphForm } from "@/components/ui/morph-form";
import { ActionButton } from "@/components/ui/action-button";
import { DownloadLink } from "@/components/ui/download-link";
import { AreaF, FileF, SelectF, TextF } from "@/components/ui/action-form";
import { GepgPanel } from "@/components/finance/gepg-panel";

export const metadata: Metadata = { title: "Invoice" };

export default async function InvoicePage({ params }: { params: Promise<{ id: string }> }) {
  const { id } = await params;
  const session = await requireSession();
  const i = await api<Invoice>(`/invoices/${id}`);
  const finance = can(session, "finance.manage");
  // Only Finance staff can read the gateway's settings; everyone else just sees the bill.
  const gateway = can(session, "finance.view") ? await api<GepgStatus>("/gepg/status") : null;
  const payable = i.can_record_payment === true;
  const exception = i.record_payment_as === "exception";
  const me = session.user.id;
  const money = (n: number) => n.toLocaleString("en-TZ", { minimumFractionDigits: 2 });
  const today = new Date().toISOString().slice(0, 10);

  return (
    <div className="grid gap-6">
      <PageHeader
        actions={
          <>
            <StatusChip map={INVOICE_STATUS} size="md" value={i.status} />
            <DownloadLink path={`invoices/${i.id}/pdf`}>Invoice PDF</DownloadLink>
            {payable ? (
              <MorphForm
                icon={<Plus className="size-4" />}
                label="Record a payment"
                pendingLabel="Submitting…"
                spec={{
                  method: "POST",
                  path: `/invoices/${i.id}/payments`,
                  multipart: true,
                  success: exception
                    ? "Recorded — another Finance Officer must verify it"
                    : "Payment submitted for verification",
                }}
                submitLabel="Submit payment"
                subtitle={
                  exception
                    ? "Only for money that reached the collection account outside GePG. Attach the bank slip and say why. A different Finance Officer must verify it, and Management and the Auditor are told when it is accepted."
                    : "Record a payment made by bank transfer or mobile money, with its evidence. Finance verifies it and issues the receipt."
                }
                title={exception ? "Record a payment outside GePG" : "Record a payment"}
              >
                <div className="grid gap-4 sm:grid-cols-2">
                  <TextF
                    isRequired
                    defaultValue={i.balance}
                    label={`Amount paid (${i.currency})`}
                    min={1}
                    name="amount"
                    type="number"
                  />
                  <TextF isRequired defaultValue={today} label="Date paid" name="paid_on" type="date" />
                  <SelectF
                    isRequired
                    defaultValue="bank"
                    label="Method"
                    name="method"
                    options={options(MANUAL_PAYMENT_METHODS)}
                  />
                  <TextF
                    isRequired
                    mono
                    description="Bank slip or mobile-money transaction ID."
                    label="Transaction reference"
                    name="payer_reference"
                  />
                  <div className="sm:col-span-2">
                    <FileF
                      isRequired
                      accept=".pdf,.jpg,.jpeg,.png"
                      description="Bank slip or confirmation message — PDF or image, up to 10 MB."
                      label="Payment evidence"
                      name="evidence"
                    />
                  </div>
                  {exception && (
                    <div className="sm:col-span-2">
                      <AreaF
                        isRequired
                        description="Kept with the payment and shown to whoever verifies or audits it."
                        label="Why it did not go through GePG"
                        name="manual_reason"
                        rows={3}
                      />
                    </div>
                  )}
                </div>
              </MorphForm>
            ) : (
              i.record_payment_block &&
              !["paid", "cancelled", "refunded"].includes(i.status) && (
                <span className="max-w-xs text-xs text-muted">{i.record_payment_block}</span>
              )
            )}
          </>
        }
        description={`${i.exhibitor!.organization_name} · ${i.exhibition!.name}`}
        eyebrow={i.number}
        title={`${i.currency} ${i.total.toLocaleString("en-TZ")}`}
      />

      <div className="grid gap-4 xl:grid-cols-[1.5fr_1fr]">
        <Panel title="Charges">
          <div className="overflow-x-auto">
            <table className="table data-table min-w-[560px]">
              <thead>
                <tr>
                  {["Space", "Category", "Size", "Unit price", "Qty", "Amount"].map((h, n) => (
                    <th key={h} className={`px-3 py-2 ${n > 2 ? "text-right" : "text-left"}`}>
                      {h}
                    </th>
                  ))}
                </tr>
              </thead>
              <tbody>
                {(i.items ?? []).map((it) => (
                  <tr key={it.id} className="border-t border-separator text-sm">
                    <td className="px-3 py-2.5">
                      <span className="font-display font-extrabold">{it.space_number}</span>
                      <span className="block text-xs text-muted">{it.description}</span>
                    </td>
                    <td className="px-3 py-2.5">{it.space_category}</td>
                    <td className="px-3 py-2.5">{it.space_size}</td>
                    <td className="px-3 py-2.5 text-right tabular">{money(it.unit_price)}</td>
                    <td className="px-3 py-2.5 text-right tabular">{it.quantity}</td>
                    <td className="px-3 py-2.5 text-right tabular">{money(it.amount)}</td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
          <dl className="ms-auto grid w-full max-w-xs gap-1.5 text-sm">
            {[
              ["Subtotal", i.subtotal],
              [`VAT (${i.vat_rate}%)`, i.vat_amount],
            ].map(([k, v]) => (
              <div key={k} className="flex justify-between">
                <dt className="text-muted">{k}</dt>
                <dd className="tabular">{money(v as number)}</dd>
              </div>
            ))}
            <div className="flex justify-between border-t border-foreground pt-2 font-display text-base font-extrabold">
              <dt>Total</dt>
              <dd className="tabular">{money(i.total)}</dd>
            </div>
            <div className="flex justify-between">
              <dt className="text-muted">Paid</dt>
              <dd className="tabular">{money(i.amount_paid)}</dd>
            </div>
            <div className="flex justify-between font-semibold">
              <dt>Balance due</dt>
              <dd className="tabular">{money(i.balance)}</dd>
            </div>
          </dl>
        </Panel>

        <div className="grid content-start gap-4">
          <GepgPanel canManage={finance} invoice={i} simulator={gateway?.mode === "simulator"} />

          <Panel title="Payment">
            <div>
              <div className="h-2.5 overflow-hidden rounded-full bg-default">
                <div
                  className="h-full rounded-r bg-prio-low"
                  style={{ width: `${Math.min(100, pct(i.amount_paid, i.total))}%` }}
                />
              </div>
              <p className="mt-1.5 text-xs text-muted">{pct(i.amount_paid, i.total)}% paid</p>
            </div>
            <dl className="grid grid-cols-2 gap-x-6 gap-y-3">
              <Fact label="Payment deadline">{fmtDate(i.due_on)}</Fact>
              <Fact label="Issued">{fmtDate(i.issued_on)}</Fact>
              <Fact label="Application">
                <Link className="font-mono text-xs hover:underline" href={`/applications/${i.application_id}`}>
                  {i.application?.reference}
                </Link>
              </Fact>
            </dl>
          </Panel>

          <Panel meta={`${i.payments?.length ?? 0}`} title="Payments and receipts">
            <ul className="grid gap-2">
              {(i.payments ?? []).map((p) => (
                <li key={p.id} className="grid gap-2 rounded-xl bg-surface-secondary p-3.5 text-sm">
                  <div className="flex flex-wrap items-center justify-between gap-2">
                    <span>
                      <span className="font-display text-lg font-extrabold tabular">
                        {p.amount.toLocaleString("en-TZ")}
                      </span>
                      <span className="block text-xs text-muted">
                        {p.psp_name ? `GePG · ${p.psp_name}` : (PAYMENT_METHODS[p.method] ?? p.method)} ·{" "}
                        {fmtDate(p.paid_on)} · <span className="font-mono">{p.payer_reference}</span>
                      </span>
                    </span>
                    <span className="flex items-center gap-1.5">
                      {p.manual_reason && <span className="badge badge-sm badge-warning badge-soft">Manual</span>}
                      <StatusChip map={PAYMENT_STATUS} value={p.verification_status} />
                    </span>
                  </div>
                  {p.submitter && p.channel !== "gateway" && (
                    <p className="text-xs text-muted">
                      Recorded by {p.submitter.name}, {fmtDateTime(p.created_at)}
                      {p.manual_reason ? ` — ${p.manual_reason}` : ""}
                    </p>
                  )}
                  {p.verified_at && (
                    <p className="text-xs text-muted">
                      {p.verification_status === "verified" ? `Receipt ${p.receipt_number} · verified` : "Rejected"} by{" "}
                      {p.verifier?.name ?? "payment gateway"}, {fmtDateTime(p.verified_at)}
                      {p.verification_notes ? ` — ${p.verification_notes}` : ""}
                    </p>
                  )}
                  <div className="flex flex-wrap gap-2">
                    {p.has_evidence && (
                      <DownloadLink path={`payments/${p.id}/evidence`} size="sm" variant="ghost">
                        Evidence
                      </DownloadLink>
                    )}
                    {p.verification_status === "verified" && (
                      <DownloadLink path={`payments/${p.id}/receipt`} size="sm">
                        Receipt PDF
                      </DownloadLink>
                    )}
                    {finance && p.verification_status === "pending" && p.submitted_by === me && (
                      <p className="text-xs text-warning">
                        You recorded this payment, so another Finance Officer must verify it.
                      </p>
                    )}
                    {finance && p.verification_status === "pending" && p.submitted_by !== me && (
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
                          title="Reject payment"
                          variant="danger"
                        >
                          <AreaF isRequired label="Why it could not be verified" name="notes" rows={3} />
                        </MorphForm>
                      </>
                    )}
                  </div>
                </li>
              ))}
              {(i.payments?.length ?? 0) === 0 && <p className="text-sm text-muted">No payment recorded yet.</p>}
            </ul>
          </Panel>
        </div>
      </div>
    </div>
  );
}
