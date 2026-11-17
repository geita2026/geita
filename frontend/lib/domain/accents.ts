/**
 * Brand accents. The whole portal takes its colour from one of these — the lit
 * nav item, primary buttons, progress bars, the sidebar glow. Ported from the
 * aio-magic accent set so the two apps share a palette.
 *
 * `on` is the text colour that sits on a filled accent, chosen for WCAG
 * contrast rather than by eye. The CSS lives in styles/globals.css keyed on
 * `[data-accent="…"]`; this list drives the picker and validates the cookie.
 */
export type Accent = {
  key: string;
  name: string;
  /** Space-separated channels so CSS can take them at any alpha. */
  rgb: string;
  bright: string;
  deep: string;
  on: string;
};

export const ACCENTS: Accent[] = [
  { key: "lime", name: "Lime", rgb: "203 248 46", bright: "228 255 134", deep: "143 191 0", on: "17 17 20" },
  { key: "mint", name: "Mint", rgb: "191 235 226", bright: "218 245 239", deep: "143 191 178", on: "17 17 20" },
  { key: "blue", name: "Blue", rgb: "59 130 246", bright: "147 197 253", deep: "29 78 216", on: "17 17 20" },
  { key: "green", name: "Green", rgb: "34 197 94", bright: "134 239 172", deep: "21 128 61", on: "17 17 20" },
  { key: "yellow", name: "Yellow", rgb: "234 179 8", bright: "253 224 71", deep: "161 98 7", on: "17 17 20" },
  { key: "orange", name: "Orange", rgb: "249 115 22", bright: "253 186 116", deep: "194 65 12", on: "17 17 20" },
  { key: "red", name: "Red", rgb: "239 68 68", bright: "252 165 165", deep: "185 28 28", on: "17 17 20" },
  { key: "magenta", name: "Magenta", rgb: "236 72 153", bright: "249 168 212", deep: "190 24 93", on: "17 17 20" },
  { key: "violet", name: "Violet", rgb: "139 92 246", bright: "196 181 253", deep: "109 40 217", on: "17 17 20" },
  { key: "cyan", name: "Cyan", rgb: "6 182 212", bright: "103 232 249", deep: "14 116 144", on: "17 17 20" },
  { key: "coral", name: "Coral", rgb: "255 122 92", bright: "255 173 152", deep: "199 74 48", on: "17 17 20" },
];

/** Gold on black is the brand. Users can still pick another accent in Settings. */
export const DEFAULT_ACCENT = "yellow";

/**
 * Cookie the chosen accent is stored in. Prefixed because browsers share
 * cookies between ports on one host: an unprefixed `accent` set by another app
 * on localhost (the Project Tracker uses that name) would recolour GeMEMIS.
 */
export const ACCENT_COOKIE = "gememis_accent";

export function isAccent(value: string | undefined): value is string {
  return !!value && ACCENTS.some((a) => a.key === value);
}

export function accentOr(value: string | undefined) {
  return isAccent(value) ? value : DEFAULT_ACCENT;
}
