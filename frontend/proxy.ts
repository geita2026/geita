import { NextResponse, type NextRequest } from "next/server";

/**
 * Route protection: everything except the sign-in and registration pages needs
 * the session cookie. This only gates "signed in or not" — whether the token
 * is still valid, and what the role may do, is decided by the API on every call.
 */
const PUBLIC = ["/login", "/register", "/api/session"];
const SESSION_COOKIE = "gememis_session";

export function proxy(req: NextRequest) {
  const { pathname } = req.nextUrl;

  if (PUBLIC.some((p) => pathname === p || pathname.startsWith(`${p}/`))) return NextResponse.next();

  if (!req.cookies.get(SESSION_COOKIE)?.value) {
    const url = req.nextUrl.clone();

    url.pathname = "/login";
    url.search = pathname !== "/" ? `?next=${encodeURIComponent(pathname + req.nextUrl.search)}` : "";

    return NextResponse.redirect(url);
  }

  return pathname === "/" ? NextResponse.redirect(new URL("/dashboard", req.url)) : NextResponse.next();
}

export const config = {
  matcher: ["/((?!_next/static|_next/image|favicon.ico|icons|cursor|manifest.webmanifest|apple-icon).*)"],
};
