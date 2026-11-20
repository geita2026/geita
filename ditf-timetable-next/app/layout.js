import "./globals.css";
import { Providers } from "./providers";

export const metadata = {
  title: "50th DITF 2026 — Official Closing Ceremony",
  description:
    "Live countdown timetable for the Official Closing Ceremony of the 50th Dar es Salaam International Trade Fair (SabaSaba), 13 July 2026.",
};

export default function RootLayout({ children }) {
  return (
    <html lang="en" data-theme="dark" className="dark">
      <head>
        {/* URL-argument dispatch: open a specific timetable via ?event=
            e.g. /timetable/?event=award-night → Golden Jubilee Awards Night.
            Runs before render so the Opening Ceremony never flashes first. */}
        <script
          dangerouslySetInnerHTML={{
            __html:
              "(function(){try{var e=(new URLSearchParams(location.search).get('event')||'').toLowerCase();" +
              "if(e==='award-night'||e==='awards'||e==='award'){location.replace('/timetable/award-night/index.html');}}catch(_){}}())",
          }}
        />
        <link
          rel="stylesheet"
          href="https://fonts.googleapis.com/css?family=Montserrat:400,600,700"
        />
        <link rel="stylesheet" href="https://fonts.cdnfonts.com/css/noto-sans-display-2" />
      </head>
      <body>
        <Providers>{children}</Providers>
      </body>
    </html>
  );
}
