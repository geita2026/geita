/** @type {import('next').NextConfig} */
const nextConfig = {
  output: "export",        // produces a static site in ./out
  basePath: "/timetable",  // served at https://tradefair.tantrade.go.tz/timetable/
  trailingSlash: true,
  images: { unoptimized: true },
};

export default nextConfig;
