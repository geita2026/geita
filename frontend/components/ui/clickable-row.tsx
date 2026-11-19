"use client";

import type { ReactNode } from "react";

import { useRouter } from "next/navigation";

/** Links, buttons and fields inside the row keep their own click. */
const INTERACTIVE = "a, button, input, select, textarea, label, [role='button'], [role='link'], [data-no-row-click]";

/**
 * A table row that opens its record when pressed anywhere — Enter works too,
 * and Ctrl/⌘-click opens it in a new tab like a link would.
 */
export function ClickableRow({ href, label, children }: { href: string; label: string; children: ReactNode }) {
  const router = useRouter();

  return (
    <tr
      aria-label={label}
      className="cursor-pointer border-t border-separator transition-colors hover:bg-surface-secondary focus-visible:bg-surface-secondary focus-visible:outline-none"
      tabIndex={0}
      onClick={(e) => {
        // React bubbles events out of portals: a click inside a dialog opened
        // from this row reaches here although it is not in the row's DOM.
        if (!e.currentTarget.contains(e.target as Node)) return;
        if ((e.target as HTMLElement).closest(INTERACTIVE)) return;
        // Selecting text in a cell is not a click.
        if (window.getSelection()?.toString()) return;
        if (e.metaKey || e.ctrlKey) window.open(href, "_blank", "noopener");
        else router.push(href);
      }}
      onKeyDown={(e) => {
        if (e.key === "Enter" && e.target === e.currentTarget) router.push(href);
      }}
    >
      {children}
    </tr>
  );
}
