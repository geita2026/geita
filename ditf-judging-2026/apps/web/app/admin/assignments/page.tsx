"use client";

import { useEffect, useMemo, useState } from "react";
import Link from "next/link";
import { Chip, Checkbox } from "@heroui/react";
import { api, getUser } from "../../../lib/api";

type Group = "ORDINARY" | "SPECIAL_EXHIBITOR" | "SPECIAL_PAVILION" | "RECOGNITION" | "GRAND";
type Category = { id: string; name: string; group: Group; order: number; rubric?: { id: string } | null };
type Assignment = { id: string; categoryId: string };
type Judge = { id: string; code: string; active: boolean; user: { name: string; email: string }; assignments: Assignment[] };

const groupColor: Record<Group, "primary" | "secondary" | "success" | "warning" | "default"> = {
  ORDINARY: "primary",
  SPECIAL_EXHIBITOR: "secondary",
  SPECIAL_PAVILION: "success",
  RECOGNITION: "default",
  GRAND: "warning",
};
const groupShort: Record<Group, string> = {
  ORDINARY: "Ordinary",
  SPECIAL_EXHIBITOR: "Special · exhibitor",
  SPECIAL_PAVILION: "Special · pavilion",
  RECOGNITION: "Recognition",
  GRAND: "Grand",
};

export default function AdminAssignmentsPage() {
  const [judges, setJudges] = useState<Judge[]>([]);
  const [categories, setCategories] = useState<Category[]>([]);
  const [selectedJudge, setSelectedJudge] = useState<string>("");
  const [isAdmin, setIsAdmin] = useState(false);
  const [busyCat, setBusyCat] = useState<string | null>(null);
  const [msg, setMsg] = useState<{ type: "error"; text: string } | null>(null);

  function loadJudges() {
    return api<Judge[]>("/admin/judges").then(setJudges).catch(() => setJudges([]));
  }

  useEffect(() => {
    setIsAdmin(getUser()?.role === "ADMIN");
    loadJudges();
    api<Category[]>("/admin/categories").then(setCategories).catch(() => setCategories([]));
  }, []);

  const judge = useMemo(() => judges.find((j) => j.id === selectedJudge) || null, [judges, selectedJudge]);
  const assignedIds = useMemo(() => new Set(judge?.assignments.map((a) => a.categoryId) ?? []), [judge]);

  async function toggle(cat: Category, assigned: boolean) {
    if (!judge) return;
    setBusyCat(cat.id);
    setMsg(null);
    try {
      if (assigned) {
        const a = judge.assignments.find((x) => x.categoryId === cat.id);
        if (a) await api(`/admin/assignments/${a.id}`, { method: "DELETE" });
      } else {
        await api("/admin/assignments", { method: "POST", body: JSON.stringify({ judgeId: judge.id, categoryId: cat.id }) });
      }
      await loadJudges();
    } catch (e: any) {
      let text = e.message;
      try { text = JSON.parse(e.message).error || text; } catch {}
      setMsg({ type: "error", text });
    } finally {
      setBusyCat(null);
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
      <div>
        <h1 className="text-2xl font-bold text-primary">Judge assignments</h1>
        <p className="text-sm text-base-content/60">Choose a judge, then tick the categories they will score.</p>
      </div>

      {msg && <div className="alert alert-error text-sm">{msg.text}</div>}

      <div className="card bg-base-100 shadow">
        <div className="card-body">
          <label className="form-control w-full max-w-md">
            <span className="label-text mb-1 block text-sm">Judge</span>
            <select className="select select-bordered w-full" value={selectedJudge} onChange={(e) => setSelectedJudge(e.target.value)}>
              <option value="">— Select a judge —</option>
              {judges.map((j) => (
                <option key={j.id} value={j.id}>
                  {j.user.name} ({j.code}) — {j.assignments.length} categor{j.assignments.length === 1 ? "y" : "ies"}
                </option>
              ))}
            </select>
          </label>
          {judges.length === 0 && (
            <p className="mt-2 text-sm text-base-content/60">
              No judges yet. Add them on the <Link href="/admin/users" className="link">Users</Link> page first.
            </p>
          )}
        </div>
      </div>

      {judge && (
        <div className="card bg-base-100 shadow">
          <div className="card-body">
            <div className="flex flex-wrap items-center justify-between gap-2">
              <h2 className="card-title text-base">
                Categories for {judge.user.name} <Chip size="sm" variant="flat" className="ml-1">{judge.code}</Chip>
              </h2>
              <span className="text-sm text-base-content/60">{assignedIds.size} of {categories.length} assigned</span>
            </div>
            <div className="mt-2 grid gap-x-6 gap-y-2 sm:grid-cols-2">
              {categories.map((c) => {
                const assigned = assignedIds.has(c.id);
                const noRubric = !c.rubric;
                return (
                  <label
                    key={c.id}
                    className={`flex items-center justify-between gap-3 rounded-lg px-3 py-2 ${noRubric ? "opacity-50" : "hover:bg-base-200"}`}
                  >
                    <span className="flex items-center gap-2">
                      <Checkbox
                        isSelected={assigned}
                        isDisabled={noRubric || busyCat === c.id}
                        onValueChange={() => toggle(c, assigned)}
                      />
                      <span className="text-sm">{c.name}</span>
                    </span>
                    {noRubric ? (
                      <span className="text-xs text-base-content/50">no rubric</span>
                    ) : (
                      <Chip size="sm" variant="flat" color={groupColor[c.group]}>{groupShort[c.group]}</Chip>
                    )}
                  </label>
                );
              })}
              {categories.length === 0 && <p className="text-sm text-base-content/60">No categories yet.</p>}
            </div>
          </div>
        </div>
      )}
    </div>
  );
}
