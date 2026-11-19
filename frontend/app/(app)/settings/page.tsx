import type { Metadata } from "next";

import { cookies } from "next/headers";
import { Card } from "@heroui/react";

import { requireSession } from "@/lib/auth/session";
import { ACCENT_COOKIE, accentOr } from "@/lib/domain/accents";
import { MOTION_COOKIE, motionOr } from "@/lib/domain/motion";
import { siteConfig } from "@/config/site";
import { AccentPicker } from "@/components/settings/accent-picker";
import { MotionPicker } from "@/components/settings/motion-picker";
import { WavePicker } from "@/components/settings/wave-picker";
import { WAVE_COOKIE, waveOr } from "@/lib/domain/wave";
import { PageHeader } from "@/components/ui/page-header";
import { Fact } from "@/components/ui/chips";
import { ActionForm, TextF } from "@/components/ui/action-form";

export const metadata: Metadata = { title: "Settings" };

export default async function SettingsPage() {
  const { user } = await requireSession();
  const jar = await cookies();

  return (
    <div className="grid gap-6">
      <PageHeader description="How GeMEMIS looks on this device, and your account." title="Settings" />
      <Card className="gap-4 p-5">
        <div>
          <h2 className="font-semibold">Accent colour</h2>
          <p className="text-sm text-muted">
            The colour of the selected nav item, primary buttons and highlights. Saved to this browser only.
          </p>
        </div>
        <AccentPicker current={accentOr(jar.get(ACCENT_COOKIE)?.value)} />
      </Card>
      <Card className="gap-4 p-5">
        <div>
          <h2 className="font-semibold">Motion</h2>
          <p className="text-sm text-muted">
            Animations show what is happening — a dialog opening out of its button, a button saving. Reduce them here if
            movement is uncomfortable.
          </p>
        </div>
        <MotionPicker current={motionOr(jar.get(MOTION_COOKIE)?.value)} />
      </Card>
      <Card className="gap-4 p-5" data-wave-demo>
        <div>
          <h2 className="font-semibold">Distortion wave</h2>
          <p className="text-sm text-muted">
            A ripple that carries the page into its new look when you switch between light and dark or change the
            accent. It is never shown while motion is reduced. Saved to this browser only.
          </p>
        </div>
        <WavePicker current={waveOr(jar.get(WAVE_COOKIE)?.value)} />
      </Card>
      <Card className="gap-4 p-5">
        <h2 className="font-semibold">Account</h2>
        <dl className="grid grid-cols-2 gap-x-6 gap-y-4 md:grid-cols-4">
          <Fact label="Name">{user.name}</Fact>
          <Fact label="Email">{user.email}</Fact>
          <Fact label="Role">{user.role_label}</Fact>
          <Fact label="Organisation">{user.exhibitor?.organization_name ?? siteConfig.organisation}</Fact>
        </dl>
      </Card>
      <Card className="gap-4 p-5">
        <div>
          <h2 className="font-semibold">Change password</h2>
          <p className="text-sm text-muted">Changing it signs you out on every other device.</p>
        </div>
        <ActionForm
          className="grid max-w-md gap-4"
          pendingLabel="Changing…"
          spec={{ method: "PUT", path: "/auth/password", success: "Password changed" }}
          submitLabel="Change password"
        >
          <TextF isRequired label="Current password" name="current_password" type="password" />
          <TextF
            isRequired
            description="At least 10 characters with upper and lower case letters and a number."
            label="New password"
            name="password"
            type="password"
          />
          <TextF isRequired label="Confirm new password" name="password_confirmation" type="password" />
        </ActionForm>
      </Card>
    </div>
  );
}
