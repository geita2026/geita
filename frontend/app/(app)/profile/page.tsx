import type { Metadata } from "next";

import { redirect } from "next/navigation";

import { requireSession } from "@/lib/auth/session";
import { listCountries } from "@/lib/countries";
import { PageHeader } from "@/components/ui/page-header";
import { Panel } from "@/components/ui/panel";
import { Fact } from "@/components/ui/chips";
import { ActionForm } from "@/components/ui/action-form";
import { ApplicantFields } from "@/components/exhibitors/applicant-fields";

export const metadata: Metadata = { title: "My profile" };

/** FR-013: who you exhibit as — an individual or a registered business — and your details. */
export default async function ProfilePage() {
  const { user } = await requireSession();
  const e = user.exhibitor;

  if (!e) redirect("/dashboard");
  const countries = await listCountries();

  return (
    <div className="grid gap-6">
      <PageHeader
        description={
          e.entity_type
            ? "These details appear on your invoices, receipts and allocation letter."
            : "You will be asked whether you exhibit as an individual or a registered business when you first apply. You can also say so here."
        }
        title="My profile"
      />
      <Panel className="max-w-3xl" title="Your account">
        <dl className="grid grid-cols-2 gap-x-6 gap-y-3 md:grid-cols-3">
          <Fact label="Name">{user.name}</Fact>
          <Fact label="Email">{user.email}</Fact>
          <Fact label="Phone">{user.phone}</Fact>
        </dl>
      </Panel>
      <Panel className="max-w-3xl" title="Exhibiting as">
        <ActionForm
          spec={{ method: "PUT", path: "/auth/profile", success: "Profile saved" }}
          submitLabel="Save profile"
        >
          <ApplicantFields countries={countries} exhibitor={e} personName={user.name} />
        </ActionForm>
      </Panel>
    </div>
  );
}
