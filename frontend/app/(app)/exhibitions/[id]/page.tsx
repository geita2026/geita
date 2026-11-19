import type { Metadata } from "next";
import type { Application, Exhibition, Paginated } from "@/lib/types";

import Link from "next/link";
import { Pencil, Plus } from "@gravity-ui/icons";

import { api } from "@/lib/api";
import { can, requireSession } from "@/lib/auth/session";
import { fmtDate, tzs } from "@/lib/domain/format";
import { EXHIBITION_STATUS, humanize } from "@/lib/domain/labels";
import { PageHeader } from "@/components/ui/page-header";
import { Panel } from "@/components/ui/panel";
import { CategoryChip, Fact, StatusChip } from "@/components/ui/chips";
import { MorphForm } from "@/components/ui/morph-form";
import { ActionButton } from "@/components/ui/action-button";
import { AreaF, SelectF, TextF } from "@/components/ui/action-form";
import { ExhibitionFields, exhibitionSpecExtras } from "@/components/exhibitions/exhibition-fields";
import { ApplicantFields } from "@/components/exhibitors/applicant-fields";
import { listCountries } from "@/lib/countries";

export const metadata: Metadata = { title: "Exhibition" };

const HALL_KINDS = [
  { id: "indoor", label: "Indoor hall" },
  { id: "outdoor", label: "Outdoor area" },
  { id: "pavilion", label: "Pavilion" },
];

/** FR-003/004: the moves the life cycle allows from each status. */
const MOVES: Record<string, { to: string; label: string; variant?: "primary" | "secondary" | "danger" }[]> = {
  draft: [{ to: "published", label: "Publish for registration" }],
  published: [
    { to: "registration_closed", label: "Close registration", variant: "secondary" },
    { to: "ongoing", label: "Mark as ongoing", variant: "secondary" },
  ],
  registration_closed: [
    { to: "published", label: "Reopen registration", variant: "secondary" },
    { to: "ongoing", label: "Mark as ongoing" },
  ],
  ongoing: [{ to: "completed", label: "Mark as completed" }],
};

export default async function ExhibitionPage({ params }: { params: Promise<{ id: string }> }) {
  const { id } = await params;
  const session = await requireSession();
  const e = await api<Exhibition>(`/exhibitions/${id}`);
  const manage = can(session, "exhibitions.manage");
  const layout = can(session, "spaces.manage");
  const isExhibitor = session.user.role === "exhibitor";
  const editable = manage && !["completed", "cancelled"].includes(e.status);
  const countries = isExhibitor ? await listCountries() : [];
  const mine = isExhibitor
    ? (await api<Paginated<Application>>("/applications", { query: { exhibition_id: e.id } })).data.find(
        (a) => !["cancelled", "rejected"].includes(a.status),
      )
    : undefined;

  return (
    <div className="grid gap-6">
      <PageHeader
        actions={
          <>
            <StatusChip map={EXHIBITION_STATUS} size="md" value={e.status} />
            {editable && (
              <MorphForm
                icon={<Pencil className="size-4" />}
                label="Edit"
                spec={{
                  method: "PUT",
                  path: `/exhibitions/${e.id}`,
                  success: "Exhibition updated",
                  ...exhibitionSpecExtras,
                }}
                title="Edit exhibition"
                variant="secondary"
                width="max-w-3xl"
              >
                <ExhibitionFields e={e} />
              </MorphForm>
            )}
            {manage &&
              (MOVES[e.status] ?? []).map((m) => (
                <ActionButton
                  key={m.to}
                  spec={{
                    method: "POST",
                    path: `/exhibitions/${e.id}/transition`,
                    extra: { status: m.to },
                    success: `Exhibition is now ${humanize(m.to).toLowerCase()}`,
                  }}
                  variant={m.variant ?? "primary"}
                >
                  {m.label}
                </ActionButton>
              ))}
            {isExhibitor &&
              (mine ? (
                <Link className="button button--primary button--md" href={`/applications/${mine.id}`}>
                  Open my application
                </Link>
              ) : (
                e.registration_open && (
                  <MorphForm
                    icon={<Plus className="size-4" />}
                    label="Apply for space"
                    pendingLabel="Saving…"
                    spec={{
                      method: "POST",
                      path: "/applications",
                      extra: { exhibition_id: e.id },
                      redirectTo: "/applications/:id",
                    }}
                    submitLabel="Save and continue"
                    subtitle="Saved as a draft. You attach the required documents and submit on the next page."
                    width="max-w-2xl"
                    title={`Apply — ${e.name}`}
                  >
                    <div className="grid gap-6">
                      <section className="grid gap-3">
                        <h3 className="border-b border-separator pb-2 text-sm font-semibold">About you</h3>
                        <ApplicantFields
                          countries={countries}
                          exhibitor={session.user.exhibitor}
                          personName={session.user.name}
                        />
                      </section>
                      <section className="grid gap-3">
                        <h3 className="border-b border-separator pb-2 text-sm font-semibold">Your application</h3>
                        <ApplicationFields e={e} />
                      </section>
                    </div>
                  </MorphForm>
                )
              ))}
          </>
        }
        description={e.theme ?? undefined}
        eyebrow={e.code}
        title={e.name}
      />

      <div className="grid gap-4 xl:grid-cols-[1.4fr_1fr]">
        <Panel title="Details">
          <dl className="grid grid-cols-2 gap-x-6 gap-y-4 md:grid-cols-3">
            <Fact className="col-span-2 md:col-span-3" label="Venue">
              {e.venue}
            </Fact>
            <Fact label="Exhibition dates">
              {fmtDate(e.start_date)} – {fmtDate(e.end_date)}
            </Fact>
            <Fact label="Registration">
              {fmtDate(e.registration_opens_on)} – {fmtDate(e.registration_closes_on)}
            </Fact>
            <Fact label="Payment deadline">{fmtDate(e.payment_deadline)}</Fact>
            <Fact label="Spaces">{e.spaces_count}</Fact>
            <Fact label="Applications">{e.applications_count}</Fact>
            <Fact label="VAT">{e.vat_rate}%</Fact>
            <Fact label="Space selection">
              {e.allow_self_selection ? "By exhibitors, confirmed by an officer" : "By officers only"}
            </Fact>
            <Fact className="col-span-2" label="Contact">
              {[e.contact_email, e.contact_phone].filter(Boolean).join(" · ") || "—"}
            </Fact>
          </dl>
          {e.objectives && (
            <div>
              <p className="text-xs font-semibold uppercase tracking-wider text-muted">Objectives</p>
              <p className="mt-1 text-sm whitespace-pre-line">{e.objectives}</p>
            </div>
          )}
        </Panel>
        <div className="grid content-start gap-4">
          <Panel title="Mandatory documents">
            <div className="grid gap-4 sm:grid-cols-2">
              <DocList docs={e.required_documents} heading="Registered businesses" />
              <DocList docs={e.required_documents_individual} heading="Individuals" />
            </div>
          </Panel>
          <Panel title="Exhibition categories">
            <div className="flex flex-wrap gap-1.5">
              {(e.categories ?? []).map((c) => (
                <span key={c} className="rounded-full bg-default px-2.5 py-1 text-xs font-semibold">
                  {c}
                </span>
              ))}
              {(e.categories?.length ?? 0) === 0 && <p className="text-sm text-muted">Not set.</p>}
            </div>
          </Panel>
        </div>
      </div>

      <div className="grid gap-4 xl:grid-cols-2">
        <Panel action={{ label: "Floor plan", href: "/map" }} meta={`${e.halls?.length ?? 0}`} title="Halls and areas">
          <ul className="grid gap-2">
            {(e.halls ?? []).map((h) => (
              <li
                key={h.id}
                className="flex items-center justify-between gap-3 rounded-xl bg-surface-secondary px-3 py-2.5 text-sm"
              >
                <span className="min-w-0">
                  <span className="font-mono text-xs text-muted">{h.code}</span>{" "}
                  <span className="font-semibold">{h.name}</span>
                  <span className="block text-xs text-muted">
                    {humanize(h.kind)} · {h.spaces_count} spaces · {h.grid_cols} columns
                  </span>
                </span>
                {layout && (
                  <span className="flex shrink-0 gap-1.5">
                    <MorphForm
                      label="Edit"
                      size="sm"
                      spec={{ method: "PUT", path: `/halls/${h.id}`, success: "Hall updated" }}
                      title={`Edit ${h.name}`}
                      variant="ghost"
                    >
                      <HallFields h={h} />
                    </MorphForm>
                    {h.spaces_count === 0 && (
                      <ActionButton size="sm" spec={{ method: "DELETE", path: `/halls/${h.id}` }} variant="ghost">
                        Delete
                      </ActionButton>
                    )}
                  </span>
                )}
              </li>
            ))}
            {(e.halls?.length ?? 0) === 0 && (
              <p className="py-4 text-center text-sm text-muted">No halls yet. Add one to start laying out spaces.</p>
            )}
          </ul>
          {layout && (
            <div>
              <MorphForm
                icon={<Plus className="size-4" />}
                label="Add hall or area"
                size="sm"
                spec={{ method: "POST", path: `/exhibitions/${e.id}/halls`, success: "Hall added" }}
                title="Add hall or area"
                variant="secondary"
              >
                <HallFields />
              </MorphForm>
            </div>
          )}
        </Panel>

        <Panel meta="FR-006 · FR-008" title="Space categories and prices">
          <ul className="grid gap-2">
            {(e.space_categories ?? []).map((c) => (
              <li
                key={c.id}
                className="flex items-center justify-between gap-3 rounded-xl bg-surface-secondary px-3 py-2.5 text-sm"
              >
                <span className="min-w-0">
                  <CategoryChip color={c.color} name={c.name} />
                  <span className="mt-1 block text-xs text-muted">
                    {tzs(c.price_per_sqm)} per m² · {c.spaces_count} spaces ·{" "}
                    {c.gfs_code ? (
                      <span className="font-mono">GFS {c.gfs_code}</span>
                    ) : (
                      <span className="text-warning">no GFS code</span>
                    )}
                  </span>
                </span>
                {layout && (
                  <span className="flex shrink-0 gap-1.5">
                    <MorphForm
                      label="Edit"
                      size="sm"
                      spec={{
                        method: "PUT",
                        path: `/categories/${c.id}`,
                        extra: { reprice: true },
                        success: "Category updated",
                      }}
                      subtitle="A new rate re-prices the spaces of this category that nobody holds yet."
                      title={`Edit ${c.name}`}
                      variant="ghost"
                    >
                      <CategoryFields c={c} />
                    </MorphForm>
                    {c.spaces_count === 0 && (
                      <ActionButton size="sm" spec={{ method: "DELETE", path: `/categories/${c.id}` }} variant="ghost">
                        Delete
                      </ActionButton>
                    )}
                  </span>
                )}
              </li>
            ))}
            {(e.space_categories?.length ?? 0) === 0 && (
              <p className="py-4 text-center text-sm text-muted">No categories yet.</p>
            )}
          </ul>
          {layout && (
            <div>
              <MorphForm
                icon={<Plus className="size-4" />}
                label="Add category"
                size="sm"
                spec={{ method: "POST", path: `/exhibitions/${e.id}/categories`, success: "Category added" }}
                title="Add space category"
                variant="secondary"
              >
                <CategoryFields />
              </MorphForm>
            </div>
          )}
        </Panel>
      </div>

      {e.terms && (
        <Panel title="Terms and conditions">
          <p className="text-sm leading-relaxed whitespace-pre-line text-muted">{e.terms}</p>
        </Panel>
      )}
    </div>
  );
}

function HallFields({
  h,
}: {
  h?: { code: string; name: string; kind: string; description: string | null; grid_cols: number };
}) {
  return (
    <div className="grid gap-4 sm:grid-cols-2">
      <TextF isRequired mono defaultValue={h?.code} label="Code" name="code" placeholder="A" />
      <TextF isRequired defaultValue={h?.name} label="Name" name="name" placeholder="Main Pavilion" />
      <SelectF isRequired defaultValue={h?.kind ?? "indoor"} label="Kind" name="kind" options={HALL_KINDS} />
      <TextF
        defaultValue={h?.grid_cols ?? 10}
        description="Spaces per row on the floor plan."
        label="Map columns"
        max={40}
        min={2}
        name="grid_cols"
        type="number"
      />
      <TextF className="sm:col-span-2" defaultValue={h?.description ?? ""} label="Description" name="description" />
    </div>
  );
}

function CategoryFields({
  c,
}: {
  c?: {
    code: string;
    name: string;
    description: string | null;
    price_per_sqm: number;
    color: string;
    gfs_code: string | null;
  };
}) {
  return (
    <div className="grid gap-4 sm:grid-cols-2">
      <TextF isRequired mono defaultValue={c?.code} label="Code" name="code" placeholder="STD" />
      <TextF isRequired defaultValue={c?.name} label="Name" name="name" placeholder="Standard booth" />
      <TextF
        isRequired
        defaultValue={c?.price_per_sqm}
        label="Price per m² (TZS)"
        min={0}
        name="price_per_sqm"
        type="number"
      />
      <label className="grid gap-1.5 text-sm font-medium">
        Map colour
        <input
          className="h-10 w-full rounded-xl border border-separator bg-field-background p-1"
          defaultValue={c?.color ?? "#3b82f6"}
          name="color"
          type="color"
        />
      </label>
      <TextF
        mono
        className="sm:col-span-2"
        defaultValue={c?.gfs_code ?? ""}
        description="The government revenue code GePG books this category's fees under. Ask the Finance Unit."
        label="GFS code"
        name="gfs_code"
        placeholder="e.g. 140301"
      />
      <TextF className="sm:col-span-2" defaultValue={c?.description ?? ""} label="Description" name="description" />
    </div>
  );
}

/** FR-014 */
function ApplicationFields({ e }: { e: Exhibition }) {
  return (
    <div className="grid gap-4 sm:grid-cols-2">
      <SelectF
        description="The price per m² depends on the category."
        label="Preferred space category"
        name="space_category_id"
        options={(e.space_categories ?? []).map((c) => ({
          id: String(c.id),
          label: `${c.name} — ${tzs(c.price_per_sqm)}/m²`,
        }))}
      />
      <SelectF
        defaultValue="3m × 3m"
        label="Preferred size"
        name="preferred_size"
        options={["3m × 3m", "3m × 6m", "6m × 6m", "6m × 12m", "Customized"].map((s) => ({ id: s, label: s }))}
      />
      <TextF
        isRequired
        defaultValue={1}
        label="Number of spaces"
        max={20}
        min={1}
        name="spaces_requested"
        type="number"
      />
      <SelectF
        isRequired
        label="Product / service category"
        name="product_category"
        options={(e.categories ?? ["General"]).map((c) => ({ id: c, label: c }))}
      />
      <AreaF
        className="sm:col-span-2"
        label="Products or services you will exhibit"
        name="products_description"
        rows={3}
      />
      <AreaF
        className="sm:col-span-2"
        label="Special requirements"
        name="special_requirements"
        placeholder="Power, access, water, security…"
        rows={2}
      />
    </div>
  );
}

/** BR-003: one list per kind of applicant. */
function DocList({ heading, docs }: { heading: string; docs: string[] | null }) {
  return (
    <div>
      <p className="mb-1.5 text-xs font-semibold uppercase tracking-wider text-muted">{heading}</p>
      {(docs?.length ?? 0) === 0 ? (
        <p className="text-sm text-muted">None required.</p>
      ) : (
        <ul className="grid gap-1.5 text-sm">
          {docs!.map((d) => (
            <li key={d} className="flex items-center gap-2">
              <span aria-hidden className="size-1.5 rounded-full bg-brand" /> {d}
            </li>
          ))}
        </ul>
      )}
    </div>
  );
}
