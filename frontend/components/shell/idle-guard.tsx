"use client";

import { useEffect, useRef, useState } from "react";
import { Button } from "@heroui/react";
import { Clock } from "@gravity-ui/icons";

import { idleLogoutAction, logoutAction } from "@/server/actions";

/**
 * eGA inactivity rule: "Connection sessions that are not active for more than
 * thirty (30) minutes must automatically terminate." Same behaviour as the
 * TANTRADE Project Tracking Portal and SAMS.
 *
 * - **Activity** is a pointer, key, wheel, scroll or touch event, shared
 *   between tabs through localStorage: working in one tab keeps the others
 *   alive, and a time-out in one sends the rest to sign-in.
 * - **Keep-alive**: while the user is active, a request at most every PING_MS
 *   reaches the API, so the server (which also enforces the limit, see
 *   AppServiceProvider) never times out someone reading without clicking.
 * - **Warning**: WARN_MS before the limit a dialog counts down. While it is
 *   open only a deliberate press or key counts — a mouse drifting past should
 *   not silently extend the session.
 * - **Time-out**: idleLogoutAction revokes the token, records it as a
 *   time-out, and opens /login?reason=idle.
 *
 * Timers in a hidden tab are throttled, so the clock is re-checked as soon as
 * the tab is shown again.
 */
const WARN_MS = 2 * 60_000;
const PING_MS = 4 * 60_000;
const ACTIVITY_KEY = "gememis:last-activity";
const SIGNED_OUT_KEY = "gememis:signed-out";

function readShared(key: string) {
  try {
    return Number(localStorage.getItem(key)) || 0;
  } catch {
    return 0;
  }
}

function writeShared(key: string, value: number) {
  try {
    localStorage.setItem(key, String(value));
  } catch {
    /* Private mode or blocked storage: this tab keeps its own clock. */
  }
}

const mmss = (ms: number) => {
  const s = Math.max(0, Math.ceil(ms / 1000));

  return `${Math.floor(s / 60)}:${String(s % 60).padStart(2, "0")}`;
};

export function IdleGuard({ minutes }: { minutes: number }) {
  /** ms left while the warning is showing; null when it is not. */
  const [left, setLeft] = useState<number | null>(null);
  const last = useRef(0);
  const lastPing = useRef(0);
  const ending = useRef(false);
  const warning = useRef(false);
  const stay = useRef<HTMLButtonElement>(null);
  /** Set by the effect: what "Stay signed in" does (a deliberate press). */
  const keep = useRef<() => void>(() => {});

  useEffect(() => {
    if (!minutes) return;
    const IDLE_MS = minutes * 60_000;
    const now = Date.now();

    // Loading the page was a request, so it counts as activity on both clocks.
    last.current = now;
    lastPing.current = now;
    writeShared(ACTIVITY_KEY, now);

    const toLogin = (reason: string) => {
      if (ending.current) return;
      ending.current = true;
      // A full load, not router.push: drop everything the page held in memory.
      // eslint-disable-next-line @next/next/no-location-assign-relative-destination
      window.location.assign(`/login?reason=${reason}`);
    };

    const ping = async () => {
      lastPing.current = Date.now();
      try {
        const res = await fetch("/api/session", { cache: "no-store", redirect: "manual" });

        if (res.status === 401 || res.type === "opaqueredirect") toLogin("expired");
      } catch {
        /* Offline: the next check or ping decides. */
      }
    };

    const expire = async () => {
      if (ending.current) return;
      ending.current = true;
      writeShared(SIGNED_OUT_KEY, Date.now());
      try {
        await idleLogoutAction(); // redirects
      } catch {
        // eslint-disable-next-line @next/next/no-location-assign-relative-destination -- full load, as in toLogin
        window.location.assign("/login?reason=idle");
      }
    };

    const check = () => {
      if (ending.current) return;
      const idle = Date.now() - Math.max(last.current, readShared(ACTIVITY_KEY));

      if (idle >= IDLE_MS) {
        void expire();
      } else if (idle >= IDLE_MS - WARN_MS) {
        warning.current = true;
        setLeft(IDLE_MS - idle);
      } else if (warning.current) {
        // Activity in another tab cleared it.
        warning.current = false;
        setLeft(null);
      }
    };

    let lastWrite = 0;
    const active = (e: Event) => {
      // While warning, only a deliberate press or key keeps the session.
      if (warning.current && e.type !== "pointerdown" && e.type !== "keydown") return;
      const t = Date.now();

      last.current = t;
      if (t - lastWrite > 5000 || warning.current) {
        lastWrite = t;
        writeShared(ACTIVITY_KEY, t);
      }
      if (warning.current) {
        warning.current = false;
        setLeft(null);
        void ping();
      } else if (t - lastPing.current > PING_MS) {
        void ping();
      }
    };

    keep.current = () => active(new Event("pointerdown"));

    const onStorage = (e: StorageEvent) => {
      if (e.key === SIGNED_OUT_KEY) toLogin("idle");
    };
    const onVisible = () => {
      if (document.visibilityState === "visible") check();
    };

    const events = ["pointerdown", "pointermove", "keydown", "wheel", "touchstart"] as const;

    events.forEach((type) => window.addEventListener(type, active, { passive: true }));
    window.addEventListener("scroll", active, { passive: true, capture: true });
    window.addEventListener("storage", onStorage);
    document.addEventListener("visibilitychange", onVisible);
    const timer = setInterval(check, 1000);

    return () => {
      events.forEach((type) => window.removeEventListener(type, active));
      window.removeEventListener("scroll", active, { capture: true });
      window.removeEventListener("storage", onStorage);
      document.removeEventListener("visibilitychange", onVisible);
      clearInterval(timer);
    };
  }, [minutes]);

  const open = left !== null;

  useEffect(() => {
    if (open) stay.current?.focus();
  }, [open]);

  if (!open) return null;

  return (
    <div className="fixed inset-0 z-[230] grid place-items-center bg-canvas/60 p-4 backdrop-blur-[2px]">
      <div
        aria-describedby="idle-desc"
        aria-labelledby="idle-title"
        aria-modal="true"
        className="grid w-full max-w-sm gap-4 rounded-2xl bg-overlay p-5 shadow-[0_20px_50px_-12px_rgb(0_0_0/0.5)]"
        role="alertdialog"
      >
        <div className="flex items-start gap-3">
          <span className="grid size-10 shrink-0 place-items-center rounded-full bg-warning-soft text-warning">
            <Clock aria-hidden className="size-5" />
          </span>
          <div className="grid gap-1">
            <h2 className="font-semibold" id="idle-title">
              Still there?
            </h2>
            <p className="text-sm text-muted" id="idle-desc">
              For security you will be signed out after {minutes} minutes without activity.
            </p>
          </div>
        </div>
        <p aria-live="off" className="text-center font-mono text-4xl font-bold tabular-nums">
          {mmss(left)}
        </p>
        {/* Announced once a minute rather than every second. */}
        <span aria-live="polite" className="sr-only">
          Signing out in {Math.ceil(left / 60_000)} minute{Math.ceil(left / 60_000) === 1 ? "" : "s"}.
        </span>
        <div className="flex justify-end gap-2">
          <Button variant="tertiary" onPress={() => void logoutAction()}>
            Sign out now
          </Button>
          <Button ref={stay} variant="primary" onPress={() => keep.current()}>
            Stay signed in
          </Button>
        </div>
      </div>
    </div>
  );
}
