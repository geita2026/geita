import "server-only";

import { cache } from "react";
import { cookies } from "next/headers";
import { redirect } from "next/navigation";

import type { Session } from "@/lib/types";

import { api, ApiError, SESSION_COOKIE } from "@/lib/api";

export const sessionCookieOptions = {
  httpOnly: true,
  sameSite: "lax" as const,
  secure: process.env.NODE_ENV === "production",
  path: "/",
  maxAge: 60 * 60 * 12, // matches SANCTUM_TOKEN_MINUTES on the API
};

/** Who is signed in, or null. One API call per request, however many callers. */
export const getSession = cache(async (): Promise<Session | null> => {
  if (!(await cookies()).get(SESSION_COOKIE)?.value) return null;

  try {
    return await api<Session>("/auth/me");
  } catch (err) {
    if (err instanceof ApiError) return null;
    throw err;
  }
});

export async function requireSession(): Promise<Session> {
  const session = await getSession();

  if (!session) redirect("/login");
  if (session.user.must_change_password) redirect("/change-password");

  return session;
}

export const can = (session: Session, ...permissions: string[]) =>
  permissions.some((p) => session.permissions.includes(p));

/** For pages a role must not open at all; controls are hidden elsewhere and the API enforces. */
export async function requirePermission(...permissions: string[]): Promise<Session> {
  const session = await requireSession();

  if (!can(session, ...permissions)) redirect("/dashboard");

  return session;
}
