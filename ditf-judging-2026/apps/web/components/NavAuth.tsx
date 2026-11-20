"use client";

import { useEffect, useState } from "react";
import Link from "next/link";
import { usePathname, useRouter } from "next/navigation";
import { getUser, logout } from "../lib/api";

export function NavAuth() {
  const [user, setUser] = useState<any>(null);
  const [mounted, setMounted] = useState(false);
  const pathname = usePathname();
  const router = useRouter();

  // Re-read the session on mount and whenever the route changes
  // (so the navbar updates immediately after login/logout).
  useEffect(() => {
    setMounted(true);
    setUser(getUser());
  }, [pathname]);

  if (!mounted) return null; // avoid hydration mismatch (localStorage is client-only)

  if (!user) {
    return (
      <Link href="/login" className="btn btn-secondary btn-sm">
        Login
      </Link>
    );
  }

  const tag = user.judgeCode ? `· ${user.judgeCode}` : user.role === "ADMIN" ? "· Admin" : "";

  return (
    <div className="flex items-center gap-2">
      <span className="badge badge-ghost hidden sm:inline-flex">
        {user.name} {tag}
      </span>
      <button
        className="btn btn-secondary btn-sm"
        onClick={() => {
          logout();
          setUser(null);
          router.push("/");
        }}
      >
        Logout
      </button>
    </div>
  );
}
