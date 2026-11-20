"use client";

import { useEffect, useState } from "react";
import Link from "next/link";
import { usePathname, useRouter } from "next/navigation";
import { getUser, logout } from "../lib/api";

const ADMIN_LINKS = [
  { href: "/admin/exhibitors", label: "Exhibitors" },
  { href: "/admin/categories", label: "Categories" },
  { href: "/admin/users", label: "Users" },
  { href: "/admin/assignments", label: "Assignments" },
  { href: "/admin/results", label: "Results" },
  { href: "/admin/survey", label: "Survey" },
  { href: "/admin/tools", label: "Tools" },
];
const JUDGE_LINKS = [{ href: "/judge", label: "Judge" }];

const linkClass =
  "btn btn-ghost btn-sm text-base-content/75 hover:text-[#FFBF00] hover:bg-[#FFBF00]/10";

export function Nav() {
  const [user, setUser] = useState<any>(null);
  const [mounted, setMounted] = useState(false);
  const pathname = usePathname();
  const router = useRouter();

  // Re-read the session on mount and on every route change so the nav reflects
  // login/logout immediately. (localStorage is client-only — hence `mounted`.)
  useEffect(() => {
    setMounted(true);
    setUser(getUser());
  }, [pathname]);

  // Identical on server + first client render → no hydration mismatch.
  if (!mounted) return <div className="flex-none" />;

  // Logged out: no navigation, just a way in.
  if (!user) {
    return (
      <div className="flex-none">
        <Link href="/login" className="btn btn-secondary btn-sm">
          Login
        </Link>
      </div>
    );
  }

  const links = user.role === "ADMIN" ? ADMIN_LINKS : JUDGE_LINKS;
  const tag = user.judgeCode ? `· ${user.judgeCode}` : user.role === "ADMIN" ? "· Admin" : "";

  return (
    <div className="flex flex-none items-center gap-1">
      {links.map((l) => (
        <Link key={l.href} href={l.href} className={linkClass}>
          {l.label}
        </Link>
      ))}
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
