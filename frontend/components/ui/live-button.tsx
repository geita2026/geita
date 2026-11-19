"use client";

import { useCallback, useEffect, useRef, useState } from "react";
import { useFormStatus } from "react-dom";
import { Button } from "@heroui/react";
import clsx from "clsx";

import type { ActionResult } from "@/server/action-result";

/**
 * A button that shows what is happening to the action behind it:
 *
 *   idle → pending (dots, shimmer) → success (green, drawn tick) | error (red, shake) → idle
 *
 * Every phase is stacked in the same grid cell (styles/globals.css, `.live-btn`),
 * so the button keeps the width of its widest phase and never jumps. Motion is
 * dropped under `prefers-reduced-motion`; colour and text still change. Each
 * phase is announced to screen readers through a polite live region.
 */
export type LivePhase = "idle" | "pending" | "success" | "error";

const HOLD = { success: 1600, error: 2200 } as const;

type Variant = "primary" | "secondary" | "tertiary" | "danger" | "ghost";

type Common = {
  children: React.ReactNode;
  pendingLabel?: React.ReactNode;
  successLabel?: React.ReactNode;
  errorLabel?: React.ReactNode;
  variant?: Variant;
  size?: "sm" | "md" | "lg";
  className?: string;
  isDisabled?: boolean;
  "aria-label"?: string;
};

function Dots() {
  return (
    <span aria-hidden className="live-dots">
      <span />
      <span />
      <span />
    </span>
  );
}

function Tick() {
  return (
    <svg aria-hidden className="live-tick" height="16" viewBox="0 0 24 24" width="16">
      <path d="M4 12.5l5 5L20 7" />
    </svg>
  );
}

function Cross() {
  return (
    <svg aria-hidden className="live-cross" height="14" viewBox="0 0 24 24" width="14">
      <path d="M6 6l12 12M18 6L6 18" />
    </svg>
  );
}

/** Text a phase announces; plain strings only (labels may be nodes). */
const said = (node: React.ReactNode, fallback: string) => (typeof node === "string" ? node : fallback);

export function LiveButton({
  phase,
  children,
  pendingLabel,
  successLabel = "Done",
  errorLabel = "Failed",
  variant = "primary",
  size = "md",
  className,
  isDisabled,
  type = "button",
  onPress,
  "aria-label": ariaLabel,
}: Common & { phase: LivePhase; type?: "button" | "submit"; onPress?: () => void }) {
  const layer = (p: LivePhase, content: React.ReactNode) => (
    <span aria-hidden={phase !== p} className="live-layer" data-for={p}>
      {content}
    </span>
  );

  return (
    <>
      <Button
        aria-busy={phase === "pending"}
        aria-label={ariaLabel}
        className={clsx("live-btn", className)}
        data-phase={phase}
        isDisabled={isDisabled}
        // Blocks a second press while the first is running, without the disabled look.
        isPending={phase === "pending"}
        size={size}
        type={type}
        variant={variant}
        onPress={onPress}
      >
        {layer("idle", children)}
        {layer(
          "pending",
          <>
            <Dots />
            {pendingLabel}
          </>,
        )}
        {layer(
          "success",
          <>
            <Tick />
            {successLabel}
          </>,
        )}
        {layer(
          "error",
          <>
            <Cross />
            {errorLabel}
          </>,
        )}
      </Button>
      <span aria-live="polite" className="sr-only" role="status">
        {phase === "pending"
          ? said(pendingLabel, "Working…")
          : phase === "success"
            ? said(successLabel, "Done")
            : phase === "error"
              ? said(errorLabel, "Failed")
              : ""}
      </span>
    </>
  );
}

/** Holds success/error for a moment, then returns to idle. */
function useSettle() {
  const [phase, setPhase] = useState<LivePhase>("idle");
  const timer = useRef<ReturnType<typeof setTimeout>>(undefined);

  useEffect(() => () => clearTimeout(timer.current), []);

  const settle = useCallback((ok: boolean) => {
    clearTimeout(timer.current);
    const p = ok ? "success" : "error";

    setPhase(p);
    timer.current = setTimeout(() => setPhase("idle"), HOLD[p]);
  }, []);

  const start = useCallback(() => {
    clearTimeout(timer.current);
    setPhase("pending");
  }, []);

  return { phase, settle, start };
}

/**
 * Submit button for a form driven by `useActionState`. Pass the action's state
 * as `result` and the button plays success or error when the submission ends;
 * without it the button shows the pending phase only.
 */
export function SubmitButton({ result, ...props }: Common & { result?: ActionResult }) {
  const { pending } = useFormStatus();
  const { phase, settle, start } = useSettle();
  const was = useRef(false);

  useEffect(() => {
    if (pending && !was.current) start();
    if (!pending && was.current) {
      if (result) settle(result.ok);
      else settle(true);
    }
    was.current = pending;
  }, [pending, result, settle, start]);

  return <LiveButton {...props} phase={pending ? "pending" : phase === "pending" ? "idle" : phase} type="submit" />;
}

/**
 * Button for an action run on press. `run` may return an ActionResult (its
 * `ok` decides success or error) or nothing (success unless it throws). The
 * caller still shows any message — the button shows the outcome.
 */
export function AsyncButton({ run, ...props }: Common & { run: () => Promise<ActionResult<unknown> | void> }) {
  const { phase, settle, start } = useSettle();

  return (
    <LiveButton
      {...props}
      phase={phase}
      onPress={async () => {
        if (phase === "pending") return;
        start();
        try {
          const r = await run();

          settle(r ? r.ok : true);
        } catch {
          settle(false);
        }
      }}
    />
  );
}
