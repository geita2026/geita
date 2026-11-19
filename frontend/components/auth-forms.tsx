"use client";

import { useActionState, useState } from "react";
import Link from "next/link";
import { Form } from "@heroui/react";

import { loginAction, registerAction } from "@/server/actions";
import { initialActionState } from "@/server/action-result";
import { FormText } from "@/components/ui/form-fields";
import { keepInput } from "@/components/ui/action-form";
import { FormAlert } from "@/components/ui/form-alert";
import { SubmitButton } from "@/components/ui/submit-button";

export function LoginForm({ next }: { next?: string }) {
  const [state, action] = useActionState(loginAction, initialActionState);
  const errors = !state.ok ? state.fieldErrors : undefined;

  return (
    <Form ref={keepInput} action={action} className="flex flex-col gap-4" validationBehavior="aria">
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

/** How strong a password is, in the terms the API checks (10+ chars, mixed case, a digit) plus a symbol. */
function strength(pw: string) {
  const checks = [pw.length >= 10, /[a-z]/.test(pw), /[A-Z]/.test(pw), /\d/.test(pw), /[^A-Za-z0-9]/.test(pw)];
  const score = checks.filter(Boolean).length;

  if (!pw) return null;
  if (score <= 2)
    return { pct: 30, tone: "bg-danger", text: "Weak — use 10+ characters with upper and lower case and a number" };
  if (score <= 4) return { pct: 65, tone: "bg-warning", text: "Fair — add a symbol or more characters" };

  return { pct: 100, tone: "bg-success", text: "Strong password (uppercase, lowercase, number, symbol)" };
}

/**
 * FR-011, as on SAMS: the person only — name, phone, email, password. Whether
 * they exhibit as an individual or a registered business, and the business
 * details, are asked when they first apply for space.
 */
export function RegisterForm() {
  const [state, action] = useActionState(registerAction, initialActionState);
  const errors = !state.ok ? state.fieldErrors : undefined;
  const [password, setPassword] = useState("");
  const [confirm, setConfirm] = useState("");
  const meter = strength(password);
  const matches = confirm.length > 0 && confirm === password;

  return (
    <Form ref={keepInput} action={action} className="grid gap-6" validationBehavior="aria">
      <section className="grid gap-4">
        <h3 className="border-b border-separator pb-2 text-sm font-semibold">Personal information</h3>
        <div className="grid gap-4 sm:grid-cols-2">
          <FormText isRequired errors={errors} label="First name" name="first_name" />
          <FormText isRequired errors={errors} label="Last name" name="last_name" />
          <FormText isRequired errors={errors} label="Phone number" name="phone" placeholder="+255…" />
          <FormText isRequired errors={errors} label="Email" name="email" type="email" />
        </div>
      </section>

      <section className="grid gap-4">
        <h3 className="border-b border-separator pb-2 text-sm font-semibold">Security</h3>
        <div className="grid gap-4 sm:grid-cols-2">
          <div className="grid gap-2">
            <FormText
              isRequired
              errors={errors}
              label="Password"
              name="password"
              type="password"
              value={password}
              onChange={setPassword}
            />
            {meter && (
              <div className="grid gap-1" aria-live="polite">
                <div className="h-1.5 overflow-hidden rounded-full bg-default">
                  <div
                    className={`h-full rounded-full transition-[width] ${meter.tone}`}
                    style={{ width: `${meter.pct}%` }}
                  />
                </div>
                <span className="text-xs text-muted">{meter.text}</span>
              </div>
            )}
          </div>
          <div className="grid content-start gap-2">
            <FormText
              isRequired
              errors={errors}
              label="Confirm password"
              name="password_confirmation"
              type="password"
              value={confirm}
              onChange={setConfirm}
            />
            {confirm && (
              <span aria-live="polite" className={`text-xs ${matches ? "text-success" : "text-danger"}`}>
                {matches ? "Passwords match" : "Passwords do not match"}
              </span>
            )}
          </div>
        </div>
      </section>

      <div className="grid gap-1">
        <label className="flex items-center gap-2.5 text-sm">
          <input className="size-4 accent-[var(--brand)]" name="terms" type="checkbox" value="1" />
          <span>
            I accept the{" "}
            <Link className="font-semibold text-brand-ink hover:underline" href="/terms" target="_blank">
              terms of use
            </Link>
          </span>
        </label>
        {errors?.terms && <span className="text-xs text-danger">{errors.terms[0]}</span>}
      </div>

      <FormAlert message={!state.ok && !errors ? state.error : undefined} />
      <div className="flex flex-wrap items-center justify-between gap-3">
        <Link className="text-sm font-semibold text-brand-ink hover:underline" href="/login">
          Back to sign in
        </Link>
        <SubmitButton pendingLabel="Creating account…" result={state}>
          Create account
        </SubmitButton>
      </div>
    </Form>
  );
}
