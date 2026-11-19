"use client";

import { useState } from "react";
import clsx from "clsx";

import type { Hall, Space } from "@/lib/types";

import {
  MorphingDialog,
  MorphingDialogContainer,
  MorphingDialogDescription,
  MorphingDialogSubtitle,
  MorphingDialogTitle,
  MorphingDialogTrigger,
} from "@/components/core/morphing-dialog";
import { tzs } from "@/lib/domain/format";
import { SPACE_STATUS, humanize } from "@/lib/domain/labels";
import { CategoryChip, Fact, StatusChip } from "@/components/ui/chips";
import { ActionForm, SelectF, TextF } from "@/components/ui/action-form";
import { ActionButton } from "@/components/ui/action-button";
import { MorphPanel } from "@/components/ui/morph-form";
import { Magnetic } from "@/components/core/magnetic";
import { RefLink } from "@/components/ui/ref-link";

const transition = { type: "spring", stiffness: 240, damping: 28 } as const;

export type MapContext = {
  /** Officer: approved applications that still need space. */
  applications?: { id: string; label: string }[];
  canAllocate?: boolean;
  canManage?: boolean;
  /** Exhibitor: their approved application, if it can still take a space. */
  myApplicationId?: number;
  highlight?: number;
};

/**
 * FR-029/030: the digital floor plan. Each hall is a grid; every space is a
 * cell that morphs into its details. Colour is the status; the stripe on the
 * left edge is the space category.
 */
export function SpaceMap({ halls, spaces, ctx }: { halls: Hall[]; spaces: Space[]; ctx: MapContext }) {
  const [only, setOnly] = useState<string | null>(null);

  const counts = spaces.reduce<Record<string, number>>(
    (acc, s) => ({ ...acc, [s.status]: (acc[s.status] ?? 0) + 1 }),
    {},
  );

  return (
    <div className="grid gap-5">
      <div aria-label="Filter by status" className="flex flex-wrap gap-2" role="group">
        {Object.entries(SPACE_STATUS)
          .filter(([k]) => counts[k])
          .map(([k, s]) => (
            <button
              key={k}
              aria-pressed={only === k}
              className={clsx(
                "inline-flex items-center gap-2 rounded-full border px-3 py-1.5 text-xs font-semibold transition-colors",
                only === k ? "border-brand bg-brand-soft" : "border-separator bg-surface hover:bg-default",
              )}
              type="button"
              onClick={() => setOnly(only === k ? null : k)}
            >
              <span aria-hidden className="size-2.5 rounded-sm" style={{ background: s.fill }} />
              {s.label}
              <span className="font-mono text-muted tabular">{counts[k]}</span>
            </button>
          ))}
      </div>

      <div className="grid gap-5">
        {halls.map((hall) => {
          const mine = spaces.filter((s) => s.hall_id === hall.id);
          const cols = Math.max(hall.grid_cols, ...mine.map((s) => s.map_col + s.map_w - 1));

          return (
            <section key={hall.id} className="soft-shadow rounded-[20px] bg-surface p-5">
              <div className="mb-4 flex flex-wrap items-baseline justify-between gap-2">
                <h2 className="font-display text-[15px] font-bold">
                  <span className="me-2 font-mono text-xs text-muted">{hall.code}</span>
                  {hall.name}
                </h2>
                <span className="text-xs text-muted">
                  {humanize(hall.kind)} ·{" "}
                  {only
                    ? `${mine.filter((s) => s.status === only).length} ${SPACE_STATUS[only].label.toLowerCase()} of ${mine.length}`
                    : `${mine.filter((s) => s.status === "available").length} of ${mine.length} available`}
                </span>
              </div>
              {mine.length === 0 ? (
                <p className="py-8 text-center text-sm text-muted">No spaces laid out in this hall yet.</p>
              ) : (
                <div className="overflow-x-auto pb-1">
                  <div
                    className="grid gap-1.5"
                    style={{
                      gridTemplateColumns: `repeat(${cols}, minmax(52px, 1fr))`,
                      gridAutoRows: "52px",
                      minWidth: cols * 54,
                    }}
                  >
                    {mine.map((s) => (
                      <Cell key={s.id} ctx={ctx} dimmed={only !== null && only !== s.status} hall={hall} space={s} />
                    ))}
                  </div>
                </div>
              )}
            </section>
          );
        })}
      </div>
    </div>
  );
}

function Cell({ space: s, hall, ctx, dimmed }: { space: Space; hall: Hall; ctx: MapContext; dimmed: boolean }) {
  const meta = SPACE_STATUS[s.status] ?? { label: s.status, fill: "var(--muted)", color: "default" as const };
  const held = s.active_allocation;
  const free = s.status === "available" || (s.status === "reserved" && ctx.canAllocate);

  return (
    /* The status filter dims this wrapper, not the button: the button is the
       morph's shared-layout element, and Motion keeps an inline `opacity: 1`
       on it that would beat any opacity class. */
    <div
      className={clsx(
        "transition-[opacity,filter] duration-200 motion-reduce:transition-none",
        dimmed && "opacity-20 grayscale",
      )}
      style={{ gridColumn: `${s.map_col} / span ${s.map_w}`, gridRow: `${s.map_row} / span ${s.map_h}` }}
    >
      {/* motion-primitives Magnetic, Basic settings: the cell leans toward the pointer. */}
      <Magnetic className="size-full">
        <MorphingDialog transition={transition}>
          <MorphingDialogTrigger
            className={clsx(
              "relative flex size-full flex-col items-center justify-center overflow-hidden border text-center leading-none transition-shadow hover:shadow-[0_0_0_2px_var(--foreground)]",
              (s.mine || ctx.highlight === s.id) && "shadow-[0_0_0_2px_var(--brand),0_0_0_4px_var(--surface)]",
            )}
            style={{
              borderRadius: 10,
              background: `color-mix(in oklab, ${meta.fill} ${s.status === "available" ? 16 : 34}%, var(--surface))`,
              borderColor: `color-mix(in oklab, ${meta.fill} 55%, transparent)`,
            }}
          >
            {/* Category stripe. A child, not a border: the button's border colour is the status. */}
            <span
              aria-hidden
              className="absolute inset-y-0 left-0 w-1"
              style={{ background: s.category?.color ?? meta.fill }}
            />
            <MorphingDialogTitle className="text-xs font-extrabold tabular">{s.number}</MorphingDialogTitle>
            <span className="mt-1 text-[10px] font-semibold text-muted">{s.mine ? "Yours" : `${s.area_sqm} m²`}</span>
          </MorphingDialogTrigger>

          <MorphingDialogContainer>
            <MorphPanel width="max-w-lg">
              <div className="flex flex-wrap items-center gap-2 pe-14">
                <StatusChip map={SPACE_STATUS} value={s.status} />
                {s.category && <CategoryChip color={s.category.color} name={s.category.name} />}
              </div>
              <MorphingDialogTitle className="mt-3 font-display text-3xl font-extrabold leading-tight">
                {s.number}
              </MorphingDialogTitle>
              <MorphingDialogSubtitle className="mt-1 text-sm text-muted">
                {hall.name} · row {s.map_row}, position {s.map_col}
              </MorphingDialogSubtitle>

              <MorphingDialogDescription disableLayoutAnimation className="mt-5 grid gap-5">
                <dl className="grid grid-cols-2 gap-x-6 gap-y-3">
                  <Fact label="Size">
                    {s.size_label} ({s.area_sqm} m²)
                  </Fact>
                  <Fact label="Price (excl. VAT)">{tzs(s.price)}</Fact>
                  {s.location_note && (
                    <Fact className="col-span-2" label="Location">
                      {s.location_note}
                    </Fact>
                  )}
                  {s.special_conditions && (
                    <Fact className="col-span-2" label="Special conditions">
                      {s.special_conditions}
                    </Fact>
                  )}
                </dl>
                {(s.facilities?.length ?? 0) > 0 && (
                  <div>
                    <p className="text-xs font-semibold uppercase tracking-wider text-muted">Facilities</p>
                    <div className="mt-1.5 flex flex-wrap gap-1.5">
                      {s.facilities!.map((f) => (
                        <span key={f} className="rounded-full bg-default px-2.5 py-1 text-xs font-semibold">
                          {f}
                        </span>
                      ))}
                    </div>
                  </div>
                )}

                {held && (
                  <div className="rounded-xl bg-surface-secondary p-4 text-sm">
                    <p className="text-xs font-semibold uppercase tracking-wider text-muted">
                      {held.status === "pending" ? "Selected by" : "Allocated to"}
                    </p>
                    <p className="mt-1 font-semibold">{held.exhibitor?.organization_name}</p>
                    <p className="mt-2 flex items-center gap-2 text-xs text-muted">
                      Open the application
                      <RefLink href={`/applications/${held.application_id}`}>{held.reference}</RefLink>
                    </p>
                  </div>
                )}

                {s.mine && (
                  <p className="rounded-xl bg-brand-soft/60 p-4 text-sm font-semibold">
                    {s.mine === "pending"
                      ? "You selected this space. It is awaiting confirmation by the Space Allocation Officer."
                      : "This space is allocated to you."}
                  </p>
                )}

                {/* FR-023 */}
                {ctx.myApplicationId && s.status === "available" && (
                  <div className="flex items-center justify-between gap-3 rounded-xl border border-brand/40 bg-brand-soft/50 p-4">
                    <p className="text-sm">An officer confirms your selection, then your invoice is issued.</p>
                    <ActionButton
                      pendingLabel="Selecting…"
                      spec={{
                        method: "POST",
                        path: "/allocations",
                        extra: { application_id: ctx.myApplicationId, space_id: s.id },
                        success: `Space ${s.number} selected`,
                      }}
                      successLabel="Selected"
                    >
                      Select this space
                    </ActionButton>
                  </div>
                )}

                {/* FR-024 */}
                {ctx.canAllocate && free && !held && (
                  <div className="rounded-xl border border-separator p-4">
                    <p className="mb-3 text-sm font-semibold">Allocate this space</p>
                    {ctx.applications?.length ? (
                      <ActionForm
                        pendingLabel="Allocating…"
                        spec={{
                          method: "POST",
                          path: "/allocations",
                          extra: { space_id: s.id },
                          success: `Space ${s.number} allocated`,
                        }}
                        submitLabel="Allocate"
                      >
                        <SelectF
                          isRequired
                          label="Approved application"
                          name="application_id"
                          options={ctx.applications}
                          placeholder="Choose an exhibitor…"
                        />
                      </ActionForm>
                    ) : (
                      <p className="text-sm text-muted">No approved application is waiting for space.</p>
                    )}
                  </div>
                )}

                {/* FR-009 */}
                {ctx.canManage && !held && (
                  <div className="rounded-xl border border-separator p-4">
                    <p className="mb-3 text-sm font-semibold">Change status</p>
                    <ActionForm
                      className="grid gap-3 sm:grid-cols-2"
                      spec={{ method: "POST", path: `/spaces/${s.id}/status`, success: `Space ${s.number} updated` }}
                      submitLabel="Update status"
                      variant="secondary"
                    >
                      <SelectF
                        isRequired
                        defaultValue={
                          ["available", "reserved", "blocked", "maintenance", "cancelled"].includes(s.status)
                            ? s.status
                            : "available"
                        }
                        label="Status"
                        name="status"
                        options={["available", "reserved", "blocked", "maintenance", "cancelled"].map((k) => ({
                          id: k,
                          label: SPACE_STATUS[k].label,
                        }))}
                      />
                      <TextF label="Reason" name="reason" placeholder="Optional" />
                    </ActionForm>
                  </div>
                )}
              </MorphingDialogDescription>
            </MorphPanel>
          </MorphingDialogContainer>
        </MorphingDialog>
      </Magnetic>
    </div>
  );
}
