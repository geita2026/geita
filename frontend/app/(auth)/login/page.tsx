import type { Metadata } from "next";

import { redirect } from "next/navigation";

import { getSession } from "@/lib/auth/session";
import { AuthFrame } from "@/components/auth-frame";
import { LoginForm } from "@/components/auth-forms";

export const metadata: Metadata = { title: "Sign in" };

export default async function LoginPage({
  searchParams,
}: {
  searchParams: Promise<{ next?: string; reason?: string }>;
}) {
  const session = await getSession();

  if (session) redirect(session.user.must_change_password ? "/change-password" : "/dashboard");
  const { next, reason } = await searchParams;

  return (
    <AuthFrame lead="Staff and registered exhibitors sign in here." title="Sign in">
      {reason === "expired" && (
        <p className="mb-4 rounded-lg bg-warning-soft px-3 py-2 text-sm text-warning" role="status">
          Your session ended. Sign in again to continue.
        </p>
      )}
      <LoginForm next={next} />
    </AuthFrame>
  );
}
