"use client";

import { Suspense, useEffect, useMemo, useRef, useState } from "react";
import { useSearchParams, useRouter } from "next/navigation";
import { Button, NumberInput, Modal, ModalContent, ModalHeader, ModalBody, ModalFooter, useDisclosure } from "@heroui/react";
import { api } from "../../../lib/api";

type Item = { id: string; code: string; description: string; maxMark: number };
type Group = { id: string; title: string; maxMark: number; items: Item[] };
type Sheet = {
  exhibitor: { id: string; companyName: string; standLocation?: string };
  category: { id: string; name: string };
  rubric: { name: string; totalPoints: number; groups: Group[] };
  myScores: Record<string, number>;
  submitted: boolean;
  submittedAt: string | null;
};

type SaveState = "idle" | "pending" | "saving" | "saved" | "error";

function ScoreSheet() {
  const exhibitorId = useSearchParams().get("exhibitor") || "";
  const router = useRouter();
  const [sheet, setSheet] = useState<Sheet | null>(null);
  const [values, setValues] = useState<Record<string, number>>({});
  const [error, setError] = useState<string | null>(null);
  const [locked, setLocked] = useState(false);
  const [submittedAt, setSubmittedAt] = useState<string | null>(null);
  const [saveState, setSaveState] = useState<SaveState>("idle");
  const [submitting, setSubmitting] = useState(false);
  const confirm = useDisclosure();

  const valuesRef = useRef(values);
  valuesRef.current = values;
  const saveTimer = useRef<ReturnType<typeof setTimeout> | null>(null);

  useEffect(() => {
    if (!exhibitorId) {
      setError("No exhibitor selected.");
      return;
    }
    api<Sheet>(`/judge/exhibitors/${exhibitorId}/sheet`)
      .then((s) => {
        setSheet(s);
        setValues(s.myScores || {});
        setLocked(s.submitted);
        setSubmittedAt(s.submittedAt);
      })
      .catch(() => setError("Could not load the scoring sheet. Log in as an assigned judge."));
    return () => { if (saveTimer.current) clearTimeout(saveTimer.current); };
  }, [exhibitorId]);

  const allItems = useMemo(() => sheet?.rubric.groups.flatMap((g) => g.items) ?? [], [sheet]);
  const totalItems = allItems.length;
  const scoredItems = useMemo(() => allItems.filter((it) => values[it.id] !== undefined && !Number.isNaN(values[it.id])).length, [allItems, values]);
  const complete = totalItems > 0 && scoredItems >= totalItems;

  const groupTotals = useMemo(() => {
    const t: Record<string, number> = {};
    sheet?.rubric.groups.forEach((g) => {
      t[g.id] = g.items.reduce((sum, it) => sum + (Number(values[it.id]) || 0), 0);
    });
    return t;
  }, [values, sheet]);
  const grandTotal = useMemo(() => Object.values(groupTotals).reduce((a, b) => a + b, 0), [groupTotals]);

  function enteredItems() {
    return allItems
      .filter((it) => values[it.id] !== undefined && !Number.isNaN(values[it.id]))
      .map((it) => ({ rubricItemId: it.id, value: Number(values[it.id]) }));
  }

  async function autosave() {
    if (locked) return;
    const items = enteredItems();
    if (!items.length) return;
    setSaveState("saving");
    try {
      await api(`/judge/exhibitors/${exhibitorId}/scores`, { method: "POST", body: JSON.stringify({ items }) });
      setSaveState("saved");
    } catch (e: any) {
      // Locked elsewhere → reflect it; otherwise mark the save failed.
      if (String(e.message).includes("locked") || String(e.message).includes("submitted")) {
        setLocked(true);
        setError("This sheet was already submitted and is now locked.");
      } else {
        setSaveState("error");
      }
    }
  }

  function setItem(item: Item, v: number) {
    if (locked) return;
    setValues((prev) => {
      const next = { ...prev };
      if (Number.isNaN(v)) delete next[item.id];
      else next[item.id] = Math.max(0, Math.min(item.maxMark, Math.round(v)));
      return next;
    });
    setSaveState("pending");
    if (saveTimer.current) clearTimeout(saveTimer.current);
    saveTimer.current = setTimeout(() => void autosave(), 700);
  }

  async function doSubmit() {
    confirm.onClose();
    setSubmitting(true);
    setError(null);
    if (saveTimer.current) clearTimeout(saveTimer.current);
    try {
      const r = await api<{ submittedAt: string }>(`/judge/exhibitors/${exhibitorId}/submit`, {
        method: "POST",
        body: JSON.stringify({ items: enteredItems() }),
      });
      setLocked(true);
      setSubmittedAt(r.submittedAt);
      setSaveState("saved");
      setTimeout(() => router.push("/judge"), 1000);
    } catch (e: any) {
      let text = e.message;
      try { text = JSON.parse(e.message).error || text; } catch {}
      setError("Could not submit. " + text);
    } finally {
      setSubmitting(false);
    }
  }

  if (error && !sheet) return <div className="alert alert-warning">{error}</div>;
  if (!sheet) return <div className="flex justify-center py-10"><span className="loading loading-spinner loading-lg text-primary" /></div>;

  const pct = Math.round((grandTotal / sheet.rubric.totalPoints) * 100);
  const saveLabel: Record<SaveState, string> = { idle: "", pending: "Saving…", saving: "Saving…", saved: "Saved", error: "Save failed — retrying on next change" };

  return (
    <div className="space-y-4 pb-32">
      <div>
        <h1 className="text-2xl font-bold text-primary">{sheet.exhibitor.companyName}</h1>
        <p className="text-sm text-base-content/60">
          {sheet.category.name} · {sheet.exhibitor.standLocation || "stand location n/a"} · {sheet.rubric.name}
        </p>
      </div>

      {error && <div className="alert alert-error text-sm"><span>{error}</span></div>}

      {locked ? (
        <div className="alert alert-success text-sm">
          <span>
            ✓ <b>Submitted{submittedAt ? ` on ${new Date(submittedAt).toLocaleString()}` : ""}.</b> These scores are
            final and locked — they can no longer be changed.
          </span>
        </div>
      ) : (
        <div className="alert alert-warning text-sm">
          <span>
            Your scores <b>save automatically</b> as you type, but they only <b>count once you press Submit</b>.
            Submission is <b>final</b> — you cannot edit afterwards.
          </span>
        </div>
      )}

      {sheet.rubric.groups.map((g) => (
        <div key={g.id} className="card bg-base-100 shadow">
          <div className="card-body">
            <div className="flex items-center justify-between">
              <h2 className="card-title text-base">{g.title}</h2>
              <span className="badge badge-lg">{groupTotals[g.id] || 0} / {g.maxMark}</span>
            </div>
            <div className="divide-y divide-base-200">
              {g.items.map((it) => (
                <div key={it.id} className="flex items-center gap-4 py-3">
                  <div className="flex-1 text-sm">{it.description}</div>
                  <div className="flex items-center gap-2">
                    <NumberInput
                      aria-label={it.description}
                      size="sm"
                      minValue={0}
                      maxValue={it.maxMark}
                      step={1}
                      hideStepper
                      isDisabled={locked}
                      value={values[it.id] ?? undefined}
                      onValueChange={(v) => setItem(it, v)}
                      formatOptions={{ useGrouping: false, maximumFractionDigits: 0 }}
                      className="w-20"
                      classNames={{ input: "text-right" }}
                    />
                    <span className="text-xs text-base-content/50 w-10">/ {it.maxMark}</span>
                  </div>
                </div>
              ))}
            </div>
          </div>
        </div>
      ))}

      {/* Sticky total bar */}
      <div className="fixed inset-x-0 bottom-0 border-t border-base-300 bg-base-100/95 backdrop-blur">
        <div className="mx-auto flex max-w-6xl items-center gap-4 p-3">
          <div className="flex-1">
            <div className="flex items-center justify-between text-sm">
              <span>
                Total · <span className={complete ? "text-success" : "text-warning"}>{scoredItems}/{totalItems} scored</span>
                {!locked && saveLabel[saveState] && <span className="ml-2 text-xs text-base-content/50">{saveLabel[saveState]}</span>}
              </span>
              <span className="font-semibold">{grandTotal} / {sheet.rubric.totalPoints}</span>
            </div>
            <progress className="progress progress-primary w-full" value={pct} max={100} />
          </div>
          {locked ? (
            <Button variant="light" onPress={() => router.push("/judge")}>Back</Button>
          ) : (
            <Button color="primary" isDisabled={!complete} isLoading={submitting} onPress={confirm.onOpen}>
              {complete ? "Submit scores" : `Score all ${totalItems} items`}
            </Button>
          )}
        </div>
      </div>

      <Modal isOpen={confirm.isOpen} onOpenChange={confirm.onOpenChange} backdrop="blur">
        <ModalContent>
          {(onClose) => (
            <>
              <ModalHeader>Submit your scores?</ModalHeader>
              <ModalBody>
                <p className="text-sm text-base-content/80">
                  You are submitting <b>{grandTotal} / {sheet.rubric.totalPoints}</b> for <b>{sheet.exhibitor.companyName}</b>.
                </p>
                <p className="text-sm text-warning">Once submitted, your scores are final and cannot be changed.</p>
              </ModalBody>
              <ModalFooter>
                <Button variant="light" onPress={onClose}>Keep editing</Button>
                <Button color="primary" isLoading={submitting} onPress={doSubmit}>Submit — final</Button>
              </ModalFooter>
            </>
          )}
        </ModalContent>
      </Modal>
    </div>
  );
}

export default function ScorePage() {
  // useSearchParams must sit under a Suspense boundary for a static export build.
  return (
    <Suspense fallback={<div className="flex justify-center py-10"><span className="loading loading-spinner loading-lg text-primary" /></div>}>
      <ScoreSheet />
    </Suspense>
  );
}
