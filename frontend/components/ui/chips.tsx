import { Chip } from "@heroui/react";

import { humanize } from "@/lib/domain/labels";

type Tone = "default" | "accent" | "success" | "warning" | "danger";

/** One chip for every status vocabulary: pass the map from lib/domain/labels.ts. */
export function StatusChip({
  map,
  value,
  size = "sm",
}: {
  map: Record<string, { label: string; color: Tone }>;
  value: string;
  size?: "sm" | "md";
}) {
  const s = map[value] ?? { label: humanize(value), color: "default" as Tone };

  return (
    <Chip color={s.color} size={size} variant="soft">
      {s.label}
    </Chip>
  );
}

/** A space category wears its own colour (set per exhibition), not a status colour. */
export function CategoryChip({ name, color, size = "sm" }: { name: string; color?: string; size?: "sm" | "md" }) {
  const c = color ?? "var(--muted)";

  return (
    <span
      className={`inline-flex items-center gap-1.5 rounded-full font-semibold whitespace-nowrap ${size === "sm" ? "px-2 py-0.5 text-xs" : "px-2.5 py-1 text-sm"}`}
      style={{
        color: `color-mix(in oklab, ${c} 70%, var(--foreground))`,
        background: `color-mix(in oklab, ${c} 16%, transparent)`,
      }}
    >
      <span aria-hidden className="size-1.5 rounded-full" style={{ background: c }} />
      {name}
    </span>
  );
}

export function Fact({ label, children, className }: { label: string; children: React.ReactNode; className?: string }) {
  return (
    <div className={className}>
      <dt className="text-xs font-semibold uppercase tracking-wider text-muted">{label}</dt>
      <dd className="mt-0.5 text-sm font-medium break-words">{children ?? "—"}</dd>
    </div>
  );
}
