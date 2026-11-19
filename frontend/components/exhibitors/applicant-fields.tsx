"use client";

import { useState } from "react";
import clsx from "clsx";
import { Briefcase, Person } from "@gravity-ui/icons";

import type { Exhibitor } from "@/lib/types";

import { AreaF, ComboF, TextF, useFieldError } from "@/components/ui/action-form";

type Kind = "individual" | "business";

const KINDS: { key: Kind; title: string; detail: string; Icon: typeof Person }[] = [
  {
    key: "individual",
    title: "An individual",
    detail: "Exhibiting in my own name, without a registered business.",
    Icon: Person,
  },
  {
    key: "business",
    title: "A registered business",
    detail: "A company, cooperative or institution with a registration number.",
    Icon: Briefcase,
  },
];

/**
 * Who is applying (FR-011, FR-013). Sign-up asks only for the person; the first
 * application asks whether they exhibit as an individual or a registered
 * business, and only the details that apply. Individuals are invoiced in their
 * own name and are never asked for a company name, registration number or TIN.
 * Pre-filled from the profile, so later applications only confirm it.
 */
export function ApplicantFields({
  exhibitor,
  personName,
  countries,
}: {
  exhibitor?: Exhibitor | null;
  personName: string;
  countries: string[];
}) {
  const [kind, setKind] = useState<Kind | null>(exhibitor?.entity_type ?? null);
  const kindError = useFieldError("entity_type");
  const business = kind === "business";

  return (
    <div className="grid gap-4 sm:grid-cols-2">
      <fieldset className="grid gap-2 sm:col-span-2">
        <legend className="mb-2 text-sm font-medium">
          Applying as <span className="text-danger">*</span>
        </legend>
        <input name="entity_type" type="hidden" value={kind ?? ""} />
        <div className="grid gap-2 sm:grid-cols-2" role="radiogroup">
          {KINDS.map(({ key, title, detail, Icon }) => {
            const on = kind === key;

            return (
              <button
                key={key}
                aria-checked={on}
                className={clsx(
                  "flex items-start gap-3 rounded-xl border p-3 text-start transition-colors",
                  on ? "border-brand bg-brand-soft" : "border-separator hover:bg-default",
                )}
                role="radio"
                type="button"
                onClick={() => setKind(key)}
              >
                <Icon className={clsx("mt-0.5 size-5 shrink-0", on ? "text-brand-ink" : "text-muted")} />
                <span>
                  <span className="block text-sm font-semibold">{title}</span>
                  <span className="block text-xs text-muted">{detail}</span>
                </span>
              </button>
            );
          })}
        </div>
        {kindError && (
          <span className="text-xs text-danger">Choose whether you apply as an individual or a business.</span>
        )}
      </fieldset>

      {kind === "individual" && (
        <>
          <p className="rounded-xl bg-surface-secondary px-3 py-2 text-sm text-muted sm:col-span-2">
            You exhibit in your own name, <strong className="text-foreground">{personName}</strong>. Invoices and
            receipts are made out to you.
          </p>
          <TextF
            defaultValue={exhibitor?.national_id ?? ""}
            description="Optional. Your NIDA number, if you have one."
            label="National ID number"
            name="national_id"
          />
        </>
      )}

      {business && (
        <>
          <TextF
            isRequired
            className="sm:col-span-2"
            defaultValue={exhibitor?.entity_type === "business" ? exhibitor.organization_name : ""}
            label="Organisation / company name"
            name="organization_name"
          />
          <TextF
            isRequired
            defaultValue={exhibitor?.registration_number ?? ""}
            label="Registration number"
            name="registration_number"
            placeholder="BRELA no."
          />
          <TextF defaultValue={exhibitor?.tin ?? ""} label="TIN" name="tin" placeholder="Where applicable" />
          <TextF
            isRequired
            defaultValue={exhibitor?.entity_type === "business" ? exhibitor.contact_person : personName}
            label="Contact person"
            name="contact_person"
          />
        </>
      )}

      {kind && (
        <>
          <TextF isRequired defaultValue={exhibitor?.phone ?? ""} label="Phone" name="phone" placeholder="+255…" />
          <ComboF
            isRequired
            defaultValue={exhibitor?.country ?? "Tanzania"}
            label="Country"
            name="country"
            options={countries.map((c) => ({ id: c, label: c }))}
          />
          <TextF
            defaultValue={exhibitor?.physical_address ?? ""}
            label={business ? "Physical address" : "Address"}
            name="physical_address"
          />
          {business && (
            <TextF
              defaultValue={exhibitor?.business_sector ?? ""}
              label="Business sector"
              name="business_sector"
              placeholder="e.g. Mining equipment"
            />
          )}
          <AreaF
            className="sm:col-span-2"
            defaultValue={exhibitor?.business_activity ?? ""}
            label={business ? "Business activity" : "What you do"}
            name="business_activity"
            rows={2}
          />
        </>
      )}
    </div>
  );
}
