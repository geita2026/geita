import "server-only";

import { cache } from "react";

import { api } from "@/lib/api";

/** The API's country list (backend/config/countries.php) — the one the API validates against. */
export const listCountries = cache(
  async () => (await api<{ countries: string[] }>("/meta", { anonymous: true })).countries,
);
