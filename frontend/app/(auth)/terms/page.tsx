import type { Metadata } from "next";

import Link from "next/link";

import { siteConfig } from "@/config/site";

export const metadata: Metadata = { title: "Terms of use" };

/**
 * Accepted at sign-up (users.terms_accepted_at). A DRAFT describing what the
 * system does: the Secretariat's legal unit must approve or replace the wording
 * before go-live. Each exhibition's own participation terms are shown on the
 * exhibition and in the allocation letter.
 */
export default function TermsPage() {
  return (
    <main className="mx-auto grid max-w-2xl gap-6 px-5 py-12 text-sm leading-relaxed">
      <div>
        <p className="font-mono text-xs text-muted">{siteConfig.shortName}</p>
        <h1 className="mt-1 font-display text-3xl font-extrabold">Terms of use</h1>
        <p className="mt-2 rounded-lg bg-warning-soft px-3 py-2 text-xs font-semibold text-warning">
          Draft for approval by the Exhibition Secretariat&apos;s legal unit.
        </p>
      </div>

      <section className="grid gap-2">
        <h2 className="font-display text-lg font-bold">Your account</h2>
        <p>
          The account is personal. Keep your password to yourself; you are responsible for what is done with your
          account. Give true details — your name, phone and email, and when you apply, whether you exhibit as an
          individual or a registered business, with that business&apos;s details.
        </p>
      </section>

      <section className="grid gap-2">
        <h2 className="font-display text-lg font-bold">Applying and paying</h2>
        <p>
          An application is a request, not a booking. A space is yours once it is allocated and its invoice is paid in
          full through the GePG control number issued for it. Each exhibition&apos;s own terms and conditions, shown on
          the exhibition, apply to your participation in it.
        </p>
      </section>

      <section className="grid gap-2">
        <h2 className="font-display text-lg font-bold">Your information</h2>
        <p>
          {siteConfig.longName} keeps the details and documents you give to process applications, allocate space, bill,
          verify payments and admit you at the exhibition. Exhibition staff see them for those purposes only, and every
          change is recorded in an audit trail. You receive notices about your applications and payments by SMS, email
          and in the system.
        </p>
      </section>

      <Link className="font-semibold text-brand-ink hover:underline" href="/register">
        ← Back to create your account
      </Link>
    </main>
  );
}
