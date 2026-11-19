import type { Metadata } from "next";
import type { Paginated, User } from "@/lib/types";

import { api } from "@/lib/api";
import { requirePermission } from "@/lib/auth/session";
import { apiQuery, toParams } from "@/lib/params";
import { fmtDateTime } from "@/lib/domain/format";
import { ROLES, options } from "@/lib/domain/labels";
import { PageHeader } from "@/components/ui/page-header";
import { Panel } from "@/components/ui/panel";
import { FilterBar } from "@/components/ui/filter-bar";
import { DataTable, Surface } from "@/components/ui/table";
import { Pagination } from "@/components/ui/pagination";
import { StatusChip } from "@/components/ui/chips";
import { ActionButton } from "@/components/ui/action-button";
import { MorphForm } from "@/components/ui/morph-form";
import { SelectF, TextF } from "@/components/ui/action-form";
import { CreateUserForm, ResetPasswordButton } from "@/components/admin/user-controls";

export const metadata: Metadata = { title: "Users" };

const STATE = {
  active: { label: "Active", color: "success" as const },
  inactive: { label: "Deactivated", color: "default" as const },
  locked: { label: "Locked", color: "danger" as const },
  temp: { label: "Temporary password", color: "warning" as const },
};

export default async function UsersPage({
  searchParams,
}: {
  searchParams: Promise<Record<string, string | undefined>>;
}) {
  const sp = await searchParams;
  const session = await requirePermission("users.manage");
  const list = await api<Paginated<User>>("/users", { query: apiQuery(sp, ["q", "role"]) });
  const state = (u: User) =>
    !u.is_active ? "inactive" : u.locked_at ? "locked" : u.must_change_password ? "temp" : "active";

  return (
    <div className="grid gap-6">
      <PageHeader
        description="Create staff accounts, assign roles, deactivate, unlock and reset passwords. Exhibitors register themselves."
        title="Users and roles"
      />
      <Panel meta="FR-043 · FR-044" title="Add a staff user">
        <CreateUserForm />
      </Panel>
      <FilterBar
        facets={[
          { key: "role", label: "Role", options: Object.entries(ROLES).map(([value, label]) => ({ value, label })) },
        ]}
        placeholder="Search name or email…"
      />
      <Surface>
        <DataTable
          columns={[
            {
              key: "name",
              label: "User",
              render: (u) => (
                <span>
                  <span className="font-semibold">{u.name}</span>
                  <span className="block text-xs text-muted">{u.email}</span>
                </span>
              ),
            },
            {
              key: "role",
              label: "Role",
              render: (u) => (
                <span>
                  {u.role_label}
                  {u.exhibitor && <span className="block text-xs text-muted">{u.exhibitor.organization_name}</span>}
                </span>
              ),
            },
            { key: "state", label: "Status", render: (u) => <StatusChip map={STATE} value={state(u)} /> },
            {
              key: "seen",
              label: "Last sign-in",
              render: (u) =>
                u.last_login_at ? fmtDateTime(u.last_login_at) : <span className="text-muted">Never</span>,
            },
            {
              key: "actions",
              label: "",
              align: "right",
              render: (u) => (
                <span className="flex flex-wrap items-center justify-end gap-1.5">
                  <MorphForm
                    label="Edit"
                    size="sm"
                    spec={{ method: "PUT", path: `/users/${u.id}`, success: "User updated" }}
                    title={`Edit ${u.name}`}
                    variant="ghost"
                  >
                    <TextF isRequired defaultValue={u.name} label="Full name" name="name" />
                    <TextF defaultValue={u.phone ?? ""} label="Phone" name="phone" />
                    {u.role !== "exhibitor" && u.id !== session.user.id && (
                      <SelectF
                        isRequired
                        defaultValue={u.role}
                        description="Changing the role signs the user out everywhere."
                        label="Role"
                        name="role"
                        options={options(ROLES).filter((r) => r.id !== "exhibitor")}
                      />
                    )}
                  </MorphForm>
                  {u.locked_at && (
                    <ActionButton
                      size="sm"
                      spec={{
                        method: "PUT",
                        path: `/users/${u.id}`,
                        extra: { unlock: true },
                        success: "Account unlocked",
                      }}
                      variant="secondary"
                    >
                      Unlock
                    </ActionButton>
                  )}
                  {u.id !== session.user.id && (
                    <ActionButton
                      size="sm"
                      spec={{
                        method: "PUT",
                        path: `/users/${u.id}`,
                        extra: { is_active: !u.is_active },
                        success: u.is_active ? "Account deactivated" : "Account activated",
                      }}
                      variant="ghost"
                    >
                      {u.is_active ? "Deactivate" : "Activate"}
                    </ActionButton>
                  )}
                  <ResetPasswordButton email={u.email} userId={u.id} />
                </span>
              ),
            },
          ]}
          empty="No users match"
          minWidth={900}
          rows={list.data}
        />
      </Surface>
      <Pagination page={list.current_page} pages={list.last_page} params={toParams(sp)} />
    </div>
  );
}
