const API = process.env.NEXT_PUBLIC_API_URL || "http://localhost:4000";

export function getToken(): string | null {
  if (typeof window === "undefined") return null;
  return window.localStorage.getItem("ditf_token");
}

export function setSession(token: string, user: any) {
  window.localStorage.setItem("ditf_token", token);
  window.localStorage.setItem("ditf_user", JSON.stringify(user));
}

export function getUser(): any | null {
  if (typeof window === "undefined") return null;
  const raw = window.localStorage.getItem("ditf_user");
  return raw ? JSON.parse(raw) : null;
}

export function logout() {
  window.localStorage.removeItem("ditf_token");
  window.localStorage.removeItem("ditf_user");
}

export async function api<T = any>(path: string, opts: RequestInit = {}): Promise<T> {
  const token = getToken();
  const res = await fetch(`${API}${path}`, {
    ...opts,
    headers: {
      "Content-Type": "application/json",
      ...(token ? { Authorization: `Bearer ${token}` } : {}),
      ...(opts.headers || {}),
    },
  });
  // An expired/invalid session on an authenticated call: clear it and bounce to login,
  // rather than letting pages render a misleading empty state. Skip for the login call
  // itself, and only when we actually had a token (so genuinely public 401s still throw).
  if (res.status === 401 && getToken() && !path.startsWith("/auth/")) {
    logout();
    if (typeof window !== "undefined") window.location.href = "/login";
  }
  if (!res.ok) {
    const body = await res.json().catch(() => ({}));
    throw new Error(body.error ? JSON.stringify(body.error) : `Request failed (${res.status})`);
  }
  return res.json();
}
