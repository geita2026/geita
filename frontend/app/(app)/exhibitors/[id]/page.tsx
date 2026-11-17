import type { Metadata } from "next";
import type { Exhibitor } from "@/lib/types";

import Link from "next/link";

import { api } from "@/lib/api";
import { can, requirePermission } from "@/lib/auth/session";
import { fmtDateTime } from "@/lib/domain/format";
import { APPLICATION_STATUS, EXHIBITOR_TYPES, options } from "@/lib/domain/labels";
import { PageHeader } from "@/components/ui/page-header";
import { Panel } from "@/components/ui/panel";
import { Fact, StatusChip } from "@/components/ui/chips";
import { MorphForm } from "@/components/ui/morph-form";
import { SelectF } from "@/components/ui/action-form";

export const metadata: Metadata = { title: "Exhibitor" };

export default async function ExhibitorPage({ params }: { params: Promise<{ id: string }> }) {
  const { id } = await params;
  const session = await requirePermission("exhibitors.view");
  const e = await api<Exhibitor>(`/exhibitors/${id}`);

  return (
    <div className="grid gap-6">
      <PageHeader
        actions={
          can(session, "exhibitors.manage") && (
            <MorphForm
              label="Classification and status"
              spec={{ method: "PUT", path: `/exhibitors/${e.id}`, success: "Exhibitor updated" }}
              title={e.organization_name}
              variant="secondary"
            >
              <SelectF
                defaultValue={e.exhibitor_type}
                label="Exhibitor type"
                name="exhibitor_type"
                options={options(EXHIBITOR_TYPES)}
              />
              <SelectF
                defaultValue={e.status}
                description="A suspended organisation keeps its records."
                label="Status"
                name="status"
                options={[
                  { id: "active", label: "Active" },
                  { id: "suspended", label: "Suspended" },
                ]}
              />
            </MorphForm>
          )
        }
        description={`${EXHIBITOR_TYPES[e.exhibitor_type] ?? e.exhibitor_type} · ${e.country}`}
        title={e.organization_name}
      />
      <div className="grid gap-4 xl:grid-cols-[1.3fr_1fr]">
        <Panel title="Organisation">
          <dl className="grid grid-cols-2 gap-x-6 gap-y-4 md:grid-cols-3">
            <Fact label="Registration no.">{e.registration_number}</Fact>
            <Fact label="TIN">{e.tin}</Fact>
            <Fact label="Business sector">{e.business_sector}</Fact>
            <Fact label="Contact person">{e.contact_person}</Fact>
            <Fact label="Phone">{e.phone}</Fact>
            <Fact label="Email">{e.email}</Fact>
            <Fact className="col-span-2 md:col-span-3" label="Physical address">
              {e.physical_address}
            </Fact>
            <Fact className="col-span-2 md:col-span-3" label="Business activity">
              {e.business_activity}
            </Fact>
          </dl>
        </Panel>
        <div className="grid content-start gap-4">
          <Panel meta={`${e.applications?.length ?? 0}`} title="Applications">
            <ul className="grid gap-2">
              {(e.applications ?? []).map((a) => (
                <li key={a.id}>
                  <Link
                    className="flex items-center justify-between gap-3 rounded-xl bg-surface-secondary px-3 py-2.5 text-sm hover:bg-default"
                    href={`/applications/${a.id}`}
                  >
                    <span>
                      <span className="font-mono text-xs font-semibold">{a.reference}</span>
                      <span className="block text-xs text-muted">
                        {a.exhibition?.name} · {a.active_allocations_count ?? 0} space(s)
                      </span>
                    </span>
                    <StatusChip map={APPLICATION_STATUS} value={a.status} />
                  </Link>
                </li>
              ))}
              {(e.applications?.length ?? 0) === 0 && <p className="text-sm text-muted">No applications yet.</p>}
            </ul>
          </Panel>
          <Panel title="User accounts">
            <ul className="grid gap-2 text-sm">
              {(e.users ?? []).map((u) => (
                <li key={u.id}>
                  <span className="font-semibold">{u.name}</span> <span className="text-muted">· {u.email}</span>
                  <span className="block text-xs text-muted">
                    {u.last_login_at ? `Last signed in ${fmtDateTime(u.last_login_at)}` : "Never signed in"}
                  </span>
                </li>
              ))}
            </ul>
          </Panel>
        </div>
      </div>
    </div>
  );
}
