"use client";

import Link from "next/link";
import { ArrowRight, Calendar, MapPin } from "@gravity-ui/icons";

import type { Exhibition } from "@/lib/types";

import { fmtDate } from "@/lib/domain/format";
import { EXHIBITION_STATUS } from "@/lib/domain/labels";
import { MorphCard } from "@/components/ui/morph-form";
import { Fact, StatusChip } from "@/components/ui/chips";

/** An exhibition tile that morphs into its summary. */
export function ExhibitionCard({ e }: { e: Exhibition }) {
  const total = e.spaces_count ?? 0;
  const free = e.available_spaces_count ?? 0;
  const taken = total ? Math.round(((total - free) / total) * 100) : 0;

  return (
    <MorphCard
      header={
        <>
          <span className="font-mono text-xs text-muted">{e.code}</span>
          <StatusChip map={EXHIBITION_STATUS} value={e.status} />
        </>
      }
      subtitle={e.theme ?? e.venue}
      title={e.name}
      trigger={({ title, subtitle }) => (
        <>
          <div className="flex items-center justify-between gap-2">
            <span className="font-mono text-xs text-muted">{e.code}</span>
            <StatusChip map={EXHIBITION_STATUS} value={e.status} />
          </div>
          {title}
          {subtitle}
          <div className="mt-auto grid gap-2 text-xs text-muted">
            <span className="flex items-center gap-1.5">
              <MapPin className="size-3.5 shrink-0" /> <span className="truncate">{e.venue}</span>
            </span>
            <span className="flex items-center gap-1.5">
              <Calendar className="size-3.5 shrink-0" /> {fmtDate(e.start_date)} – {fmtDate(e.end_date)}
            </span>
            <div className="flex items-center gap-2">
              <div
                aria-label={`${taken}% of spaces taken`}
                className="h-2 flex-1 overflow-hidden rounded-full bg-default"
                role="img"
              >
                <div className="h-full rounded-r bg-brand" style={{ width: `${taken}%` }} />
              </div>
              <span className="font-mono tabular">
                {free}/{total} free
              </span>
            </div>
          </div>
        </>
      )}
    >
      {e.objectives && <p className="text-sm whitespace-pre-line text-muted">{e.objectives}</p>}
      <dl className="grid grid-cols-2 gap-x-6 gap-y-3 sm:grid-cols-3">
        <Fact className="col-span-2 sm:col-span-3" label="Venue">
          {e.venue}
        </Fact>
        <Fact label="Opens">{fmtDate(e.start_date)}</Fact>
        <Fact label="Closes">{fmtDate(e.end_date)}</Fact>
        <Fact label="Spaces free">
          {free} of {total}
        </Fact>
        <Fact label="Registration">
          {fmtDate(e.registration_opens_on)} – {fmtDate(e.registration_closes_on)}
        </Fact>
        <Fact label="Payment deadline">{fmtDate(e.payment_deadline)}</Fact>
        <Fact label="Applications">{e.applications_count ?? 0}</Fact>
      </dl>
      {(e.categories?.length ?? 0) > 0 && (
        <div className="flex flex-wrap gap-1.5">
          {e.categories!.map((c) => (
            <span key={c} className="rounded-full bg-default px-2.5 py-1 text-xs font-semibold">
              {c}
            </span>
          ))}
        </div>
      )}
      <div className="flex justify-end">
        <Link className="button button--primary button--md" href={`/exhibitions/${e.id}`}>
          Open exhibition <ArrowRight className="size-4" />
        </Link>
      </div>
    </MorphCard>
  );
}
