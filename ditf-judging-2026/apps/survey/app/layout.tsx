import type { Metadata } from "next";
import "./globals.css";

export const metadata: Metadata = {
  title: "Fomu ya Maoni — DITF",
  description: "Fomu ya Maoni ya Semina ya Awali ya Washiriki — Maonyesho ya Biashara ya Kimataifa ya Dar es Salaam",
};

export default function RootLayout({ children }: { children: React.ReactNode }) {
  return (
    <html lang="sw" data-theme="tantrade" className="dark">
      <body>
        <div className="navbar sticky top-0 z-50 border-b border-[#FFBF00]/25 bg-[#070506]/85 backdrop-blur">
          <div className="mx-auto flex w-full max-w-3xl items-center gap-2 px-4">
            <img
              src={`${process.env.NEXT_PUBLIC_BASE_PATH ?? ""}/50th.png`}
              alt="50th DITF"
              className="h-9 w-9 object-contain"
            />
            <span className="text-lg font-semibold gold-text">· Maoni</span>
          </div>
        </div>
        <main className="mx-auto max-w-3xl p-4 md:p-6">{children}</main>
      </body>
    </html>
  );
}
