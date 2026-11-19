import type { Exhibition } from "@/lib/types";

import { AreaF, CheckF, TextF } from "@/components/ui/action-form";

/** FR-001: the fields captured for an exhibition, shared by create and edit. */
export function ExhibitionFields({ e }: { e?: Exhibition }) {
  return (
    <div className="grid gap-4 sm:grid-cols-2">
      <TextF isRequired className="sm:col-span-2" defaultValue={e?.name} label="Exhibition name" name="name" />
      <TextF
        isRequired
        mono
        defaultValue={e?.code}
        description="Short, unique. Used in every reference number."
        label="Code"
        name="code"
        placeholder="GMTE27"
      />
      <TextF isRequired defaultValue={e?.venue} label="Venue" name="venue" />
      <TextF className="sm:col-span-2" defaultValue={e?.theme ?? ""} label="Theme" name="theme" />
      <TextF isRequired defaultValue={e?.start_date} label="Start date" name="start_date" type="date" />
      <TextF isRequired defaultValue={e?.end_date} label="End date" name="end_date" type="date" />
      <TextF
        isRequired
        defaultValue={e?.registration_opens_on}
        label="Registration opens"
        name="registration_opens_on"
        type="date"
      />
      <TextF
        isRequired
        defaultValue={e?.registration_closes_on}
        label="Registration closes"
        name="registration_closes_on"
        type="date"
      />
      <TextF
        isRequired
        defaultValue={e?.payment_deadline}
        label="Payment deadline"
        name="payment_deadline"
        type="date"
      />
      <TextF
        defaultValue={e?.vat_rate ?? 18}
        label="VAT rate (%)"
        max={100}
        min={0}
        name="vat_rate"
        step={0.5}
        type="number"
      />
      <AreaF
        className="sm:col-span-2"
        defaultValue={e?.objectives ?? ""}
        label="Objectives"
        name="objectives"
        rows={3}
      />
      <AreaF
        defaultValue={(e?.categories ?? []).join("\n")}
        description="One per line."
        label="Exhibition categories"
        name="categories"
        rows={4}
      />
      <AreaF
        defaultValue={(e?.required_documents ?? []).join("\n")}
        description="One per line. An application cannot be submitted or confirmed without these."
        label="Mandatory documents — businesses"
        name="required_documents"
        rows={4}
      />
      <AreaF
        defaultValue={(e?.required_documents_individual ?? []).join("\n")}
        description="One per line. Asked of individuals instead — they have no business licence or TIN."
        label="Mandatory documents — individuals"
        name="required_documents_individual"
        rows={4}
      />
      <AreaF
        className="sm:col-span-2"
        defaultValue={e?.terms ?? ""}
        label="Terms and conditions"
        name="terms"
        rows={4}
      />
      <TextF defaultValue={e?.contact_email ?? ""} label="Contact email" name="contact_email" type="email" />
      <TextF defaultValue={e?.contact_phone ?? ""} label="Contact phone" name="contact_phone" />
      <div className="sm:col-span-2">
        <CheckF
          defaultChecked={e?.allow_self_selection ?? true}
          description="Approved exhibitors pick an available space on the floor plan; an officer confirms it."
          label="Let exhibitors select their own space"
          name="allow_self_selection"
        />
      </div>
    </div>
  );
}

export const exhibitionSpecExtras = {
  lists: ["categories", "required_documents", "required_documents_individual"],
  bools: ["allow_self_selection"],
};
