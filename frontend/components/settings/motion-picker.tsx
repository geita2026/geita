"use client";

import { startTransition, useEffect, useOptimistic } from "react";
import { toast } from "@heroui/react";
import clsx from "clsx";

import type { MotionPref } from "@/lib/domain/motion";
import { setMotionAction } from "@/server/settings/actions";

const OPTIONS: { key: MotionPref; title: string; detail: string }[] = [
  {
    key: "system",
    title: "Follow my computer",
    detail: "Animations play, unless your computer's Reduce motion setting is on.",
  },
  {
    key: "reduce",
    title: "Reduce motion",
    detail: "No movement anywhere in the portal: buttons, bars and dialogs change at once.",
  },
];

/** Applied to <html> on click, then saved to the cookie — like the accent picker. */
export function MotionPicker({ current }: { current: MotionPref }) {
  const [selected, setSelected] = useOptimistic(current, (_p: MotionPref, next: MotionPref) => next);

  useEffect(() => {
    document.documentElement.dataset.motion = selected;
  }, [selected]);

  return (
    <div aria-label="Motion" className="grid gap-2 sm:grid-cols-2" role="radiogroup">
      {OPTIONS.map((o) => {
        const on = o.key === selected;

        return (
          <button
            key={o.key}
            aria-checked={on}
            className={clsx(
              "grid gap-0.5 rounded-xl border p-3 text-start transition-colors",
              on ? "border-brand bg-brand-soft" : "border-separator hover:bg-default",
            )}
            role="radio"
            type="button"
            onClick={() => {
              if (on) return;
              startTransition(async () => {
                setSelected(o.key);
                const r = await setMotionAction(o.key);

                if (!r.ok) toast(r.error, { variant: "danger" });
              });
            }}
          >
            <span className="text-sm font-semibold">{o.title}</span>
            <span className="text-xs text-muted">{o.detail}</span>
          </button>
        );
      })}
    </div>
  );
}
