"use client";

import { useActionState } from "react";
import Link from "next/link";
import { Form } from "@heroui/react";

import { loginAction, registerAction } from "@/server/actions";
import { initialActionState } from "@/server/action-result";
import { EXHIBITOR_TYPES, options } from "@/lib/domain/labels";
import { FormCombo, FormSelect, FormText, FormTextArea } from "@/components/ui/form-fields";
import { FormAlert } from "@/components/ui/form-alert";
import { SubmitButton } from "@/components/ui/submit-button";

export function LoginForm({ next }: { next?: string }) {
  const [state, action] = useActionState(loginAction, initialActionState);
  const errors = !state.ok ? state.fieldErrors : undefined;

  return (
    <Form action={action} className="flex flex-col gap-4" validationBehavior="aria">
      {next && <input name="next" type="hidden" value={next} />}
      <FormText isRequired errors={errors} label="Email" name="email" placeholder="you@company.co.tz" type="email" />
      <FormText isRequired errors={errors} label="Password" name="password" type="password" />
      <FormAlert message={!state.ok && !errors ? state.error : undefined} />
      <SubmitButton pendingLabel="Signing in…" result={state}>
        Sign in
      </SubmitButton>
      <p className="text-sm text-muted">
        Exhibiting for the first time?{" "}
        <Link className="font-semibold text-brand-ink hover:underline" href="/register">
          Create an exhibitor account
        </Link>
      </p>
      <p className="text-xs text-muted">Forgotten your password? Ask the Exhibition Secretariat to reset it.</p>
    </Form>
  );
}

/** FR-011: organisation profile and its first user, in one step. */
export function RegisterForm({ countries }: { countries: string[] }) {
  const [state, action] = useActionState(registerAction, initialActionState);
  const errors = !state.ok ? state.fieldErrors : undefined;

  return (
    <Form action={action} className="grid gap-4 sm:grid-cols-2" validationBehavior="aria">
      <FormText
        isRequired
        className="sm:col-span-2"
        errors={errors}
        label="Organisation / company name"
        name="organization_name"
      />
      <FormText errors={errors} label="Registration number" name="registration_number" placeholder="BRELA no." />
      <FormText errors={errors} label="TIN" name="tin" placeholder="Where applicable" />
      <FormText isRequired errors={errors} label="Contact person" name="contact_person" />
      <FormText isRequired errors={errors} label="Phone" name="phone" placeholder="+255…" />
      <FormText isRequired errors={errors} label="Email (your sign-in)" name="email" type="email" />
      <FormCombo
        isRequired
        defaultValue="Tanzania"
        errors={errors}
        label="Country"
        name="country"
        options={countries.map((c) => ({ id: c, label: c }))}
      />
      <FormSelect
        defaultValue="local"
        errors={errors}
        label="Exhibitor type"
        name="exhibitor_type"
        options={options(EXHIBITOR_TYPES)}
      />
      <FormText errors={errors} label="Business sector" name="business_sector" placeholder="e.g. Mining equipment" />
      <FormText className="sm:col-span-2" errors={errors} label="Physical address" name="physical_address" />
      <FormTextArea
        className="sm:col-span-2"
        errors={errors}
        label="Business activity"
        name="business_activity"
        rows={2}
      />
      <FormText
        isRequired
        description="At least 10 characters with upper and lower case letters and a number."
        errors={errors}
        label="Password"
        name="password"
        type="password"
      />
      <FormText isRequired errors={errors} label="Confirm password" name="password_confirmation" type="password" />
      <div className="grid gap-3 sm:col-span-2">
        <FormAlert message={!state.ok && !errors ? state.error : undefined} />
        <SubmitButton pendingLabel="Creating account…" result={state}>
          Create account
        </SubmitButton>
        <p className="text-sm text-muted">
          Already registered?{" "}
          <Link className="font-semibold text-brand-ink hover:underline" href="/login">
            Sign in
          </Link>
        </p>
      </div>
    </Form>
  );
}
