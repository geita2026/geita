import type { Metadata } from "next";

import { redirect } from "next/navigation";

import { getSession } from "@/lib/auth/session";
import { AuthFrame } from "@/components/auth-frame";
import { RegisterForm } from "@/components/auth-forms";

export const metadata: Metadata = { title: "Create your account" };

export default async function RegisterPage() {
  if (await getSession()) redirect("/dashboard");

  return (
    <AuthFrame
      wide
      lead="Just you for now. When you apply for space, you say whether you exhibit as an individual or a registered business."
      title="Create your account"
    >
      <RegisterForm />
    </AuthFrame>
  );
}
