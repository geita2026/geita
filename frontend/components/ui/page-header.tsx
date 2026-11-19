import type { ReactNode } from "react";

export function PageHeader({
  title,
  description,
  actions,
  eyebrow,
}: {
  title: string;
  description?: ReactNode;
  actions?: ReactNode;
  eyebrow?: string;
}) {
  return (
    <div className="flex flex-wrap items-center justify-between gap-4 px-1">
      <div className="min-w-0">
        {eyebrow && <p className="mb-1 font-mono text-xs text-muted">{eyebrow}</p>}
        <h1 className="font-display text-[26px] font-extrabold tracking-tight">{title}</h1>
        {description && <p className="mt-0.5 text-sm text-muted">{description}</p>}
      </div>
      {actions && <div className="flex flex-wrap items-center gap-2">{actions}</div>}
    </div>
  );
}
