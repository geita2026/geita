/**
 * Motion preference. "system" follows the computer's own reduce-motion setting
 * (prefers-reduced-motion); "reduce" calms the portal whatever the computer
 * says. Stored in a cookie and read in app/layout.tsx, like the accent, so the
 * first paint is already right.
 */
export const MOTION_COOKIE = "motion";

export type MotionPref = "system" | "reduce";

export const motionOr = (value: string | undefined): MotionPref => (value === "reduce" ? "reduce" : "system");
