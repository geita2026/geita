import Link from "next/link";

export default function Home() {
  return (
    <div className="space-y-6">
      <div className="hero rounded-box bg-base-200 py-10">
        <div className="hero-content text-center">
          <div className="max-w-2xl">
            <h1 className="text-3xl font-bold text-primary">50th DITF 2026 — Exhibitor Awards Judging</h1>
            <p className="py-4 text-base-content/70">
              “Dar es Salaam International Trade Fair (sabasaba), The pride of Tanzania.” Register your stand,
              let the judging panel score exhibitors against the official rubric, and publish category winners.
            </p>
            <div className="flex flex-wrap justify-center gap-3">
              <Link href="/admin/exhibitors" className="btn btn-primary">Sync exhibitors</Link>
              <Link href="/judge" className="btn btn-outline">Judge portal</Link>
              <Link href="/admin/results" className="btn btn-ghost">View results</Link>
            </div>
          </div>
        </div>
      </div>

      <div className="grid gap-4 md:grid-cols-3">
        <div className="card bg-base-100 shadow">
          <div className="card-body">
            <h2 className="card-title">23 Ordinary categories</h2>
            <p className="text-sm text-base-content/70">Sectorial awards scored on a 100-point rubric across 5 groups.</p>
          </div>
        </div>
        <div className="card bg-base-100 shadow">
          <div className="card-body">
            <h2 className="card-title">Special awards</h2>
            <p className="text-sm text-base-content/70">Best Foreign / Tanzanian Manufacturer and Best Branded Pavilion.</p>
          </div>
        </div>
        <div className="card bg-base-100 shadow">
          <div className="card-body">
            <h2 className="card-title">Second round</h2>
            <p className="text-sm text-base-content/70">Ordinary first-winners re-judged for the Golden Jubilee Grand Award.</p>
          </div>
        </div>
      </div>
    </div>
  );
}
