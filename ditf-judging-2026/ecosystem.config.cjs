// pm2 process definitions for the DITF Judging app (run from the project root on the server).
//   pm2 startOrReload ecosystem.config.cjs --update-env
//
// The web frontend is a STATIC export (apps/web → ./out) served directly by nginx
// under /award/, so there is no Next.js Node process. Only the Express API runs here.
module.exports = {
  apps: [
    {
      name: "award-api",
      cwd: "apps/api",
      script: "dist/index.js", // built by `npm run build --workspace api`
      env: { NODE_ENV: "production" }, // PORT etc. come from apps/api/.env
      max_restarts: 10,
    },
  ],
};
