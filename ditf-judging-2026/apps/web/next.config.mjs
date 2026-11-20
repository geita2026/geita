// basePath is env-driven so the same code runs at "/" locally and "/award" in prod.
const basePath = process.env.NEXT_PUBLIC_BASE_PATH || "";

/** @type {import('next').NextConfig} */
const nextConfig = {
  reactStrictMode: true,
  output: "export", // emit a static site into ./out (served by nginx under basePath)
  trailingSlash: true, // clean folder URLs (/award/judge/) map to .../index.html
  images: { unoptimized: true }, // no Next image server in a static export
  ...(basePath ? { basePath } : {}),
};

export default nextConfig;
