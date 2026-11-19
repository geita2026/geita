import type { GepgBill, Invoice } from "@/lib/types";

import { ArrowsRotateLeft, CircleExclamation } from "@gravity-ui/icons";

import { fmtDate, fmtDateTime } from "@/lib/domain/format";
import { GEPG_BILL_STATUS } from "@/lib/domain/labels";
import { Panel } from "@/components/ui/panel";
import { StatusChip } from "@/components/ui/chips";
import { ActionButton } from "@/components/ui/action-button";
import { MorphForm } from "@/components/ui/morph-form";
import { CheckF, TextF } from "@/components/ui/action-form";
import { WaveLoader } from "@/components/ui/wave-loader";

/**
 * FR-059: the invoice's GePG control number — the one thing an exhibitor needs
 * to pay. The current bill is shown large; earlier bills (replaced when the
 * amount changed, or cancelled) are listed under it so a payment made against
 * an old number can still be traced.
 */
export function GepgPanel({
  invoice: i,
  canManage,
  simulator,
}: {
  invoice: Invoice;
  /** Finance: may retry a failed request. */
  canManage: boolean;
  /** GePG is the built-in simulator: show the "pay as the exhibitor" control. */
  simulator: boolean;
}) {
  const bills = i.gepg_bills ?? [];
  const current = bills.find((b) => b.status === "issued" || b.status === "requesting");
  const failed = !current && i.gepg_status === "failed" ? bills.find((b) => b.status === "failed") : undefined;
  const open = !["paid", "cancelled", "refunded"].includes(i.status);
  const earlier = bills.filter((b) => b !== current && b !== failed);

  return (
    <Panel meta="GePG" title="Control number">
      {current?.status === "issued" && current.control_number ? (
        <div className="rounded-2xl border-b-4 border-brand bg-[#111114] p-5 text-white">
          <p className="text-xs font-bold uppercase tracking-[0.16em] opacity-70">Pay with control number</p>
          <p className="mt-1 font-mono text-[28px] font-extrabold tracking-[0.06em] text-brand tabular">
            {current.control_number.replace(/(\d{4})(?=\d)/g, "$1 ")}
          </p>
          <p className="mt-2 text-sm opacity-80">
            {i.currency} {(current.amount - current.paid_amount).toLocaleString("en-TZ", { minimumFractionDigits: 2 })}{" "}
            · at any bank or by mobile money · before {fmtDate(current.expires_at)}
          </p>
        </div>
      ) : current?.status === "requesting" ? (
        <div className="flex items-center gap-3 rounded-2xl bg-warning-soft p-4 text-sm text-warning">
          <WaveLoader />
          GePG is issuing the control number. It is sent by SMS and email as soon as it arrives.
        </div>
      ) : failed ? (
        <div className="grid gap-3 rounded-2xl bg-danger-soft p-4 text-sm text-danger">
          <p className="flex items-start gap-2">
            <CircleExclamation className="mt-0.5 size-4 shrink-0" />
            <span>
              GePG has not issued a control number for this invoice. {failed.error}
              {canManage ? " It is retried automatically every morning." : " The Finance Unit has been told."}
            </span>
          </p>
          {canManage && (
            <div>
              <ActionButton
                pendingLabel="Asking GePG…"
                size="sm"
                spec={{
                  method: "POST",
                  path: `/invoices/${i.id}/gepg/retry`,
                  success: "Control number requested again",
                }}
                variant="danger"
              >
                <ArrowsRotateLeft className="size-4" /> Retry now
              </ActionButton>
            </div>
          )}
        </div>
      ) : open && i.gepg_status === null ? (
        <p className="text-sm text-muted">
          No control number has been requested{canManage ? "." : ". The Finance Unit will issue one."}
        </p>
      ) : (
        <p className="text-sm text-muted">
          {i.status === "paid"
            ? "Paid in full — no control number is open."
            : "No control number is open for this invoice."}
        </p>
      )}

      {simulator && canManage && current?.status === "issued" && (
        <div className="rounded-xl border border-dashed border-warning/60 p-3">
          <p className="mb-2 text-xs text-muted">
            GePG is the built-in <strong>simulator</strong>. Pay this control number as the exhibitor would at the bank,
            to see the payment, receipt and confirmation arrive.
          </p>
          <MorphForm
            label="Simulate a GePG payment"
            pendingLabel="Paying…"
            size="sm"
            spec={{
              method: "POST",
              path: `/invoices/${i.id}/gepg/simulate-payment`,
              bools: ["silent"],
              success: "Simulated payment sent",
            }}
            submitLabel="Pay"
            subtitle={`Control number ${current.control_number}. Nothing real is charged.`}
            title="Simulate a GePG payment"
            variant="secondary"
          >
            <TextF
              isRequired
              defaultValue={current.amount - current.paid_amount}
              description="Less than the balance makes a part payment."
              label={`Amount (${i.currency})`}
              min={1}
              name="amount"
              type="number"
            />
            <CheckF
              description="GePG takes the money but its notification never arrives — the next reconciliation must recover it."
              label="Lose the payment notification"
              name="silent"
            />
          </MorphForm>
        </div>
      )}

      {earlier.length > 0 && (
        <div>
          <p className="mb-2 text-xs font-semibold uppercase tracking-wider text-muted">Earlier bills</p>
          <ul className="grid gap-1.5">
            {earlier.map((b) => (
              <EarlierBill key={b.id} bill={b} currency={i.currency} />
            ))}
          </ul>
        </div>
      )}
    </Panel>
  );
}

function EarlierBill({ bill: b, currency }: { bill: GepgBill; currency: string }) {
  return (
    <li className="flex flex-wrap items-center justify-between gap-2 rounded-lg bg-surface-secondary px-3 py-2 text-xs">
      <span className="min-w-0">
        <span className="font-mono font-semibold">{b.control_number ?? b.bill_id}</span>
        <span className="block text-muted">
          {currency} {b.amount.toLocaleString("en-TZ")} · {fmtDateTime(b.ended_at ?? b.requested_at)}
          {b.error ? ` · ${b.error}` : ""}
        </span>
      </span>
      <StatusChip map={GEPG_BILL_STATUS} value={b.status} />
    </li>
  );
}
