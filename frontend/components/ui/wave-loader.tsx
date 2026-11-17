import clsx from "clsx";

/**
 * Four blocks that rise and fall in turn — the "wave" loader. Sized in em-free
 * px to sit exactly where an 18px icon was (4 × 3px blocks + 3 × 2px gaps), in
 * `currentColor`, so it takes the colour of whatever it replaces. Styles are
 * `.wave-loader` in globals.css; static under reduced motion.
 */
export function WaveLoader({ className }: { className?: string }) {
  return (
    <span aria-hidden className={clsx("wave-loader", className)}>
      <span />
      <span />
      <span />
      <span />
    </span>
  );
}
