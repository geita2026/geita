import type { CSSProperties } from "react";

type Variant = "gold" | "white" | "orange";

// Side gradient + shadow colour per medal: gold (1st), white diamond (2nd), orange (3rd).
const VARIANTS: Record<Variant, { gradient: string; shadow: string }> = {
  gold: { gradient: "conic-gradient(from 0deg, #fff1b8, #ffbf00, #8a6200, #ffbf00, #fff1b8)", shadow: "#8a6200" },
  white: { gradient: "conic-gradient(from 0deg, #ffffff, #dbe7f2, #9fb3c8, #dbe7f2, #ffffff)", shadow: "#9fb3c8" },
  orange: { gradient: "conic-gradient(from 0deg, #ffd9a8, #ff8c00, #a64b00, #ff8c00, #ffd9a8)", shadow: "#a64b00" },
};

// A slowly spinning 3D gem pyramid — used as a medal gem prepended to winners.
// `size` is the rendered px dimension; all geometry scales off it.
export function GemPyramid({
  size = 28,
  variant = "gold",
  className,
}: {
  size?: number;
  variant?: Variant;
  className?: string;
}) {
  const v = VARIANTS[variant];
  return (
    <div
      className={`pyramid-loader${className ? ` ${className}` : ""}`}
      style={{ "--pl-size": `${size}px`, "--pl-gradient": v.gradient, "--pl-shadow": v.shadow } as CSSProperties}
      aria-hidden="true"
    >
      <div className="wrapper">
        <span className="side side1" />
        <span className="side side2" />
        <span className="side side3" />
        <span className="side side4" />
        <span className="side side5" />
        <span className="side side6" />
        <span className="side side7" />
        <span className="side side8" />
        <span className="shadow" />
      </div>
    </div>
  );
}
