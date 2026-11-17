"use client";

import { useEffect, useState, useTransition } from "react";
import { useRouter, useSearchParams } from "next/navigation";
import { InputGroup, TextField } from "@heroui/react";
import { Magnifier, Xmark } from "@gravity-ui/icons";

import { FacetFilter, type FacetOption } from "@/components/ui/facet-filter";

/**
 * Search box plus faceted filters (FR-056/057). State lives in the URL, so a
 * filtered list survives a reload and can be linked or bookmarked.
 */
export function FilterBar({
  placeholder = "Search…",
  facets = [],
  dates = false,
  children,
}: {
  placeholder?: string;
  facets?: { key: string; label: string; options: FacetOption[]; searchable?: boolean }[];
  /** Adds a from/to date range (written as `from` and `to`). */
  dates?: boolean;
  children?: React.ReactNode;
}) {
  const router = useRouter();
  const params = useSearchParams();
  const [, startTransition] = useTransition();
  const [q, setQ] = useState(params.get("q") ?? "");

  function set(key: string, value: string | string[] | null) {
    const next = new URLSearchParams(params.toString());
    const v = Array.isArray(value) ? value.join(",") : value;

    if (v) next.set(key, v);
    else next.delete(key);
    next.delete("page");
    startTransition(() => router.replace(`?${next.toString()}`, { scroll: false }));
  }

  useEffect(() => {
    const t = setTimeout(() => {
      if ((params.get("q") ?? "") !== q) set("q", q || null);
    }, 300);

    return () => clearTimeout(t);
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [q]);

  const active = [...params.keys()].some((k) => k !== "page" && k !== "view");
  const dateCls = "h-9 rounded-lg border border-separator bg-field-background px-2.5 text-sm text-foreground";

  return (
    <div className="flex flex-wrap items-center gap-2">
      <TextField aria-label="Search" className="w-full sm:w-72" type="search" value={q} onChange={setQ}>
        <InputGroup>
          <InputGroup.Prefix>
            <Magnifier className="size-4 text-muted" />
          </InputGroup.Prefix>
          <InputGroup.Input placeholder={placeholder} />
        </InputGroup>
      </TextField>
      {facets.map((f) => (
        <FacetFilter
          key={f.key}
          label={f.label}
          options={f.options}
          searchable={f.searchable}
          selected={(params.get(f.key) ?? "").split(",").filter(Boolean)}
          onChange={(v) => set(f.key, v)}
        />
      ))}
      {dates && (
        <span className="flex items-center gap-1.5 text-sm text-muted">
          <input
            aria-label="From date"
            className={dateCls}
            type="date"
            value={params.get("from") ?? ""}
            onChange={(e) => set("from", e.target.value)}
          />
          –
          <input
            aria-label="To date"
            className={dateCls}
            type="date"
            value={params.get("to") ?? ""}
            onChange={(e) => set("to", e.target.value)}
          />
        </span>
      )}
      {active && (
        <button
          className="inline-flex h-9 items-center gap-1.5 rounded-lg px-3 text-sm font-medium text-muted transition-colors hover:bg-default hover:text-foreground"
          type="button"
          onClick={() => {
            setQ("");
            startTransition(() => router.replace("?", { scroll: false }));
          }}
        >
          Reset <Xmark className="size-3.5" />
        </button>
      )}
      {children && <div className="ms-auto flex items-center gap-2">{children}</div>}
    </div>
  );
}
