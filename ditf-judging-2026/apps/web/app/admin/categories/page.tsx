"use client";

import { useEffect, useState } from "react";
import Link from "next/link";
import {
  Input,
  Button,
  Chip,
  Modal,
  ModalContent,
  ModalHeader,
  ModalBody,
  ModalFooter,
  useDisclosure,
} from "@heroui/react";
import { api, getUser } from "../../../lib/api";

type Group = "ORDINARY" | "SPECIAL_EXHIBITOR" | "SPECIAL_PAVILION" | "RECOGNITION" | "GRAND";
type Rubric = { id: string; code: string; name: string; totalPoints: number };
type Category = {
  id: string;
  name: string;
  group: Group;
  order: number;
  rubric?: Rubric | null;
  _count: { exhibitors: number; assignments: number };
};

const GROUPS: { value: Group; label: string }[] = [
  { value: "ORDINARY", label: "Ordinary (sectorial)" },
  { value: "SPECIAL_EXHIBITOR", label: "Special — exhibitor" },
  { value: "SPECIAL_PAVILION", label: "Special — pavilion" },
  { value: "RECOGNITION", label: "Recognition (no scoring)" },
  { value: "GRAND", label: "Grand (round 2)" },
];
const groupLabel = (g: Group) => GROUPS.find((x) => x.value === g)?.label || g;
const groupColor: Record<Group, "primary" | "secondary" | "success" | "warning" | "default"> = {
  ORDINARY: "primary",
  SPECIAL_EXHIBITOR: "secondary",
  SPECIAL_PAVILION: "success",
  RECOGNITION: "default",
  GRAND: "warning",
};

const blankForm = { name: "", group: "ORDINARY" as Group, rubricId: "", order: "" };

export default function AdminCategoriesPage() {
  const [list, setList] = useState<Category[]>([]);
  const [rubrics, setRubrics] = useState<Rubric[]>([]);
  const [isAdmin, setIsAdmin] = useState(false);
  const [busy, setBusy] = useState(false);
  const [msg, setMsg] = useState<{ type: "success" | "error"; text: string } | null>(null);

  const [editingId, setEditingId] = useState<string | null>(null);
  const [form, setForm] = useState(blankForm);
  const { isOpen, onOpen, onClose, onOpenChange } = useDisclosure();

  function load() {
    api<Category[]>("/admin/categories").then(setList).catch(() => setList([]));
  }

  useEffect(() => {
    setIsAdmin(getUser()?.role === "ADMIN");
    api<Rubric[]>("/admin/rubrics").then(setRubrics).catch(() => setRubrics([]));
    load();
  }, []);

  function startAdd() {
    setEditingId(null);
    setForm(blankForm);
    setMsg(null);
    onOpen();
  }
  function startEdit(c: Category) {
    setEditingId(c.id);
    setForm({ name: c.name, group: c.group, rubricId: c.rubric?.id || "", order: String(c.order) });
    setMsg(null);
    onOpen();
  }

  function errText(e: any): string {
    try {
      const p = JSON.parse(e.message);
      return p.error?.fieldErrors ? JSON.stringify(p.error.fieldErrors) : p.error || e.message;
    } catch {
      return e.message;
    }
  }

  async function save() {
    setBusy(true);
    setMsg(null);
    try {
      const body: any = { name: form.name, group: form.group };
      // empty rubric select => null (explicit "no rubric"); otherwise the chosen rubric
      body.rubricId = form.rubricId || null;
      if (form.order.trim()) body.order = Number(form.order);
      if (editingId) {
        await api(`/admin/categories/${editingId}`, { method: "PATCH", body: JSON.stringify(body) });
        setMsg({ type: "success", text: `Category "${form.name}" updated.` });
      } else {
        await api("/admin/categories", { method: "POST", body: JSON.stringify(body) });
        setMsg({ type: "success", text: `Category "${form.name}" added.` });
      }
      onClose();
      load();
    } catch (e: any) {
      setMsg({ type: "error", text: "Save failed. " + errText(e) });
    } finally {
      setBusy(false);
    }
  }

  async function remove(c: Category) {
    if (!confirm(`Delete category "${c.name}"? Judge assignments for it will also be removed.`)) return;
    setMsg(null);
    try {
      await api(`/admin/categories/${c.id}`, { method: "DELETE" });
      load();
    } catch (e: any) {
      setMsg({ type: "error", text: errText(e) });
    }
  }

  // When adding (not editing), preselect the rubric most groups default to, for convenience.
  function onGroupChange(g: Group) {
    setForm((f) => {
      if (editingId) return { ...f, group: g };
      const def =
        g === "ORDINARY" || g === "GRAND"
          ? rubrics.find((r) => r.code === "ORDINARY")
          : g === "SPECIAL_EXHIBITOR"
          ? rubrics.find((r) => r.code === "BEST_EXHIBITOR")
          : g === "SPECIAL_PAVILION"
          ? rubrics.find((r) => r.code === "BEST_BRANDED_PAVILION")
          : undefined;
      return { ...f, group: g, rubricId: def?.id || "" };
    });
  }

  const canSave = form.name.trim().length >= 2;

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
          <h1 className="text-2xl font-bold text-primary">Award categories</h1>
          <p className="text-sm text-base-content/60">Add, edit, or remove categories and their scoring rubric — no need to re-seed.</p>
        </div>
        <Button color="primary" onPress={startAdd}>+ Add category</Button>
      </div>

      {msg?.type === "success" && <div className="alert alert-success text-sm">{msg.text}</div>}
      {msg?.type === "error" && !isOpen && <div className="alert alert-error text-sm">{msg.text}</div>}

      <div className="card bg-base-100 shadow">
        <div className="card-body">
          <div className="overflow-x-auto">
            <table className="table">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Name</th>
                  <th>Group</th>
                  <th>Rubric</th>
                  <th>Exhibitors</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
                {list.map((c) => (
                  <tr key={c.id}>
                    <td className="text-sm text-base-content/60">{c.order}</td>
                    <td className="font-medium">{c.name}</td>
                    <td><Chip color={groupColor[c.group]} variant="flat" size="sm">{groupLabel(c.group)}</Chip></td>
                    <td className="text-sm">{c.rubric ? `${c.rubric.name}` : "—"}</td>
                    <td className="text-sm text-base-content/60">{c._count.exhibitors}</td>
                    <td className="text-right whitespace-nowrap">
                      <button className="btn btn-ghost btn-xs" onClick={() => startEdit(c)}>Edit</button>
                      <button
                        className="btn btn-ghost btn-xs text-error"
                        onClick={() => remove(c)}
                        disabled={c._count.exhibitors > 0}
                        title={c._count.exhibitors > 0 ? "Has exhibitors — cannot delete" : "Delete"}
                      >
                        Delete
                      </button>
                    </td>
                  </tr>
                ))}
                {list.length === 0 && (
                  <tr><td colSpan={6} className="text-center text-base-content/50">No categories yet.</td></tr>
                )}
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <Modal isOpen={isOpen} onOpenChange={onOpenChange} placement="top-center" size="2xl">
        <ModalContent>
          <ModalHeader>{editingId ? "Edit category" : "Add a category"}</ModalHeader>
          <ModalBody className="gap-3">
            {msg?.type === "error" && <div className="alert alert-error text-sm">{msg.text}</div>}
            <Input label="Name" value={form.name} onValueChange={(v) => setForm((f) => ({ ...f, name: v }))} />
            <label className="form-control w-full">
              <span className="label-text mb-1 block text-sm">Group</span>
              <select className="select select-bordered w-full" value={form.group} onChange={(e) => onGroupChange(e.target.value as Group)}>
                {GROUPS.map((g) => (
                  <option key={g.value} value={g.value}>{g.label}</option>
                ))}
              </select>
            </label>
            <label className="form-control w-full">
              <span className="label-text mb-1 block text-sm">Rubric</span>
              <select className="select select-bordered w-full" value={form.rubricId} onChange={(e) => setForm((f) => ({ ...f, rubricId: e.target.value }))}>
                <option value="">— No rubric (committee-selected) —</option>
                {rubrics.map((r) => (
                  <option key={r.id} value={r.id}>{r.name} ({r.totalPoints} pts)</option>
                ))}
              </select>
            </label>
            <Input
              label="Order"
              type="number"
              value={form.order}
              onValueChange={(v) => setForm((f) => ({ ...f, order: v }))}
              description={editingId ? undefined : "Leave blank to append to the end"}
            />
          </ModalBody>
          <ModalFooter>
            <Button variant="light" onPress={onClose}>Cancel</Button>
            <Button color="primary" isLoading={busy} isDisabled={!canSave} onPress={save}>
              {editingId ? "Save changes" : "Add category"}
            </Button>
          </ModalFooter>
        </ModalContent>
      </Modal>
    </div>
  );
}
