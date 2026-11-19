/**
 * The distortion wave (lib/wave-effect.ts) is a display preference, like the
 * accent and motion: stored per browser in a cookie and read in app/layout.tsx,
 * so the first paint already knows. On unless switched off in Settings.
 */
export const WAVE_COOKIE = "gememis_wave";

export type WavePref = "on" | "off";

export const waveOr = (value: string | undefined): WavePref => (value === "off" ? "off" : "on");
