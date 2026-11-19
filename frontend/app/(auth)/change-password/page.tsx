import type { Metadata } from "next";

import { redirect } from "next/navigation";

import { getSession } from "@/lib/auth/session";
import { AuthFrame } from "@/components/auth-frame";
import { ActionForm, TextF } from "@/components/ui/action-form";

export const metadata: Metadata = { title: "Change password" };

export default async function ChangePasswordPage() {
  const session = await getSession();

  if (!session) redirect("/login");

  return (
    <AuthFrame
      lead={
        session.user.must_change_password
          ? "You are using a temporary password. Choose your own to continue."
          : "Choose a new password."
      }
      title="Change password"
    >
      <ActionForm
        pendingLabel="Changing…"
        spec={{ method: "PUT", path: "/auth/password", redirectTo: "/dashboard" }}
        submitLabel="Change password"
      >
        <TextF isRequired label="Current password" name="current_password" type="password" />
        <TextF
          isRequired
          description="At least 10 characters with upper and lower case letters and a number."
          label="New password"
          name="password"
          type="password"
        />
        <TextF isRequired label="Confirm new password" name="password_confirmation" type="password" />
      </ActionForm>
    </AuthFrame>
  );
}
