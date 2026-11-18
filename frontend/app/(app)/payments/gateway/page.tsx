import type { Metadata } from "next";
import type { GepgMessage, GepgReconciliation, GepgStatus, Paginated } from "@/lib/types";

import clsx from "clsx";

import { api } from "@/lib/api";
import { can, requirePermission } from "@/lib/auth/session";
import { apiQuery, toParams } from "@/lib/params";
import { fmtDateTime, yesterdayIso } from "@/lib/domain/format";
import { GEPG_BILL_STATUS, GEPG_MODE } from "@/lib/domain/labels";
import { GEPG_MESSAGE_TYPE as TYPE, MessageCard, ReconciliationCard } from "@/components/finance/gateway-cards";
import { PageHeader } from "@/components/ui/page-header";
import { Panel } from "@/components/ui/panel";
import { Fact, StatusChip } from "@/components/ui/chips";
import { MorphForm } from "@/components/ui/morph-form";
import { TextF } from "@/components/ui/action-form";
import { FilterBar } from "@/components/ui/filter-bar";
import { DataTable, Surface } from "@/components/ui/table";
import { Pagination } from "@/components/ui/pagination";

export const metadata: Metadata = { title: "Payment gateway" };

/** FR-059: how GeMEMIS is talking to GePG — settings, bills, reconciliation, and every message. */
export default async function GatewayPage({
  searchParams,
}: {
  searchParams: Promise<Record<string, string | undefined>>;
}) {
  const sp = await searchParams;
  const session = await requirePermission("finance.view");
  const manage = can(session, "finance.manage");
  const [status, runs, messages] = await Promise.all([
    api<GepgStatus>("/gepg/status"),
    api<GepgReconciliation[]>("/gepg/reconciliations"),
    api<Paginated<GepgMessage>>("/gepg/messages", { query: apiQuery(sp, ["q", "errors"]) }),
  ]);
  const yesterday = yesterdayIso();

  return (
    <div className="grid gap-6">
      <PageHeader
        actions={
          <>
            <StatusChip map={GEPG_MODE} size="md" value={status.mode} />
            {manage && status.mode !== "off" && (
              <MorphForm
                label="Reconcile a day"
                pendingLabel="Asking GePG…"
                spec={{ method: "POST", path: "/gepg/reconciliations", success: "Reconciliation finished" }}
                submitLabel="Reconcile"
                subtitle="GePG lists every payment it took for the day; GeMEMIS records any it missed and flags differences. Runs by itself every morning for the day before."
                title="Reconcile with GePG"
              >
                <TextF isRequired defaultValue={yesterday} label="Transaction date" name="date" type="date" />
              </MorphForm>
            )}
          </>
        }
        description="GePG (the Government Electronic Payment Gateway) issues control numbers and reports payments."
        title="Payment gateway"
      />

      {status.mode === "simulator" && (
        <p className="rounded-2xl bg-warning-soft px-4 py-3 text-sm text-warning" role="status">
          <strong>Simulator.</strong> Control numbers and payments come from GeMEMIS&apos;s built-in stand-in for GePG,
          not the real gateway. Switch to live once the Ministry of Finance has registered the institution and issued
          the settings (see docs/09-operations.md).
        </p>
      )}
      {status.mode === "off" && (
        <p className="rounded-2xl bg-default px-4 py-3 text-sm text-muted">
          GePG is switched off: invoices carry no control number and every payment is recorded by hand.
        </p>
      )}

      <div className="grid gap-4 xl:grid-cols-[1fr_1.2fr]">
        <Panel title="Service Provider settings">
          <dl className="grid grid-cols-2 gap-x-6 gap-y-4">
            <Fact label="SP code">
              <span className="font-mono">{status.sp_code}</span>
            </Fact>
            <Fact label="Sub-SP code">
              <span className="font-mono">{status.sub_sp_code}</span>
            </Fact>
            <Fact label="System ID">
              <span className="font-mono">{status.sp_sys_id}</span>
            </Fact>
            <Fact label="Payment option">
              {{ 1: "Full payment", 2: "Part payments allowed", 3: "Exact amount" }[status.payment_option] ??
                status.payment_option}
            </Fact>
            <Fact className="col-span-2" label="GePG address">
              <span className="font-mono text-xs">
                {status.base_url || (status.mode === "live" ? "Not set" : "— (simulator)")}
              </span>
            </Fact>
            <Fact className="col-span-2" label="Callback address to register with GePG">
              <span className="font-mono text-xs">{status.callback_url}</span>
            </Fact>
            <Fact label="Signing keys">
              {status.simulator_keys ? <span className="text-warning">Simulator keys</span> : "Installed"}
            </Fact>
            <Fact label="Default GFS code">
              <span className="font-mono">{status.default_gfs_code || "—"}</span>
            </Fact>
          </dl>
        </Panel>

        <Panel
          meta={status.last_message_at ? `last message ${fmtDateTime(status.last_message_at)}` : "no messages yet"}
          title="Bills"
        >
          <div className="flex flex-wrap gap-2">
            {Object.entries(status.bills).map(([s, n]) => (
              <span
                key={s}
                className="inline-flex items-center gap-2 rounded-full bg-surface-secondary px-3 py-1.5 text-sm"
              >
                <StatusChip map={GEPG_BILL_STATUS} value={s} />
                <span className="font-display font-extrabold tabular">{n}</span>
              </span>
            ))}
            {Object.keys(status.bills).length === 0 && <p className="text-sm text-muted">No bill has been sent yet.</p>}
          </div>
          {status.last_failure && (
            <p className="rounded-xl bg-danger-soft px-3 py-2 text-xs text-danger">
              Last problem, {fmtDateTime(status.last_failure.created_at)} (
              {TYPE[status.last_failure.type] ?? status.last_failure.type}
              {status.last_failure.reference ? `, ${status.last_failure.reference}` : ""}): {status.last_failure.error}
            </p>
          )}
        </Panel>
      </div>

      <Panel meta="the last 30" title="Reconciliation">
        {runs.length === 0 ? (
          <p className="py-4 text-center text-sm text-muted">
            Nothing reconciled yet. It runs every morning for the day before.
          </p>
        ) : (
          <div className="grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
            {runs.map((r) => (
              <ReconciliationCard key={r.id} run={r} />
            ))}
          </div>
        )}
      </Panel>

      <div className="grid min-w-0 gap-3">
        <h2 className="px-1 font-display text-[15px] font-bold">Message log</h2>
        <FilterBar
          facets={[{ key: "errors", label: "Problems", options: [{ value: "1", label: "Only problems" }] }]}
          placeholder="Bill ID, control number or reconciliation ID…"
        />
        <Surface>
          <DataTable
            columns={[
              { key: "when", label: "Time", className: "whitespace-nowrap", render: (m) => fmtDateTime(m.created_at) },
              {
                key: "dir",
                label: "",
                render: (m) => (
                  <span
                    className={clsx(
                      "rounded-md px-1.5 py-0.5 text-xs font-bold",
                      m.direction === "out" ? "bg-default text-muted" : "bg-brand-soft text-brand-ink",
                    )}
                  >
                    {m.direction === "out" ? "To GePG" : "From GePG"}
                  </span>
                ),
              },
              { key: "type", label: "Message", render: (m) => <MessageCard message={m} /> },
              {
                key: "ref",
                label: "Reference",
                render: (m) => <span className="font-mono text-xs">{m.reference ?? "—"}</span>,
              },
              {
                key: "code",
                label: "Status",
                render: (m) =>
                  m.error || m.signature_valid === false ? (
                    <span className="text-xs font-semibold text-danger">{m.error ?? "Bad signature"}</span>
                  ) : (
                    <span className="font-mono text-xs text-muted">{m.status_code ?? "—"}</span>
                  ),
              },
            ]}
            empty="No messages"
            emptyDescription="Messages appear here as bills are sent and GePG answers."
            minWidth={880}
            rows={messages.data}
          />
        </Surface>
        <Pagination page={messages.current_page} pages={messages.last_page} params={toParams(sp)} />
      </div>
    </div>
  );
}
