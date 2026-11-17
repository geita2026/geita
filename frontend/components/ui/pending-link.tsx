"use client";

import { useEffect, useState } from "react";
import { createPortal } from "react-dom";
import Link, { useLinkStatus } from "next/link";
import { FileText } from "@gravity-ui/icons";

/**
 * A link that puts a loading dialog over the page while the next route is
 * still being fetched — Vuetify's dialog loader, in this app's tokens.
 *
 * Opening a change request runs a server render with several joins behind it,
 * and until it returns the browser shows the old page with nothing happening.
 * `useLinkStatus` reports that gap, and it only reports it when there is one:
 * a navigation served from the prefetch cache never goes pending, so the
 * dialog does not appear on a fast one.
 */
export function PendingLink({
  href,
  className,
  label = "Opening…",
  children,
}: {
  href: string;
  className?: string;
  /** Shown in the dialog. Say what is opening, not that something is loading. */
  label?: string;
  children: React.ReactNode;
}) {
  return (
    <Link className={className} href={href}>
      {children}
      {/* Must be inside the Link: useLinkStatus reads the nearest one. It
          renders nothing inline — the dialog goes out through a portal — so it
          cannot disturb the link's own layout. */}
      <LinkPending label={label} />
    </Link>
  );
}

function LinkPending({ label }: { label: string }) {
  const { pending } = useLinkStatus();

  /* Mounted only while pending, so leaving that state resets the delay below
     by unmounting rather than by a second piece of state. */
  return pending ? <DelayedLoader label={label} /> : null;
}

/**
 * Waits before showing. A navigation that resolves in 80ms would otherwise
 * flash a dialog on and off, which reads as a glitch rather than as progress —
 * worse than showing nothing at all.
 */
function DelayedLoader({ label, delay = 150 }: { label: string; delay?: number }) {
  const [shown, setShown] = useState(false);

  useEffect(() => {
    const t = setTimeout(() => setShown(true), delay);

    return () => clearTimeout(t);
  }, [delay]);

  if (!shown || typeof document === "undefined") return null;

  return createPortal(<RouteLoader label={label} />, document.body);
}

/** The dialog itself: a 320px card with an icon, a line of text and a spinner. */
export function RouteLoader({ label }: { label: string }) {
  return (
    <div
      aria-busy="true"
      aria-live="polite"
      className="fixed inset-0 z-[200] grid place-items-center bg-canvas/55 p-4 backdrop-blur-[2px]"
      role="status"
    >
      <div className="flex w-full max-w-[320px] items-center gap-4 rounded-lg bg-overlay px-4 py-3 shadow-[0_10px_30px_-8px_rgb(0_0_0/0.5)]">
        <FileText aria-hidden className="size-8 shrink-0 text-brand-ink" />
        <span className="min-w-0 flex-1 text-[15px] font-medium">{label}</span>
        {/* A fixed-length arc that rotates — Vuetify's indeterminate spinner
            with disable-shrink, which does not pulse its length. */}
        <svg
          aria-hidden
          className="size-4 shrink-0 animate-spin text-brand-ink motion-reduce:animate-none"
          fill="none"
          viewBox="0 0 24 24"
        >
          <circle
            cx="12"
            cy="12"
            r="10"
            stroke="currentColor"
            strokeDasharray="47 16"
            strokeLinecap="round"
            strokeWidth="2"
          />
        </svg>
      </div>
    </div>
  );
}
