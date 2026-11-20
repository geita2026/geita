"use client";

import { useEffect, useState } from "react";
import Link from "next/link";
import { api, getUser } from "../../lib/api";

type Exhibitor = { id: string; companyName: string; standLocation?: string; scoredItems: number; totalItems: number; complete: boolean; submitted: boolean };
type Assignment = { round: number; category: { id: string; name: string; group: string }; exhibitors: Exhibitor[] };

export default function JudgeHome() {
  const [data, setData] = useState<Assignment[]>([]);
  const [error, setError] = useState<string | null>(null);
  const [user, setUser] = useState<any>(null);

  useEffect(() => {
    setUser(getUser());
    api<Assignment[]>("/judge/assignments")
      .then(setData)
      .catch(() => setError("Could not load assignments. Please log in as a judge."));
  }, []);

  if (error)
    return (
      <div className="alert alert-warning">
        {error} <Link href="/login" className="link ml-2">Go to login</Link>
      </div>
    );

  const pending = data.flatMap((a) => a.exhibitors).filter((e) => !e.submitted).length;

  return (
    <div className="space-y-5">
      <div className="flex items-center justify-between">
        <h1 className="text-2xl font-bold text-primary">Judge portal</h1>
        {user?.judgeCode && <span className="badge badge-secondary">{user.judgeCode} · {user.name}</span>}
      </div>

      {data.some((a) => a.round >= 2) && (
        <div className="alert alert-warning">
          <span>🏆 <b>Grand final — Round 2.</b> You are scoring the overall first-winners. Round 1 is closed.</span>
        </div>
      )}

      {pending > 0 && (
        <div className="alert alert-warning">
          <span>
            ⚠ <b>{pending} exhibitor{pending === 1 ? "" : "s"}</b> not yet submitted. Your scores only count
            once you <b>Submit</b> each one — submitted scores are final and locked.
          </span>
        </div>
      )}

      {data.length === 0 && <p className="text-base-content/60">No categories assigned yet.</p>}

      {data.map((a) => (
        <div key={a.category.id} className="card bg-base-100 shadow">
          <div className="card-body">
            <h2 className="card-title">{a.category.name}</h2>
            <div className="overflow-x-auto">
              <table className="table">
                <thead>
                  <tr>
                    <th>Exhibitor</th>
                    <th>Stand</th>
                    <th>Progress</th>
                    <th></th>
                  </tr>
                </thead>
                <tbody>
                  {a.exhibitors.map((ex) => (
                    <tr key={ex.id}>
                      <td className="font-medium">{ex.companyName}</td>
                      <td className="text-sm text-base-content/60">{ex.standLocation || "—"}</td>
                      <td>
                        {ex.submitted ? (
                          <span className="badge badge-success gap-1">✓ Submitted</span>
                        ) : ex.complete ? (
                          <span className="badge badge-warning">Ready — not submitted</span>
                        ) : (
                          <span className="badge badge-ghost">{ex.scoredItems}/{ex.totalItems}</span>
                        )}
                      </td>
                      <td className="text-right">
                        <Link
                          href={`/judge/score/?exhibitor=${ex.id}`}
                          className={`btn btn-sm ${ex.submitted ? "btn-ghost" : "btn-primary"}`}
                        >
                          {ex.submitted ? "Review" : ex.scoredItems > 0 ? "Continue" : "Score"}
                        </Link>
                      </td>
                    </tr>
                  ))}
                  {a.exhibitors.length === 0 && (
                    <tr><td colSpan={4} className="text-center text-base-content/50">No exhibitors registered.</td></tr>
                  )}
                </tbody>
              </table>
            </div>
          </div>
        </div>
      ))}
    </div>
  );
}
