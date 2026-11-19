import clsx from "clsx";

/**
 * daisyUI `stack`, wrapped around a dialog so it reads as a small pile of
 * cards with two edges peeking out behind it.
 *
 * It sits *between* HeroUI's Modal.Container and Modal.Dialog: the container is
 * a flex box that centres the dialog, and `.stack` is an inline-grid, so
 * putting the class on the container itself would take that centring away.
 *
 * daisyUI stacks by source order — the first child sits on top, so the real
 * dialog goes first and the two decorative cards follow. They are `aria-hidden`
 * and inert; only the dialog is reachable.
 */
export function DialogStack({ children, className }: { children: React.ReactNode; className?: string }) {
  // The sheets take the same bite as the dialog: a square-cornered sheet shows
  // through the notch and fills it in, so the bite stops reading as a bite.
  const sheet = "notch-tr pointer-events-none border border-separator";
  // daisyUI only offsets the sheets by 3px and 4px, so a sheet in the dialog's
  // own colour leaves nothing to see. Mixing toward --foreground lightens them
  // on a dark theme and darkens them on a light one, so the edge reads in both.
  const sheetStyle = (depth: number) => ({
    background: `color-mix(in oklab, var(--overlay) ${88 - depth * 8}%, var(--foreground))`,
    boxShadow: "var(--overlay-shadow)",
  });

  return (
    <div className={clsx("stack w-full", className)}>
      {children}
      <div aria-hidden className={sheet} style={sheetStyle(0)} />
      <div aria-hidden className={sheet} style={sheetStyle(1)} />
    </div>
  );
}
