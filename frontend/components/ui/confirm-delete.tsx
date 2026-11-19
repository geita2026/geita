"use client";

import { AlertDialog, Button } from "@heroui/react";
import { TriangleExclamationFill } from "@gravity-ui/icons";

import { DialogStack } from "@/components/ui/dialog-stack";

/**
 * Destructive-action confirmation. The backdrop turns deep red so an
 * irreversible delete never looks like an ordinary dialog, and the safe choice
 * (keep) sits below the destructive one rather than beside it.
 *
 * Use this for anything that cannot be undone; ordinary confirmations should
 * stay on a plain Modal.
 */
export function ConfirmDelete({
  trigger,
  heading,
  description,
  confirmLabel = "Delete forever",
  cancelLabel = "Keep it",
  isPending = false,
  onConfirm,
}: {
  trigger: React.ReactNode;
  heading: string;
  description: React.ReactNode;
  confirmLabel?: string;
  cancelLabel?: string;
  isPending?: boolean;
  onConfirm: () => void;
}) {
  return (
    <AlertDialog>
      {trigger}
      <AlertDialog.Backdrop
        className="overflow-hidden backdrop-blur-[2px]"
        // The dark base does the dimming; the red glow lives in .danger-pulse
        // above it, so it can breathe without re-painting the whole backdrop.
        style={{ backgroundColor: "rgb(0 0 0 / 0.78)" }}
      >
        <div aria-hidden className="danger-pulse" />
        <AlertDialog.Container className="relative z-10 justify-center">
          <DialogStack className="sm:max-w-md">
            <AlertDialog.Dialog className="notch-shell max-w-none">
              <div className="notch-tr notch-panel">
                <AlertDialog.Header className="flex-col items-center gap-3 text-center">
                  <AlertDialog.Icon status="danger">
                    <TriangleExclamationFill className="size-5" />
                  </AlertDialog.Icon>
                  <AlertDialog.Heading className="text-lg font-semibold">{heading}</AlertDialog.Heading>
                </AlertDialog.Header>
                <AlertDialog.Body className="text-center text-sm leading-relaxed text-muted">
                  {description}
                </AlertDialog.Body>
                <AlertDialog.Footer className="flex-col gap-2">
                  <Button fullWidth isPending={isPending} variant="danger" onPress={onConfirm}>
                    {confirmLabel}
                  </Button>
                  <Button fullWidth slot="close" variant="secondary">
                    {cancelLabel}
                  </Button>
                </AlertDialog.Footer>
              </div>
              <AlertDialog.CloseTrigger className="notch-tr-close" />
            </AlertDialog.Dialog>
          </DialogStack>
        </AlertDialog.Container>
      </AlertDialog.Backdrop>
    </AlertDialog>
  );
}
