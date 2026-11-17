import "server-only";

import { cache } from "react";
import { cookies } from "next/headers";

import type { Exhibition } from "@/lib/types";

import { api } from "@/lib/api";

export const EXHIBITION_COOKIE = "gememis_exhibition";

export const listExhibitions = cache(() => api<Exhibition[]>("/exhibitions"));

/**
 * Most pages work inside one exhibition. The choice lives in a cookie set by
 * the switcher in the top bar; with none, the first open exhibition wins.
 */
export const currentExhibition = cache(async (override?: string | number): Promise<Exhibition | null> => {
  const all = await listExhibitions();
  const wanted = Number(override ?? (await cookies()).get(EXHIBITION_COOKIE)?.value);

  return (
    all.find((e) => e.id === wanted) ??
    all.find((e) => e.status === "published") ??
    all.find((e) => !["completed", "cancelled"].includes(e.status)) ??
    all[0] ??
    null
  );
});
