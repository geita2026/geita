import { NextResponse, type NextRequest } from "next/server";

import { SESSION_COOKIE, apiUrl } from "@/lib/api";

export const dynamic = "force-dynamic";

/**
 * Keep-alive for the idle watcher (components/shell/idle-guard.tsx). The
 * request is the point: it reaches the API, which stamps the token's last
 * use, so someone reading a page without clicking keeps the server's
 * inactivity clock in step with the browser's. 401 once the session is gone
 * (timed out, signed out elsewhere, account deactivated).
 */
export async function GET(req: NextRequest) {
  const token = req.cookies.get(SESSION_COOKIE)?.value;
  const headers = { "Cache-Control": "no-store" };

  if (!token) return NextResponse.json({ error: "signed out" }, { status: 401, headers });

  try {
    const res = await fetch(apiUrl("/auth/me"), {
      headers: { Authorization: `Bearer ${token}`, Accept: "application/json" },
      cache: "no-store",
    });

    if (res.status === 401) {
      const out = NextResponse.json({ error: "signed out" }, { status: 401, headers });

      out.cookies.delete(SESSION_COOKIE);

      return out;
    }
  } catch {
    // API unreachable: not a reason to sign anyone out.
  }

  return new NextResponse(null, { status: 204, headers });
}
