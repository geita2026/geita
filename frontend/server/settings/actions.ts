"use server";

import { cookies } from "next/headers";
import { revalidatePath } from "next/cache";

import { ACCENT_COOKIE, accentOr } from "@/lib/domain/accents";
import { MOTION_COOKIE, motionOr } from "@/lib/domain/motion";
import { WAVE_COOKIE, waveOr } from "@/lib/domain/wave";
import { failure, type ActionResult } from "@/server/action-result";

/**
 * The accent is a display preference, so it lives in a cookie rather than the
 * database: the root layout reads it during the server render, which means the
 * right colour is in the HTML from the first paint (no flash of the default).
 */
export async function setAccentAction(key: string): Promise<ActionResult> {
  try {
    const jar = await cookies();

    jar.set(ACCENT_COOKIE, accentOr(key), {
      path: "/",
      maxAge: 60 * 60 * 24 * 365,
      sameSite: "lax",
    });
    revalidatePath("/", "layout");

    return { ok: true, message: "Accent saved" };
  } catch (err) {
    return failure(err);
  }
}

/** Motion preference — a display setting like the accent (lib/domain/motion.ts). */
export async function setMotionAction(pref: string): Promise<ActionResult> {
  try {
    const jar = await cookies();

    jar.set(MOTION_COOKIE, motionOr(pref), { path: "/", maxAge: 60 * 60 * 24 * 365, sameSite: "lax" });
    revalidatePath("/", "layout");

    return { ok: true };
  } catch (err) {
    return failure(err);
  }
}

/** Distortion wave on or off — a display setting like motion (lib/domain/wave.ts). */
export async function setWaveAction(pref: string): Promise<ActionResult> {
  try {
    const jar = await cookies();

    jar.set(WAVE_COOKIE, waveOr(pref), { path: "/", maxAge: 60 * 60 * 24 * 365, sameSite: "lax" });
    revalidatePath("/", "layout");

    return { ok: true };
  } catch (err) {
    return failure(err);
  }
}
