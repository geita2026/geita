"use client";

import { startTransition, useEffect, useOptimistic } from "react";
import { toast } from "@heroui/react";
import clsx from "clsx";

import type { WavePref } from "@/lib/domain/wave";

import { setWaveAction } from "@/server/settings/actions";
import { playWave } from "@/lib/wave-effect";

const OPTIONS: { key: WavePref; title: string; detail: string }[] = [
  {
    key: "on",
    title: "On",
    detail: "A ripple carries the page into its new look when you switch between light and dark or change the accent.",
  },
  {
    key: "off",
    title: "Off",
    detail: "Changes happen at once, without the ripple.",
  },
];

/** Applied to <html> on click, then saved to the cookie — like the motion picker. */
export function WavePicker({ current }: { current: WavePref }) {
  const [selected, setSelected] = useOptimistic(current, (_p: WavePref, next: WavePref) => next);

  useEffect(() => {
    document.documentElement.dataset.wave = selected;
  }, [selected]);

  return (
    <div aria-label="Distortion wave" className="grid gap-2 sm:grid-cols-2" role="radiogroup">
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
            onClick={(e) => {
              if (on) return;
              const card = e.currentTarget.closest("[data-wave-demo]");
              const save = () =>
                startTransition(async () => {
                  setSelected(o.key);
                  const r = await setWaveAction(o.key);

                  if (!r.ok) toast(r.error, { variant: "danger" });
                });

              // Switching it on shows what you get: the card ripples into its new state.
              if (o.key === "on") {
                document.documentElement.dataset.wave = "on";
                void playWave(card, save, { source: e.currentTarget, focus: e.nativeEvent });
              } else save();
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
