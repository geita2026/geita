import type { NextConfig } from "next";
import { execSync } from "node:child_process";

import pkg from "./package.json";

const isDev = process.env.NODE_ENV !== "production";

/**
 * Security headers on every response. Inline scripts stay allowed because
 * Next.js and next-themes emit them without a nonce; everything else is locked
 * to this origin — the browser never talks to the Laravel API directly, the
 * Next server does (lib/api.ts), so `connect-src 'self'` holds.
 */
const csp = [
  "default-src 'self'",
  `script-src 'self' 'unsafe-inline'${isDev ? " 'unsafe-eval'" : ""}`,
  "style-src 'self' 'unsafe-inline'",
  "img-src 'self' data: blob:",
  "font-src 'self' data:",
  `connect-src 'self'${isDev ? " ws: wss:" : ""}`,
  "object-src 'none'",
  "base-uri 'self'",
  "form-action 'self'",
  "frame-ancestors 'none'",
].join("; ");

const securityHeaders = [
  { key: "Content-Security-Policy", value: csp },
  { key: "X-Content-Type-Options", value: "nosniff" },
  { key: "X-Frame-Options", value: "DENY" },
  { key: "Referrer-Policy", value: "strict-origin-when-cross-origin" },
  { key: "Permissions-Policy", value: "camera=(), microphone=(), geolocation=(), payment=()" },
  ...(isDev ? [] : [{ key: "Strict-Transport-Security", value: "max-age=31536000; includeSubDomains" }]),
];

function git(cmd: string) {
  try {
    return execSync(`git ${cmd}`, { stdio: ["ignore", "pipe", "ignore"] })
      .toString()
      .trim();
  } catch {
    return "";
  }
}

const nextConfig: NextConfig = {
  poweredByHeader: false,
  allowedDevOrigins: (process.env.DEV_ORIGINS ?? "").split(",").filter(Boolean),
  env: {
    NEXT_PUBLIC_APP_VERSION: pkg.version,
    NEXT_PUBLIC_APP_BUILD: git("rev-list --count HEAD") || "0",
    NEXT_PUBLIC_APP_COMMIT: git("rev-parse --short HEAD"),
    NEXT_PUBLIC_APP_BUILT: new Date().toISOString().slice(0, 10),
  },
  // Supporting documents and payment evidence go through server actions.
  experimental: { serverActions: { bodySizeLimit: "12mb" } },
  async headers() {
    return [{ source: "/:path*", headers: securityHeaders }];
  },
};

export default nextConfig;
