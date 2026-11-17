import { siteConfig } from "@/config/site";

/** The split sign-in layout: brand panel on the left, the form on the right. */
export function AuthFrame({
  title,
  lead,
  children,
  wide,
}: {
  title: string;
  lead: string;
  children: React.ReactNode;
  wide?: boolean;
}) {
  return (
    <main className="grid min-h-screen lg:grid-cols-[1.1fr_1fr]">
      <section className="relative hidden flex-col justify-between overflow-hidden bg-accent p-10 text-accent-foreground lg:flex">
        {/* A faceted "ore body": pure decoration, drawn in the accent's own ink. */}
        <svg
          aria-hidden
          className="pointer-events-none absolute -right-24 -bottom-24 size-[560px] opacity-[0.13]"
          viewBox="0 0 200 200"
        >
          <g fill="none" stroke="currentColor" strokeWidth="1.2">
            <polygon points="100,8 172,52 188,128 132,190 60,186 14,124 30,48" />
            <polygon points="100,8 132,190 14,124 172,52 60,186 188,128 30,48" />
            <polygon points="100,60 138,84 144,124 114,154 78,150 56,120 66,82" />
          </g>
        </svg>
        <div className="flex items-center gap-3">
          <span className="grid size-10 place-items-center rounded-lg bg-white/15 font-display text-sm font-bold">
            Ge
          </span>
          <span className="font-display text-lg font-bold tracking-wide">{siteConfig.shortName}</span>
        </div>
        <div className="max-w-md">
          <h1 className="font-display text-4xl font-semibold leading-tight">Every exhibition space, accounted for.</h1>
          <p className="mt-4 text-base/relaxed opacity-90">
            Register, apply, choose your space on the floor plan, pay and check in — one transparent process for the
            Geita mineral exhibitions, with no space ever allocated twice.
          </p>
        </div>
        <p className="text-xs opacity-80">{siteConfig.longName}</p>
      </section>
      <section className="flex items-center justify-center p-6">
        <div className={`w-full ${wide ? "max-w-xl" : "max-w-sm"}`}>
          <div className="mb-8 lg:hidden">
            <span className="font-display text-lg font-bold tracking-wide">{siteConfig.shortName}</span>
            <p className="text-sm text-muted">{siteConfig.longName}</p>
          </div>
          <h2 className="text-2xl font-semibold">{title}</h2>
          <p className="mt-1 text-sm text-muted">{lead}</p>
          <div className="mt-6">{children}</div>
        </div>
      </section>
    </main>
  );
}
