import type { Metadata } from "next";
import type { Application, Paginated, Space } from "@/lib/types";

import Link from "next/link";
import { Paperclip, Plus } from "@gravity-ui/icons";

import { api } from "@/lib/api";
import { can, requireSession } from "@/lib/auth/session";
import { fmtDate, fmtDateTime, tzs } from "@/lib/domain/format";
import { ALLOCATION_STATUS, APPLICATION_STATUS, INVOICE_STATUS, OCCUPANCY_STATUS } from "@/lib/domain/labels";
import { PageHeader } from "@/components/ui/page-header";
import { Panel } from "@/components/ui/panel";
import { CategoryChip, Fact, StatusChip } from "@/components/ui/chips";
import { MorphForm } from "@/components/ui/morph-form";
import { ActionButton } from "@/components/ui/action-button";
import { DownloadLink } from "@/components/ui/download-link";
import { AreaF, CheckF, FileF, SelectF, TextF } from "@/components/ui/action-form";

export const metadata: Metadata = { title: "Application" };

export default async function ApplicationPage({ params }: { params: Promise<{ id: string }> }) {
  const { id } = await params;
  const session = await requireSession();
  const a = await api<Application>(`/applications/${id}`);
  const e = a.exhibition!;
  const isOwner = session.user.exhibitor_id === a.exhibitor_id;
  const review = can(session, "applications.review");
  const allocate = can(session, "allocations.manage");
  const authorize = can(session, "allocations.authorize");
  const open = a.status === "draft" || a.status === "returned";
  const live = (a.allocations ?? []).filter((x) => x.status === "pending" || x.status === "allocated");
  const needsSpace = live.length < a.spaces_requested;
  const allocatable =
    ["approved", "pending_payment", "confirmed"].includes(a.status) || (a.status === "submitted" && authorize);

  const [free, others] = await Promise.all([
    allocate ? api<Space[]>(`/exhibitions/${e.id}/spaces`, { query: { status: "available,reserved" } }) : [],
    allocate && authorize && live.length
      ? api<Paginated<Application>>("/applications", {
          query: { exhibition_id: e.id, status: "approved,pending_payment,confirmed", per_page: 100 },
        })
      : null,
  ]);
  const spaceOptions = free.map((s) => ({
    id: String(s.id),
    label: `${s.number} — ${s.hall?.name} · ${s.category?.name} · ${s.size_label} · ${tzs(s.price)}`,
  }));
  const recipients = (others?.data ?? [])
    .filter((o) => o.exhibitor_id !== a.exhibitor_id)
    .map((o) => ({ id: String(o.id), label: `${o.exhibitor?.organization_name} — ${o.reference}` }));
  const docTypes = [...new Set([...(e.required_documents ?? []), "Company profile", "Product catalogue", "Other"])].map(
    (t) => ({ id: t, label: t }),
  );

  return (
    <div className="grid gap-6">
      <PageHeader
        actions={
          <>
            <StatusChip map={APPLICATION_STATUS} size="md" value={a.status} />
            {a.status === "confirmed" && (
              <DownloadLink path={`applications/${a.id}/allocation-letter`}>Allocation letter</DownloadLink>
            )}
            {open && (isOwner || can(session, "exhibitors.manage")) && (
              <ActionButton
                pendingLabel="Submitting…"
                spec={{ method: "POST", path: `/applications/${a.id}/submit`, success: "Application submitted" }}
                successLabel="Submitted"
              >
                Submit application
              </ActionButton>
            )}
            {review && a.status === "submitted" && (
              <>
                <ActionButton
                  pendingLabel="Approving…"
                  spec={{
                    method: "POST",
                    path: `/applications/${a.id}/review`,
                    extra: { decision: "approve" },
                    success: "Application approved",
                  }}
                  successLabel="Approved"
                >
                  Approve
                </ActionButton>
                <MorphForm
                  label="Return"
                  spec={{
                    method: "POST",
                    path: `/applications/${a.id}/review`,
                    extra: { decision: "return" },
                    success: "Returned to the exhibitor",
                  }}
                  submitLabel="Return for correction"
                  subtitle="The exhibitor is told what to fix and can submit again."
                  title="Return for correction"
                  variant="secondary"
                >
                  <AreaF isRequired label="What needs correcting" name="notes" rows={4} />
                </MorphForm>
                <MorphForm
                  label="Reject"
                  spec={{
                    method: "POST",
                    path: `/applications/${a.id}/review`,
                    extra: { decision: "reject" },
                    success: "Application rejected",
                  }}
                  submitLabel="Reject application"
                  subtitle="The exhibitor is notified with this reason."
                  title="Reject application"
                  variant="danger"
                >
                  <AreaF isRequired label="Reason for rejection" name="notes" rows={4} />
                </MorphForm>
              </>
            )}
          </>
        }
        description={`${e.name} · ${a.submitted_at ? `submitted ${fmtDateTime(a.submitted_at)}` : "not submitted yet"}`}
        eyebrow={a.reference}
        title={a.exhibitor!.organization_name}
      />

      {a.review_notes && ["returned", "rejected", "cancelled"].includes(a.status) && (
        <p className="rounded-2xl bg-warning-soft px-4 py-3 text-sm text-warning" role="status">
          <strong>{APPLICATION_STATUS[a.status].label}:</strong> {a.review_notes}
        </p>
      )}
      {(a.missing_documents?.length ?? 0) > 0 && !["rejected", "cancelled"].includes(a.status) && (
        <p className="rounded-2xl bg-danger-soft px-4 py-3 text-sm text-danger" role="status">
          Mandatory documents missing: <strong>{a.missing_documents!.join(", ")}</strong>. The application cannot be
          submitted or confirmed without them.
        </p>
      )}

      <div className="grid gap-4 xl:grid-cols-[1.3fr_1fr]">
        <div className="grid content-start gap-4">
          <Panel title="Application">
            <dl className="grid grid-cols-2 gap-x-6 gap-y-4 md:grid-cols-3">
              <Fact label="Preferred category">
                {a.category ? <CategoryChip color={a.category.color} name={a.category.name} /> : "Any"}
              </Fact>
              <Fact label="Preferred size">{a.preferred_size}</Fact>
              <Fact label="Spaces requested">{a.spaces_requested}</Fact>
              <Fact label="Product category">{a.product_category}</Fact>
              <Fact label="Reviewed by">{a.reviewer ? `${a.reviewer.name}, ${fmtDate(a.reviewed_at)}` : "—"}</Fact>
              <Fact label="Confirmed">{fmtDate(a.confirmed_at)}</Fact>
              <Fact className="col-span-2 md:col-span-3" label="Products / services">
                {a.products_description}
              </Fact>
              <Fact className="col-span-2 md:col-span-3" label="Special requirements">
                {a.special_requirements}
              </Fact>
            </dl>
            <div className="flex flex-wrap gap-2">
              {open && (
                <MorphForm
                  label="Edit application"
                  size="sm"
                  spec={{ method: "PUT", path: `/applications/${a.id}`, success: "Application updated" }}
                  title="Edit application"
                  variant="secondary"
                >
                  <div className="grid gap-4 sm:grid-cols-2">
                    <SelectF
                      defaultValue={a.space_category_id ? String(a.space_category_id) : undefined}
                      label="Preferred category"
                      name="space_category_id"
                      options={(e.space_categories ?? []).map((c) => ({ id: String(c.id), label: c.name }))}
                    />
                    <TextF defaultValue={a.preferred_size ?? ""} label="Preferred size" name="preferred_size" />
                    <TextF
                      isRequired
                      defaultValue={a.spaces_requested}
                      label="Number of spaces"
                      max={20}
                      min={1}
                      name="spaces_requested"
                      type="number"
                    />
                    <TextF
                      isRequired
                      defaultValue={a.product_category ?? ""}
                      label="Product category"
                      name="product_category"
                    />
                    <AreaF
                      className="sm:col-span-2"
                      defaultValue={a.products_description ?? ""}
                      label="Products / services"
                      name="products_description"
                      rows={3}
                    />
                    <AreaF
                      className="sm:col-span-2"
                      defaultValue={a.special_requirements ?? ""}
                      label="Special requirements"
                      name="special_requirements"
                      rows={2}
                    />
                  </div>
                </MorphForm>
              )}
              {!["cancelled", "rejected"].includes(a.status) && live.length === 0 && (isOwner || review) && (
                <MorphForm
                  label="Cancel application"
                  size="sm"
                  spec={{ method: "POST", path: `/applications/${a.id}/cancel`, success: "Application cancelled" }}
                  submitLabel="Cancel application"
                  title="Cancel application"
                  variant="ghost"
                  formVariant="danger"
                >
                  <AreaF isRequired label="Reason" name="reason" rows={3} />
                </MorphForm>
              )}
            </div>
          </Panel>

          {/* FR-022..028 */}
          <Panel meta={`${live.length} of ${a.spaces_requested}`} title="Space">
            {(a.allocations ?? []).length === 0 && (
              <p className="text-sm text-muted">
                {a.status === "approved" ? "No space yet." : "Space is allocated after the application is approved."}
              </p>
            )}
            <ul className="grid gap-2">
              {(a.allocations ?? []).map((x) => {
                const on = x.status === "pending" || x.status === "allocated";

                return (
                  <li
                    key={x.id}
                    className={`grid gap-3 rounded-xl bg-surface-secondary p-3.5 ${on ? "" : "opacity-60"}`}
                  >
                    <div className="flex flex-wrap items-center justify-between gap-2">
                      <span className="flex items-center gap-3">
                        <span className="font-display text-2xl font-extrabold">{x.space?.number}</span>
                        <span className="text-sm">
                          {x.space?.hall?.name}
                          <span className="block text-xs text-muted">
                            {x.space?.size_label} · {tzs(x.space?.price)} · {x.reference}
                          </span>
                        </span>
                      </span>
                      <span className="flex flex-wrap gap-1.5">
                        <StatusChip map={ALLOCATION_STATUS} value={x.status} />
                        {x.status === "allocated" && <StatusChip map={OCCUPANCY_STATUS} value={x.occupancy_status} />}
                      </span>
                    </div>
                    {x.change_reason && <p className="text-xs text-muted">Reason: {x.change_reason}</p>}
                    {on && (
                      <div className="flex flex-wrap gap-2">
                        {allocate && x.status === "pending" && (
                          <ActionButton
                            pendingLabel="Confirming…"
                            size="sm"
                            spec={{
                              method: "POST",
                              path: `/allocations/${x.id}/confirm`,
                              success: "Selection confirmed and invoiced",
                            }}
                            successLabel="Confirmed"
                          >
                            Confirm selection
                          </ActionButton>
                        )}
                        {allocate && x.status === "allocated" && (
                          <MorphForm
                            label="Reallocate"
                            size="sm"
                            spec={{
                              method: "POST",
                              path: `/allocations/${x.id}/reallocate`,
                              success: "Space reallocated",
                            }}
                            submitLabel="Reallocate"
                            subtitle={`Move ${a.exhibitor!.organization_name} from ${x.space?.number} to another space. The invoice follows.`}
                            title="Reallocate space"
                            variant="secondary"
                          >
                            <SelectF isRequired label="New space" name="space_id" options={spaceOptions} />
                            <AreaF isRequired label="Reason for reallocation" name="reason" rows={3} />
                          </MorphForm>
                        )}
                        {allocate && authorize && x.status === "allocated" && recipients.length > 0 && (
                          <MorphForm
                            label="Transfer"
                            size="sm"
                            spec={{
                              method: "POST",
                              path: `/allocations/${x.id}/transfer`,
                              success: "Space transferred",
                            }}
                            submitLabel="Transfer space"
                            subtitle={`Hand space ${x.space?.number} to another exhibitor's approved application.`}
                            title="Transfer space"
                            variant="secondary"
                          >
                            <SelectF
                              isRequired
                              label="Receiving application"
                              name="application_id"
                              options={recipients}
                            />
                            <AreaF isRequired label="Reason for transfer" name="reason" rows={3} />
                          </MorphForm>
                        )}
                        {(allocate || (isOwner && x.status === "pending")) && (
                          <MorphForm
                            label={x.status === "pending" ? "Withdraw selection" : "Cancel allocation"}
                            size="sm"
                            spec={{
                              method: "POST",
                              path: `/allocations/${x.id}/cancel`,
                              success: "Allocation cancelled",
                            }}
                            submitLabel="Cancel allocation"
                            subtitle={`Space ${x.space?.number} becomes available again and comes off the invoice.`}
                            title="Cancel allocation"
                            variant="danger"
                          >
                            <AreaF isRequired label="Reason for cancellation" name="reason" rows={3} />
                          </MorphForm>
                        )}
                      </div>
                    )}
                  </li>
                );
              })}
            </ul>
            {allocate && allocatable && needsSpace && (
              <div className="flex flex-wrap gap-2 border-t border-separator pt-4">
                <MorphForm
                  icon={<Plus className="size-4" />}
                  label="Allocate a space"
                  size="sm"
                  spec={{
                    method: "POST",
                    path: "/allocations",
                    extra: { application_id: a.id },
                    bools: ["exception"],
                    success: "Space allocated and invoiced",
                  }}
                  submitLabel="Allocate"
                  title="Allocate a space"
                >
                  <SelectF isRequired label="Available space" name="space_id" options={spaceOptions} />
                  {a.status === "submitted" && (
                    <CheckF
                      description="BR-002: allocating before approval needs an Exhibition Administrator."
                      label="Authorise as an exception"
                      name="exception"
                    />
                  )}
                </MorphForm>
                {a.status !== "submitted" && (
                  <ActionButton
                    pendingLabel="Finding a space…"
                    size="sm"
                    spec={{
                      method: "POST",
                      path: `/applications/${a.id}/auto-allocate`,
                      success: "Space allocated automatically",
                    }}
                    variant="secondary"
                  >
                    Auto-allocate
                  </ActionButton>
                )}
              </div>
            )}
            {isOwner && a.status === "approved" && needsSpace && e.allow_self_selection && (
              <div>
                <Link className="button button--primary button--md" href="/map">
                  Choose a space on the floor plan
                </Link>
              </div>
            )}
          </Panel>
        </div>

        <div className="grid content-start gap-4">
          <Panel meta={`${a.documents?.length ?? 0}`} title="Supporting documents">
            <ul className="grid gap-2">
              {(a.documents ?? []).map((d) => (
                <li key={d.id} className="flex items-center justify-between gap-3 text-sm">
                  <a
                    className="flex min-w-0 items-center gap-2 hover:underline"
                    href={`/api/files/documents/${d.id}`}
                    rel="noopener"
                    target="_blank"
                  >
                    <Paperclip className="size-4 shrink-0 text-muted" />
                    <span className="min-w-0">
                      <span className="block truncate font-semibold">{d.type}</span>
                      <span className="block truncate text-xs text-muted">
                        {d.original_name} · {Math.max(1, Math.round(d.size / 1024))} KB
                      </span>
                    </span>
                  </a>
                  {open && isOwner && (
                    <ActionButton size="sm" spec={{ method: "DELETE", path: `/documents/${d.id}` }} variant="ghost">
                      Remove
                    </ActionButton>
                  )}
                </li>
              ))}
              {(a.documents?.length ?? 0) === 0 && <p className="text-sm text-muted">Nothing uploaded yet.</p>}
            </ul>
            {!["rejected", "cancelled"].includes(a.status) && (isOwner || can(session, "exhibitors.manage")) && (
              <div>
                <MorphForm
                  icon={<Plus className="size-4" />}
                  label="Upload document"
                  size="sm"
                  spec={{
                    method: "POST",
                    path: `/applications/${a.id}/documents`,
                    multipart: true,
                    success: "Document uploaded",
                  }}
                  submitLabel="Upload"
                  title="Upload a document"
                  variant="secondary"
                >
                  <SelectF
                    isRequired
                    defaultValue={a.missing_documents?.[0]}
                    label="Document type"
                    name="type"
                    options={docTypes}
                  />
                  <FileF
                    isRequired
                    accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"
                    description="PDF, image or Word, up to 10 MB."
                    label="File"
                    name="file"
                  />
                </MorphForm>
              </div>
            )}
          </Panel>

          <Panel title="Invoices">
            <ul className="grid gap-2">
              {(a.invoices ?? []).map((i) => (
                <li key={i.id}>
                  <Link
                    className="flex items-center justify-between gap-3 rounded-xl bg-surface-secondary px-3 py-2.5 text-sm hover:bg-default"
                    href={`/invoices/${i.id}`}
                  >
                    <span>
                      <span className="font-mono text-xs font-semibold">{i.number}</span>
                      <span className="block text-xs text-muted">
                        {tzs(i.total)} · due {fmtDate(i.due_on)}
                      </span>
                    </span>
                    <StatusChip map={INVOICE_STATUS} value={i.status} />
                  </Link>
                </li>
              ))}
              {(a.invoices?.length ?? 0) === 0 && (
                <p className="text-sm text-muted">An invoice is generated when space is allocated.</p>
              )}
            </ul>
          </Panel>

          <Panel
            action={
              can(session, "exhibitors.view") ? { label: "Profile", href: `/exhibitors/${a.exhibitor_id}` } : undefined
            }
            title="Exhibitor"
          >
            <dl className="grid grid-cols-2 gap-x-6 gap-y-3">
              <Fact label="Contact">{a.exhibitor!.contact_person}</Fact>
              <Fact label="Phone">{a.exhibitor!.phone}</Fact>
              <Fact className="col-span-2" label="Email">
                {a.exhibitor!.email}
              </Fact>
              <Fact label="Country">{a.exhibitor!.country}</Fact>
              <Fact label="TIN">{a.exhibitor!.tin}</Fact>
            </dl>
          </Panel>
        </div>
      </div>
    </div>
  );
}
