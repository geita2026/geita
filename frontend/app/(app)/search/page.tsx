import type { Metadata } from "next";

import Link from "next/link";

import { api } from "@/lib/api";
import { requireSession } from "@/lib/auth/session";
import { PageHeader } from "@/components/ui/page-header";
import { EmptyState } from "@/components/ui/empty-state";
import { Surface } from "@/components/ui/table";

export const metadata: Metadata = { title: "Search" };

type Result = { type: string; title: string; subtitle: string; href: string };

/** FR-056 */
export default async function SearchPage({ searchParams }: { searchParams: Promise<{ q?: string }> }) {
  const { q = "" } = await searchParams;

  await requireSession();
  const { results } = await api<{ results: Result[] }>("/search", { query: { q } });
  const groups = results.reduce<Record<string, Result[]>>(
    (acc, r) => ({ ...acc, [r.type]: [...(acc[r.type] ?? []), r] }),
    {},
  );

  return (
    <div className="grid gap-6">
      <PageHeader
        description={`${results.length} result${results.length === 1 ? "" : "s"} for “${q}”`}
        title="Search"
      />
      {results.length === 0 ? (
        <EmptyState
          description="Try a reference number, an organisation name, a space number or an invoice number."
          title="Nothing found"
        />
      ) : (
        <div className="grid gap-4 lg:grid-cols-2">
          {Object.entries(groups).map(([type, items]) => (
            <Surface key={type} className="p-4 sm:p-4">
              <h2 className="mb-2 px-2 text-xs font-bold uppercase tracking-[0.14em] text-muted">
                {type}s · {items.length}
              </h2>
              <ul>
                {items.map((r) => (
                  <li key={r.href + r.title}>
                    <Link className="block rounded-xl px-2 py-2 transition-colors hover:bg-default" href={r.href}>
                      <span className="text-sm font-semibold">{r.title}</span>
                      <span className="block text-xs text-muted">{r.subtitle}</span>
                    </Link>
                  </li>
                ))}
              </ul>
            </Surface>
          ))}
        </div>
      )}
    </div>
  );
}
