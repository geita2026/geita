import { differenceInCalendarDays, format, parseISO } from "date-fns";

/** Compact TZS: 2.4 bn, 185 M, 45 K */
export function tzsCompact(value: number | string | { toString(): string } | null | undefined) {
  const n = Number(value ?? 0);
  const abs = Math.abs(n);

  if (abs >= 1e9) return `${trim((n / 1e9).toFixed(2))} bn`;
  if (abs >= 1e6) return `${trim((n / 1e6).toFixed(1))} M`;
  if (abs >= 1e3) return `${(n / 1e3).toFixed(0)} K`;

  return n.toLocaleString("en-TZ");
}

/** Full TZS with thousands separators. */
export function tzs(value: number | string | { toString(): string } | null | undefined) {
  return `TZS ${Number(value ?? 0).toLocaleString("en-TZ", { maximumFractionDigits: 0 })}`;
}

function trim(s: string) {
  return s.replace(/\.?0+$/, "");
}

export function fmtDate(d: Date | string | null | undefined, pattern = "dd MMM yyyy") {
  if (!d) return "—";
  const date = typeof d === "string" ? parseISO(d) : d;

  return format(date, pattern);
}

export function fmtDateTime(d: Date | string | null | undefined) {
  return fmtDate(d, "dd MMM yyyy, HH:mm");
}

/** yyyy-MM-dd for <input type="date"> */
export function toInputDate(d: Date | null | undefined) {
  return d ? format(d, "yyyy-MM-dd") : "";
}

export function daysBetween(a: Date, b: Date) {
  return differenceInCalendarDays(b, a);
}

export function pct(part: number, total: number) {
  return total > 0 ? Math.round((part / total) * 100) : 0;
}
