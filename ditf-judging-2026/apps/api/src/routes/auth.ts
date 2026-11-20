import { Router } from "express";
import bcrypt from "bcryptjs";
import { z } from "zod";
import { prisma } from "../db.js";
import { sign } from "../auth.js";
import { fetchScannerPin, samsConfigured } from "../sams.js";

const router = Router();

// `email` doubles as a username so the admin can sign in as "admin" (not an email).
const loginSchema = z.object({ email: z.string().min(1), password: z.string().min(1) });

// --- Brute-force guard for the admin PIN (in-memory; single pm2 process) ---
const WINDOW_MS = 10 * 60 * 1000; // 10 minutes
const MAX_FAILS = 5;
type Bucket = { fails: number; first: number; lockedUntil: number };
const buckets = new Map<string, Bucket>();
function lockSecondsLeft(ip: string): number {
  const b = buckets.get(ip);
  if (b && b.lockedUntil > Date.now()) return Math.ceil((b.lockedUntil - Date.now()) / 1000);
  return 0;
}
function recordFail(ip: string) {
  const now = Date.now();
  let b = buckets.get(ip);
  if (!b || now - b.first > WINDOW_MS) b = { fails: 0, first: now, lockedUntil: 0 };
  b.fails++;
  if (b.fails >= MAX_FAILS) b.lockedUntil = now + WINDOW_MS;
  buckets.set(ip, b);
}
function clearFails(ip: string) {
  buckets.delete(ip);
}

router.post("/login", async (req, res) => {
  const parsed = loginSchema.safeParse(req.body);
  if (!parsed.success) return res.status(400).json({ error: "Invalid input" });
  const { email, password } = parsed.data;

  // --- Admin: username "admin" + the SAMS scanner PIN (read live from SAMS) ---
  if (email.trim().toLowerCase() === "admin") {
    const ip = req.ip || "unknown";
    const wait = lockSecondsLeft(ip);
    if (wait > 0) return res.status(429).json({ error: `Too many attempts. Try again in ${Math.ceil(wait / 60)} min.` });
    if (!samsConfigured()) return res.status(503).json({ error: "Admin login is unavailable — SAMS is not connected." });

    let pin: string;
    try {
      pin = await fetchScannerPin();
    } catch {
      return res.status(502).json({ error: "Could not verify the PIN (SAMS unreachable)." });
    }
    if (password !== pin) {
      recordFail(ip);
      return res.status(401).json({ error: "Invalid credentials" });
    }
    clearFails(ip);
    const token = sign({ id: "sams-admin", role: "ADMIN" });
    return res.json({ token, user: { id: "sams-admin", name: "Administrator", email: "admin", role: "ADMIN" } });
  }

  // --- Everyone else: normal email + password (judges, other users) ---
  const user = await prisma.user.findUnique({ where: { email }, include: { judge: true } });
  if (!user) return res.status(401).json({ error: "Invalid credentials" });
  const ok = await bcrypt.compare(password, user.password);
  if (!ok) return res.status(401).json({ error: "Invalid credentials" });

  const token = sign({ id: user.id, role: user.role, judgeId: user.judge?.id });
  res.json({
    token,
    user: { id: user.id, name: user.name, email: user.email, role: user.role, judgeId: user.judge?.id, judgeCode: user.judge?.code },
  });
});

export default router;
