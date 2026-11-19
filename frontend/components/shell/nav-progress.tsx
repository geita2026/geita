"use client";

import { useEffect, useRef, useState, useSyncExternalStore } from "react";
import { usePathname } from "next/navigation";
import clsx from "clsx";

/**
 * Which sidebar destination is loading, shared between the nav (which lights
 * the clicked item at once) and the progress bar along the top of the page.
 *
 * A module-level store rather than `useLinkStatus`, because on a phone the
 * drawer closes on the same tap and unmounts the link — the bar has to outlive
 * it. Cleared when the pathname changes, i.e. when the new view has rendered.
 */
let pendingHref: string | null = null;
let failsafe: ReturnType<typeof setTimeout> | undefined;
const listeners = new Set<() => void>();

function set(href: string | null) {
  if (pendingHref === href) return;
  pendingHref = href;
  clearTimeout(failsafe);
  // A navigation that never lands (offline, server error) must not leave the
  // nav spinning forever.
  if (href) failsafe = setTimeout(() => set(null), 15000);
  listeners.forEach((l) => l());
}

const subscribe = (l: () => void) => {
  listeners.add(l);

  return () => listeners.delete(l);
};

/**
 * Call from a nav link's click. Ignores clicks that open a new tab or window,
 * and the page you are already on; returns whether a navigation started.
 */
export function startNav(e: React.MouseEvent, href: string, current: string | undefined) {
  if (e.button !== 0 || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return false;
  if (href === current) return false;
  set(href);

  return true;
}

export function useNavPending() {
  return useSyncExternalStore(
    subscribe,
    () => pendingHref,
    () => null,
  );
}

/** 3px brand bar across the top: trickles toward 85% while loading, then completes and fades. */
export function NavProgress() {
  const pathname = usePathname();
  const pending = useNavPending();
  const [phase, setPhase] = useState<"idle" | "loading" | "done">("idle");
  const shown = useRef(false);

  // The new view has rendered.
  useEffect(() => set(null), [pathname]);

  useEffect(() => {
    if (pending) {
      // Prefetched routes land in a few ms; a bar that flashes on and off
      // reads as a glitch, so it waits before appearing.
      const t = setTimeout(() => {
        shown.current = true;
        setPhase("loading");
      }, 120);

      return () => clearTimeout(t);
    }
    if (!shown.current) return;
    shown.current = false;
    const t0 = setTimeout(() => setPhase("done"), 0);
    const t1 = setTimeout(() => setPhase("idle"), 380);

    return () => {
      clearTimeout(t0);
      clearTimeout(t1);
    };
  }, [pending]);

  return (
    <>
      <div aria-hidden className={clsx("nav-progress", phase !== "idle" && `nav-progress--${phase}`)} />
      <span aria-live="polite" className="sr-only" role="status">
        {phase === "loading" ? "Loading page…" : ""}
      </span>
    </>
  );
}
