"use client";

import clsx from "clsx";

import type { MutationSpec } from "@/server/actions";

import {
  MorphingDialog,
  MorphingDialogClose,
  MorphingDialogContainer,
  MorphingDialogContent,
  MorphingDialogDescription,
  MorphingDialogSubtitle,
  MorphingDialogTitle,
  MorphingDialogTrigger,
  useMorphingDialog,
} from "@/components/core/morphing-dialog";
import { ActionForm } from "@/components/ui/action-form";

const transition = { type: "spring", stiffness: 220, damping: 26 } as const;

const TRIGGER = {
  primary: "bg-brand text-brand-foreground hover:brightness-105",
  secondary: "bg-default text-foreground hover:bg-default-hover",
  danger: "bg-danger-soft text-danger hover:bg-danger/20",
  ghost: "text-muted hover:bg-default hover:text-foreground",
} as const;

/**
 * A button that morphs into a dialog holding a form — the app's one way to
 * create or change a record without leaving the page. The button and the
 * dialog are the same element to the eye (shared layout animation), so the
 * form visibly comes out of the thing you pressed, and goes back into it.
 */
export function MorphForm({
  label,
  icon,
  title,
  subtitle,
  spec,
  children,
  submitLabel,
  pendingLabel,
  variant = "primary",
  size = "md",
  width = "max-w-xl",
  formVariant,
  triggerClassName,
}: {
  label: React.ReactNode;
  icon?: React.ReactNode;
  title: string;
  subtitle?: React.ReactNode;
  spec: MutationSpec;
  children?: React.ReactNode;
  submitLabel?: string;
  pendingLabel?: string;
  variant?: keyof typeof TRIGGER;
  size?: "sm" | "md";
  width?: string;
  formVariant?: "primary" | "danger";
  triggerClassName?: string;
}) {
  return (
    <MorphingDialog transition={transition}>
      <MorphingDialogTrigger
        className={
          triggerClassName ??
          clsx(
            "inline-flex items-center justify-center gap-2 font-semibold whitespace-nowrap transition-[filter,background-color]",
            size === "sm" ? "h-8 px-3 text-xs" : "h-10 px-4 text-sm",
            TRIGGER[variant],
          )
        }
        style={{ borderRadius: size === "sm" ? 10 : 12 }}
      >
        {icon}
        <MorphingDialogTitle>{label}</MorphingDialogTitle>
      </MorphingDialogTrigger>
      <MorphingDialogContainer>
        <MorphingDialogContent
          className={clsx(
            "relative flex max-h-[90vh] w-full flex-col overflow-y-auto border border-separator bg-overlay p-6 shadow-2xl",
            width,
          )}
          style={{ borderRadius: 20 }}
        >
          <MorphingDialogTitle className="pe-10 font-display text-xl font-bold leading-tight">
            {title}
          </MorphingDialogTitle>
          {subtitle && <MorphingDialogSubtitle className="mt-1 text-sm text-muted">{subtitle}</MorphingDialogSubtitle>}
          <MorphingDialogDescription disableLayoutAnimation className="mt-5">
            <Body
              formVariant={formVariant ?? (variant === "danger" ? "danger" : "primary")}
              pendingLabel={pendingLabel}
              spec={spec}
              submitLabel={submitLabel}
            >
              {children}
            </Body>
          </MorphingDialogDescription>
          <MorphingDialogClose />
        </MorphingDialogContent>
      </MorphingDialogContainer>
    </MorphingDialog>
  );
}

function Body({
  spec,
  children,
  submitLabel,
  pendingLabel,
  formVariant,
}: {
  spec: MutationSpec;
  children: React.ReactNode;
  submitLabel?: string;
  pendingLabel?: string;
  formVariant: "primary" | "danger";
}) {
  const { setIsOpen } = useMorphingDialog();

  return (
    <ActionForm
      pendingLabel={pendingLabel}
      spec={spec}
      submitLabel={submitLabel}
      variant={formVariant}
      // Let the button show its tick before the dialog folds away.
      onDone={() => setTimeout(() => setIsOpen(false), 650)}
    >
      {children}
    </ActionForm>
  );
}

/** A card (or row) that morphs into a read-only detail dialog. */
export function MorphCard({
  trigger,
  triggerClassName,
  radius = 16,
  title,
  subtitle,
  header,
  children,
  width = "max-w-2xl",
}: {
  /** What the closed card shows, apart from the title and subtitle. */
  trigger: (parts: { title: React.ReactNode; subtitle: React.ReactNode }) => React.ReactNode;
  triggerClassName?: string;
  radius?: number;
  title: React.ReactNode;
  subtitle?: React.ReactNode;
  /** Chips above the title in the open dialog. */
  header?: React.ReactNode;
  children: React.ReactNode;
  width?: string;
}) {
  return (
    <MorphingDialog transition={transition}>
      <MorphingDialogTrigger
        className={
          triggerClassName ??
          "flex h-full w-full flex-col gap-3 rounded-2xl border border-separator bg-surface p-4 shadow-surface transition-shadow hover:shadow-lg"
        }
        style={{ borderRadius: radius }}
      >
        {trigger({
          title: (
            <MorphingDialogTitle className="line-clamp-2 font-display text-base font-semibold leading-snug">
              {title}
            </MorphingDialogTitle>
          ),
          subtitle: subtitle ? (
            <MorphingDialogSubtitle className="text-xs text-muted">{subtitle}</MorphingDialogSubtitle>
          ) : null,
        })}
      </MorphingDialogTrigger>
      <MorphingDialogContainer>
        <MorphingDialogContent
          className={clsx(
            "relative flex max-h-[90vh] w-full flex-col overflow-y-auto border border-separator bg-overlay p-6 shadow-2xl",
            width,
          )}
          style={{ borderRadius: 20 }}
        >
          {header && <div className="flex flex-wrap items-center gap-2 pe-10">{header}</div>}
          <MorphingDialogTitle
            className={clsx("pe-10 font-display text-2xl font-semibold leading-tight", header && "mt-3")}
          >
            {title}
          </MorphingDialogTitle>
          {subtitle && <MorphingDialogSubtitle className="mt-1 text-sm text-muted">{subtitle}</MorphingDialogSubtitle>}
          <MorphingDialogDescription disableLayoutAnimation className="mt-5 grid gap-5">
            {children}
          </MorphingDialogDescription>
          <MorphingDialogClose />
        </MorphingDialogContent>
      </MorphingDialogContainer>
    </MorphingDialog>
  );
}
