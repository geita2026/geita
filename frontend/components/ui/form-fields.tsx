"use client";

import { useState } from "react";
import {
  ComboBox,
  Description,
  FieldError,
  Input,
  InputGroup,
  Label,
  ListBox,
  Select,
  TextArea,
  TextField,
} from "@heroui/react";
import { Eye, EyeSlash } from "@gravity-ui/icons";

/**
 * Thin wrappers over HeroUI form primitives so every form in the portal
 * looks the same and wires server-side field errors identically.
 */

type Errors = Record<string, string[]> | undefined;

function firstError(errors: Errors, name: string) {
  return errors?.[name]?.[0];
}

export function FormText({
  name,
  label,
  errors,
  description,
  type = "text",
  defaultValue,
  placeholder,
  isRequired,
  className = "",
  mono,
  min,
  max,
  step,
  variant,
  value,
  onChange,
}: {
  name: string;
  label: string;
  errors?: Errors;
  description?: string;
  type?: "text" | "email" | "password" | "number" | "date" | "search";
  variant?: "primary" | "secondary";
  defaultValue?: string | number;
  /** Pass with `onChange` to control the field; otherwise it is uncontrolled. */
  value?: string;
  onChange?: (value: string) => void;
  placeholder?: string;
  isRequired?: boolean;
  className?: string;
  mono?: boolean;
  min?: number;
  max?: number;
  step?: number;
}) {
  const error = firstError(errors, name);
  /* Password fields get a show/hide toggle: typing a long passphrase blind is
     where most failed sign-ins (and so lock-outs) come from. */
  const [revealed, setRevealed] = useState(false);
  const isPassword = type === "password";

  return (
    <TextField
      className={`w-full ${className}`}
      defaultValue={defaultValue === undefined ? undefined : String(defaultValue)}
      isInvalid={!!error}
      isRequired={isRequired}
      name={name}
      type={isPassword && revealed ? "text" : type}
      value={value}
      variant={variant}
      onChange={onChange}
    >
      <Label>{label}</Label>
      {isPassword ? (
        <InputGroup>
          <InputGroup.Input
            autoCapitalize="none"
            autoCorrect="off"
            className={`w-full ${mono ? "font-mono" : ""}`}
            placeholder={placeholder}
            spellCheck={false}
          />
          <InputGroup.Suffix>
            <button
              aria-label={revealed ? "Hide password" : "Show password"}
              aria-pressed={revealed}
              className="grid size-7 place-items-center rounded-md text-muted transition-colors hover:bg-default hover:text-foreground focus-visible:outline-2 focus-visible:outline-offset-1 focus-visible:outline-focus"
              title={revealed ? "Hide password" : "Show password"}
              type="button"
              onClick={() => setRevealed((r) => !r)}
            >
              {revealed ? <EyeSlash aria-hidden className="size-4" /> : <Eye aria-hidden className="size-4" />}
            </button>
          </InputGroup.Suffix>
        </InputGroup>
      ) : (
        <Input
          className={`w-full ${mono ? "font-mono" : ""}`}
          max={max}
          min={min}
          placeholder={placeholder}
          step={step}
        />
      )}
      {description && !error && <Description>{description}</Description>}
      {error && <FieldError>{error}</FieldError>}
    </TextField>
  );
}

export function FormTextArea({
  name,
  label,
  errors,
  description,
  defaultValue,
  placeholder,
  rows = 4,
  className = "",
  variant,
  isRequired,
  value,
  onChange,
  "aria-label": ariaLabel,
}: {
  name: string;
  label?: string;
  errors?: Errors;
  description?: string;
  defaultValue?: string;
  placeholder?: string;
  rows?: number;
  className?: string;
  variant?: "primary" | "secondary";
  isRequired?: boolean;
  /** Pass with `onChange` to control the field; otherwise it is uncontrolled. */
  value?: string;
  onChange?: (value: string) => void;
  /** Use instead of a visible label when the field is titled by its section. */
  "aria-label"?: string;
}) {
  const error = firstError(errors, name);

  return (
    <TextField
      className={`w-full ${className}`}
      aria-label={label ? undefined : ariaLabel}
      defaultValue={defaultValue}
      isInvalid={!!error}
      isRequired={isRequired}
      name={name}
      value={value}
      variant={variant}
      onChange={onChange}
    >
      {label && <Label>{label}</Label>}
      <TextArea className="w-full" placeholder={placeholder} rows={rows} />
      {description && !error && <Description>{description}</Description>}
      {error && <FieldError>{error}</FieldError>}
    </TextField>
  );
}

export type Option = { id: string; label: string };

export function FormSelect({
  name,
  label,
  options,
  errors,
  description,
  defaultValue,
  value,
  onChange,
  placeholder = "Select…",
  isRequired,
  isDisabled,
  className = "",
  variant,
}: {
  name: string;
  label: string;
  options: Option[];
  variant?: "primary" | "secondary";
  errors?: Errors;
  description?: string;
  defaultValue?: string | null;
  value?: string | null;
  onChange?: (value: string | null) => void;
  placeholder?: string;
  isRequired?: boolean;
  isDisabled?: boolean;
  className?: string;
}) {
  const error = firstError(errors, name);

  return (
    <Select
      className={`w-full ${className}`}
      defaultValue={value === undefined ? (defaultValue ?? null) : undefined}
      isDisabled={isDisabled}
      isInvalid={!!error}
      isRequired={isRequired}
      name={name}
      placeholder={placeholder}
      value={value === undefined ? undefined : value}
      variant={variant}
      onChange={onChange ? (v) => onChange(v == null ? null : String(v)) : undefined}
    >
      <Label>{label}</Label>
      <Select.Trigger>
        <Select.Value />
        <Select.Indicator />
      </Select.Trigger>
      {description && !error && <Description>{description}</Description>}
      {error && <FieldError>{error}</FieldError>}
      <Select.Popover>
        <ListBox>
          {options.map((o) => (
            <ListBox.Item key={o.id} id={o.id} textValue={o.label}>
              {o.label}
              <ListBox.ItemIndicator />
            </ListBox.Item>
          ))}
        </ListBox>
      </Select.Popover>
    </Select>
  );
}

/**
 * A select you can type into, for long lists (countries). Typing filters the
 * options, but only a listed option can be chosen — free text is not accepted,
 * which is the point: no misspelt values reach the database.
 */
export function FormCombo({
  name,
  label,
  options,
  errors,
  description,
  defaultValue,
  placeholder = "Type to search…",
  isRequired,
  className = "",
}: {
  name: string;
  label: string;
  options: Option[];
  errors?: Errors;
  description?: string;
  defaultValue?: string | null;
  placeholder?: string;
  isRequired?: boolean;
  className?: string;
}) {
  const error = firstError(errors, name);

  return (
    <ComboBox
      className={`w-full ${className}`}
      defaultSelectedKey={defaultValue ?? undefined}
      formValue="key"
      isInvalid={!!error}
      isRequired={isRequired}
      menuTrigger="focus"
      name={name}
    >
      <Label>{label}</Label>
      <ComboBox.InputGroup>
        <Input placeholder={placeholder} />
        <ComboBox.Trigger />
      </ComboBox.InputGroup>
      {description && !error && <Description>{description}</Description>}
      {error && <FieldError>{error}</FieldError>}
      <ComboBox.Popover>
        <ListBox>
          {options.map((o) => (
            <ListBox.Item key={o.id} id={o.id} textValue={o.label}>
              {o.label}
              <ListBox.ItemIndicator />
            </ListBox.Item>
          ))}
        </ListBox>
      </ComboBox.Popover>
    </ComboBox>
  );
}
