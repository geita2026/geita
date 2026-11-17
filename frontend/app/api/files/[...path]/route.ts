import { type NextRequest } from "next/server";

import { apiUrl, authHeader } from "@/lib/api";

/**
 * Downloads (PDFs, CSV reports, uploaded documents) are streamed through the
 * Next server so the bearer token stays out of the browser. Only document-like
 * API paths are reachable; the API still checks the caller may see the file.
 */
const ALLOWED = [
  /^documents\/\d+$/,
  /^payments\/\d+\/(evidence|receipt)$/,
  /^invoices\/\d+\/pdf$/,
  /^applications\/\d+\/allocation-letter$/,
  /^exhibitions\/\d+\/schedule$/,
  /^exhibitions\/\d+\/reports\/[a-z-]+$/,
];

export async function GET(req: NextRequest, { params }: { params: Promise<{ path: string[] }> }) {
  const path = (await params).path.join("/");

  if (!ALLOWED.some((re) => re.test(path))) return new Response("Not found", { status: 404 });

  const url = apiUrl(`/${path}`);

  req.nextUrl.searchParams.forEach((v, k) => url.searchParams.set(k, v));
  const upstream = await fetch(url, {
    headers: { Accept: "application/json", ...(await authHeader()) },
    cache: "no-store",
  });

  if (!upstream.ok) {
    const body = await upstream.json().catch(() => ({}));

    return new Response(body.message ?? "This file is not available.", { status: upstream.status });
  }

  const headers = new Headers();

  for (const h of ["content-type", "content-disposition", "content-length"]) {
    const v = upstream.headers.get(h);

    if (v) headers.set(h, v);
  }
  headers.set("Cache-Control", "private, no-store");

  return new Response(upstream.body, { headers });
}
