"use client";

import { useState } from "react";
import { Button, Modal, ModalContent, ModalHeader, ModalBody, ModalFooter, useDisclosure } from "@heroui/react";
import { api } from "../../../lib/api";

type Check = { label: string; ok: boolean };
type SelfTest = { ok: boolean; pass: number; fail: number; checks: Check[]; error?: string };

const PHASES = ["REGISTRATION", "SCORING", "R1_DONE", "SECOND_ROUND", "CLOSED"] as const;

export default function ToolsPage() {
  // Reset selection
  const [results, setResults] = useState(true);
  const [scores, setScores] = useState(false);
  const [exhibitors, setExhibitors] = useState(false);
  const [assignments, setAssignments] = useState(false);
  const [setPhase, setSetPhase] = useState(true);
  const [phase, setPhase_] = useState<(typeof PHASES)[number]>("SCORING");

  const [resetMsg, setResetMsg] = useState<string | null>(null);
  const [resetting, setResetting] = useState(false);

  const [test, setTest] = useState<SelfTest | null>(null);
  const [testing, setTesting] = useState(false);

  const confirmReset = useDisclosure();

  const nothing = !results && !scores && !exhibitors && !assignments && !setPhase;

  const resetParts = [
    results && "computed results",
    scores && "judge scores",
    exhibitors && "exhibitors (and their scores/results)",
    assignments && "judge assignments",
    setPhase && `set phase → ${phase}`,
  ].filter(Boolean) as string[];

  async function runReset() {
    confirmReset.onClose();
    setResetting(true);
    setResetMsg(null);
    try {
      const body = JSON.stringify({ results, scores, exhibitors, assignments, phase: setPhase ? phase : null });
      const r = await api<{ cleared: Record<string, number | string> }>("/admin/reset", { method: "POST", body });
      const lines = Object.entries(r.cleared).map(([k, v]) => (k === "phase" ? `phase set to ${v}` : `${v} ${k} cleared`));
      setResetMsg("Done — " + (lines.join(", ") || "nothing changed") + ".");
    } catch (e: any) {
      setResetMsg("Reset failed. " + e.message);
    } finally {
      setResetting(false);
    }
  }

  async function runSelfTest() {
    setTesting(true);
    setTest(null);
    try {
      setTest(await api<SelfTest>("/admin/self-test", { method: "POST" }));
    } catch (e: any) {
      setTest({ ok: false, pass: 0, fail: 1, checks: [], error: e.message });
    } finally {
      setTesting(false);
    }
  }

  const Box = ({ on, set, label, hint }: { on: boolean; set: (b: boolean) => void; label: string; hint: string }) => (
    <label className="flex cursor-pointer items-start gap-3 rounded-lg p-2 hover:bg-base-200">
      <input type="checkbox" className="checkbox checkbox-sm mt-0.5" checked={on} onChange={(e) => set(e.target.checked)} />
      <span>
        <span className="font-medium">{label}</span>
        <span className="block text-xs text-base-content/60">{hint}</span>
      </span>
    </label>
  );

  return (
    <div className="space-y-6">
      <h1 className="text-2xl font-bold text-primary">Admin tools</h1>

      {/* Self-test */}
      <div className="card bg-base-100 shadow">
        <div className="card-body">
          <div className="flex items-center justify-between">
            <div>
              <h2 className="card-title text-base">Self-test</h2>
              <p className="text-sm text-base-content/60">Runs the full flow (score → rank → second round → grand winner) on a throwaway event, then cleans up. Touches no real data.</p>
            </div>
            <Button color="primary" size="sm" isLoading={testing} onPress={runSelfTest}>Run self-test</Button>
          </div>
          {test && (
            <div className={`mt-2 rounded-lg border p-3 text-sm ${test.ok ? "border-success/40 bg-success/10" : "border-error/40 bg-error/10"}`}>
              <div className="mb-1 font-semibold">{test.ok ? "✓ All checks passed" : "✗ Some checks failed"} — {test.pass} passed, {test.fail} failed</div>
              <ul className="space-y-0.5">
                {test.checks.map((c, i) => (
                  <li key={i} className={c.ok ? "text-success" : "text-error"}>{c.ok ? "✓" : "✗"} {c.label}</li>
                ))}
              </ul>
              {test.error && <p className="mt-1 text-error">Error: {test.error}</p>}
            </div>
          )}
        </div>
      </div>

      {/* Reset */}
      <div className="card border border-error/30 bg-base-100 shadow">
        <div className="card-body">
          <h2 className="card-title text-base text-error">Reset event data</h2>
          <p className="text-sm text-base-content/60">Tick exactly what to clear. Each part is scoped to this event and cannot be undone.</p>

          <div className="grid gap-1 sm:grid-cols-2">
            <Box on={results} set={setResults} label="Computed results" hint="Ranks & averages (round 1 + 2). Re-run Compute to rebuild." />
            <Box on={scores} set={setScores} label="Judge scores" hint="All raw rubric scores. Judges must re-score." />
            <Box on={exhibitors} set={setExhibitors} label="Exhibitors" hint="Removes registrations (and their scores/results)." />
            <Box on={assignments} set={setAssignments} label="Judge assignments" hint="Unassigns judges from every category." />
          </div>

          <label className="mt-2 flex cursor-pointer items-center gap-3 rounded-lg p-2 hover:bg-base-200">
            <input type="checkbox" className="checkbox checkbox-sm" checked={setPhase} onChange={(e) => setSetPhase(e.target.checked)} />
            <span className="font-medium">Set event phase to</span>
            <select className="select select-bordered select-sm" value={phase} disabled={!setPhase} onChange={(e) => setPhase_(e.target.value as any)}>
              {PHASES.map((p) => <option key={p} value={p}>{p}</option>)}
            </select>
          </label>

          {resetMsg && <div className="alert alert-info text-sm">{resetMsg}</div>}

          <div className="card-actions justify-end">
            <Button color="danger" size="sm" isLoading={resetting} isDisabled={nothing} onPress={confirmReset.onOpen}>Reset selected</Button>
          </div>
        </div>
      </div>

      <Modal isOpen={confirmReset.isOpen} onOpenChange={confirmReset.onOpenChange} backdrop="blur">
        <ModalContent>
          {(onClose) => (
            <>
              <ModalHeader className="text-error">Confirm reset</ModalHeader>
              <ModalBody>
                <p className="text-sm text-base-content/80">This will permanently:</p>
                <ul className="list-disc space-y-1 pl-5 text-sm">
                  {resetParts.map((p) => <li key={p}>{p}</li>)}
                </ul>
                <p className="text-sm font-medium text-error">This cannot be undone.</p>
              </ModalBody>
              <ModalFooter>
                <Button variant="light" onPress={onClose}>Cancel</Button>
                <Button color="danger" isLoading={resetting} onPress={runReset}>Reset</Button>
              </ModalFooter>
            </>
          )}
        </ModalContent>
      </Modal>
    </div>
  );
}
