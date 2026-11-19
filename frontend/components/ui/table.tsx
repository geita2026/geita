import type { ReactNode } from "react";

import { EmptyState } from "@/components/ui/empty-state";

export type Column<T> = {
  key: string;
  label: string;
  align?: "right";
  className?: string;
  render: (row: T) => ReactNode;
};

/** The app's one table: `.table .data-table` from globals.css inside a surface card. */
export function DataTable<T extends { id: number | string }>({
  columns,
  rows,
  empty = "Nothing here yet",
  emptyDescription,
  minWidth = 760,
}: {
  columns: Column<T>[];
  rows: T[];
  empty?: string;
  emptyDescription?: string;
  minWidth?: number;
}) {
  if (!rows.length) return <EmptyState description={emptyDescription} title={empty} />;

  return (
    <div className="overflow-x-auto">
      <table className="table data-table" style={{ minWidth }}>
        <thead>
          <tr>
            {columns.map((c) => (
              <th key={c.key} className={`px-3 py-2.5 text-left ${c.align === "right" ? "text-right" : ""}`}>
                {c.label}
              </th>
            ))}
          </tr>
        </thead>
        <tbody>
          {rows.map((row) => (
            <tr key={row.id} className="border-t border-separator">
              {columns.map((c) => (
                <td
                  key={c.key}
                  className={`px-3 py-2.5 align-middle text-sm ${c.align === "right" ? "text-right tabular" : ""} ${c.className ?? ""}`}
                >
                  {c.render(row)}
                </td>
              ))}
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  );
}

export function Surface({ children, className = "" }: { children: ReactNode; className?: string }) {
  // min-w-0: a grid item will not shrink below its content otherwise, and a wide
  // table would stretch the whole page instead of scrolling inside its card.
  return (
    <section className={`soft-shadow min-w-0 rounded-[20px] bg-surface p-2 sm:p-3 ${className}`}>{children}</section>
  );
}
