"use client";

import { useEffect, useMemo, useState } from "react";
import Link from "next/link";
import { Button, Modal, ModalContent, ModalHeader, ModalBody, ModalFooter, useDisclosure } from "@heroui/react";
import { api, getUser } from "../../../lib/api";

type Exhibitor = {
  id: string;
  companyName: string;
  standLocation?: string;
  source: string;
  category?: { name: string; group: string };
  _count?: { scores: number; submissions: number; results: number };
};

export default function AdminExhibitorsPage() {
  const [list, setList] = useState<Exhibitor[]>([]);
  const [isAdmin, setIsAdmin] = useState(false);
  const [configured, setConfigured] = useState<boolean | null>(null);
  const [busy, setBusy] = useState(false);
  const [msg, setMsg] = useState<{ type: "success" | "error" | "info"; text: string } | null>(null);
  const [target, setTarget] = useState<Exhibitor | null>(null);
  const [deleting, setDeleting] = useState(false);
  const confirmDel = useDisclosure();

  function load() {
    api<Exhibitor[]>("/exhibitors").then(setList).catch(() => setList([]));
  }

  // Names that appear more than once — flagged so duplicates are easy to spot.
  const dupeNames = useMemo(() => {
    const counts = new Map<string, number>();
    for (const e of list) {
      const k = e.companyName.trim().toLowerCase();
      counts.set(k, (counts.get(k) || 0) + 1);
    }
    return new Set([...counts].filter(([, n]) => n > 1).map(([k]) => k));
  }, [list]);

  const hasScores = (e: Exhibitor) => !!(e._count && (e._count.scores || e._count.submissions || e._count.results));

  async function remove() {
    if (!target) return;
    setDeleting(true);
    setMsg(null);
    try {
      await api(`/exhibitors/${target.id}`, { method: "DELETE" });
      setMsg({ type: "success", text: `Removed "${target.companyName}".` });
      confirmDel.onClose();
      load();
    } catch (e: any) {
      let text = e.message;
      try { text = JSON.parse(e.message).error || text; } catch {}
      setMsg({ type: "error", text });
      confirmDel.onClose();
    } finally {
      setDeleting(false);
    }
  }

  useEffect(() => {
    setIsAdmin(getUser()?.role === "ADMIN");
    api<{ configured: boolean }>("/admin/sync/status").then((r) => setConfigured(r.configured)).catch(() => setConfigured(null));
    load();
  }, []);

  async function sync() {
    setBusy(true);
    setMsg(null);
    try {
      const r = await api<any>("/admin/sync/exhibitors", { method: "POST" });
      let text = `Synced from SAMS: ${r.created} added, ${r.updated} updated, ${r.fetched} confirmed exhibitors found.`;
      if (r.skippedNoCategory > 0) text += ` ${r.skippedNoCategory} skipped (no matching category: ${r.unmatchedCategories.join(", ")}).`;
      setMsg({ type: "success", text });
      load();
    } catch (e: any) {
      setMsg({ type: "error", text: "Sync failed. " + e.message });
    } finally {
      setBusy(false);
    }
  }

  if (!isAdmin)
    return (
      <div className="alert alert-warning">
        Admin only. <Link href="/login" className="link ml-2">Log in as admin</Link>
      </div>
    );

  return (
    <div className="space-y-5">
      <div className="flex flex-wrap items-center justify-between gap-3">
        <div>
          <h1 className="text-2xl font-bold text-primary">Exhibitors</h1>
          <p className="text-sm text-base-content/60">Pulled from SAMS — confirmed for judging in the active event.</p>
        </div>
        <Button color="primary" isLoading={busy} onPress={sync} isDisabled={configured === false}>
          Sync from SAMS
        </Button>
      </div>

      {configured === false && (
        <div className="alert alert-warning text-sm">
          SAMS is not connected. Set <code>SAMS_DATABASE_URL</code> in <code>apps/api/.env</code> and restart the API.
        </div>
      )}
      {msg && <div className={`alert ${msg.type === "success" ? "alert-success" : msg.type === "error" ? "alert-error" : "alert-info"} text-sm`}>{msg.text}</div>}

      <div className="stats shadow">
        <div className="stat">
          <div className="stat-title">Total exhibitors</div>
          <div className="stat-value text-primary">{list.length}</div>
        </div>
        <div className="stat">
          <div className="stat-title">From SAMS</div>
          <div className="stat-value">{list.filter((e) => e.source === "SAMS").length}</div>
        </div>
      </div>

      <div className="card bg-base-100 shadow">
        <div className="card-body">
          <div className="overflow-x-auto">
            <table className="table">
              <thead>
                <tr>
                  <th>Company</th>
                  <th>Category</th>
                  <th>Stand</th>
                  <th>Source</th>
                  <th className="text-right">Scores</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
                {list.map((e) => {
                  const dupe = dupeNames.has(e.companyName.trim().toLowerCase());
                  const scored = hasScores(e);
                  return (
                    <tr key={e.id}>
                      <td className="font-medium">
                        {e.companyName}
                        {dupe && <span className="badge badge-warning badge-sm ml-2">duplicate</span>}
                      </td>
                      <td className="text-sm">{e.category?.name || "—"}</td>
                      <td className="text-sm text-base-content/60">{e.standLocation || "—"}</td>
                      <td><span className={`badge ${e.source === "SAMS" ? "badge-primary" : "badge-ghost"}`}>{e.source}</span></td>
                      <td className="text-right text-sm tabular-nums">{e._count?.scores ?? 0}</td>
                      <td className="text-right">
                        <Button
                          size="sm"
                          variant="light"
                          color="danger"
                          isDisabled={scored}
                          onPress={() => { setTarget(e); confirmDel.onOpen(); }}
                          title={scored ? "Has scores — cannot delete" : "Delete this exhibitor"}
                        >
                          Delete
                        </Button>
                      </td>
                    </tr>
                  );
                })}
                {list.length === 0 && (
                  <tr><td colSpan={6} className="text-center text-base-content/50">No exhibitors yet. Click “Sync from SAMS”.</td></tr>
                )}
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <Modal isOpen={confirmDel.isOpen} onOpenChange={confirmDel.onOpenChange} backdrop="blur">
        <ModalContent>
          {(onClose) => (
            <>
              <ModalHeader className="text-error">Delete exhibitor?</ModalHeader>
              <ModalBody>
                <p className="text-sm text-base-content/80">
                  Remove <b>{target?.companyName}</b>{target?.standLocation ? ` (${target.standLocation})` : ""} from this event?
                </p>
                <p className="text-sm text-base-content/60">
                  This exhibitor has no scores, so nothing judged is lost. This cannot be undone.
                  {target?.source === "SAMS" && " Note: it may reappear on the next SAMS sync if it still exists there."}
                </p>
              </ModalBody>
              <ModalFooter>
                <Button variant="light" onPress={onClose}>Cancel</Button>
                <Button color="danger" isLoading={deleting} onPress={remove}>Delete</Button>
              </ModalFooter>
            </>
          )}
        </ModalContent>
      </Modal>
    </div>
  );
}
