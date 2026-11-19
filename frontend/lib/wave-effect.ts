"use client";

import type { DistortionWaveOptions } from "distortion-wave";

/**
 * distortion-wave (MIT, Lucas Bonomi — github.com/LukyVj/distortion-wave):
 * snapshots an element, lets `update` change the DOM, and carries the old
 * state into the new one with a WebGL2 ripple. Same model as
 * document.startViewTransition().
 *
 * `update` always runs exactly once — with the ripple, or without it when the
 * wave is switched off in Settings, motion is reduced (the app's setting or
 * the computer's), WebGL2 is missing, or the library fails to load.
 */

/** The brand: black into gold. */
const GOLD = ["#111114", "#3f2d05", "#a16207", "#eab308", "#fde047"];

export function waveEnabled() {
  if (typeof document === "undefined") return false;
  const { wave, motion } = document.documentElement.dataset;

  return wave !== "off" && motion !== "reduce" && !window.matchMedia("(prefers-reduced-motion: reduce)").matches;
}

export async function playWave(
  target: Element | null | undefined,
  update: () => void | Promise<void>,
  options: DistortionWaveOptions = {},
): Promise<void> {
  let ran = false;
  const once = async () => {
    if (ran) return;
    ran = true;
    await update();
  };

  if (!target || !waveEnabled()) return once();

  try {
    // Loaded on first use: the effect costs nothing until someone triggers it.
    const { distortionWave } = await import("distortion-wave");

    await distortionWave(target, once, { palette: GOLD, ...options }).updateCallbackDone;
  } catch {
    await once();
  }
}

/** Resolves on the next painted frame — after React and next-themes have applied a change. */
export function nextFrame(): Promise<void> {
  return new Promise((resolve) => requestAnimationFrame(() => requestAnimationFrame(() => resolve())));
}
