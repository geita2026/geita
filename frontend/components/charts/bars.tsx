import type { ReactNode } from "react";

/**
 * Server-rendered horizontal bars. One scale per chart; labels wear text
 * tokens, only the mark carries colour. Good enough for portfolio counts and
 * budgets — Recharts is reserved for the Reports page.
 */
export type BarDatum = {
  key: string;
  label: ReactNode;
  value: number;
  display?: string;
  color?: string;
  title?: string;
};

export function HBars({ data, max, empty = "No data" }: { data: BarDatum[]; max?: number; empty?: string }) {
  if (!data.length) return <p className="py-6 text-center text-sm text-muted">{empty}</p>;
  const top = max ?? Math.max(1, ...data.map((d) => d.value));

  return (
    <div className="grid gap-2">
      {data.map((d) => (
        <div
          key={d.key}
          className="grid grid-cols-[minmax(80px,150px)_1fr_auto] items-center gap-3 text-sm"
          title={d.title}
        >
          <span className="truncate">{d.label}</span>
          <div className="h-3.5 overflow-hidden rounded bg-default">
            <div
              className="h-full rounded-r"
              style={{
                width: `${(d.value / top) * 100}%`,
                minWidth: d.value ? 2 : 0,
                background: d.color ?? "var(--accent)",
              }}
            />
          </div>
          <span className="min-w-9 text-right font-mono text-xs text-muted tabular">{d.display ?? d.value}</span>
        </div>
      ))}
    </div>
  );
}

/** Two-segment stacked bar (ICT vs Non-ICT). */
export function SplitBar({
  a,
  b,
  label,
  display,
  title,
}: {
  a: number;
  b: number;
  label: string;
  display: string;
  title?: string;
}) {
  const total = a + b || 1;

  return (
    <div className="grid grid-cols-[minmax(80px,150px)_1fr_auto] items-center gap-3 text-sm" title={title}>
      <span className="truncate">{label}</span>
      <div className="flex h-4 gap-0.5 overflow-hidden rounded bg-default">
        <span className="h-full" style={{ width: `${(a / total) * 100}%`, background: "var(--cat-ict)" }} />
        <span className="h-full" style={{ width: `${(b / total) * 100}%`, background: "var(--cat-non-ict)" }} />
      </div>
      <span className="min-w-9 text-right font-mono text-xs text-muted tabular">{display}</span>
    </div>
  );
}

export function Legend({ items }: { items: { label: string; color: string }[] }) {
  return (
    <div className="mt-3 flex flex-wrap gap-x-4 gap-y-1 text-xs text-muted">
      {items.map((i) => (
        <span key={i.label} className="inline-flex items-center gap-1.5">
          <i className="inline-block size-2.5 rounded-sm" style={{ background: i.color }} />
          {i.label}
        </span>
      ))}
    </div>
  );
}
