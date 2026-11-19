"use client";

import { createContext, useActionState, useContext, useEffect, useRef } from "react";
import { Form, toast } from "@heroui/react";

import type { ActionResult } from "@/server/action-result";

import { mutate, type MutationSpec } from "@/server/actions";
import { FormAlert } from "@/components/ui/form-alert";
import { SubmitButton } from "@/components/ui/submit-button";
import { FormCombo, FormSelect, FormText, FormTextArea } from "@/components/ui/form-fields";

type Errors = Record<string, string[]> | undefined;

/**
 * React resets a form after its action runs — even when the server refused it —
 * and the HeroUI/React Aria fields listen for that reset and clear themselves,
 * wiping everything the person typed. Their listener sits on the form, so the
 * reset is stopped there first, in the capture phase. A successful submit
 * closes its dialog or moves to another page anyway.
 *
 * Use as the form's ref: `<Form ref={keepInput} …>`.
 */
export function keepInput(form: HTMLFormElement | null) {
  if (!form) return;
  const stop = (e: Event) => {
    e.preventDefault();
    e.stopImmediatePropagation();
  };

  form.addEventListener("reset", stop, { capture: true });

  return () => form.removeEventListener("reset", stop, { capture: true });
}
const ErrorsContext = createContext<Errors>(undefined);

const initial: ActionResult<unknown> = { ok: false, error: "" };

/**
 * A form wired to the generic `mutate` server action: field errors from the
 * API land under their fields, the button plays pending/success/error, and a
 * toast confirms. Fields inside it (`TextF`, `AreaF`, `SelectF`) pick their
 * errors up from context, so a page only lists the fields.
 */
export function ActionForm({
  spec,
  children,
  submitLabel = "Save",
  pendingLabel = "Saving…",
  onDone,
  className = "grid gap-4",
  variant = "primary",
  footer,
}: {
  spec: MutationSpec;
  children: React.ReactNode;
  submitLabel?: string;
  pendingLabel?: string;
  onDone?: (data: unknown) => void;
  className?: string;
  variant?: "primary" | "danger" | "secondary";
  /** Rendered beside the submit button (e.g. a Cancel control). */
  footer?: React.ReactNode;
}) {
  const [state, action] = useActionState(mutate.bind(null, spec), initial);
  const seen = useRef(state);

  useEffect(() => {
    if (state === seen.current) return;
    seen.current = state;
    if (state.ok) {
      if (state.message) toast(state.message, { variant: "success" });
      onDone?.(state.data);
    }
  }, [state, onDone]);

  return (
    <Form ref={keepInput} action={action} className={className} validationBehavior="aria">
      <ErrorsContext.Provider value={!state.ok ? state.fieldErrors : undefined}>{children}</ErrorsContext.Provider>
      {/* col-span-full: forms laid out as a grid keep the alert and buttons on their own row. */}
      <div className="col-span-full empty:hidden">
        <FormAlert message={!state.ok ? state.error : undefined} />
      </div>
      <div className="col-span-full flex flex-wrap items-center justify-end gap-2">
        {footer}
        <SubmitButton pendingLabel={pendingLabel} result={state as ActionResult} variant={variant}>
          {submitLabel}
        </SubmitButton>
      </div>
    </Form>
  );
}

/** The first error the API returned for a field of the surrounding ActionForm. */
export function useFieldError(name: string) {
  return useContext(ErrorsContext)?.[name]?.[0];
}

export function TextF(props: Omit<React.ComponentProps<typeof FormText>, "errors">) {
  return <FormText {...props} errors={useContext(ErrorsContext)} />;
}

export function AreaF(props: Omit<React.ComponentProps<typeof FormTextArea>, "errors">) {
  return <FormTextArea {...props} errors={useContext(ErrorsContext)} />;
}

export function SelectF(props: Omit<React.ComponentProps<typeof FormSelect>, "errors">) {
  return <FormSelect {...props} errors={useContext(ErrorsContext)} />;
}

export function ComboF(props: Omit<React.ComponentProps<typeof FormCombo>, "errors">) {
  return <FormCombo {...props} errors={useContext(ErrorsContext)} />;
}

/** Plain file input in the app's field style (HeroUI has no file field). */
export function FileF({
  name,
  label,
  accept,
  description,
  isRequired,
}: {
  name: string;
  label: string;
  accept?: string;
  description?: string;
  isRequired?: boolean;
}) {
  const error = useContext(ErrorsContext)?.[name]?.[0];

  return (
    <label className="grid gap-1.5 text-sm">
      <span className="font-medium">
        {label}
        {isRequired && <span className="text-danger"> *</span>}
      </span>
      <input
        accept={accept}
        className="w-full rounded-xl border border-separator bg-field-background px-3 py-2 text-sm file:me-3 file:rounded-lg file:border-0 file:bg-default file:px-3 file:py-1.5 file:text-sm file:font-semibold"
        name={name}
        required={isRequired}
        type="file"
      />
      {error ? (
        <span className="text-xs text-danger">{error}</span>
      ) : (
        description && <span className="text-xs text-muted">{description}</span>
      )}
    </label>
  );
}

export function CheckF({
  name,
  label,
  defaultChecked,
  description,
}: {
  name: string;
  label: string;
  defaultChecked?: boolean;
  description?: string;
}) {
  return (
    <label className="flex items-start gap-3 rounded-xl border border-separator p-3 text-sm">
      <input
        className="mt-0.5 size-4 accent-[var(--brand)]"
        defaultChecked={defaultChecked}
        name={name}
        type="checkbox"
      />
      <span>
        <span className="font-medium">{label}</span>
        {description && <span className="block text-xs text-muted">{description}</span>}
      </span>
    </label>
  );
}
