import type { Metadata } from "next";

import { redirect } from "next/navigation";

import { requireSession } from "@/lib/auth/session";
import { EXHIBITOR_TYPES } from "@/lib/domain/labels";
import { PageHeader } from "@/components/ui/page-header";
import { Panel } from "@/components/ui/panel";
import { ActionForm, AreaF, TextF } from "@/components/ui/action-form";

export const metadata: Metadata = { title: "My organisation" };

/** FR-013 */
export default async function ProfilePage() {
  const { user } = await requireSession();
  const e = user.exhibitor;

  if (!e) redirect("/dashboard");

  return (
    <div className="grid gap-6">
      <PageHeader
        description={`${EXHIBITOR_TYPES[e.exhibitor_type] ?? e.exhibitor_type} · these details appear on your invoices and allocation letter.`}
        title="My organisation"
      />
      <Panel className="max-w-3xl" title="Organisation profile">
        <ActionForm
          className="grid gap-4 sm:grid-cols-2"
          spec={{ method: "PUT", path: "/auth/profile", success: "Profile saved" }}
          submitLabel="Save profile"
        >
          <TextF
            isRequired
            className="sm:col-span-2"
            defaultValue={e.organization_name}
            label="Organisation / company name"
            name="organization_name"
          />
          <TextF defaultValue={e.registration_number ?? ""} label="Registration number" name="registration_number" />
          <TextF defaultValue={e.tin ?? ""} label="TIN" name="tin" />
          <TextF isRequired defaultValue={e.contact_person} label="Contact person" name="contact_person" />
          <TextF isRequired defaultValue={e.phone} label="Phone" name="phone" />
          <TextF isRequired defaultValue={e.country} label="Country" name="country" />
          <TextF defaultValue={e.business_sector ?? ""} label="Business sector" name="business_sector" />
          <TextF
            className="sm:col-span-2"
            defaultValue={e.physical_address ?? ""}
            label="Physical address"
            name="physical_address"
          />
          <AreaF
            className="sm:col-span-2"
            defaultValue={e.business_activity ?? ""}
            label="Business activity"
            name="business_activity"
            rows={3}
          />
        </ActionForm>
      </Panel>
    </div>
  );
}
