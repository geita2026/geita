import type { Metadata } from "next";
import "./globals.css";
import { Providers } from "./providers";
import { Nav } from "../components/Nav";
import { AuthGuard } from "../components/AuthGuard";
import Link from "next/link";

export const metadata: Metadata = {
  title: "DITF 2026 Judging",
  description: "50th Dar es Salaam International Trade Fair — Exhibitor Awards Judging",
};

export default function RootLayout({ children }: { children: React.ReactNode }) {
  return (
    <html lang="en" data-theme="tantrade" className="dark">
      <body>
        <Providers>
          <div className="navbar sticky top-0 z-50 border-b border-[#FFBF00]/25 bg-[#070506]/85 backdrop-blur">
            <div className="flex-1">
              <Link href="/" className="btn btn-ghost gap-2 text-lg normal-case gold-text font-semibold hover:bg-[#FFBF00]/10">
                <img
                  src={`${process.env.NEXT_PUBLIC_BASE_PATH ?? ""}/50th.png`}
                  alt="50th DITF"
                  className="h-9 w-9 object-contain"
                />
                · Judging
              </Link>
            </div>
            <Nav />
          </div>
          <main className="mx-auto max-w-6xl p-4 md:p-6">
            <AuthGuard>{children}</AuthGuard>
          </main>
        </Providers>
      </body>
    </html>
  );
}
