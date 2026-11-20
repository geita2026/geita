"use client";

import { useEffect } from "react";
import { useRouter } from "next/navigation";
import Link from "next/link";

// Registration is no longer used — exhibitors are synced from SAMS.
// Kept as a friendly redirect so the old URL doesn't 404.
export default function RegisterRedirect() {
  const router = useRouter();
  useEffect(() => {
    const t = setTimeout(() => router.replace("/admin/exhibitors"), 2500);
    return () => clearTimeout(t);
  }, [router]);

  return (
    <div className="mx-auto max-w-xl">
      <div className="alert alert-info">
        <span>
          Exhibitors are now synced from SAMS — there’s no manual registration.
          Redirecting to <Link href="/admin/exhibitors" className="link font-medium">Exhibitors</Link>…
        </span>
      </div>
    </div>
  );
}
