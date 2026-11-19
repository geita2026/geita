"use client";

import { startTransition, useEffect, useOptimistic } from "react";
import { toast } from "@heroui/react";
import { Check } from "@gravity-ui/icons";
import clsx from "clsx";

import { ACCENTS } from "@/lib/domain/accents";
import { setAccentAction } from "@/server/settings/actions";
import { AnimatedBackground } from "@/components/core/animated-background";
import { playWave } from "@/lib/wave-effect";

/**
 * Accent picker. The swatch is applied to <html> immediately so the whole page
 * recolours on click, then persisted to the cookie; the optimistic state keeps
 * the tick in step with what you can already see.
 */
export function AccentPicker({ current }: { current: string }) {
  const [selected, setSelected] = useOptimistic(current, (_prev: string, next: string) => next);

  // Recolour the page as soon as the optimistic value changes, rather than
  // waiting for the server round-trip. If the save fails, useOptimistic falls
  // back to `current` and this puts the old accent straight back.
  useEffect(() => {
    document.documentElement.dataset.accent = selected;
  }, [selected]);

  function choose(key: string, from: HTMLElement, event: MouseEvent) {
    if (key === selected) return;
    // The page ripples from the swatch into the new colour (Settings → Distortion wave).
    void playWave(
      document.body,
      () => {
        document.documentElement.dataset.accent = key;
      },
      { source: from, focus: event },
    );
    startTransition(async () => {
      setSelected(key);
      const r = await setAccentAction(key);

      if (!r.ok) toast(r.error, { variant: "danger" });
    });
  }

  return (
    /* motion-primitives' "Animated Card Background" hover example: the cells
       carry no border or fill of their own, so the one solid highlight
       sliding beneath them *is* the card, with the example's springy
       transition. The selected accent keeps a brand ring (a box-shadow, so it
       stays visible whether or not the highlight is under it). */
    <AnimatedBackground
      enableHover
      className="rounded-xl bg-default"
      containerClassName="grid grid-cols-2 gap-1 sm:grid-cols-3 lg:grid-cols-4"
      contentClassName="flex min-w-0 items-center gap-3"
      transition={{ type: "spring", bounce: 0.2, duration: 0.6 }}
    >
      {ACCENTS.map((a) => {
        const isOn = a.key === selected;

        return (
          <button
            key={a.key}
            aria-label={`Use the ${a.name} accent`}
            aria-pressed={isOn}
            className={clsx(
              "flex select-none items-center rounded-xl p-3 text-start",
              isOn && "bg-brand-soft ring-1 ring-brand",
            )}
            data-id={a.key}
            type="button"
            onClick={(e) => choose(a.key, e.currentTarget, e.nativeEvent)}
          >
            <span
              aria-hidden
              className="grid size-9 shrink-0 place-items-center rounded-full"
              style={{
                background: `rgb(${a.rgb})`,
                color: `rgb(${a.on})`,
                boxShadow: `0 0 0 1px rgb(${a.deep} / 0.5), 0 6px 16px -6px rgb(${a.rgb} / 0.8)`,
              }}
            >
              {isOn && <Check className="size-4" />}
            </span>
            <span className="min-w-0">
              <span className="block text-sm font-semibold">{a.name}</span>
              <span className="block font-mono text-xs text-muted">rgb({a.rgb})</span>
            </span>
          </button>
        );
      })}
    </AnimatedBackground>
  );
}
