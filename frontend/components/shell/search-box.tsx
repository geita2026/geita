"use client";

import { useRouter } from "next/navigation";
import { Magnifier } from "@gravity-ui/icons";

/** FR-056: one box for exhibitors, applications, spaces, invoices, payments and exhibitions. */
export function SearchBox() {
  const router = useRouter();

  return (
    <form
      className="flex min-w-0 flex-1 items-center gap-3"
      role="search"
      onSubmit={(e) => {
        e.preventDefault();
        const q = new FormData(e.currentTarget).get("q")?.toString().trim() ?? "";

        if (q) router.push(`/search?q=${encodeURIComponent(q)}`);
      }}
    >
      <Magnifier className="size-5 shrink-0 text-muted" />
      <input
        aria-label="Search GeMEMIS"
        className="min-w-0 flex-1 bg-transparent text-[15px] outline-none placeholder:text-muted"
        name="q"
        placeholder="Search exhibitors, applications, spaces, invoices…"
        type="search"
      />
    </form>
  );
}
