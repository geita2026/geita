import clsx from "clsx";

/*
 * Tailwind only sees class names it can read literally in the source, so these
 * are spelled out rather than built with `aura-${size}` — a template literal
 * compiles fine but the class never makes it into the stylesheet.
 */
const SIZE_CLASS = {
  xs: "aura-xs",
  sm: "aura-sm",
  md: "aura-md",
  lg: "aura-lg",
  xl: "aura-xl",
} as const;

const VARIANT_CLASS = {
  solid: "",
  dual: "aura-dual",
  rainbow: "aura-rainbow",
  holo: "aura-holo",
  gold: "aura-gold",
  silver: "aura-silver",
} as const;

/**
 * daisyUI's `aura`: an animated border-light that wraps a single element.
 * The effect is drawn in `currentColor`, so set a text colour on the wrapper
 * (default: brand) — `variant` swaps in daisyUI's own multi-colour gradients.
 *
 * Only the `aura` component is pulled in from daisyUI; see styles/globals.css.
 */
export function Aura({
  children,
  variant = "solid",
  size = "md",
  glow = false,
  className,
  radius,
  isActive = true,
}: {
  children: React.ReactNode;
  /** `solid` and `dual` follow currentColor; the rest are daisyUI's fixed palettes. */
  variant?: "solid" | "dual" | "rainbow" | "holo" | "gold" | "silver";
  size?: "xs" | "sm" | "md" | "lg" | "xl";
  /** Pulse in place instead of rotating around the border. */
  glow?: boolean;
  className?: string;
  /** Corner radius of the wrapped element, e.g. "1rem" — defaults to --radius-box. */
  radius?: string;
  /** Set false to render the wrapper without the effect (e.g. only glow on hover). */
  isActive?: boolean;
}) {
  return (
    <div
      className={clsx(
        isActive && [
          "aura",
          SIZE_CLASS[size],
          VARIANT_CLASS[variant],
          glow && "aura-glow",
          variant === "solid" || variant === "dual" ? "text-brand" : undefined,
        ],
        className,
      )}
      style={radius ? ({ "--aura-radius": radius } as React.CSSProperties) : undefined}
    >
      {children}
    </div>
  );
}
