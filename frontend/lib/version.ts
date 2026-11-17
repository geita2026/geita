/**
 * The running build, stamped by next.config.ts: `version` from package.json,
 * `build` the git commit count (rises with every commit), `commit` the short
 * hash (empty outside git), `built` the build date.
 */
export const appVersion = {
  version: process.env.NEXT_PUBLIC_APP_VERSION ?? "0.0.0",
  build: process.env.NEXT_PUBLIC_APP_BUILD ?? "0",
  commit: process.env.NEXT_PUBLIC_APP_COMMIT ?? "",
  built: process.env.NEXT_PUBLIC_APP_BUILT ?? "",
};

/** "v0.1.0 (42)" — the Android-style name + code. */
export const versionLabel = `v${appVersion.version} (${appVersion.build})`;
