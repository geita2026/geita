"use client";

import { useEffect, useState } from "react";
import { submitSurvey } from "../lib/api";

const Q1 = ["Kuridhika sana", "Kuridhika", "Kutoridhika", "Kutoridhika sana"];
const Q2 = ["Muhimu sana", "Muhimu", "Muhimu kwa kiasi fulani", "Haifai"];
const Q3 = ["Bora", "Nzuri", "Nzuri kiasi", "Haikidhi"];
const Q4 = ["Kanuni za Ushiriki", "Uchaguzi wa banda bora", "Mjue Jirani yako", "Ulinzi na Usalama", "Mazingira", "Afya"];

type Form = {
  website: string; // honeypot — stays empty for real users
  companyName: string;
  participant: string;
  phone: string;
  surveyDate: string;
  q1?: string;
  q2?: string;
  q3?: string;
  q4: string[];
  q5: string;
  q6: string;
  q7: string;
};

const EMPTY: Form = { website: "", companyName: "", participant: "", phone: "", surveyDate: "", q4: [], q5: "", q6: "", q7: "" };

export default function SurveyPage() {
  const [f, setF] = useState<Form>(EMPTY);
  const [saving, setSaving] = useState(false);
  const [error, setError] = useState<string | null>(null);
  const [done, setDone] = useState(false);

  // Prefill today's date (client-only to avoid a hydration mismatch).
  useEffect(() => {
    setF((p) => (p.surveyDate ? p : { ...p, surveyDate: new Date().toISOString().slice(0, 10) }));
  }, []);

  const set = <K extends keyof Form>(k: K, v: Form[K]) => setF((p) => ({ ...p, [k]: v }));
  const toggleTopic = (t: string) =>
    setF((p) => ({ ...p, q4: p.q4.includes(t) ? p.q4.filter((x) => x !== t) : [...p.q4, t] }));

  async function submit() {
    setError(null);
    if (!f.companyName.trim() || !f.participant.trim()) {
      setError("Tafadhali jaza jina la kampuni na jina la mshiriki.");
      window.scrollTo({ top: 0, behavior: "smooth" });
      return;
    }
    setSaving(true);
    try {
      await submitSurvey({ ...f, phone: f.phone || undefined, surveyDate: f.surveyDate || undefined });
      setDone(true);
      window.scrollTo({ top: 0, behavior: "smooth" });
    } catch (e: any) {
      setError(e.message);
    } finally {
      setSaving(false);
    }
  }

  if (done) {
    return (
      <div className="card bg-base-100 shadow">
        <div className="card-body items-center text-center">
          <div className="text-5xl">🎉</div>
          <h1 className="card-title gold-text">Asante kwa maoni yako!</h1>
          <p className="text-base-content/70">Majibu yako yamepokelewa. Tunathamini muda wako.</p>
          <button
            className="btn btn-secondary btn-sm mt-2"
            onClick={() => {
              setF({ ...EMPTY, surveyDate: new Date().toISOString().slice(0, 10) });
              setDone(false);
            }}
          >
            Jaza fomu nyingine
          </button>
        </div>
      </div>
    );
  }

  return (
    <div className="space-y-5 pb-10">
      <header className="space-y-1">
        <h1 className="text-2xl font-bold gold-text">Fomu ya Maoni ya Semina ya Awali ya Washiriki</h1>
        <p className="text-sm text-base-content/60">Maonyesho ya 50 ya Biashara ya Kimataifa ya Dar es Salaam (DITF)</p>
      </header>

      {/* Honeypot — off-screen, not announced; real users never fill it. */}
      <input
        type="text"
        name="website"
        tabIndex={-1}
        autoComplete="off"
        aria-hidden="true"
        value={f.website}
        onChange={(e) => set("website", e.target.value)}
        style={{ position: "absolute", left: "-9999px", width: 1, height: 1, opacity: 0 }}
      />

      {error && <div className="alert alert-warning text-sm"><span>{error}</span></div>}

      {/* Identity */}
      <div className="card bg-base-100 shadow">
        <div className="card-body gap-4">
          <Field label="Jina la Kampuni / Taasisi" required>
            <input className="input input-bordered w-full" value={f.companyName} onChange={(e) => set("companyName", e.target.value)} />
          </Field>
          <Field label="Jina la Mshiriki" required>
            <input className="input input-bordered w-full" value={f.participant} onChange={(e) => set("participant", e.target.value)} />
          </Field>
          <div className="grid gap-4 sm:grid-cols-2">
            <Field label="Namba ya Simu">
              <input className="input input-bordered w-full" inputMode="tel" value={f.phone} onChange={(e) => set("phone", e.target.value)} />
            </Field>
            <Field label="Tarehe">
              <input type="date" className="input input-bordered w-full" value={f.surveyDate} onChange={(e) => set("surveyDate", e.target.value)} />
            </Field>
          </div>
        </div>
      </div>

      <ChoiceCard n={1} title="Je, umeridhishwa kwa kiasi gani na Mafunzo ya awali ya ushiriki?" options={Q1} value={f.q1} onChange={(v) => set("q1", v)} />
      <ChoiceCard n={2} title="Maudhui yaliyowasilishwa yalikuwa na manufaa kwa kiasi gani?" options={Q2} value={f.q2} onChange={(v) => set("q2", v)} />
      <ChoiceCard n={3} title="Je, Wakufunzi walikuwa na ufanisi kiasi gani katika kutoa maudhui?" options={Q3} value={f.q3} onChange={(v) => set("q3", v)} />

      {/* Q4 — multi-select */}
      <div className="card bg-base-100 shadow">
        <div className="card-body">
          <h2 className="font-semibold"><span className="gold-text">4.</span> Ni mada gani ilikufaa zaidi? <span className="text-xs font-normal text-base-content/50">(unaweza kuchagua zaidi ya moja)</span></h2>
          <div className="mt-1 grid gap-2 sm:grid-cols-2">
            {Q4.map((t) => (
              <label key={t} className="flex cursor-pointer items-center gap-3 rounded-lg border border-base-300 p-3 hover:bg-base-200">
                <input type="checkbox" className="checkbox checkbox-warning checkbox-sm" checked={f.q4.includes(t)} onChange={() => toggleTopic(t)} />
                <span className="text-sm">{t}</span>
              </label>
            ))}
          </div>
        </div>
      </div>

      <TextCard n={5} title="Ni nini ulichopenda zaidi kuhusu Mafunzo?" value={f.q5} onChange={(v) => set("q5", v)} />
      <TextCard n={6} title="Ni nini kinaweza kuboreshwa katika vipindi vijavyo?" value={f.q6} onChange={(v) => set("q6", v)} />
      <TextCard n={7} title="Maoni au Mapendekezo yoyote ya ziada?" value={f.q7} onChange={(v) => set("q7", v)} />

      <button className={`btn btn-primary w-full ${saving ? "btn-disabled" : ""}`} onClick={submit}>
        {saving ? <span className="loading loading-spinner loading-sm" /> : "Tuma Maoni"}
      </button>
    </div>
  );
}

function Field({ label, required, children }: { label: string; required?: boolean; children: React.ReactNode }) {
  return (
    <label className="block">
      <span className="mb-1 block text-sm text-base-content/70">{label}{required && <span className="text-error"> *</span>}</span>
      {children}
    </label>
  );
}

function ChoiceCard({ n, title, options, value, onChange }: { n: number; title: string; options: string[]; value?: string; onChange: (v: string) => void }) {
  return (
    <div className="card bg-base-100 shadow">
      <div className="card-body">
        <h2 className="font-semibold"><span className="gold-text">{n}.</span> {title}</h2>
        <div className="mt-1 grid gap-2 sm:grid-cols-2">
          {options.map((o) => (
            <label key={o} className={`flex cursor-pointer items-center gap-3 rounded-lg border p-3 hover:bg-base-200 ${value === o ? "border-primary bg-primary/10" : "border-base-300"}`}>
              <input type="radio" name={`q${n}`} className="radio radio-warning radio-sm" checked={value === o} onChange={() => onChange(o)} />
              <span className="text-sm">{o}</span>
            </label>
          ))}
        </div>
      </div>
    </div>
  );
}

function TextCard({ n, title, value, onChange }: { n: number; title: string; value: string; onChange: (v: string) => void }) {
  return (
    <div className="card bg-base-100 shadow">
      <div className="card-body">
        <h2 className="font-semibold"><span className="gold-text">{n}.</span> {title}</h2>
        <textarea className="textarea textarea-bordered mt-1 min-h-24 w-full" value={value} onChange={(e) => onChange(e.target.value)} />
      </div>
    </div>
  );
}
