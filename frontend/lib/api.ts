import "server-only";

import { cookies } from "next/headers";
import { redirect } from "next/navigation";

/**
 * The only door to the Laravel API. Runs on the Next server: it reads the
 * session token from the httpOnly cookie and sends it as a bearer token, so
 * the token never reaches browser JavaScript.
 */
export const SESSION_COOKIE = "gememis_session";
export const API_URL = (process.env.API_URL ?? "http://127.0.0.1:8000/api/v1").replace(/\/$/, "");

export class ApiError extends Error {
  constructor(
    message: string,
    public status: number,
    public fieldErrors?: Record<string, string[]>,
    public code?: string,
  ) {
    super(message);
    this.name = "ApiError";
  }
}

type Options = {
  method?: "GET" | "POST" | "PUT" | "DELETE";
  body?: unknown;
  formData?: FormData;
  query?: Record<string, string | number | undefined | null>;
  /** Public endpoints (login, register) are called without a token. */
  anonymous?: boolean;
};

export async function authHeader(): Promise<Record<string, string>> {
  const token = (await cookies()).get(SESSION_COOKIE)?.value;

  return token ? { Authorization: `Bearer ${token}` } : {};
}

export function apiUrl(path: string, query?: Options["query"]) {
  const url = new URL(API_URL + path);

  for (const [k, v] of Object.entries(query ?? {})) {
    if (v !== undefined && v !== null && v !== "") url.searchParams.set(k, String(v));
  }

  return url;
}

export async function api<T>(path: string, opts: Options = {}): Promise<T> {
  const headers: Record<string, string> = { Accept: "application/json", ...(opts.anonymous ? {} : await authHeader()) };
  let body: BodyInit | undefined;

  if (opts.formData) body = opts.formData;
  else if (opts.body !== undefined) {
    headers["Content-Type"] = "application/json";
    body = JSON.stringify(opts.body);
  }

  let res: Response;

  try {
    res = await fetch(apiUrl(path, opts.query), { method: opts.method ?? "GET", headers, body, cache: "no-store" });
  } catch {
    throw new ApiError("The GeMEMIS server cannot be reached. Try again in a moment.", 503);
  }

  if (res.status === 204) return undefined as T;

  const data = await res.json().catch(() => ({}));

  if (res.ok) return data as T;

  // The session ended (expired, revoked, account deactivated): back to sign-in.
  if (res.status === 401 && !opts.anonymous) redirect("/api/session/end?reason=expired");
  if (res.status === 403 && data.code === "password_change_required") redirect("/change-password");

  throw new ApiError(data.message ?? "The request could not be completed.", res.status, data.errors, data.code);
}
