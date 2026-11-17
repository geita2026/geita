"use client";

import type { ReactNode } from "react";

import clsx from "clsx";

import { AnimatedBackground } from "@/components/core/animated-background";

type Tone = "default" | "good" | "alert";

export type KpiItem = {
  label: string;
  value: ReactNode;
  hint?: ReactNode;
  tone?: Tone;
};

const TONE: Record<Tone, string> = {
  default: "text-foreground",
  good: "text-prio-low",
  alert: "text-prio-high",
};

/**
 * The portfolio figures, as one set rather than six separate cards: a single
 * tint slides between them as you move across the row.
 *
 * Takes the figures as data instead of children — the highlight has to read a
 * `data-id` off each card and wrap its contents, which it can only do to
 * elements built on this side of the client boundary.
 */
export function KpiRow({ items }: { items: KpiItem[] }) {
  return (
    <AnimatedBackground
      enableHover
      /* Translucent by necessity: `overlay="over"` flies this above the cards
         it crosses, so it has to tint them rather than blank them. */
      className="rounded-2xl bg-brand/12 ring-1 ring-brand/40"
      overlay="over"
      containerClassName="grid grid-cols-2 gap-3 md:grid-cols-3 xl:grid-cols-5"
      contentClassName="flex flex-col gap-1"
      transition={{ type: "spring", bounce: 0, duration: 0.25 }}
    >
      {items.map((k) => (
        <div key={k.label} className="soft-shadow rounded-2xl bg-surface p-4" data-id={k.label}>
          <p className="text-xs font-bold uppercase tracking-[0.12em] text-muted">{k.label}</p>
          <p
            className={clsx("font-display text-[26px] font-extrabold leading-tight tabular", TONE[k.tone ?? "default"])}
          >
            {k.value}
          </p>
          {k.hint && <p className="text-xs text-muted">{k.hint}</p>}
        </div>
      ))}
    </AnimatedBackground>
  );
}
