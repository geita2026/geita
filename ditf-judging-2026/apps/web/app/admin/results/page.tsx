"use client";

import { useEffect, useState } from "react";
import Link from "next/link";
import { Button, Modal, ModalContent, ModalHeader, ModalBody, ModalFooter, useDisclosure } from "@heroui/react";
import { api, getUser } from "../../../lib/api";
import { GemPyramid } from "../../../components/GemPyramid";

type Winner = { rank: number; average: number; medal: string; manual?: boolean; exhibitor: { id: string; companyName: string; standLocation?: string } };
type CategoryResult = { category: { id: string; name: string; group: string }; manual?: boolean; winners: Winner[] };
type Candidate = { id: string; companyName: string; average: number | null };
type Slot = { rank: number; exhibitorId: string; average: string };
type Finalist = { exhibitorId: string; companyName: string; sourceCategory: string; round1Average: number };
type Preview = { grandCategoryId: string | null; grandCategoryName: string | null; judgesAssigned: number; finalists: Finalist[] };

export default function ResultsPage() {
  const [data, setData] = useState<CategoryResult[]>([]);
  const [round, setRound] = useState(1);
  const [isAdmin, setIsAdmin] = useState(false);
  const [msg, setMsg] = useState<string | null>(null);
  const [busy, setBusy] = useState(false);
  const secondRound = useDisclosure();
  const clearConfirm = useDisclosure();
  const editModal = useDisclosure();
  const [editCat, setEditCat] = useState<CategoryResult["category"] | null>(null);
  const [candidates, setCandidates] = useState<Candidate[]>([]);
  const [slots, setSlots] = useState<Slot[]>([]);
  const [editLoading, setEditLoading] = useState(false);
  const [editSaving, setEditSaving] = useState(false);
  const [preview, setPreview] = useState<Preview | null>(null);
  const [previewLoading, setPreviewLoading] = useState(false);

  function load(r = round) {
    api<CategoryResult[]>(`/results?round=${r}`).then(setData).catch(() => setData([]));
  }

  useEffect(() => {
    setIsAdmin(getUser()?.role === "ADMIN");
    load(round);
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [round]);

  async function compute() {
    setBusy(true);
    setMsg(null);
    try {
      const r = await api<{ categoriesRanked: number }>(`/admin/results/compute?round=${round}`, { method: "POST" });
      setMsg(`Computed results for ${r.categoriesRanked} categories.`);
      load(round);
    } catch (e: any) {
      setMsg("Compute failed (admin login required). " + e.message);
    } finally {
      setBusy(false);
    }
  }

  async function openEdit(cat: CategoryResult["category"]) {
    setEditCat(cat);
    setCandidates([]);
    setSlots([1, 2, 3].map((rank) => ({ rank, exhibitorId: "", average: "" })));
    setEditLoading(true);
    editModal.onOpen();
    try {
      const r = await api<{ candidates: Candidate[]; current: { exhibitorId: string; rank: number; average: number }[] }>(
        `/admin/results/candidates?category=${cat.id}&round=${round}`
      );
      setCandidates(r.candidates);
      setSlots([1, 2, 3].map((rank) => {
        const cur = r.current.find((c) => c.rank === rank);
        return { rank, exhibitorId: cur?.exhibitorId ?? "", average: cur ? String(cur.average) : "" };
      }));
    } catch (e: any) {
      setMsg("Could not load candidates. " + e.message);
    } finally {
      setEditLoading(false);
    }
  }

  function pickExhibitor(rank: number, exhibitorId: string) {
    const cand = candidates.find((c) => c.id === exhibitorId);
    setSlots((prev) => prev.map((s) => (s.rank === rank ? { ...s, exhibitorId, average: exhibitorId && s.average === "" && cand?.average != null ? String(cand.average) : s.average } : s)));
  }

  async function saveEdit() {
    if (!editCat) return;
    const places = slots.filter((s) => s.exhibitorId).map((s) => ({ exhibitorId: s.exhibitorId, average: Number(s.average) || 0 }));
    if (places.length === 0) { setMsg("Pick at least one exhibitor."); return; }
    if (new Set(places.map((p) => p.exhibitorId)).size !== places.length) { setMsg("Each exhibitor can hold only one place."); return; }
    setEditSaving(true);
    try {
      await api(`/admin/results/manual`, { method: "POST", body: JSON.stringify({ categoryId: editCat.id, round, places }) });
      setMsg(`Saved manual podium for ${editCat.name}.`);
      editModal.onClose();
      load(round);
    } catch (e: any) {
      let text = e.message; try { text = JSON.parse(e.message).error || text; } catch {}
      setMsg("Could not save. " + text);
    } finally {
      setEditSaving(false);
    }
  }

  async function revertEdit() {
    if (!editCat) return;
    setEditSaving(true);
    try {
      await api(`/admin/results/revert?category=${editCat.id}&round=${round}`, { method: "POST" });
      setMsg(`Reverted ${editCat.name} to the computed ranking.`);
      editModal.onClose();
      load(round);
    } catch (e: any) {
      setMsg("Could not revert. " + e.message);
    } finally {
      setEditSaving(false);
    }
  }

  async function clearResults() {
    clearConfirm.onClose();
    setBusy(true);
    setMsg(null);
    try {
      const r = await api<{ cleared: number }>(`/admin/results/clear?round=${round}`, { method: "POST" });
      setMsg(`Cleared ${r.cleared} result row(s) for Round ${round}. Scores are untouched — Compute to rebuild.`);
      load(round);
    } catch (e: any) {
      setMsg("Could not clear results. " + e.message);
    } finally {
      setBusy(false);
    }
  }

  async function openSecondRound() {
    setPreview(null);
    setPreviewLoading(true);
    secondRound.onOpen();
    try {
      setPreview(await api<Preview>(`/admin/second-round/finalists`));
    } catch {
      setPreview(null);
    } finally {
      setPreviewLoading(false);
    }
  }

  async function startSecondRound() {
    secondRound.onClose();
    setBusy(true);
    setMsg(null);
    try {
      const r = await api<{ finalists: number; judgesAssigned: number }>(`/admin/second-round/start`, { method: "POST" });
      setMsg(
        `Second round open: ${r.finalists} finalists promoted` +
          (r.judgesAssigned === 0 ? ". ⚠ No judges are assigned to the grand final yet — add them in Assignments." : ` to ${r.judgesAssigned} assigned judge(s).`)
      );
      setRound(2);
    } catch (e: any) {
      setMsg("Could not start the second round. " + e.message);
    } finally {
      setBusy(false);
    }
  }

  const medalColor = (rank: number) => (rank === 1 ? "badge-warning" : rank === 2 ? "badge-neutral" : rank === 3 ? "badge-accent" : "badge-ghost");

  return (
    <div className="space-y-5">
      <div className="flex flex-wrap items-center justify-between gap-3">
        <h1 className="text-2xl font-bold text-primary">Results — Round {round}</h1>
        <div className="flex items-center gap-2">
          <div className="join">
            <button className={`btn btn-sm join-item ${round === 1 ? "btn-primary" : "btn-ghost"}`} onClick={() => setRound(1)}>Round 1</button>
            <button className={`btn btn-sm join-item ${round === 2 ? "btn-primary" : "btn-ghost"}`} onClick={() => setRound(2)}>Round 2</button>
          </div>
          {isAdmin && <Button color="secondary" size="sm" isLoading={busy} onPress={compute}>Compute results</Button>}
          {isAdmin && data.length > 0 && <Button variant="light" size="sm" isLoading={busy} onPress={clearConfirm.onOpen}>Clear round {round}</Button>}
          {isAdmin && <Button color="warning" size="sm" isLoading={busy} onPress={openSecondRound}>Run second round</Button>}
        </div>
      </div>

      {msg && <div className="alert alert-info text-sm">{msg}</div>}
      {!isAdmin && (
        <p className="text-sm text-base-content/60">
          Viewing published results. <Link href="/login" className="link">Admin login</Link> to compute.
        </p>
      )}

      {data.length === 0 && <p className="text-base-content/60">No results yet for this round.</p>}

      <div className="grid gap-6 md:grid-cols-2">
        {data.map((c) => (
          <div key={c.category.id} className="card bg-base-100 shadow">
            <div className="card-body gap-0 p-6">
              <div className="mb-4 flex items-center justify-between gap-2 border-b border-base-300/60 pb-3">
                <h2 className="card-title text-base">
                  {c.category.name}
                  {c.manual && <span className="badge badge-warning badge-sm ml-2">edited</span>}
                </h2>
                {isAdmin && <Button size="sm" variant="light" onPress={() => openEdit(c.category)}>Edit top 3</Button>}
              </div>
              <ul className="divide-y divide-base-300/40">
                {c.winners.slice(0, 3).map((w) => (
                  <li key={w.exhibitor.id} className="flex items-center justify-between gap-3 py-3">
                    <span className="flex items-center gap-3">
                      {w.rank <= 3 && <GemPyramid size={28} variant={w.rank === 1 ? "gold" : w.rank === 2 ? "white" : "orange"} />}
                      <span className={`badge ${medalColor(w.rank)}`}>{w.medal}</span>
                      <span className="leading-snug">{w.exhibitor.companyName}</span>
                    </span>
                    <span className="font-mono text-sm tabular-nums text-base-content/80">{w.average.toFixed(2)}</span>
                  </li>
                ))}
              </ul>
            </div>
          </div>
        ))}
      </div>

      <Modal isOpen={editModal.isOpen} onOpenChange={editModal.onOpenChange} backdrop="blur" size="2xl" scrollBehavior="inside">
        <ModalContent>
          {(onClose) => (
            <>
              <ModalHeader className="flex-col items-start gap-1">
                <span>Edit top 3 — {editCat?.name}</span>
                <span className="text-xs font-normal text-base-content/50">Round {round}. Ranked by the score you enter (highest = 1st). Overrides the computed ranking and survives Compute.</span>
              </ModalHeader>
              <ModalBody>
                {editLoading ? (
                  <div className="flex justify-center py-6"><span className="loading loading-spinner loading-lg text-primary" /></div>
                ) : (
                  <div className="space-y-4">
                    <div className="space-y-3">
                      {slots.map((s, i) => {
                        const chosenElsewhere = new Set(slots.filter((o) => o.rank !== s.rank && o.exhibitorId).map((o) => o.exhibitorId));
                        return (
                          <div key={s.rank} className="flex items-center gap-2">
                            <span className="w-16 shrink-0 text-xs text-base-content/40">Pick {i + 1}</span>
                            <select
                              className="select select-bordered select-sm flex-1"
                              value={s.exhibitorId}
                              onChange={(e) => pickExhibitor(s.rank, e.target.value)}
                            >
                              <option value="">— none —</option>
                              {candidates.map((cand) => (
                                <option key={cand.id} value={cand.id} disabled={chosenElsewhere.has(cand.id)}>
                                  {cand.companyName}{cand.average != null ? ` (${cand.average.toFixed(2)})` : " (no score)"}
                                </option>
                              ))}
                            </select>
                            <input
                              type="number"
                              step="0.01"
                              min={0}
                              placeholder="score"
                              className="input input-bordered input-sm w-24 text-right"
                              value={s.average}
                              onChange={(e) => setSlots((prev) => prev.map((o) => (o.rank === s.rank ? { ...o, average: e.target.value } : o)))}
                              disabled={!s.exhibitorId}
                            />
                          </div>
                        );
                      })}
                    </div>

                    {(() => {
                      const preview = slots
                        .filter((s) => s.exhibitorId)
                        .map((s) => ({ name: candidates.find((c) => c.id === s.exhibitorId)?.companyName ?? "—", score: Number(s.average) || 0 }))
                        .sort((a, b) => b.score - a.score);
                      const variants = ["gold", "white", "orange"] as const;
                      return preview.length > 0 ? (
                        <div className="rounded-lg border border-base-300/60 bg-base-200/40 p-3">
                          <div className="mb-2 text-xs font-medium text-base-content/60">Resulting podium (by score)</div>
                          <ul className="space-y-1.5">
                            {preview.map((w, i) => (
                              <li key={w.name} className="flex items-center gap-2 text-sm">
                                {i < 3 && <GemPyramid size={22} variant={variants[i]} />}
                                <span className={`badge badge-sm ${i === 0 ? "badge-warning" : i === 1 ? "badge-neutral" : "badge-accent"}`}>{i === 0 ? "1st" : i === 1 ? "2nd" : `${i + 1}th`}</span>
                                <span className="flex-1 truncate">{w.name}</span>
                                <span className="font-mono text-xs tabular-nums text-base-content/70">{w.score.toFixed(2)}</span>
                              </li>
                            ))}
                          </ul>
                        </div>
                      ) : (
                        <p className="text-xs text-base-content/50">Pick at least one exhibitor. To force a winner, give them the highest score.</p>
                      );
                    })()}
                  </div>
                )}
              </ModalBody>
              <ModalFooter className="justify-between">
                <Button variant="light" color="danger" isDisabled={editSaving} onPress={revertEdit}>Revert to computed</Button>
                <div className="flex gap-2">
                  <Button variant="light" onPress={onClose}>Cancel</Button>
                  <Button color="primary" isLoading={editSaving} onPress={saveEdit}>Save podium</Button>
                </div>
              </ModalFooter>
            </>
          )}
        </ModalContent>
      </Modal>

      <Modal isOpen={clearConfirm.isOpen} onOpenChange={clearConfirm.onOpenChange} backdrop="blur">
        <ModalContent>
          {(onClose) => (
            <>
              <ModalHeader>Clear Round {round} results?</ModalHeader>
              <ModalBody>
                <p className="text-sm text-base-content/80">
                  This removes the published rankings for <b>Round {round}</b> only.
                </p>
                <p className="text-sm text-base-content/60">
                  Judges&apos; scores and submissions are <b>not</b> affected — pressing <b>Compute results</b> will
                  rebuild the exact same rankings. Use this to reverse an accidental compute.
                </p>
              </ModalBody>
              <ModalFooter>
                <Button variant="light" onPress={onClose}>Cancel</Button>
                <Button color="danger" isLoading={busy} onPress={clearResults}>Clear Round {round}</Button>
              </ModalFooter>
            </>
          )}
        </ModalContent>
      </Modal>

      <Modal isOpen={secondRound.isOpen} onOpenChange={secondRound.onOpenChange} backdrop="blur" size="2xl">
        <ModalContent>
          {(onClose) => {
            const noFinalists = !previewLoading && (preview?.finalists.length ?? 0) === 0;
            const noJudges = !previewLoading && !!preview && preview.judgesAssigned === 0;
            return (
              <>
                <ModalHeader>Start the grand final?</ModalHeader>
                <ModalBody>
                  <p className="text-sm text-base-content/80">
                    This takes the <b>winner of each category</b> and enters them into the final round
                    to decide the overall champion. Judges assigned to the grand final start scoring round 2.
                  </p>

                  {previewLoading && (
                    <div className="flex items-center gap-2 py-2 text-sm text-base-content/60">
                      <span className="loading loading-spinner loading-sm" /> Checking finalists…
                    </div>
                  )}

                  {!previewLoading && preview && (
                    <div className="rounded-lg border border-base-300 bg-base-200/40 p-3 text-sm">
                      <div className="flex items-center justify-between">
                        <span>Finalists to promote</span>
                        <span className="badge badge-primary">{preview.finalists.length}</span>
                      </div>
                      <div className="mt-1 flex items-center justify-between">
                        <span>Judges assigned to the grand final</span>
                        <span className={`badge ${preview.judgesAssigned === 0 ? "badge-warning" : "badge-success"}`}>{preview.judgesAssigned}</span>
                      </div>
                      {preview.finalists.length > 0 && (
                        <ul className="mt-2 max-h-40 space-y-0.5 overflow-y-auto text-xs text-base-content/70">
                          {preview.finalists.map((f) => (
                            <li key={f.exhibitorId} className="flex justify-between gap-3">
                              <span className="truncate">{f.companyName}</span>
                              <span className="shrink-0 text-base-content/40">{f.sourceCategory}</span>
                            </li>
                          ))}
                        </ul>
                      )}
                    </div>
                  )}

                  {noFinalists && (
                    <div className="alert alert-warning text-sm">
                      <span>No category winners yet. Click <b>Compute results</b> on Round 1 first — there&apos;s nothing to promote.</span>
                    </div>
                  )}

                  {noJudges && !noFinalists && (
                    <div className="alert alert-warning text-sm">
                      <span>⚠ No judges are assigned to the grand final yet. You can still open the round now and assign them on the <b>Assignments</b> page — finalists appear for them immediately.</span>
                    </div>
                  )}
                </ModalBody>
                <ModalFooter>
                  <Button variant="light" onPress={onClose}>Cancel</Button>
                  <Button color="warning" isLoading={busy} isDisabled={previewLoading || noFinalists} onPress={startSecondRound}>
                    Start grand final
                  </Button>
                </ModalFooter>
              </>
            );
          }}
        </ModalContent>
      </Modal>
    </div>
  );
}
