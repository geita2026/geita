"use server";

import { cookies } from "next/headers";
import { redirect } from "next/navigation";
import { revalidatePath } from "next/cache";

import type { ActionResult } from "@/server/action-result";
import type { Session } from "@/lib/types";

import { api, ApiError, SESSION_COOKIE } from "@/lib/api";
import { sessionCookieOptions } from "@/lib/auth/session";
import { EXHIBITION_COOKIE } from "@/lib/exhibition";

function fail(err: unknown): ActionResult {
  if (err instanceof ApiError) return { ok: false, error: err.message, fieldErrors: err.fieldErrors };
  throw err; // redirects and real faults propagate
}

/* ------------------------------------------------------------------ auth */

async function openSession(token: string) {
  (await cookies()).set(SESSION_COOKIE, token, sessionCookieOptions);
}

export async function loginAction(_prev: ActionResult, form: FormData): Promise<ActionResult> {
  let session: Session & { token: string };

  try {
    session = await api("/auth/login", {
      method: "POST",
      anonymous: true,
      body: { email: form.get("email"), password: form.get("password") },
    });
  } catch (err) {
    return fail(err);
  }
  await openSession(session.token);
  const next = String(form.get("next") ?? "");

  redirect(
    session.user.must_change_password
      ? "/change-password"
      : next.startsWith("/") && !next.startsWith("//")
        ? next
        : "/dashboard",
  );
}

export async function registerAction(_prev: ActionResult, form: FormData): Promise<ActionResult> {
  let session: { token: string };

  try {
    session = await api("/auth/register", { method: "POST", anonymous: true, body: Object.fromEntries(form) });
  } catch (err) {
    return fail(err);
  }
  await openSession(session.token);
  redirect("/dashboard");
}

/**
 * Called by the idle watcher (components/shell/idle-guard.tsx) after the
 * inactivity limit: the API records it as a time-out, not a sign-out.
 */
export async function idleLogoutAction() {
  try {
    await api("/auth/logout", { method: "POST", body: { reason: "idle" } });
  } catch {
    // Already revoked by the API's own clock: the cookie is cleared either way.
  }
  (await cookies()).delete(SESSION_COOKIE);
  redirect("/login?reason=idle");
}

export async function logoutAction() {
  try {
    await api("/auth/logout", { method: "POST" });
  } catch {
    // The token may already be gone; the cookie is cleared either way.
  }
  (await cookies()).delete(SESSION_COOKIE);
  redirect("/login");
}

export async function setExhibitionAction(id: number): Promise<ActionResult> {
  (await cookies()).set(EXHIBITION_COOKIE, String(id), { path: "/", maxAge: 60 * 60 * 24 * 180, sameSite: "lax" });
  revalidatePath("/", "layout");

  return { ok: true };
}

/* ------------------------------------------------------------- mutations */

/**
 * One generic write action for every form and button. The spec is bound on the
 * client (`mutate.bind(null, spec)`), which is safe because it carries no
 * authority: the request runs with the caller's own token and Laravel decides
 * what that role may do (FR-047). This file only translates form data to the
 * API's JSON and API errors back to field errors.
 */
export type MutationSpec = {
  method: "POST" | "PUT" | "DELETE";
  path: string;
  /** Send the form as multipart (file uploads) instead of JSON. */
  multipart?: boolean;
  /** Fields holding one value per line → sent as arrays. */
  lists?: string[];
  /** Checkbox fields → booleans. */
  bools?: string[];
  /** Extra values merged into the body. */
  extra?: Record<string, unknown>;
  success?: string;
  /** Navigate here on success; `:id` is replaced with the returned record's id. */
  redirectTo?: string;
};

function toBody(form: FormData, spec: MutationSpec) {
  const body: Record<string, unknown> = {};

  for (const key of new Set(form.keys())) {
    if (key.startsWith("$ACTION")) continue;
    const value = form.get(key);

    if (typeof value !== "string") continue;
    if (spec.lists?.includes(key)) {
      body[key] = value
        .split(/\r?\n/)
        .map((s) => s.trim())
        .filter(Boolean);
    } else body[key] = value === "" ? null : value;
  }
  for (const key of spec.bools ?? []) body[key] = form.get(key) === "on" || form.get(key) === "true";

  return { ...body, ...spec.extra };
}

async function run(
  spec: MutationSpec,
  form?: FormData,
): Promise<ActionResult<{ id?: number } & Record<string, unknown>>> {
  if (!spec.path.startsWith("/") || spec.path.includes("..") || spec.path.includes("//")) {
    return { ok: false, error: "Invalid request." };
  }
  let data: ({ id?: number } & Record<string, unknown>) | undefined;

  try {
    if (spec.multipart && form) {
      for (const [k, v] of Object.entries(spec.extra ?? {})) form.set(k, String(v));
      // An empty file input would fail the API's file validation.
      for (const [k, v] of [...form.entries()]) if (v instanceof File && v.size === 0) form.delete(k);
      data = await api(spec.path, { method: spec.method, formData: form });
    } else {
      data = await api(spec.path, { method: spec.method, body: form ? toBody(form, spec) : (spec.extra ?? {}) });
    }
  } catch (err) {
    return fail(err) as ActionResult<never>;
  }
  revalidatePath("/", "layout");
  if (spec.redirectTo) redirect(spec.redirectTo.replace(":id", String(data?.id ?? "")));

  return { ok: true, data, message: spec.success };
}

/** For `useActionState` forms. */
export async function mutate(spec: MutationSpec, _prev: ActionResult<unknown>, form: FormData) {
  return run(spec, form);
}

/** For buttons. */
export async function call(spec: MutationSpec) {
  return run(spec);
}

export async function markNotificationsRead(id?: number): Promise<void> {
  try {
    await api("/notifications/read", { method: "POST", body: id ? { id } : {} });
    // The bell's list comes from the layout: refresh it so the next page agrees.
    revalidatePath("/", "layout");
  } catch {
    // Non-critical: the badge already shows it read; the server catches up next time.
  }
}
