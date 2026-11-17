/** Copies the list filters the API understands from the page's query string. */
export function apiQuery(
  sp: Record<string, string | undefined>,
  keys: string[],
  extra: Record<string, string | number | undefined> = {},
) {
  const out: Record<string, string | number | undefined> = { page: sp.page, ...extra };

  for (const k of keys) if (sp[k]) out[k] = sp[k];

  return out;
}

export function toParams(sp: Record<string, string | undefined>) {
  const p = new URLSearchParams();

  for (const [k, v] of Object.entries(sp)) if (v) p.set(k, v);

  return p;
}
