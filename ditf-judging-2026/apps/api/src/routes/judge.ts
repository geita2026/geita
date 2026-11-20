import { Router } from "express";
import { z } from "zod";
import { prisma } from "../db.js";
import { auth, requireRole } from "../auth.js";
import { phaseToRound, grandFinalists, grandFinalistIds } from "../services/results.js";

const router = Router();
router.use(auth(), requireRole("JUDGE", "ADMIN"));

// Categories assigned to the current judge, with their exhibitors + my scoring progress.
// The active round follows the event phase: round 1 normally, round 2 once the
// second round is open. In round 2 the GRAND category's field is the derived set
// of round-1 first-winners; ordinary/special categories are finished and hidden.
router.get("/assignments", async (req, res) => {
  const judgeId = req.user!.judgeId;
  if (!judgeId) return res.status(400).json({ error: "Not a judge account" });

  const assignments = await prisma.judgeAssignment.findMany({
    where: { judgeId },
    include: {
      category: {
        include: {
          event: { select: { id: true, phase: true } },
          rubric: { include: { groups: { include: { items: true } } } },
          exhibitors: true,
        },
      },
    },
  });

  const result = [];
  for (const a of assignments) {
    const cat = a.category;
    const round = phaseToRound(cat.event.phase);
    const itemCount = cat.rubric?.groups.reduce((n, g) => n + g.items.length, 0) ?? 0;

    let roster = cat.exhibitors;
    if (round >= 2) {
      // Only the grand final is active in round 2.
      if (cat.group !== "GRAND") continue;
      const finalists = await grandFinalists(cat.event.id);
      roster = finalists.map((f) => ({ ...(f.exhibitor as any), categoryId: cat.id })) as typeof cat.exhibitors;
    } else if (cat.group === "GRAND") {
      // The grand final has no field until round 2 opens.
      continue;
    }

    const exhibitors = [];
    for (const ex of roster) {
      const scored = await prisma.score.count({ where: { exhibitorId: ex.id, judgeId, round } });
      const submission = await prisma.judgeSubmission.findUnique({
        where: { judgeId_exhibitorId_round: { judgeId, exhibitorId: ex.id, round } },
      });
      exhibitors.push({
        ...ex,
        scoredItems: scored,
        totalItems: itemCount,
        complete: itemCount > 0 && scored >= itemCount,
        submitted: !!submission,
        submittedAt: submission?.submittedAt ?? null,
      });
    }
    result.push({
      round,
      category: { id: cat.id, name: cat.name, group: cat.group, rubricId: cat.rubricId },
      exhibitors,
    });
  }
  res.json(result);
});

// Rubric sheet for one exhibitor + this judge's existing scores (for the active round).
router.get("/exhibitors/:id/sheet", async (req, res) => {
  const judgeId = req.user!.judgeId!;
  const exhibitor = await prisma.exhibitor.findUnique({
    where: { id: req.params.id },
    include: {
      event: { select: { phase: true } },
      category: { include: { rubric: { include: { groups: { orderBy: { sort: "asc" }, include: { items: { orderBy: { sort: "asc" } } } } } } } },
    },
  });
  if (!exhibitor) return res.status(404).json({ error: "Exhibitor not found" });
  if (!exhibitor.category.rubric) return res.status(400).json({ error: "Category has no rubric" });

  const round = phaseToRound(exhibitor.event.phase);
  const myScores = await prisma.score.findMany({ where: { exhibitorId: exhibitor.id, judgeId, round } });
  const scoreByItem: Record<string, number> = {};
  for (const s of myScores) scoreByItem[s.rubricItemId] = s.value;

  const submission = await prisma.judgeSubmission.findUnique({
    where: { judgeId_exhibitorId_round: { judgeId, exhibitorId: exhibitor.id, round } },
  });

  res.json({
    round,
    exhibitor: { id: exhibitor.id, companyName: exhibitor.companyName, standLocation: exhibitor.standLocation },
    category: { id: exhibitor.category.id, name: exhibitor.category.name },
    rubric: exhibitor.category.rubric,
    myScores: scoreByItem,
    submitted: !!submission,
    submittedAt: submission?.submittedAt ?? null,
  });
});

const scoreSchema = z.object({
  items: z.array(z.object({ rubricItemId: z.string(), value: z.number().min(0) })).min(1),
});

// Load the exhibitor + rubric and enforce the round-2 (grand-final) gates shared by
// autosave and submit. Returns { error, status } to bail, or { exhibitor, round, itemMap }.
async function loadScorable(judgeId: string, exhibitorId: string) {
  const fail = (error: string, status: number) => ({ ok: false as const, error, status });
  const exhibitor = await prisma.exhibitor.findUnique({
    where: { id: exhibitorId },
    include: {
      event: { select: { id: true, phase: true } },
      category: { include: { rubric: { include: { groups: { include: { items: true } } } } } },
    },
  });
  if (!exhibitor?.category.rubric) return fail("Exhibitor/rubric not found", 404);

  const round = phaseToRound(exhibitor.event.phase);
  if (round >= 2) {
    const finalistIds = await grandFinalistIds(exhibitor.event.id);
    if (!finalistIds.includes(exhibitor.id)) return fail("This exhibitor is not in the second-round field.", 403);
    const onGrand = await prisma.judgeAssignment.count({
      where: { judgeId, category: { eventId: exhibitor.event.id, group: "GRAND" } },
    });
    if (onGrand === 0) return fail("You are not assigned to the grand final.", 403);
  }

  const itemMap = new Map<string, number>();
  for (const g of exhibitor.category.rubric.groups) for (const it of g.items) itemMap.set(it.id, it.maxMark);
  return { ok: true as const, exhibitor, round, itemMap };
}

// Autosave this judge's (draft) scores. Rejected once the sheet has been submitted.
router.post("/exhibitors/:id/scores", async (req, res) => {
  const judgeId = req.user!.judgeId!;
  const parsed = scoreSchema.safeParse(req.body);
  if (!parsed.success) return res.status(400).json({ error: parsed.error.flatten() });

  const ctx = await loadScorable(judgeId, req.params.id);
  if (!ctx.ok) return res.status(ctx.status).json({ error: ctx.error });
  const { exhibitor, round, itemMap } = ctx;

  const locked = await prisma.judgeSubmission.findUnique({
    where: { judgeId_exhibitorId_round: { judgeId, exhibitorId: exhibitor.id, round } },
  });
  if (locked) return res.status(409).json({ error: "Already submitted — your scores are locked and cannot be changed." });

  for (const s of parsed.data.items) {
    const cap = itemMap.get(s.rubricItemId);
    if (cap === undefined) return res.status(400).json({ error: `Unknown rubric item ${s.rubricItemId}` });
    if (s.value > cap) return res.status(400).json({ error: `Score ${s.value} exceeds max ${cap}` });
  }

  await prisma.$transaction(
    parsed.data.items.map((s) =>
      prisma.score.upsert({
        where: { exhibitorId_judgeId_rubricItemId_round: { exhibitorId: exhibitor.id, judgeId, rubricItemId: s.rubricItemId, round } },
        update: { value: s.value },
        create: { exhibitorId: exhibitor.id, judgeId, rubricItemId: s.rubricItemId, value: s.value, round },
      })
    )
  );

  res.json({ ok: true, round, savedItems: parsed.data.items.length });
});

// Finalise the sheet: optionally save the supplied scores, require EVERY item to be
// scored, then lock it. After this the judge can no longer edit, and the scores
// start counting in the results.
router.post("/exhibitors/:id/submit", async (req, res) => {
  const judgeId = req.user!.judgeId!;
  const parsed = z.object({ items: scoreSchema.shape.items.optional() }).safeParse(req.body ?? {});
  if (!parsed.success) return res.status(400).json({ error: parsed.error.flatten() });

  const ctx = await loadScorable(judgeId, req.params.id);
  if (!ctx.ok) return res.status(ctx.status).json({ error: ctx.error });
  const { exhibitor, round, itemMap } = ctx;

  const existing = await prisma.judgeSubmission.findUnique({
    where: { judgeId_exhibitorId_round: { judgeId, exhibitorId: exhibitor.id, round } },
  });
  if (existing) return res.status(409).json({ error: "Already submitted — your scores are locked." });

  // Save any final edits passed with the submit (validated against item caps).
  if (parsed.data.items?.length) {
    for (const s of parsed.data.items) {
      const cap = itemMap.get(s.rubricItemId);
      if (cap === undefined) return res.status(400).json({ error: `Unknown rubric item ${s.rubricItemId}` });
      if (s.value > cap) return res.status(400).json({ error: `Score ${s.value} exceeds max ${cap}` });
    }
    await prisma.$transaction(
      parsed.data.items.map((s) =>
        prisma.score.upsert({
          where: { exhibitorId_judgeId_rubricItemId_round: { exhibitorId: exhibitor.id, judgeId, rubricItemId: s.rubricItemId, round } },
          update: { value: s.value },
          create: { exhibitorId: exhibitor.id, judgeId, rubricItemId: s.rubricItemId, value: s.value, round },
        })
      )
    );
  }

  // Completeness gate: a score row must exist for every rubric item.
  const scoredCount = await prisma.score.count({ where: { exhibitorId: exhibitor.id, judgeId, round } });
  const totalItems = itemMap.size;
  if (scoredCount < totalItems) {
    return res.status(400).json({ error: `Score all ${totalItems} items before submitting (${scoredCount}/${totalItems} done).` });
  }

  const submission = await prisma.judgeSubmission.create({
    data: { judgeId, exhibitorId: exhibitor.id, round },
  });
  res.json({ ok: true, round, submitted: true, submittedAt: submission.submittedAt });
});

export default router;
