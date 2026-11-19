import "@/styles/globals.css";
import type { Metadata, Viewport } from "next";

import { Providers } from "./providers";

import { siteConfig } from "@/config/site";
import { cookies } from "next/headers";

import { ACCENT_COOKIE, accentOr } from "@/lib/domain/accents";
import { MOTION_COOKIE, motionOr } from "@/lib/domain/motion";
import { WAVE_COOKIE, waveOr } from "@/lib/domain/wave";
import { fontSans } from "@/config/fonts";

export const metadata: Metadata = {
  title: {
    default: siteConfig.name,
    template: `%s · ${siteConfig.shortName}`,
  },
  description: siteConfig.description,
  icons: { icon: "/favicon.ico", apple: "/apple-icon" },
  // Home Screen install on iPhone: full-screen, named, status bar over the page
  // (the shell pads itself with the safe-area insets).
  appleWebApp: { capable: true, title: siteConfig.shortName, statusBarStyle: "default" },
};

export const viewport: Viewport = {
  // Draw under the notch / Dynamic Island and home indicator; the shell keeps its
  // content clear with env(safe-area-inset-*).
  viewportFit: "cover",
  themeColor: [
    { media: "(prefers-color-scheme: light)", color: "#e6e9f3" },
    { media: "(prefers-color-scheme: dark)", color: "#1c1b26" },
  ],
};

export default async function RootLayout({ children }: { children: React.ReactNode }) {
  // Read on the server so the chosen accent is in the first paint.
  const jar = await cookies();
  const accent = accentOr(jar.get(ACCENT_COOKIE)?.value);
  const motion = motionOr(jar.get(MOTION_COOKIE)?.value);
  const wave = waveOr(jar.get(WAVE_COOKIE)?.value);

  return (
    // The font variable goes on <html>, not <body>: the theme declares
    // `--font-sans: var(--font-manrope), …` on :root, and a custom property is
    // resolved where it is declared — set lower down, --font-manrope did not
    // exist at :root, --font-sans came out empty, and every page fell back to
    // the system font.
    <html
      suppressHydrationWarning
      className={fontSans.variable}
      data-accent={accent}
      data-motion={motion}
      data-wave={wave}
      lang="en"
    >
      <body suppressHydrationWarning className="min-h-screen bg-background font-sans text-foreground antialiased">
        <Providers
          reduceMotion={motion === "reduce"}
          themeProps={{
            attribute: "class",
            defaultTheme: "system",
            enableSystem: true,
            disableTransitionOnChange: true,
          }}
        >
          {children}
        </Providers>
      </body>
    </html>
  );
}
