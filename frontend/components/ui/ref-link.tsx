import clsx from "clsx";

import { PendingLink } from "@/components/ui/pending-link";

/**
 * A reference number (application, invoice, allocation) that opens its record.
 * Plain monospace text did not read as a link, so it carries a chain icon in a
 * small disc; on hover or keyboard focus the icon gives one beat and a spark
 * flicks off the link end (styles: `.ref-link` in globals.css; still under
 * reduced motion). Adapted from a Uiverse.io "Copy link" button by elijahgummer.
 */
export function RefLink({ href, children, className }: { href: string; children: string; className?: string }) {
  return (
    <PendingLink className={clsx("ref-link", className)} href={href} label={`Opening ${children}…`}>
      <RefBody>{children}</RefBody>
    </PendingLink>
  );
}

/** The same look inside something that is already a link (a whole clickable row). */
export function RefBadge({ children, className }: { children: string; className?: string }) {
  return (
    <span className={clsx("ref-link", className)}>
      <RefBody>{children}</RefBody>
    </span>
  );
}

function RefBody({ children }: { children: string }) {
  return (
    <>
      <span className="ref-link__text">{children}</span>
      <span aria-hidden className="ref-link__icon">
        <svg fill="none" stroke="currentColor" strokeWidth="1.6" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
          <path className="spark" d="M12.0017 6V4M8.14886 7.40371L6.86328 5.87162M15.864 7.40367L17.1496 5.87158" />
          <path
            d="M13.19 8.688a4.5 4.5 0 0 1 1.242 7.244l-4.5 4.5a4.5 4.5 0 0 1-6.364-6.364l1.757-1.757m13.35-.622 1.757-1.757a4.5 4.5 0 0 0-6.364-6.364l-4.5 4.5a4.5 4.5 0 0 0 1.242 7.244"
            strokeLinecap="round"
            strokeLinejoin="round"
          />
        </svg>
      </span>
    </>
  );
}
