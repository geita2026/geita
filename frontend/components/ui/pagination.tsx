import Link from "next/link";

export function Pagination({ page, pages, params }: { page: number; pages: number; params: URLSearchParams }) {
  if (pages <= 1) return null;
  const href = (p: number) => {
    const next = new URLSearchParams(params);

    next.set("page", String(p));

    return `?${next.toString()}`;
  };
  const cls =
    "rounded-md border border-separator px-2.5 py-1 text-sm hover:bg-default aria-disabled:pointer-events-none aria-disabled:opacity-40";

  return (
    <nav aria-label="Pagination" className="flex items-center gap-2 text-sm">
      <Link aria-disabled={page <= 1} className={cls} href={href(page - 1)}>
        Previous
      </Link>
      <span className="text-muted">
        Page {page} of {pages}
      </span>
      <Link aria-disabled={page >= pages} className={cls} href={href(page + 1)}>
        Next
      </Link>
    </nav>
  );
}
