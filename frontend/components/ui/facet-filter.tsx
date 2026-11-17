"use client";

import { useState } from "react";
import { Button, Popover } from "@heroui/react";
import { Check, CirclePlus, Magnifier } from "@gravity-ui/icons";
import clsx from "clsx";

export type FacetOption = { value: string; label: string; count?: number };

/**
 * A faceted filter chip, in the tablecn idiom: a dashed outline button that
 * opens a checkbox list with counts, and shows the chosen values on the chip
 * itself once something is picked.
 *
 * Selection is multi-value and lives in the URL, so it survives a reload and
 * can be linked — the parent owns the state and writes the query string.
 */
export function FacetFilter({
  label,
  options,
  selected,
  onChange,
  searchable = false,
}: {
  label: string;
  options: FacetOption[];
  selected: string[];
  onChange: (next: string[]) => void;
  /** Adds a filter box — worth it past a handful of options. */
  searchable?: boolean;
}) {
  const [query, setQuery] = useState("");
  const visible =
    searchable && query ? options.filter((o) => o.label.toLowerCase().includes(query.toLowerCase())) : options;

  function toggle(value: string) {
    onChange(selected.includes(value) ? selected.filter((v) => v !== value) : [...selected, value]);
  }

  const chosen = options.filter((o) => selected.includes(o.value));

  return (
    <Popover>
      {/* HeroUI's Popover renders React Aria's DialogTrigger, which only wires
          press handling into a RAC Button — a plain <button> here renders fine
          but never opens the popover. */}
      <Button
        className={clsx(
          "h-9 gap-2 rounded-lg border border-dashed px-3 text-sm font-normal",
          selected.length ? "border-brand/50 bg-brand-soft/50 text-foreground" : "border-separator",
        )}
        size="sm"
        variant="ghost"
      >
        <CirclePlus className="size-4 text-muted" />
        {label}
        {chosen.length > 0 && (
          <>
            <span aria-hidden className="h-4 w-px bg-separator" />
            <span className="flex items-center gap-1">
              {chosen.length > 2 ? (
                <span className="rounded-md bg-default px-1.5 py-0.5 text-xs font-semibold">
                  {chosen.length} selected
                </span>
              ) : (
                chosen.map((o) => (
                  <span key={o.value} className="rounded-md bg-default px-1.5 py-0.5 text-xs font-semibold">
                    {o.label}
                  </span>
                ))
              )}
            </span>
          </>
        )}
      </Button>

      <Popover.Content
        className="w-60 rounded-xl border border-separator bg-overlay p-0 shadow-lg"
        placement="bottom start"
      >
        {searchable && (
          <div className="flex items-center gap-2 border-b border-separator px-3 py-2">
            <Magnifier className="size-4 shrink-0 text-muted" />
            <input
              className="w-full bg-transparent text-sm outline-none placeholder:text-muted"
              placeholder={label}
              value={query}
              onChange={(e) => setQuery(e.target.value)}
            />
          </div>
        )}

        <ul className="max-h-64 overflow-y-auto p-1.5">
          {visible.map((o) => {
            const on = selected.includes(o.value);

            return (
              <li key={o.value}>
                <button
                  aria-pressed={on}
                  className="flex w-full items-center gap-2.5 rounded-md px-2 py-1.5 text-start text-sm transition-colors hover:bg-default"
                  type="button"
                  onClick={() => toggle(o.value)}
                >
                  <span
                    className={clsx(
                      "grid size-4 shrink-0 place-items-center rounded-sm border transition-colors",
                      on ? "border-brand bg-brand text-brand-foreground" : "border-separator text-transparent",
                    )}
                  >
                    <Check className="size-3" />
                  </span>
                  <span className="min-w-0 flex-1 truncate">{o.label}</span>
                  {o.count != null && <span className="font-mono text-xs text-muted tabular">{o.count}</span>}
                </button>
              </li>
            );
          })}
          {visible.length === 0 && <li className="px-2 py-3 text-center text-sm text-muted">No matches</li>}
        </ul>

        {selected.length > 0 && (
          <div className="border-t border-separator p-1.5">
            <button
              className="w-full rounded-md px-2 py-1.5 text-sm font-medium text-muted transition-colors hover:bg-default hover:text-foreground"
              type="button"
              onClick={() => onChange([])}
            >
              Clear {label.toLowerCase()}
            </button>
          </div>
        )}
      </Popover.Content>
    </Popover>
  );
}
