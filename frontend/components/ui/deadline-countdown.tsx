"use client";

import { useEffect, useState, useSyncExternalStore } from "react";
import { Clock, TriangleExclamation, Check, Ban } from "@gravity-ui/icons";
import clsx from "clsx";

import { fmtDate } from "@/lib/domain/format";

interface DeadlineCountdownProps {
  deadline: Date | string | null | undefined;
  /** The thing being counted down to has closed: no countdown, just the outcome. */
  status?: "paid" | "cancelled";
  variant?: "boxes" | "badge" | "mini" | "inline";
  className?: string;
  showSeconds?: boolean;
  /** Heading of the boxed view while time remains. */
  label?: string;
}

/**
 * daisyUI `countdown`: live sliding digits for days, hours, minutes and seconds.
 * Brought over from the TANTRADE Project Tracker's project-deadline countdown;
 * here it counts down to a GePG control number's expiry.
 */
// `Date.now()` differs between the server render and hydration, so the live
// values can only be shown once we are on the client. Same idiom as ThemeSwitch.
const subscribe = () => () => {};
const useMounted = () =>
  useSyncExternalStore(
    subscribe,
    () => true,
    () => false,
  );

export function DeadlineCountdown({
  deadline,
  status,
  variant = "badge",
  className,
  showSeconds = true,
  label = "Time remaining",
}: DeadlineCountdownProps) {
  const mounted = useMounted();
  const [now, setNow] = useState(() => Date.now());

  useEffect(() => {
    // Tick every second for live countdown animation
    const timer = setInterval(() => {
      setNow(Date.now());
    }, 1000);

    return () => clearInterval(timer);
  }, []);

  if (!deadline) {
    return <span className={clsx("text-xs text-muted", className)}>No expiry set</span>;
  }

  if (status === "paid") {
    return (
      <span
        className={clsx(
          "inline-flex items-center gap-1.5 rounded-lg border border-success/30 bg-success/10 px-2 py-1 text-xs font-semibold text-success",
          className,
        )}
      >
        <Check className="size-3.5" /> Paid
      </span>
    );
  }

  if (status === "cancelled") {
    return (
      <span
        className={clsx(
          "inline-flex items-center gap-1.5 rounded-lg border border-danger/30 bg-danger/10 px-2 py-1 text-xs font-semibold text-danger",
          className,
        )}
      >
        <Ban className="size-3.5" /> Cancelled
      </span>
    );
  }

  const targetDate = typeof deadline === "string" ? new Date(deadline) : deadline;

  // Until hydration, render the due date itself — deterministic on both sides.
  if (!mounted) {
    return <span className={clsx("font-mono text-xs text-muted tabular", className)}>{fmtDate(targetDate)}</span>;
  }

  const total = targetDate.getTime() - now;
  const isOverdue = total < 0;
  const abs = Math.abs(total);

  const days = Math.floor(abs / (1000 * 60 * 60 * 24));
  const hours = Math.floor((abs / (1000 * 60 * 60)) % 24);
  const minutes = Math.floor((abs / (1000 * 60)) % 60);
  const seconds = Math.floor((abs / 1000) % 60);

  const isDueSoon = !isOverdue && days <= 7;
  const isCritical = !isOverdue && days === 0;

  // Boxed counter view (ideal for project header, morphing dialog, and detail view)
  if (variant === "boxes") {
    return (
      <div className={clsx("flex flex-col gap-2", className)}>
        <div className="flex items-center gap-1.5 text-xs font-semibold">
          {isOverdue ? (
            <span className="inline-flex items-center gap-1 text-danger">
              <TriangleExclamation className="size-3.5" /> Expired — a new expiry date is set the next morning
            </span>
          ) : isCritical ? (
            <span className="inline-flex items-center gap-1 text-danger">
              <Clock className="size-3.5" /> Expires today!
            </span>
          ) : isDueSoon ? (
            <span className="inline-flex items-center gap-1 text-warning">
              <Clock className="size-3.5" /> Expires soon
            </span>
          ) : (
            <span className="inline-flex items-center gap-1 text-muted">
              <Clock className="size-3.5" /> {label}
            </span>
          )}
        </div>

        <div className="flex items-center gap-1.5 sm:gap-2">
          {/* Days */}
          <div
            className={clsx(
              "flex min-w-[50px] flex-col items-center justify-center rounded-xl border p-2 text-center transition-colors shadow-xs",
              isOverdue
                ? "border-danger/40 bg-danger-soft/70"
                : isDueSoon
                  ? "border-warning/40 bg-warning-soft/70"
                  : "border-separator bg-surface",
            )}
          >
            <span
              className={clsx(
                "countdown font-mono text-xl font-bold leading-none",
                isOverdue ? "text-danger" : isDueSoon ? "text-warning" : "text-foreground",
              )}
            >
              <span style={{ "--value": Math.min(999, days) } as React.CSSProperties}>{days}</span>
            </span>
            <span className="mt-1 text-[10px] font-bold uppercase tracking-wider text-muted">days</span>
          </div>

          <span className="font-mono text-base font-bold text-muted/60">:</span>

          {/* Hours */}
          <div
            className={clsx(
              "flex min-w-[50px] flex-col items-center justify-center rounded-xl border p-2 text-center transition-colors shadow-xs",
              isOverdue
                ? "border-danger/40 bg-danger-soft/70"
                : isDueSoon
                  ? "border-warning/40 bg-warning-soft/70"
                  : "border-separator bg-surface",
            )}
          >
            <span
              className={clsx(
                "countdown font-mono text-xl font-bold leading-none",
                isOverdue ? "text-danger" : isDueSoon ? "text-warning" : "text-foreground",
              )}
            >
              <span style={{ "--value": hours } as React.CSSProperties}>{hours}</span>
            </span>
            <span className="mt-1 text-[10px] font-bold uppercase tracking-wider text-muted">hours</span>
          </div>

          <span className="font-mono text-base font-bold text-muted/60">:</span>

          {/* Minutes */}
          <div
            className={clsx(
              "flex min-w-[50px] flex-col items-center justify-center rounded-xl border p-2 text-center transition-colors shadow-xs",
              isOverdue
                ? "border-danger/40 bg-danger-soft/70"
                : isDueSoon
                  ? "border-warning/40 bg-warning-soft/70"
                  : "border-separator bg-surface",
            )}
          >
            <span
              className={clsx(
                "countdown font-mono text-xl font-bold leading-none",
                isOverdue ? "text-danger" : isDueSoon ? "text-warning" : "text-foreground",
              )}
            >
              <span style={{ "--value": minutes } as React.CSSProperties}>{minutes}</span>
            </span>
            <span className="mt-1 text-[10px] font-bold uppercase tracking-wider text-muted">min</span>
          </div>

          {showSeconds && (
            <>
              <span className="font-mono text-base font-bold text-muted/60">:</span>
              {/* Seconds */}
              <div
                className={clsx(
                  "flex min-w-[50px] flex-col items-center justify-center rounded-xl border p-2 text-center transition-colors shadow-xs",
                  isOverdue
                    ? "border-danger/40 bg-danger-soft/70"
                    : isDueSoon
                      ? "border-warning/40 bg-warning-soft/70"
                      : "border-separator bg-surface",
                )}
              >
                <span
                  className={clsx(
                    "countdown font-mono text-xl font-bold leading-none",
                    isOverdue ? "text-danger" : "text-brand-ink",
                  )}
                >
                  <span style={{ "--value": seconds } as React.CSSProperties}>{seconds}</span>
                </span>
                <span className="mt-1 text-[10px] font-bold uppercase tracking-wider text-muted">sec</span>
              </div>
            </>
          )}
        </div>
      </div>
    );
  }

  // Mini / super compact view (for table cells and dense lists)
  if (variant === "mini") {
    return (
      <span
        className={clsx(
          "inline-flex items-center gap-1 font-mono text-xs tabular font-medium",
          isOverdue
            ? "text-danger"
            : isCritical
              ? "text-danger font-semibold"
              : isDueSoon
                ? "text-warning font-semibold"
                : "text-muted",
          className,
        )}
      >
        {isOverdue ? (
          <span>
            -
            <span className="countdown">
              <span style={{ "--value": Math.min(999, days) } as React.CSSProperties}>{days}</span>
            </span>
            d
          </span>
        ) : (
          <span>
            <span className="countdown">
              <span style={{ "--value": Math.min(999, days) } as React.CSSProperties}>{days}</span>
            </span>
            d{" "}
            <span className="countdown">
              <span style={{ "--value": hours } as React.CSSProperties}>{hours}</span>
            </span>
            h
          </span>
        )}
      </span>
    );
  }

  // Inline view
  if (variant === "inline") {
    return (
      <span
        className={clsx(
          "inline-flex items-center gap-1 font-mono text-xs tabular",
          isOverdue ? "text-danger font-semibold" : "text-foreground",
          className,
        )}
      >
        {isOverdue && <TriangleExclamation className="size-3 text-danger" />}
        <span className="countdown">
          <span style={{ "--value": Math.min(999, days) } as React.CSSProperties}>{days}</span>
        </span>
        <span className="text-muted">d</span>
        <span className="countdown">
          <span style={{ "--value": hours } as React.CSSProperties}>{hours}</span>
        </span>
        <span className="text-muted">h</span>
        <span className="countdown">
          <span style={{ "--value": minutes } as React.CSSProperties}>{minutes}</span>
        </span>
        <span className="text-muted">m</span>
        {showSeconds && (
          <>
            <span className="countdown">
              <span style={{ "--value": seconds } as React.CSSProperties}>{seconds}</span>
            </span>
            <span className="text-muted">s</span>
          </>
        )}
        {isOverdue ? " expired" : " remaining"}
      </span>
    );
  }

  // Default "badge" view (for project cards and tiles)
  return (
    // A span, not a div: the badge sits inside sentences and paragraphs.
    <span
      className={clsx(
        "inline-flex items-center gap-1.5 rounded-lg border px-2.5 py-1 text-xs font-medium transition-colors shadow-xs",
        isOverdue
          ? "border-danger/40 bg-danger-soft/80 text-danger"
          : isCritical
            ? "border-danger/40 bg-danger-soft/60 text-danger"
            : isDueSoon
              ? "border-warning/40 bg-warning-soft/60 text-warning"
              : "border-separator bg-default/70 text-foreground",
        className,
      )}
    >
      {isOverdue ? (
        <TriangleExclamation className="size-3.5 shrink-0 text-danger" />
      ) : (
        <Clock className="size-3.5 shrink-0 text-muted" />
      )}

      {isOverdue ? (
        <span className="font-mono text-xs font-semibold tabular">
          Expired{" "}
          <span className="countdown">
            <span style={{ "--value": Math.min(999, days) } as React.CSSProperties}>{days}</span>
          </span>
          d{" "}
          <span className="countdown">
            <span style={{ "--value": hours } as React.CSSProperties}>{hours}</span>
          </span>
          h ago
        </span>
      ) : (
        <span className="inline-flex items-center gap-0.5 font-mono text-xs font-semibold tabular">
          {days > 0 && (
            <>
              <span className="countdown">
                <span style={{ "--value": Math.min(999, days) } as React.CSSProperties}>{days}</span>
              </span>
              <span className="mr-1 font-sans text-[11px] font-normal text-muted">d</span>
            </>
          )}
          <span className="countdown">
            <span style={{ "--value": hours } as React.CSSProperties}>{hours}</span>
          </span>
          <span className="mr-1 font-sans text-[11px] font-normal text-muted">h</span>
          <span className="countdown">
            <span style={{ "--value": minutes } as React.CSSProperties}>{minutes}</span>
          </span>
          <span className="font-sans text-[11px] font-normal text-muted">m</span>
          {showSeconds && (
            <>
              <span className="countdown">
                <span style={{ "--value": seconds } as React.CSSProperties}>{seconds}</span>
              </span>
              <span className="font-sans text-[11px] font-normal text-muted">s</span>
            </>
          )}
          <span className="ml-1 font-sans text-[11px] font-normal text-muted">left</span>
        </span>
      )}
    </span>
  );
}
