"use client";

/**
 * Morphing Dialog — adapted from motion-primitives (MIT, ibelick/motion-primitives).
 * A trigger element morphs (shared layout animation) into a centred dialog.
 */
import React, { useCallback, useContext, useEffect, useId, useMemo, useRef, useState } from "react";
import { createPortal } from "react-dom";
import { AnimatePresence, MotionConfig, motion, type Transition, type Variant } from "motion/react";
import clsx from "clsx";
import { Xmark } from "@gravity-ui/icons";

import { useClickOutside } from "@/hooks/use-click-outside";

type Ctx = {
  isOpen: boolean;
  setIsOpen: React.Dispatch<React.SetStateAction<boolean>>;
  uniqueId: string;
  triggerRef: React.RefObject<HTMLButtonElement | null>;
};

const MorphingDialogContext = React.createContext<Ctx | null>(null);

export function useMorphingDialog() {
  const ctx = useContext(MorphingDialogContext);

  if (!ctx) throw new Error("useMorphingDialog must be used within <MorphingDialog>");

  return ctx;
}

export function MorphingDialog({ children, transition }: { children: React.ReactNode; transition?: Transition }) {
  const [isOpen, setIsOpen] = useState(false);
  const uniqueId = useId();
  const triggerRef = useRef<HTMLButtonElement>(null);
  const value = useMemo(() => ({ isOpen, setIsOpen, uniqueId, triggerRef }), [isOpen, uniqueId]);

  return (
    <MorphingDialogContext.Provider value={value}>
      <MotionConfig transition={transition}>{children}</MotionConfig>
    </MorphingDialogContext.Provider>
  );
}

export function MorphingDialogTrigger({
  children,
  className,
  style,
}: {
  children: React.ReactNode;
  className?: string;
  style?: React.CSSProperties;
}) {
  const { setIsOpen, isOpen, uniqueId, triggerRef } = useMorphingDialog();

  return (
    <motion.button
      ref={triggerRef}
      aria-controls={`morphing-dialog-content-${uniqueId}`}
      aria-expanded={isOpen}
      aria-haspopup="dialog"
      className={clsx("relative cursor-pointer text-left", className)}
      layoutId={`dialog-${uniqueId}`}
      style={style}
      type="button"
      onClick={() => setIsOpen((v) => !v)}
    >
      {children}
    </motion.button>
  );
}

export function MorphingDialogContent({
  children,
  className,
  style,
}: {
  children: React.ReactNode;
  className?: string;
  style?: React.CSSProperties;
}) {
  const { setIsOpen, isOpen, uniqueId, triggerRef } = useMorphingDialog();
  const containerRef = useRef<HTMLDivElement>(null);

  useEffect(() => {
    const onKey = (e: KeyboardEvent) => {
      if (e.key !== "Escape") return;
      setIsOpen(false);
    };

    document.addEventListener("keydown", onKey);

    return () => document.removeEventListener("keydown", onKey);
  }, [setIsOpen]);

  useEffect(() => {
    if (isOpen) {
      document.body.style.overflow = "hidden";
      const first = containerRef.current?.querySelector<HTMLElement>(
        'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])',
      );

      first?.focus();
    } else {
      document.body.style.overflow = "";
      triggerRef.current?.focus();
    }

    return () => {
      document.body.style.overflow = "";
    };
  }, [isOpen, triggerRef]);

  useClickOutside(
    containerRef,
    useCallback(
      (e: MouseEvent | TouchEvent) => {
        /* Only a press on the dialog's own backdrop closes it. Select lists and
           date pickers inside a form are portalled to <body>, outside this
           element — a press on one of those must not dismiss the form. */
        if (!(e.target as Element | null)?.closest?.("[data-morph-layer]")) return;
        if (isOpen) setIsOpen(false);
      },
      [isOpen, setIsOpen],
    ),
  );

  return (
    <motion.div
      ref={containerRef}
      aria-labelledby={`morphing-dialog-title-${uniqueId}`}
      aria-modal="true"
      className={clsx("overflow-hidden", className)}
      id={`morphing-dialog-content-${uniqueId}`}
      layoutId={`dialog-${uniqueId}`}
      role="dialog"
      style={style}
    >
      {children}
    </motion.div>
  );
}

export function MorphingDialogContainer({ children }: { children: React.ReactNode }) {
  const { isOpen, uniqueId } = useMorphingDialog();
  const [mounted, setMounted] = useState(false);

  useEffect(() => {
    // Portal target only exists on the client.
    // eslint-disable-next-line react-hooks/set-state-in-effect
    setMounted(true);
  }, []);

  if (!mounted) return null;

  return createPortal(
    <AnimatePresence initial={false} mode="sync">
      {isOpen && (
        <>
          <motion.div
            key={`backdrop-${uniqueId}`}
            animate={{ opacity: 1 }}
            className="fixed inset-0 z-40 h-full w-full bg-background/60 backdrop-blur-xs"
            exit={{ opacity: 0 }}
            initial={{ opacity: 0 }}
          />
          <div className="fixed inset-0 z-50 flex items-center justify-center p-4" data-morph-layer>
            {children}
          </div>
        </>
      )}
    </AnimatePresence>,
    document.body,
  );
}

export function MorphingDialogTitle({
  children,
  className,
  style,
}: {
  children: React.ReactNode;
  className?: string;
  style?: React.CSSProperties;
}) {
  const { uniqueId } = useMorphingDialog();

  return (
    <motion.div
      layout
      className={className}
      id={`morphing-dialog-title-${uniqueId}`}
      layoutId={`dialog-title-${uniqueId}`}
      style={style}
    >
      {children}
    </motion.div>
  );
}

export function MorphingDialogSubtitle({
  children,
  className,
  style,
}: {
  children: React.ReactNode;
  className?: string;
  style?: React.CSSProperties;
}) {
  const { uniqueId } = useMorphingDialog();

  return (
    <motion.div className={className} layoutId={`dialog-subtitle-${uniqueId}`} style={style}>
      {children}
    </motion.div>
  );
}

type FadeVariants = { initial: Variant; animate: Variant; exit: Variant };

export const fadeUp: FadeVariants = {
  initial: { opacity: 0, y: 8 },
  animate: { opacity: 1, y: 0, transition: { delay: 0.15 } },
  exit: { opacity: 0, y: 8, transition: { duration: 0.1 } },
};

export function MorphingDialogDescription({
  children,
  className,
  variants = fadeUp,
  disableLayoutAnimation,
}: {
  children: React.ReactNode;
  className?: string;
  variants?: FadeVariants;
  disableLayoutAnimation?: boolean;
}) {
  const { uniqueId } = useMorphingDialog();

  return (
    <motion.div
      key={`dialog-description-${uniqueId}`}
      animate="animate"
      className={className}
      exit="exit"
      initial="initial"
      layoutId={disableLayoutAnimation ? undefined : `dialog-description-${uniqueId}`}
      variants={variants}
    >
      {children}
    </motion.div>
  );
}

export function MorphingDialogClose({
  children,
  className,
  variants = fadeUp,
}: {
  children?: React.ReactNode;
  className?: string;
  variants?: FadeVariants;
}) {
  const { setIsOpen, uniqueId } = useMorphingDialog();

  return (
    <motion.button
      key={`dialog-close-${uniqueId}`}
      animate="animate"
      aria-label="Close dialog"
      className={clsx(
        "absolute top-4 right-4 grid size-8 place-items-center rounded-full bg-default text-foreground hover:bg-default-hover",
        className,
      )}
      exit="exit"
      initial="initial"
      type="button"
      variants={variants}
      onClick={() => setIsOpen(false)}
    >
      {children ?? <Xmark className="size-4" />}
    </motion.button>
  );
}
