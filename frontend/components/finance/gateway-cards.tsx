"use client";

import type { GepgMessage, GepgReconciliation } from "@/lib/types";

import clsx from "clsx";

import { fmtDate, fmtDateTime, tzs } from "@/lib/domain/format";
import { Fact } from "@/components/ui/chips";
import { MorphCard } from "@/components/ui/morph-form";

/** Readable names for GePG's message types. */
export const GEPG_MESSAGE_TYPE: Record<string, string> = {
  gepgBillSubReq: "Bill sent",
  gepgBillSubReqAck: "Bill acknowledged",
  gepgBillSubResp: "Control number received",
  gepgBillSubRespAck: "Control number acknowledged",
  gepgPmtSpInfo: "Payment received",
  gepgPmtSpInfoAck: "Payment acknowledged",
  gepgBillCanclReq: "Cancellation sent",
  gepgBillCanclResp: "Cancellation answered",
  gepgSpReconcReq: "Reconciliation requested",
  gepgSpReconcReqAck: "Reconciliation acknowledged",
  gepgSpReconcResp: "Reconciliation received",
  gepgSpReconcRespAck: "Reconciliation acknowledged",
};

export function ReconciliationCard({ run: r }: { run: GepgReconciliation }) {
  const issues = (r.recovered?.length ?? 0) + (r.missing_at_gepg?.length ?? 0) + (r.amount_mismatches?.length ?? 0);
  const tone = r.status === "failed" ? "text-danger" : issues ? "text-warning" : "text-success";

  return (
    <MorphCard
      header={<span className="font-mono text-xs text-muted">{r.request_id}</span>}
      subtitle={
        r.status === "received"
          ? `${r.gepg_count} payment${r.gepg_count === 1 ? "" : "s"} at GePG · ${tzs(r.gepg_total)}`
          : r.status
      }
      title={`Payments of ${fmtDate(r.transaction_date)}`}
      trigger={({ title, subtitle }) => (
        <>
          <span className={clsx("text-xs font-bold uppercase tracking-wider", tone)}>
            {r.status === "failed"
              ? "Failed"
              : r.status === "requested"
                ? "Waiting for GePG"
                : issues
                  ? `${issues} to check`
                  : "All matched"}
          </span>
          {title}
          {subtitle}
        </>
      )}
      width="max-w-xl"
    >
      {r.error && <p className="rounded-xl bg-danger-soft px-3 py-2 text-sm text-danger">{r.error}</p>}
      <dl className="grid grid-cols-3 gap-4">
        <Fact label="Matched">{r.matched}</Fact>
        <Fact label="At GePG">{r.gepg_count}</Fact>
        <Fact label="Asked by">{r.requester?.name ?? "Schedule"}</Fact>
      </dl>
      <Issue
        empty="None."
        rows={(r.recovered ?? []).map((x) => `${x.trx} · control number ${x.control_number ?? "—"} · ${tzs(x.amount)}`)}
        title="Recovered — paid at GePG, notification never arrived, now recorded"
      />
      <Issue
        empty="None."
        rows={(r.missing_at_gepg ?? []).map((x) => `${x.payment} · ${x.trx} · ${tzs(x.amount)}`)}
        title="Recorded here as GePG payments but unknown to GePG — check with GePG"
      />
      <Issue
        empty="None."
        rows={(r.amount_mismatches ?? []).map(
          (x) => `${x.payment} · ${x.trx} · here ${tzs(x.ours)}, GePG ${tzs(x.gepg)}`,
        )}
        title="Amounts that differ"
      />
    </MorphCard>
  );
}

function Issue({ title, rows, empty }: { title: string; rows: string[]; empty: string }) {
  return (
    <div>
      <p className="text-xs font-semibold uppercase tracking-wider text-muted">{title}</p>
      {rows.length ? (
        <ul className="mt-1.5 grid gap-1 font-mono text-xs">
          {rows.map((r) => (
            <li key={r}>{r}</li>
          ))}
        </ul>
      ) : (
        <p className="mt-1 text-sm text-muted">{empty}</p>
      )}
    </div>
  );
}

/** The message name; opens to the verbatim XML, as needed for a dispute or for GePG support. */
export function MessageCard({ message: m }: { message: GepgMessage }) {
  return (
    <MorphCard
      header={
        <>
          <span className="font-mono text-xs text-muted">{m.type}</span>
          {m.signature_valid === true && <span className="text-xs font-semibold text-success">Signature valid</span>}
          {m.signature_valid === false && <span className="text-xs font-semibold text-danger">Signature invalid</span>}
        </>
      }
      radius={8}
      subtitle={`${m.direction === "out" ? "Sent to" : "Received from"} GePG · ${fmtDateTime(m.created_at)}`}
      title={GEPG_MESSAGE_TYPE[m.type] ?? m.type}
      trigger={({ title }) => <span className="text-sm font-semibold hover:underline">{title}</span>}
      triggerClassName="text-left"
      width="max-w-3xl"
    >
      {m.error && <p className="rounded-xl bg-danger-soft px-3 py-2 text-sm text-danger">{m.error}</p>}
      <pre className="max-h-[55vh] overflow-auto rounded-xl bg-[var(--foreground)] p-4 font-mono text-xs leading-relaxed whitespace-pre-wrap break-all text-[var(--background)]">
        {(m.payload ?? "").replace(/></g, ">\n<")}
      </pre>
    </MorphCard>
  );
}
