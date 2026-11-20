"use client";

import { useEffect, useState } from "react";
import { usePathname, useRouter } from "next/navigation";
import { getUser } from "../lib/api";

// Pages reachable without a session. Everything else requires login.
const PUBLIC = ["/", "/login", "/register"];

export function AuthGuard({ children }: { children: React.ReactNode }) {
  const pathname = usePathname();
  const router = useRouter();
  const [mounted, setMounted] = useState(false);
  const [allowed, setAllowed] = useState(false);

  useEffect(() => {
    setMounted(true);
    const user = getUser();
    // trailingSlash is on, so usePathname() yields e.g. "/login/" — normalise it.
    const raw = pathname || "/";
    const path = raw !== "/" && raw.endsWith("/") ? raw.slice(0, -1) : raw;

    if (!user) {
      // Logged out → only public pages are reachable.
      if (!PUBLIC.includes(path)) {
        router.replace("/login");
        return;
      }
    } else {
      // Logged in → don't sit on the login page.
      if (path === "/login") {
        router.replace(user.role === "ADMIN" ? "/admin/results" : "/judge");
        return;
      }
      // Role guard: only admins get the /admin/* area.
      if (path.startsWith("/admin") && user.role !== "ADMIN") {
        router.replace("/judge");
        return;
      }
    }
    setAllowed(true);
  }, [pathname, router]);

  // Render nothing until the client has checked the session (matches the
  // server's empty first render → no hydration mismatch, no protected flash).
  if (!mounted || !allowed) return null;
  return <>{children}</>;
}
