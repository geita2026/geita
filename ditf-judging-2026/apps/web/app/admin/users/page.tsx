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

type Role = "ADMIN" | "JUDGE" | "EXHIBITOR";
type User = {
  id: string;
  name: string;
  email: string;
  role: Role;
  createdAt: string;
  judge?: { code: string; active: boolean; _count: { assignments: number } } | null;
};

const roleColor: Record<Role, "primary" | "secondary" | "default"> = {
  ADMIN: "primary",
  JUDGE: "secondary",
  EXHIBITOR: "default",
};

export default function AdminUsersPage() {
  const [list, setList] = useState<User[]>([]);
  const [isAdmin, setIsAdmin] = useState(false);
  const [meId, setMeId] = useState<string | null>(null);
  const [busy, setBusy] = useState(false);
  const [msg, setMsg] = useState<{ type: "success" | "error"; text: string } | null>(null);
  const { isOpen, onOpen, onClose, onOpenChange } = useDisclosure();

  // form / mode
  const [editing, setEditing] = useState<User | null>(null); // null = add mode
  const [name, setName] = useState("");
  const [email, setEmail] = useState("");
  const [role, setRole] = useState<Role>("JUDGE");
  const [code, setCode] = useState("");
  const [password, setPassword] = useState("");

  function load() {
    api<User[]>("/admin/users").then(setList).catch(() => setList([]));
  }

  useEffect(() => {
    const u = getUser();
    setIsAdmin(u?.role === "ADMIN");
    setMeId(u?.id ?? null);
    load();
  }, []);

  function startAdd() {
    setEditing(null);
    setName("");
    setEmail("");
    setRole("JUDGE");
    setCode("");
    setPassword("");
    setMsg(null);
    onOpen();
  }
  function startEdit(u: User) {
    setEditing(u);
    setName(u.name);
    setEmail(u.email);
    setRole(u.role);
    setCode(u.judge?.code || "");
    setPassword("");
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
      if (editing) {
        // Edit: update name/email (and password if a new one was typed). Code + role are kept.
        const body: any = { name, email };
        if (password.trim()) body.password = password;
        await api(`/admin/users/${editing.id}`, { method: "PATCH", body: JSON.stringify(body) });
        setMsg({ type: "success", text: `"${name}" updated.` });
      } else {
        const body: any = { name, email, role, password };
        if (role === "JUDGE") body.code = code;
        await api("/admin/users", { method: "POST", body: JSON.stringify(body) });
        setMsg({ type: "success", text: `${role === "JUDGE" ? "Judge" : "User"} "${name}" added.` });
      }
      onClose();
      load();
    } catch (e: any) {
      setMsg({ type: "error", text: (editing ? "Could not update user. " : "Could not add user. ") + errText(e) });
    } finally {
      setBusy(false);
    }
  }

  async function removeUser(u: User) {
    if (!confirm(`Delete ${u.name} (${u.email})? This cannot be undone.`)) return;
    setMsg(null);
    try {
      await api(`/admin/users/${u.id}`, { method: "DELETE" });
      load();
    } catch (e: any) {
      setMsg({ type: "error", text: errText(e) });
    }
  }

  const emailValid = /\S+@\S+\.\S+/.test(email);
  const canSubmit = editing
    ? name.trim().length >= 2 && emailValid && (password === "" || password.length >= 6)
    : name.trim().length >= 2 && emailValid && password.length >= 6 && (role !== "JUDGE" || code.trim().length > 0);

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
          <h1 className="text-2xl font-bold text-primary">Users &amp; Judges</h1>
          <p className="text-sm text-base-content/60">Create accounts for judges and administrators.</p>
        </div>
        <Button color="primary" onPress={startAdd}>+ Add user</Button>
      </div>

      {msg?.type === "success" && <div className="alert alert-success text-sm">{msg.text}</div>}
      {msg?.type === "error" && !isOpen && <div className="alert alert-error text-sm">{msg.text}</div>}

      <div className="card bg-base-100 shadow">
        <div className="card-body">
          <div className="overflow-x-auto">
            <table className="table">
              <thead>
                <tr>
                  <th>Name</th>
                  <th>Email</th>
                  <th>Role</th>
                  <th>Judge code</th>
                  <th>Categories</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
                {list.map((u) => (
                  <tr key={u.id}>
                    <td className="font-medium">
                      {u.name} {u.id === meId && <Chip size="sm" variant="flat" className="ml-1">you</Chip>}
                    </td>
                    <td className="text-sm">{u.email}</td>
                    <td><Chip color={roleColor[u.role]} variant="flat" size="sm">{u.role}</Chip></td>
                    <td className="text-sm">{u.judge?.code || "—"}</td>
                    <td className="text-sm text-base-content/60">{u.role === "JUDGE" ? u.judge?._count.assignments ?? 0 : "—"}</td>
                    <td className="text-right whitespace-nowrap">
                      <button className="btn btn-ghost btn-xs" onClick={() => startEdit(u)}>Edit</button>
                      {u.id !== meId && (
                        <button className="btn btn-ghost btn-xs text-error" onClick={() => removeUser(u)}>
                          Delete
                        </button>
                      )}
                    </td>
                  </tr>
                ))}
                {list.length === 0 && (
                  <tr><td colSpan={6} className="text-center text-base-content/50">No users yet.</td></tr>
                )}
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <Modal isOpen={isOpen} onOpenChange={onOpenChange} placement="top-center" size="2xl">
        <ModalContent>
          <ModalHeader>{editing ? `Edit ${editing.name}` : "Add a user"}</ModalHeader>
          <ModalBody className="gap-3">
            {msg?.type === "error" && <div className="alert alert-error text-sm">{msg.text}</div>}
            <Input label="Full name" value={name} onValueChange={setName} />
            <Input label="Email" type="email" value={email} onValueChange={setEmail} />

            {editing ? (
              <div className="flex flex-wrap gap-2 text-sm">
                <Chip color={roleColor[editing.role]} variant="flat" size="sm">{editing.role}</Chip>
                {editing.judge?.code && (
                  <Chip variant="flat" size="sm">Code {editing.judge.code} (kept)</Chip>
                )}
              </div>
            ) : (
              <>
                <label className="form-control w-full">
                  <span className="label-text mb-1 block text-sm">Role</span>
                  <select className="select select-bordered w-full" value={role} onChange={(e) => setRole(e.target.value as Role)}>
                    <option value="JUDGE">Judge</option>
                    <option value="ADMIN">Administrator</option>
                  </select>
                </label>
                {role === "JUDGE" && (
                  <Input label="Judge code" value={code} onValueChange={setCode} description="Short unique code, e.g. J7" />
                )}
              </>
            )}

            <Input
              label={editing ? "New password (optional)" : "Temporary password"}
              type="password"
              value={password}
              onValueChange={setPassword}
              description={editing ? "Leave blank to keep the current password" : "At least 6 characters"}
            />
          </ModalBody>
          <ModalFooter>
            <Button variant="light" onPress={onClose}>Cancel</Button>
            <Button color="primary" isLoading={busy} isDisabled={!canSubmit} onPress={save}>
              {editing ? "Save changes" : `Add ${role === "JUDGE" ? "judge" : "user"}`}
            </Button>
          </ModalFooter>
        </ModalContent>
      </Modal>
    </div>
  );
}
