"use client";

import { useEffect, useRef, useState } from "react";
import { Button } from "@heroui/react";
import { QRCodeCanvas } from "qrcode.react";
import { api, getToken } from "../../../lib/api";

type Response = {
  id: string;
  companyName: string;
  participant: string;
  phone?: string;
  surveyDate?: string;
  q1?: string;
  q2?: string;
  q3?: string;
  q4: string[];
  q5?: string;
  q6?: string;
  q7?: string;
  createdAt: string;
};
type Data = {
  total: number;
  options: { q1: string[]; q2: string[]; q3: string[]; q4: string[] };
  tallies: { q1: Record<string, number>; q2: Record<string, number>; q3: Record<string, number>; q4: Record<string, number> };
  responses: Response[];
};

const API = process.env.NEXT_PUBLIC_API_URL || "http://localhost:4000";

export default function SurveyAdminPage() {
  const [data, setData] = useState<Data | null>(null);
  const [error, setError] = useState<string | null>(null);
  const [downloading, setDownloading] = useState(false);
  const [surveyUrl, setSurveyUrl] = useState("");
  const [copied, setCopied] = useState(false);
  const qrRef = useRef<HTMLDivElement>(null);

  useEffect(() => {
    setSurveyUrl(`${window.location.origin}/survey/`);
    api<Data>("/survey/responses").then(setData).catch(() => setError("Could not load responses. Admin login required."));
  }, []);

  async function copyLink() {
    await navigator.clipboard.writeText(surveyUrl);
    setCopied(true);
    setTimeout(() => setCopied(false), 1500);
  }

  function downloadQr() {
    const canvas = qrRef.current?.querySelector("canvas");
    if (!canvas) return;
    const a = document.createElement("a");
    a.href = canvas.toDataURL("image/png");
    a.download = "ditf-survey-qr.png";
    a.click();
  }

  async function downloadCsv() {
    setDownloading(true);
    try {
      const res = await fetch(`${API}/survey/responses.csv`, { headers: { Authorization: `Bearer ${getToken()}` } });
      const blob = await res.blob();
      const url = URL.createObjectURL(blob);
      const a = document.createElement("a");
      a.href = url;
      a.download = "ditf-survey-responses.csv";
      a.click();
      URL.revokeObjectURL(url);
    } finally {
      setDownloading(false);
    }
  }

  if (error) return <div className="alert alert-warning">{error}</div>;
  if (!data) return <div className="flex justify-center py-10"><span className="loading loading-spinner loading-lg text-primary" /></div>;

  return (
    <div className="space-y-6">
      <div className="flex flex-wrap items-center justify-between gap-3">
        <h1 className="text-2xl font-bold text-primary">Survey responses <span className="text-base font-normal text-base-content/50">· {data.total}</span></h1>
        <Button color="secondary" size="sm" isLoading={downloading} isDisabled={data.total === 0} onPress={downloadCsv}>Export CSV</Button>
      </div>

      {/* Share — give participants the link / QR to the public form */}
      <div className="card bg-base-100 shadow">
        <div className="card-body flex-col gap-4 sm:flex-row sm:items-center">
          <div ref={qrRef} className="mx-auto shrink-0 rounded-lg bg-white p-2 sm:mx-0">
            {surveyUrl && <QRCodeCanvas value={surveyUrl} size={132} level="M" includeMargin={false} />}
          </div>
          <div className="flex-1 space-y-2">
            <h2 className="card-title text-base">Share the survey</h2>
            <p className="text-sm text-base-content/60">Participants scan the code or open the link to fill the feedback form — no login needed.</p>
            <div className="flex flex-wrap items-center gap-2">
              <code className="rounded bg-base-200 px-2 py-1 text-sm break-all">{surveyUrl || "…"}</code>
            </div>
            <div className="flex gap-2">
              <Button color="secondary" size="sm" onPress={copyLink}>{copied ? "Copied!" : "Copy link"}</Button>
              <Button color="secondary" size="sm" onPress={downloadQr}>Download QR</Button>
            </div>
          </div>
        </div>
      </div>

      {data.total === 0 && <p className="text-base-content/60">No responses yet.</p>}

      {data.total > 0 && (
        <>
          {/* Tallies */}
          <div className="grid gap-4 md:grid-cols-2">
            <Tally title="1. Kuridhika na mafunzo" options={data.options.q1} counts={data.tallies.q1} total={data.total} />
            <Tally title="2. Manufaa ya maudhui" options={data.options.q2} counts={data.tallies.q2} total={data.total} />
            <Tally title="3. Ufanisi wa wakufunzi" options={data.options.q3} counts={data.tallies.q3} total={data.total} />
            <Tally title="4. Mada zilizofaa zaidi" options={data.options.q4} counts={data.tallies.q4} total={data.total} />
          </div>

          {/* Responses */}
          <div className="space-y-4">
            {data.responses.map((r) => (
              <div key={r.id} className="card bg-base-100 shadow">
                <div className="card-body gap-2">
                  <div className="flex flex-wrap items-baseline justify-between gap-2">
                    <h2 className="card-title text-base">{r.companyName}</h2>
                    <span className="text-xs text-base-content/50">{new Date(r.createdAt).toLocaleString()}</span>
                  </div>
                  <p className="text-sm text-base-content/70">{r.participant}{r.phone ? ` · ${r.phone}` : ""}{r.surveyDate ? ` · ${r.surveyDate}` : ""}</p>
                  <div className="flex flex-wrap gap-2 text-xs">
                    {r.q1 && <span className="badge badge-ghost">Kuridhika: {r.q1}</span>}
                    {r.q2 && <span className="badge badge-ghost">Maudhui: {r.q2}</span>}
                    {r.q3 && <span className="badge badge-ghost">Wakufunzi: {r.q3}</span>}
                    {r.q4.map((t) => <span key={t} className="badge badge-warning badge-outline">{t}</span>)}
                  </div>
                  {(r.q5 || r.q6 || r.q7) && (
                    <dl className="mt-1 space-y-1 text-sm">
                      {r.q5 && <Answer q="Ulichopenda zaidi" a={r.q5} />}
                      {r.q6 && <Answer q="Kuboreshwa" a={r.q6} />}
                      {r.q7 && <Answer q="Maoni ya ziada" a={r.q7} />}
                    </dl>
                  )}
                </div>
              </div>
            ))}
          </div>
        </>
      )}
    </div>
  );
}

function Tally({ title, options, counts, total }: { title: string; options: string[]; counts: Record<string, number>; total: number }) {
  return (
    <div className="card bg-base-100 shadow">
      <div className="card-body gap-3">
        <h2 className="card-title text-sm">{title}</h2>
        <div className="space-y-2">
          {options.map((o) => {
            const n = counts[o] || 0;
            const pct = total ? Math.round((n / total) * 100) : 0;
            return (
              <div key={o}>
                <div className="flex justify-between text-xs text-base-content/70"><span>{o}</span><span>{n} · {pct}%</span></div>
                <progress className="progress progress-warning h-1.5 w-full" value={pct} max={100} />
              </div>
            );
          })}
        </div>
      </div>
    </div>
  );
}

function Answer({ q, a }: { q: string; a: string }) {
  return (
    <div>
      <dt className="text-xs uppercase tracking-wide text-base-content/40">{q}</dt>
      <dd className="whitespace-pre-wrap text-base-content/85">{a}</dd>
    </div>
  );
}
