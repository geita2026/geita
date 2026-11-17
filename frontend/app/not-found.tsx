import Link from "next/link";

export default function NotFound() {
  return (
    <main className="flex min-h-screen flex-col items-center justify-center gap-3 p-6 text-center">
      <p className="text-sm font-semibold uppercase tracking-wider text-muted">404</p>
      <h1 className="text-2xl font-semibold">Page not found</h1>
      <p className="text-muted">The page you were looking for doesn&apos;t exist or was moved.</p>
      <Link className="text-brand-ink underline-offset-4 hover:underline" href="/dashboard">
        Back to the dashboard
      </Link>
    </main>
  );
}
