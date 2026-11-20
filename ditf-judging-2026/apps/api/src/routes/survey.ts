import { Router } from "express";
import { z } from "zod";
import { prisma } from "../db.js";
import { auth, requireRole } from "../auth.js";
import { rateLimit } from "../rateLimit.js";

const router = Router();

// Generous per-IP limit: stops a script hammering the endpoint without blocking a
// roomful of participants sharing the venue WiFi (one public IP).
const submitLimiter = rateLimit({
  windowMs: 60_000,
  max: 30,
  message: "Umejaribu mara nyingi sana. Tafadhali subiri kidogo kisha ujaribu tena.",
});

// Allowed option values (Kiswahili) — kept server-side so submissions can't smuggle
// arbitrary values into the rating fields.
const Q1 = ["Kuridhika sana", "Kuridhika", "Kutoridhika", "Kutoridhika sana"] as const;
const Q2 = ["Muhimu sana", "Muhimu", "Muhimu kwa kiasi fulani", "Haifai"] as const;
const Q3 = ["Bora", "Nzuri", "Nzuri kiasi", "Haikidhi"] as const;
const Q4 = ["Kanuni za Ushiriki", "Uchaguzi wa banda bora", "Mjue Jirani yako", "Ulinzi na Usalama", "Mazingira", "Afya"] as const;

const text = (max: number) => z.string().trim().max(max);

const submitSchema = z.object({
  // Honeypot — hidden in the form, must stay empty. Bots that fill every field trip it.
  website: text(200).optional(),
  companyName: text(200).min(1, "Jina la kampuni linahitajika"),
  participant: text(200).min(1, "Jina la mshiriki linahitajika"),
  phone: text(40).optional(),
  surveyDate: text(40).optional(),
  q1: z.enum(Q1).optional(),
  q2: z.enum(Q2).optional(),
  q3: z.enum(Q3).optional(),
  q4: z.array(z.enum(Q4)).max(Q4.length).optional().default([]),
  q5: text(4000).optional(),
  q6: text(4000).optional(),
  q7: text(4000).optional(),
});

// Public — submit a feedback response.
router.post("/responses", submitLimiter, async (req, res) => {
  const parsed = submitSchema.safeParse(req.body);
  if (!parsed.success) return res.status(400).json({ error: parsed.error.flatten() });
  const d = parsed.data;

  // Honeypot tripped → pretend success, store nothing (don't tip off the bot).
  if (d.website && d.website.trim()) return res.status(201).json({ ok: true });

  const saved = await prisma.surveyResponse.create({
    data: {
      companyName: d.companyName,
      participant: d.participant,
      phone: d.phone || null,
      surveyDate: d.surveyDate || null,
      q1: d.q1 || null,
      q2: d.q2 || null,
      q3: d.q3 || null,
      q4: JSON.stringify(d.q4 ?? []),
      q5: d.q5 || null,
      q6: d.q6 || null,
      q7: d.q7 || null,
    },
    select: { id: true },
  });
  res.status(201).json({ ok: true, id: saved.id });
});

// Admin — list responses (newest first) with simple per-option tallies.
router.get("/responses", auth(), requireRole("ADMIN"), async (_req, res) => {
  const rows = await prisma.surveyResponse.findMany({ orderBy: { createdAt: "desc" } });
  const responses = rows.map((r) => ({ ...r, q4: safeArr(r.q4) }));

  const tally = (key: "q1" | "q2" | "q3", opts: readonly string[]) =>
    Object.fromEntries(opts.map((o) => [o, rows.filter((r) => r[key] === o).length]));
  const q4Tally = Object.fromEntries(Q4.map((o) => [o, responses.filter((r) => r.q4.includes(o)).length]));

  res.json({
    total: rows.length,
    options: { q1: Q1, q2: Q2, q3: Q3, q4: Q4 },
    tallies: { q1: tally("q1", Q1), q2: tally("q2", Q2), q3: tally("q3", Q3), q4: q4Tally },
    responses,
  });
});

// Admin — CSV export (Excel-friendly, UTF-8 BOM).
router.get("/responses.csv", auth(), requireRole("ADMIN"), async (_req, res) => {
  const rows = await prisma.surveyResponse.findMany({ orderBy: { createdAt: "desc" } });
  const headers = ["Tarehe ya kujaza", "Kampuni/Taasisi", "Mshiriki", "Simu", "Tarehe", "Swali 1", "Swali 2", "Swali 3", "Mada (Swali 4)", "Swali 5", "Swali 6", "Swali 7"];
  const cell = (v: unknown) => `"${String(v ?? "").replace(/"/g, '""')}"`;
  const lines = [headers.map(cell).join(",")];
  for (const r of rows) {
    lines.push([
      r.createdAt.toISOString(),
      r.companyName,
      r.participant,
      r.phone,
      r.surveyDate,
      r.q1,
      r.q2,
      r.q3,
      safeArr(r.q4).join("; "),
      r.q5,
      r.q6,
      r.q7,
    ].map(cell).join(","));
  }
  res.setHeader("Content-Type", "text/csv; charset=utf-8");
  res.setHeader("Content-Disposition", 'attachment; filename="ditf-survey-responses.csv"');
  res.send("﻿" + lines.join("\r\n"));
});

function safeArr(s: string): string[] {
  try {
    const v = JSON.parse(s);
    return Array.isArray(v) ? v : [];
  } catch {
    return [];
  }
}

export default router;
