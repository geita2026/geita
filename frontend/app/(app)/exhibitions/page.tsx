import type { Metadata } from "next";

import { Plus } from "@gravity-ui/icons";

import { can, requireSession } from "@/lib/auth/session";
import { listExhibitions } from "@/lib/exhibition";
import { PageHeader } from "@/components/ui/page-header";
import { EmptyState } from "@/components/ui/empty-state";
import { MorphForm } from "@/components/ui/morph-form";
import { ExhibitionCard } from "@/components/exhibitions/exhibition-card";
import { ExhibitionFields, exhibitionSpecExtras } from "@/components/exhibitions/exhibition-fields";

export const metadata: Metadata = { title: "Exhibitions" };

export default async function ExhibitionsPage() {
  const session = await requireSession();
  const exhibitions = await listExhibitions();
  const manage = can(session, "exhibitions.manage");

  return (
    <div className="grid gap-6">
      <PageHeader
        actions={
          manage && (
            <MorphForm
              icon={<Plus className="size-4" />}
              label="New exhibition"
              pendingLabel="Creating…"
              spec={{ method: "POST", path: "/exhibitions", redirectTo: "/exhibitions/:id", ...exhibitionSpecExtras }}
              submitLabel="Create exhibition"
              subtitle="It starts as a draft. Add halls and spaces, then publish it for registration."
              title="New exhibition"
              width="max-w-3xl"
            >
              <ExhibitionFields />
            </MorphForm>
          )
        }
        description={
          manage
            ? "Create an exhibition, lay out its spaces, then publish it for registration."
            : "Exhibitions you can apply to exhibit at."
        }
        title="Exhibitions"
      />
      {exhibitions.length === 0 ? (
        <EmptyState description="Nothing has been published yet." title="No exhibitions" />
      ) : (
        <div className="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
          {exhibitions.map((e) => (
            <ExhibitionCard key={e.id} e={e} />
          ))}
        </div>
      )}
    </div>
  );
}
