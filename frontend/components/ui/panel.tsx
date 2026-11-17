import type { ReactNode } from "react";

import Link from "next/link";

export function Panel({
  title,
  meta,
  action,
  children,
  className = "",
}: {
  title: string;
  meta?: ReactNode;
  action?: { label: string; href: string };
  children: ReactNode;
  className?: string;
}) {
  return (
    <section className={`soft-shadow flex flex-col gap-4 rounded-[20px] bg-surface p-5 ${className}`}>
      <div className="flex items-center justify-between gap-3">
        <div className="flex items-baseline gap-2.5">
          <h3 className="font-display text-[15px] font-bold">{title}</h3>
          {meta && <span className="text-xs text-muted">{meta}</span>}
        </div>
        {action && (
          <Link
            className="rounded-full bg-default px-3 py-1 text-xs font-semibold text-muted hover:text-foreground"
            href={action.href}
          >
            {action.label}
          </Link>
        )}
      </div>
      {children}
    </section>
  );
}
