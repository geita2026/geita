import { NextResponse, type NextRequest } from "next/server";

import { SESSION_COOKIE } from "@/lib/api";

/** Where lib/api.ts sends a request whose token the API refused: clear the cookie, go to sign-in. */
export function GET(req: NextRequest) {
  const url = new URL("/login", req.url);
  const reason = req.nextUrl.searchParams.get("reason");

  if (reason) url.searchParams.set("reason", reason);
  const res = NextResponse.redirect(url);

  res.cookies.delete(SESSION_COOKIE);

  return res;
}
