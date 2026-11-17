import type { Metadata } from "next";
import type { Exhibition, Space } from "@/lib/types";

import Link from "next/link";
import { Plus } from "@gravity-ui/icons";

import { api } from "@/lib/api";
import { can, requirePermission } from "@/lib/auth/session";
import { currentExhibition } from "@/lib/exhibition";
import { tzs } from "@/lib/domain/format";
import { SPACE_STATUS } from "@/lib/domain/labels";
import { PageHeader } from "@/components/ui/page-header";
import { EmptyState } from "@/components/ui/empty-state";
import { FilterBar } from "@/components/ui/filter-bar";
import { DataTable, Surface } from "@/components/ui/table";
import { CategoryChip, StatusChip } from "@/components/ui/chips";
import { MorphForm } from "@/components/ui/morph-form";
import { ActionButton } from "@/components/ui/action-button";
import { AreaF, SelectF, TextF } from "@/components/ui/action-form";

export const metadata: Metadata = { title: "Spaces" };

export default async function SpacesPage({
  searchParams,
}: {
  searchParams: Promise<Record<string, string | undefined>>;
}) {
  const sp = await searchParams;
  const session = await requirePermission("staff");
  const current = await currentExhibition();

  if (!current) return <EmptyState description="Create an exhibition first." title="No exhibition" />;

  const [e, all] = await Promise.all([
    api<Exhibition>(`/exhibitions/${current.id}`),
    api<Space[]>(`/exhibitions/${current.id}/spaces`),
  ]);
  const manage = can(session, "spaces.manage");
  const pick = (key: string) => (sp[key] ?? "").split(",").filter(Boolean);
  const q = (sp.q ?? "").toLowerCase();
  const rows = all.filter(
    (s) =>
      (!q ||
        s.number.toLowerCase().includes(q) ||
        (s.location_note ?? "").toLowerCase().includes(q) ||
        (s.active_allocation?.exhibitor?.organization_name ?? "").toLowerCase().includes(q)) &&
      (!pick("hall").length || pick("hall").includes(String(s.hall_id))) &&
      (!pick("category").length || pick("category").includes(String(s.space_category_id))) &&
      (!pick("status").length || pick("status").includes(s.status)),
  );
  const tally = (f: (s: Space) => string) =>
    all.reduce<Record<string, number>>((a, s) => ({ ...a, [f(s)]: (a[f(s)] ?? 0) + 1 }), {});
  const byHall = tally((s) => String(s.hall_id));
  const byCat = tally((s) => String(s.space_category_id));
  const byStatus = tally((s) => s.status);
  const halls = (e.halls ?? []).map((h) => ({ id: String(h.id), label: `${h.code} — ${h.name}` }));
  const cats = (e.space_categories ?? []).map((c) => ({
    id: String(c.id),
    label: `${c.name} (${tzs(c.price_per_sqm)}/m²)`,
  }));
  const ready = halls.length > 0 && cats.length > 0;

  return (
    <div className="grid gap-6">
      <PageHeader
        actions={
          manage &&
          (ready ? (
            <>
              <MorphForm
                label="Generate a block"
                spec={{
                  method: "POST",
                  path: `/exhibitions/${e.id}/spaces/generate`,
                  lists: ["facilities"],
                  success: "Spaces generated",
                }}
                submitLabel="Generate spaces"
                subtitle="Creates identical spaces numbered in sequence and lays them out in the hall."
                title="Generate a block of spaces"
                variant="secondary"
              >
                <div className="grid gap-4 sm:grid-cols-2">
                  <SelectF isRequired label="Hall / area" name="hall_id" options={halls} />
                  <SelectF isRequired label="Category" name="space_category_id" options={cats} />
                  <TextF isRequired mono label="Number prefix" name="prefix" placeholder="C" />
                  <TextF isRequired defaultValue={10} label="How many" max={200} min={1} name="count" type="number" />
                  <TextF
                    isRequired
                    defaultValue={3}
                    label="Width (m)"
                    min={0.5}
                    name="width_m"
                    step={0.5}
                    type="number"
                  />
                  <TextF
                    isRequired
                    defaultValue={3}
                    label="Length (m)"
                    min={0.5}
                    name="length_m"
                    step={0.5}
                    type="number"
                  />
                  <AreaF
                    className="sm:col-span-2"
                    description="One per line."
                    label="Facilities"
                    name="facilities"
                    rows={3}
                  />
                </div>
              </MorphForm>
              <MorphForm
                icon={<Plus className="size-4" />}
                label="New space"
                spec={{
                  method: "POST",
                  path: `/exhibitions/${e.id}/spaces`,
                  lists: ["facilities"],
                  success: "Space created",
                }}
                submitLabel="Create space"
                title="New space"
              >
                <SpaceFields cats={cats} halls={halls} />
              </MorphForm>
            </>
          ) : (
            <Link className="button button--primary button--md" href={`/exhibitions/${e.id}`}>
              Add halls and categories first
            </Link>
          ))
        }
        description={`${all.length} spaces · ${byStatus.available ?? 0} available`}
        eyebrow={e.code}
        title="Spaces"
      />
      <FilterBar
        facets={[
          {
            key: "hall",
            label: "Hall",
            options: (e.halls ?? []).map((h) => ({ value: String(h.id), label: h.name, count: byHall[h.id] ?? 0 })),
          },
          {
            key: "category",
            label: "Category",
            options: (e.space_categories ?? []).map((c) => ({
              value: String(c.id),
              label: c.name,
              count: byCat[c.id] ?? 0,
            })),
          },
          {
            key: "status",
            label: "Status",
            options: Object.entries(SPACE_STATUS).map(([value, s]) => ({
              value,
              label: s.label,
              count: byStatus[value] ?? 0,
            })),
          },
        ]}
        placeholder="Search number, location, exhibitor…"
      />
      <Surface>
        <DataTable
          columns={[
            {
              key: "number",
              label: "Space",
              render: (s) => <span className="font-display font-extrabold">{s.number}</span>,
            },
            { key: "hall", label: "Hall", render: (s) => s.hall?.name },
            {
              key: "category",
              label: "Category",
              render: (s) => s.category && <CategoryChip color={s.category.color} name={s.category.name} />,
            },
            { key: "size", label: "Size", render: (s) => `${s.size_label} · ${s.area_sqm} m²` },
            { key: "price", label: "Price (TZS)", align: "right", render: (s) => s.price.toLocaleString("en-TZ") },
            { key: "status", label: "Status", render: (s) => <StatusChip map={SPACE_STATUS} value={s.status} /> },
            {
              key: "holder",
              label: "Held by",
              render: (s) =>
                s.active_allocation ? (
                  <Link
                    className="font-medium hover:underline"
                    href={`/applications/${s.active_allocation.application_id}`}
                  >
                    {s.active_allocation.exhibitor?.organization_name}
                  </Link>
                ) : (
                  <span className="text-muted">—</span>
                ),
            },
            ...(manage
              ? [
                  {
                    key: "actions",
                    label: "",
                    align: "right" as const,
                    render: (s: Space) => (
                      <span className="flex justify-end gap-1.5">
                        <MorphForm
                          label="Edit"
                          size="sm"
                          spec={{
                            method: "PUT",
                            path: `/spaces/${s.id}`,
                            lists: ["facilities"],
                            success: `Space ${s.number} updated`,
                          }}
                          subtitle={
                            s.active_allocation
                              ? "This space is held: its size, category and price are locked."
                              : undefined
                          }
                          title={`Edit space ${s.number}`}
                          variant="ghost"
                        >
                          <SpaceFields cats={cats} halls={halls} s={s} />
                        </MorphForm>
                        {!s.active_allocation && s.status === "available" && (
                          <ActionButton size="sm" spec={{ method: "DELETE", path: `/spaces/${s.id}` }} variant="ghost">
                            Delete
                          </ActionButton>
                        )}
                      </span>
                    ),
                  },
                ]
              : []),
          ]}
          empty="No spaces match"
          emptyDescription={
            all.length ? "Change or reset the filters." : "Generate a block of spaces to lay out the first hall."
          }
          minWidth={980}
          rows={rows}
        />
      </Surface>
    </div>
  );
}

/** FR-005/007: one space. Leave the price empty to take it from the category's rate. */
function SpaceFields({
  s,
  halls,
  cats,
}: {
  s?: Space;
  halls: { id: string; label: string }[];
  cats: { id: string; label: string }[];
}) {
  return (
    <div className="grid gap-4 sm:grid-cols-2">
      <TextF isRequired mono defaultValue={s?.number} label="Space number" name="number" placeholder="A41" />
      <SelectF
        isRequired
        defaultValue={s ? String(s.hall_id) : undefined}
        label="Hall / area"
        name="hall_id"
        options={halls}
      />
      <SelectF
        isRequired
        defaultValue={s ? String(s.space_category_id) : undefined}
        label="Category"
        name="space_category_id"
        options={cats}
      />
      <TextF
        defaultValue={s?.price}
        description="Empty = area × the category's rate."
        label="Price (TZS)"
        min={0}
        name="price"
        type="number"
      />
      <TextF
        isRequired
        defaultValue={s?.width_m ?? 3}
        label="Width (m)"
        min={0.5}
        name="width_m"
        step={0.5}
        type="number"
      />
      <TextF
        isRequired
        defaultValue={s?.length_m ?? 3}
        label="Length (m)"
        min={0.5}
        name="length_m"
        step={0.5}
        type="number"
      />
      <TextF defaultValue={s?.map_col ?? 1} label="Map column" min={1} name="map_col" type="number" />
      <TextF defaultValue={s?.map_row ?? 1} label="Map row" min={1} name="map_row" type="number" />
      <TextF defaultValue={s?.map_w ?? 1} label="Columns wide" max={12} min={1} name="map_w" type="number" />
      <TextF defaultValue={s?.map_h ?? 1} label="Rows deep" max={12} min={1} name="map_h" type="number" />
      <TextF
        className="sm:col-span-2"
        defaultValue={s?.location_note ?? ""}
        label="Location"
        name="location_note"
        placeholder="e.g. Facing the main entrance"
      />
      <TextF
        className="sm:col-span-2"
        defaultValue={s?.special_conditions ?? ""}
        label="Special conditions"
        name="special_conditions"
      />
      <AreaF
        className="sm:col-span-2"
        defaultValue={(s?.facilities ?? []).join("\n")}
        description="One per line."
        label="Available facilities"
        name="facilities"
        rows={3}
      />
    </div>
  );
}
