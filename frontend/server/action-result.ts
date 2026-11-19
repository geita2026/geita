/** Uniform return type for server actions used with `useActionState`. */
export type ActionResult<T = undefined> =
  { ok: true; data?: T; message?: string } | { ok: false; error: string; fieldErrors?: Record<string, string[]> };

export const initialActionState: ActionResult = { ok: false, error: "" };

/** Turn a thrown error into a user-facing ActionResult. */
export function failure(err: unknown, fallback = "Something went wrong. Please try again."): ActionResult {
  if (err instanceof Error && err.name === "ForbiddenError") return { ok: false, error: err.message };
  if (err instanceof Error && err.name === "ValidationError") return { ok: false, error: err.message };

  console.error(err);

  return { ok: false, error: fallback };
}
