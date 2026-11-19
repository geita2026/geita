import { ArrowDownToLine } from "@gravity-ui/icons";

/** A file served through /api/files (PDF, CSV, uploaded document). Opens in a new tab. */
export function DownloadLink({
  path,
  children,
  variant = "secondary",
  size = "md",
}: {
  path: string;
  children: React.ReactNode;
  variant?: "primary" | "secondary" | "ghost";
  size?: "sm" | "md";
}) {
  return (
    <a
      className={`button button--${variant} button--${size}`}
      href={`/api/files/${path}`}
      rel="noopener"
      target="_blank"
    >
      <ArrowDownToLine className="size-4" />
      {children}
    </a>
  );
}
