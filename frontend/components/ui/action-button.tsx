"use client";

import { toast } from "@heroui/react";

import { call, type MutationSpec } from "@/server/actions";
import { AsyncButton } from "@/components/ui/live-button";

/** A button that runs one API call and shows its outcome on itself (live-button.tsx). */
export function ActionButton({
  spec,
  children,
  pendingLabel = "Working…",
  successLabel = "Done",
  variant = "primary",
  size = "md",
  className,
}: {
  spec: MutationSpec;
  children: React.ReactNode;
  pendingLabel?: string;
  successLabel?: string;
  variant?: "primary" | "secondary" | "tertiary" | "danger" | "ghost";
  size?: "sm" | "md" | "lg";
  className?: string;
}) {
  return (
    <AsyncButton
      className={className}
      pendingLabel={pendingLabel}
      run={async () => {
        const r = await call(spec);

        if (!r.ok) toast(r.error, { variant: "danger" });
        else if (r.message) toast(r.message, { variant: "success" });

        return r as { ok: boolean } as never;
      }}
      size={size}
      successLabel={successLabel}
      variant={variant}
    >
      {children}
    </AsyncButton>
  );
}
