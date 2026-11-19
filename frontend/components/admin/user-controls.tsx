"use client";

import { useState } from "react";
import { Button, toast } from "@heroui/react";

import { call } from "@/server/actions";
import { ROLES, options } from "@/lib/domain/labels";
import { ActionForm, SelectF, TextF } from "@/components/ui/action-form";

/** A temporary password is shown once, here, and never again. */
function OneTimePassword({ value, who }: { value: string; who: string }) {
  return (
    <div className="rounded-xl border border-brand/40 bg-brand-soft/60 p-4 text-sm" role="status">
      <p className="font-semibold">Temporary password for {who}</p>
      <code className="mt-2 block select-all rounded-lg bg-surface px-3 py-2 font-mono text-base">{value}</code>
      <p className="mt-2 text-xs text-muted">
        Shown once. Pass it on securely — they must change it when they first sign in.
      </p>
    </div>
  );
}

/** FR-043/044 */
export function CreateUserForm() {
  const [created, setCreated] = useState<{ email: string; password: string } | null>(null);

  return (
    <div className="grid gap-4">
      <ActionForm
        className="grid gap-4 sm:grid-cols-2 xl:grid-cols-4"
        pendingLabel="Creating…"
        spec={{ method: "POST", path: "/users", success: "User created" }}
        submitLabel="Create user"
        onDone={(data) => {
          const d = data as { user: { email: string }; temporary_password: string };

          setCreated({ email: d.user.email, password: d.temporary_password });
        }}
      >
        <TextF isRequired label="Full name" name="name" />
        <TextF isRequired label="Email" name="email" type="email" />
        <TextF label="Phone" name="phone" placeholder="+255…" />
        <SelectF isRequired label="Role" name="role" options={options(ROLES).filter((r) => r.id !== "exhibitor")} />
      </ActionForm>
      {created && <OneTimePassword value={created.password} who={created.email} />}
    </div>
  );
}

/** FR-046 */
export function ResetPasswordButton({ userId, email }: { userId: number; email: string }) {
  const [password, setPassword] = useState<string | null>(null);
  const [busy, setBusy] = useState(false);

  if (password) return <OneTimePassword value={password} who={email} />;

  return (
    <Button
      isPending={busy}
      size="sm"
      variant="ghost"
      onPress={async () => {
        setBusy(true);
        const r = await call({ method: "POST", path: `/users/${userId}/reset-password` });

        setBusy(false);
        if (r.ok) setPassword(String(r.data?.temporary_password));
        else toast(r.error, { variant: "danger" });
      }}
    >
      Reset password
    </Button>
  );
}
