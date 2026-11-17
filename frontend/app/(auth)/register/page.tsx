import type { Metadata } from "next";

import { redirect } from "next/navigation";

import { getSession } from "@/lib/auth/session";
import { AuthFrame } from "@/components/auth-frame";
import { RegisterForm } from "@/components/auth-forms";

export const metadata: Metadata = { title: "Register as an exhibitor" };

export default async function RegisterPage() {
  if (await getSession()) redirect("/dashboard");

  return (
    <AuthFrame
      wide
      lead="Tell us about your organisation. You can apply for space as soon as the account exists."
      title="Register as an exhibitor"
    >
      <RegisterForm />
    </AuthFrame>
  );
}
