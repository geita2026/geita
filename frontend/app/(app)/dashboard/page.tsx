import type { Metadata } from "next";
import type { ExhibitorDashboard, StaffDashboard } from "@/lib/types";

import Link from "next/link";

import { api } from "@/lib/api";
import { can, requireSession } from "@/lib/auth/session";
import { currentExhibition } from "@/lib/exhibition";
import { fmtDate, fmtDateTime, pct, tzs, tzsCompact } from "@/lib/domain/format";
import { APPLICATION_STATUS, EXHIBITION_STATUS, INVOICE_STATUS, SPACE_STATUS } from "@/lib/domain/labels";
import { PageHeader } from "@/components/ui/page-header";
import { KpiRow } from "@/components/ui/kpi";
import { Panel } from "@/components/ui/panel";
import { HBars } from "@/components/charts/bars";
import { StatusChip } from "@/components/ui/chips";
import { EmptyState } from "@/components/ui/empty-state";

export const metadata: Metadata = { title: "Dashboard" };

export default async function DashboardPage() {
  const session = await requireSession();
  const exhibition = await currentExhibition();

  if (!exhibition) {
    return (
      <div className="grid gap-6">
        <PageHeader title="Dashboard" />
        <EmptyState
          action={
            can(session, "exhibitions.manage") ? (
              <Link className="button button--primary button--md" href="/exhibitions">
                Create the first exhibition
              </Link>
            ) : undefined
          }
          description="Nothing has been published yet. Check back soon."
          title="No exhibition to show"
        />
      </div>
    );
  }

  return session.user.role === "exhibitor" ? (
    <ExhibitorHome
      data={await api<ExhibitorDashboard>(`/exhibitions/${exhibition.id}/dashboard`)}
      name={session.user.name}
    />
  ) : (
    <StaffHome data={await api<StaffDashboard>(`/exhibitions/${exhibition.id}/dashboard`)} />
  );
}

/* ------------------------------------------------------------------ staff */

function StaffHome({ data }: { data: StaffDashboard }) {
  const k = data.kpis;
  const e = data.exhibition;
  const queue = [
    { n: k.pending_applications, label: "applications to review", href: "/applications?status=submitted" },
    { n: k.selections_to_confirm, label: "space selections to confirm", href: "/allocations?status=pending" },
    { n: k.payments_to_verify, label: "payments to verify", href: "/payments?status=pending" },
  ].filter((q) => q.n > 0);

  return (
    <div className="grid gap-6">
      <PageHeader
        actions={<StatusChip map={EXHIBITION_STATUS} size="md" value={e.status} />}
        description={`${e.venue} · ${fmtDate(e.start_date)} – ${fmtDate(e.end_date)}`}
        eyebrow={e.code}
        title={e.name}
      />

      {/* FR-055 */}
      <KpiRow
        items={[
          { label: "Total spaces", value: k.total_spaces, hint: `${k.allocation_rate}% allocated` },
          { label: "Available", value: k.available_spaces, tone: k.available_spaces ? "good" : "alert" },
          { label: "Allocated", value: k.allocated_spaces, hint: `${k.checked_in} checked in` },
          { label: "Occupancy rate", value: `${k.occupancy_rate}%`, hint: "spaces occupied on site" },
          { label: "Exhibitors", value: k.registered_exhibitors, hint: "with an application" },
          { label: "Approved", value: k.approved_applications, tone: "good" },
          {
            label: "Awaiting review",
            value: k.pending_applications,
            tone: k.pending_applications ? "alert" : "default",
          },
          { label: "Billed", value: tzsCompact(k.total_billed), hint: "TZS, incl. VAT" },
          {
            label: "Collected",
            value: tzsCompact(k.total_collected),
            hint: `${pct(k.total_collected, k.total_billed)}% of billed`,
            tone: "good",
          },
          { label: "Outstanding", value: tzsCompact(k.outstanding), tone: k.outstanding ? "alert" : "default" },
        ]}
      />

      {queue.length > 0 && (
        <div className="flex flex-wrap gap-3">
          {queue.map((q) => (
            <Link
              key={q.href}
              className="soft-shadow flex items-center gap-3 rounded-2xl border border-brand/40 bg-brand-soft/60 px-4 py-3 text-sm font-semibold transition-colors hover:bg-brand-soft"
              href={q.href}
            >
              <span className="grid size-8 place-items-center rounded-full bg-brand font-display font-extrabold text-brand-foreground tabular">
                {q.n}
              </span>
              {q.label}
            </Link>
          ))}
        </div>
      )}

      <div className="grid gap-4 xl:grid-cols-3">
        <Panel action={{ label: "Open map", href: "/map" }} meta={`${k.total_spaces} spaces`} title="Spaces by status">
          <HBars
            data={data.spaces_by_status
              .filter((s) => s.count > 0)
              .map((s) => ({
                key: s.status,
                label: SPACE_STATUS[s.status]?.label ?? s.status,
                value: s.count,
                color: SPACE_STATUS[s.status]?.fill,
              }))}
            empty="No spaces configured yet"
          />
        </Panel>
        <Panel action={{ label: "All applications", href: "/applications" }} title="Applications">
          <HBars
            data={data.applications_by_status
              .filter((s) => s.count > 0)
              .map((s) => ({ key: s.status, label: APPLICATION_STATUS[s.status]?.label ?? s.status, value: s.count }))}
            empty="No applications yet"
          />
        </Panel>
        <Panel meta="spaces taken" title="Halls and areas">
          <HBars
            data={data.by_hall.map((h) => ({
              key: h.code,
              label: h.name,
              value: h.held_count,
              display: `${h.held_count}/${h.spaces_count}`,
            }))}
            empty="No halls yet"
            max={Math.max(1, ...data.by_hall.map((h) => h.spaces_count))}
          />
        </Panel>
      </div>

      <div className="grid gap-4 xl:grid-cols-[1.2fr_1fr]">
        <Panel
          action={{ label: "Reports", href: "/reports?type=performance" }}
          meta="billed, excl. VAT"
          title="Revenue by space category"
        >
          <HBars
            data={data.by_category.map((c) => ({
              key: c.name,
              label: c.name,
              value: c.billed,
              display: `${tzsCompact(c.billed)} · ${c.held}/${c.spaces}`,
              color: c.color,
              title: `${c.held} of ${c.spaces} spaces taken at ${tzs(c.price_per_sqm)}/m²`,
            }))}
          />
        </Panel>
        <Panel
          action={data.recent_activity.length ? { label: "Audit trail", href: "/admin/audit" } : undefined}
          title="Recent activity"
        >
          {data.recent_activity.length === 0 ? (
            <p className="py-6 text-center text-sm text-muted">
              Activity is visible to administrators, management and auditors.
            </p>
          ) : (
            <ul className="grid gap-3">
              {data.recent_activity.map((a) => (
                <li key={a.id} className="grid gap-0.5 text-sm">
                  <span className="leading-snug">{a.summary}</span>
                  <span className="text-xs text-muted">
                    {a.user_name} · {fmtDateTime(a.created_at)}
                  </span>
                </li>
              ))}
            </ul>
          )}
        </Panel>
      </div>
    </div>
  );
}

/* -------------------------------------------------------------- exhibitor */

const STEPS = ["Apply", "Review", "Space", "Payment", "Confirmed"];

function ExhibitorHome({ data, name }: { data: ExhibitorDashboard; name: string }) {
  const { exhibition: e, application: a } = data;
  const held = a?.active_allocations ?? [];
  const reached = !a
    ? 0
    : a.status === "draft" || a.status === "returned"
      ? 1
      : a.status === "submitted"
        ? 2
        : a.status === "approved"
          ? 3
          : a.status === "pending_payment"
            ? 4
            : 5;

  const next = !a
    ? e.registration_open
      ? {
          text: "Registration is open. Apply for exhibition space to begin.",
          href: `/exhibitions/${e.id}`,
          cta: "Apply for space",
        }
      : { text: "Registration for this exhibition is not open.", href: "/exhibitions", cta: "See exhibitions" }
    : a.status === "draft" || a.status === "returned"
      ? {
          text: data.missing_documents.length
            ? `Upload ${data.missing_documents.join(" and ")}, then submit your application.`
            : a.status === "returned"
              ? "Your application was returned for correction. Fix it and submit again."
              : "Your application is a draft. Submit it for review.",
          href: `/applications/${a.id}`,
          cta: "Open application",
        }
      : a.status === "submitted"
        ? {
            text: "Your application is with the Registration Officer. You will be notified of the decision.",
            href: `/applications/${a.id}`,
            cta: "View application",
          }
        : a.status === "approved"
          ? held.length
            ? {
                text: "Your space selection is awaiting confirmation by the Space Allocation Officer.",
                href: `/applications/${a.id}`,
                cta: "View application",
              }
            : e.allow_self_selection
              ? {
                  text: `Approved. ${data.available_spaces} spaces are available — choose yours on the floor plan.`,
                  href: "/map",
                  cta: "Choose a space",
                }
              : {
                  text: "Approved. A Space Allocation Officer will allocate your space.",
                  href: `/applications/${a.id}`,
                  cta: "View application",
                }
          : a.status === "pending_payment"
            ? {
                text: `Pay ${tzs(data.billed - data.paid)} by ${fmtDate(e.payment_deadline)} to confirm your participation.`,
                href: "/invoices",
                cta: "Pay invoice",
              }
            : {
                text: "Participation confirmed. Download your allocation letter and bring it to check-in.",
                href: `/applications/${a.id}`,
                cta: "Allocation letter",
              };

  return (
    <div className="grid gap-6">
      <PageHeader
        actions={<StatusChip map={EXHIBITION_STATUS} size="md" value={e.status} />}
        description={`${e.name} · ${e.venue} · ${fmtDate(e.start_date)} – ${fmtDate(e.end_date)}`}
        title={`Karibu, ${name.split(" ")[0]}`}
      />

      <section className="soft-shadow grid gap-6 rounded-[20px] bg-surface p-6">
        {/* The FRD's process flow, as the exhibitor experiences it. */}
        <ul className="steps w-full text-xs font-semibold">
          {STEPS.map((s, i) => (
            <li key={s} className={`step ${i < reached ? "step-primary" : ""}`}>
              {s}
            </li>
          ))}
        </ul>
        <div className="flex flex-wrap items-center justify-between gap-4 rounded-2xl bg-brand-soft/60 p-5">
          <div className="min-w-0">
            <p className="text-xs font-bold uppercase tracking-[0.14em] text-muted">What happens next</p>
            <p className="mt-1 max-w-2xl font-display text-lg font-bold leading-snug">{next.text}</p>
          </div>
          <Link className="button button--primary button--md" href={next.href}>
            {next.cta}
          </Link>
        </div>
      </section>

      <div className="grid gap-4 lg:grid-cols-3">
        <Panel action={a ? { label: "Open", href: `/applications/${a.id}` } : undefined} title="Application">
          {a ? (
            <div className="grid gap-2 text-sm">
              <span className="font-mono text-xs text-muted">{a.reference}</span>
              <StatusChip map={APPLICATION_STATUS} value={a.status} />
              <span className="text-muted">
                {a.spaces_requested} space(s) · {a.category?.name ?? "any category"}
              </span>
            </div>
          ) : (
            <p className="py-4 text-sm text-muted">You have not applied for this exhibition yet.</p>
          )}
        </Panel>
        <Panel action={{ label: "Floor plan", href: "/map" }} title="Your space">
          {held.length ? (
            <ul className="grid gap-2">
              {held.map((h) => (
                <li key={h.id} className="flex items-center justify-between gap-3 text-sm">
                  <span>
                    <span className="font-display text-xl font-extrabold">{h.space?.number}</span>
                    <span className="ms-2 text-muted">{h.space?.hall?.name}</span>
                  </span>
                  <span className="text-xs text-muted">{h.space?.size_label}</span>
                </li>
              ))}
            </ul>
          ) : (
            <p className="py-4 text-sm text-muted">No space allocated yet. {data.available_spaces} available.</p>
          )}
        </Panel>
        <Panel action={{ label: "Invoices", href: "/invoices" }} title="Payments">
          {data.invoices.length ? (
            <div className="grid gap-2 text-sm">
              <p className="font-display text-2xl font-extrabold tabular">{tzs(data.billed - data.paid)}</p>
              <p className="text-xs text-muted">
                outstanding of {tzs(data.billed)} · due {fmtDate(e.payment_deadline)}
              </p>
              <div className="flex flex-wrap gap-1.5">
                {data.invoices.map((i) => (
                  <StatusChip key={i.id} map={INVOICE_STATUS} value={i.status} />
                ))}
              </div>
            </div>
          ) : (
            <p className="py-4 text-sm text-muted">An invoice is issued when your space is allocated.</p>
          )}
        </Panel>
      </div>
    </div>
  );
}
