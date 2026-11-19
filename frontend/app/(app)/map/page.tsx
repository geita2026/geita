import type { Metadata } from "next";
import type { Application, Exhibition, Paginated, Space } from "@/lib/types";

import Link from "next/link";

import { api } from "@/lib/api";
import { can, requireSession } from "@/lib/auth/session";
import { currentExhibition } from "@/lib/exhibition";
import { PageHeader } from "@/components/ui/page-header";
import { EmptyState } from "@/components/ui/empty-state";
import { ActionButton } from "@/components/ui/action-button";
import { SpaceMap } from "@/components/spaces/space-map";

export const metadata: Metadata = { title: "Space map" };

export default async function MapPage({
  searchParams,
}: {
  searchParams: Promise<{ exhibition?: string; space?: string }>;
}) {
  const sp = await searchParams;
  const session = await requireSession();
  const current = await currentExhibition(sp.exhibition);

  if (!current) return <EmptyState description="Create or publish an exhibition first." title="No exhibition" />;

  const canAllocate = can(session, "allocations.manage");
  const isExhibitor = session.user.role === "exhibitor";
  const [exhibition, spaces, apps] = await Promise.all([
    api<Exhibition>(`/exhibitions/${current.id}`),
    api<Space[]>(`/exhibitions/${current.id}/spaces`),
    canAllocate || isExhibitor
      ? api<Paginated<Application>>("/applications", {
          query: { exhibition_id: current.id, status: "approved,pending_payment,confirmed", per_page: 100 },
        })
      : null,
  ]);
  const needing = (apps?.data ?? []).filter((a) => (a.active_allocations_count ?? 0) < a.spaces_requested);
  const mine = isExhibitor && exhibition.allow_self_selection ? needing[0] : undefined;

  return (
    <div className="grid gap-6">
      <PageHeader
        actions={
          <>
            {canAllocate && needing.length > 0 && (
              <ActionButton
                pendingLabel="Allocating…"
                spec={{
                  method: "POST",
                  path: `/exhibitions/${current.id}/auto-allocate`,
                  success: "Automatic allocation finished",
                }}
                variant="secondary"
              >
                Auto-allocate {needing.length} waiting
              </ActionButton>
            )}
            {can(session, "spaces.manage") && (
              <Link className="button button--secondary button--md" href="/spaces">
                Manage spaces
              </Link>
            )}
          </>
        }
        description={
          isExhibitor
            ? mine
              ? "Your application is approved. Press an available space to see it and select it."
              : "Press a space to see its size, price and facilities."
            : "Press a space to see who holds it, allocate it, or change its status."
        }
        eyebrow={exhibition.code}
        title="Space map"
      />
      <SpaceMap
        ctx={{
          canAllocate,
          canManage: can(session, "spaces.manage"),
          applications: canAllocate
            ? needing.map((a) => ({ id: String(a.id), label: `${a.exhibitor?.organization_name} — ${a.reference}` }))
            : undefined,
          myApplicationId: mine?.id,
          highlight: sp.space ? Number(sp.space) : undefined,
        }}
        halls={exhibition.halls ?? []}
        spaces={spaces}
      />
    </div>
  );
}
