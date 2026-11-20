import { Router } from "express";
import bcrypt from "bcryptjs";
import { z } from "zod";
import { prisma } from "../db.js";
import { auth, requireRole } from "../auth.js";
import { computeResults, grandFinalists, grandFinalistIds, candidateAverages } from "../services/results.js";
import { syncExhibitorsFromSams } from "../services/samsSync.js";
import { samsConfigured } from "../sams.js";

const router = Router();
router.use(auth(), requireRole("ADMIN"));

// --- Judges ---
router.get("/judges", async (_req, res) => {
  const judges = await prisma.judge.findMany({ include: { user: { select: { name: true, email: true } }, assignments: true } });
  res.json(judges);
});

const judgeSchema = z.object({ name: z.string().min(2), email: z.string().email(), code: z.string().min(1), password: z.string().min(6) });
router.post("/judges", async (req, res) => {
  const parsed = judgeSchema.safeParse(req.body);
  if (!parsed.success) return res.status(400).json({ error: parsed.error.flatten() });
  const { name, email, code, password } = parsed.data;
  try {
    const judge = await prisma.$transaction(async (tx) => {
      const user = await tx.user.create({ data: { name, email, role: "JUDGE", password: await bcrypt.hash(password, 10) } });
      return tx.judge.create({ data: { userId: user.id, code } });
    });
    res.status(201).json(judge);
  } catch (e: any) {
    if (e?.code === "P2002") return res.status(409).json({ error: "A user with that email or judge code already exists." });
    throw e;
  }
});

// --- Users (admin can create judges and other users) ---
router.get("/users", async (_req, res) => {
  const users = await prisma.user.findMany({
    orderBy: { createdAt: "asc" },
    select: {
      id: true,
      name: true,
      email: true,
      role: true,
      createdAt: true,
      judge: { select: { code: true, active: true, _count: { select: { assignments: true } } } },
    },
  });
  res.json(users);
});

const userSchema = z
  .object({
    name: z.string().min(2),
    email: z.string().email(),
    role: z.enum(["ADMIN", "JUDGE", "EXHIBITOR"]),
    password: z.string().min(6),
    code: z.string().min(1).optional(),
  })
  .refine((d) => d.role !== "JUDGE" || !!d.code, { message: "A judge code is required for judges.", path: ["code"] });

router.post("/users", async (req, res) => {
  const parsed = userSchema.safeParse(req.body);
  if (!parsed.success) return res.status(400).json({ error: parsed.error.flatten() });
  const { name, email, role, password, code } = parsed.data;
  try {
    const user = await prisma.$transaction(async (tx) => {
      const u = await tx.user.create({ data: { name, email, role, password: await bcrypt.hash(password, 10) } });
      if (role === "JUDGE") await tx.judge.create({ data: { userId: u.id, code: code! } });
      return u;
    });
    res.status(201).json({ id: user.id, name: user.name, email: user.email, role: user.role });
  } catch (e: any) {
    if (e?.code === "P2002") return res.status(409).json({ error: "A user with that email or judge code already exists." });
    throw e;
  }
});

// Edit a user's profile (name/email, optional password reset). Judge code + role are
// intentionally left untouched — repurposing a judge account keeps its code.
const userUpdateSchema = z.object({
  name: z.string().min(2).optional(),
  email: z.string().email().optional(),
  password: z.string().min(6).optional(),
});
router.patch("/users/:id", async (req, res) => {
  const parsed = userUpdateSchema.safeParse(req.body);
  if (!parsed.success) return res.status(400).json({ error: parsed.error.flatten() });
  const data: Record<string, unknown> = {};
  if (parsed.data.name !== undefined) data.name = parsed.data.name;
  if (parsed.data.email !== undefined) data.email = parsed.data.email;
  if (parsed.data.password !== undefined) data.password = await bcrypt.hash(parsed.data.password, 10);
  try {
    const u = await prisma.user.update({ where: { id: req.params.id }, data, select: { id: true, name: true, email: true, role: true } });
    res.json(u);
  } catch (e: any) {
    if (e?.code === "P2002") return res.status(409).json({ error: "A user with that email already exists." });
    if (e?.code === "P2025") return res.status(404).json({ error: "User not found." });
    throw e;
  }
});

router.delete("/users/:id", async (req, res) => {
  const id = req.params.id;
  if (id === req.user?.id) return res.status(400).json({ error: "You cannot delete your own account." });
  const target = await prisma.user.findUnique({ where: { id } });
  if (!target) return res.status(404).json({ error: "User not found." });
  if (target.role === "ADMIN") {
    const admins = await prisma.user.count({ where: { role: "ADMIN" } });
    if (admins <= 1) return res.status(400).json({ error: "Cannot delete the last administrator." });
  }
  await prisma.user.delete({ where: { id } }); // Judge row cascades via onDelete: Cascade
  res.json({ ok: true });
});

// --- Assignments ---
const assignSchema = z.object({ judgeId: z.string(), categoryId: z.string() });
router.post("/assignments", async (req, res) => {
  const parsed = assignSchema.safeParse(req.body);
  if (!parsed.success) return res.status(400).json({ error: "Invalid input" });
  const a = await prisma.judgeAssignment.upsert({
    where: { judgeId_categoryId: parsed.data },
    update: {},
    create: parsed.data,
  });
  res.status(201).json(a);
});

router.delete("/assignments/:id", async (req, res) => {
  await prisma.judgeAssignment.delete({ where: { id: req.params.id } });
  res.json({ ok: true });
});

// --- Categories (editable; no longer seed-only) ---
const GROUPS = ["ORDINARY", "SPECIAL_EXHIBITOR", "SPECIAL_PAVILION", "RECOGNITION", "GRAND"] as const;
const groupEnum = z.enum(GROUPS);

// Default rubric per group (mirrors the seed mapping); used when the caller omits rubricId.
const RUBRIC_FOR_GROUP: Record<string, string | null> = {
  ORDINARY: "ORDINARY",
  SPECIAL_EXHIBITOR: "BEST_EXHIBITOR",
  SPECIAL_PAVILION: "BEST_BRANDED_PAVILION",
  RECOGNITION: null,
  GRAND: "ORDINARY",
};
async function defaultRubricIdForGroup(group: string): Promise<string | null> {
  const code = RUBRIC_FOR_GROUP[group];
  if (!code) return null;
  const r = await prisma.rubric.findUnique({ where: { code }, select: { id: true } });
  return r?.id ?? null;
}

// Rubrics list (for the category editor dropdown)
router.get("/rubrics", async (_req, res) => {
  const rubrics = await prisma.rubric.findMany({ orderBy: { code: "asc" }, select: { id: true, code: true, name: true, totalPoints: true } });
  res.json(rubrics);
});

router.get("/categories", async (req, res) => {
  const eventId = (req.query.event as string) || "ditf-2026";
  const categories = await prisma.category.findMany({
    where: { eventId },
    orderBy: { order: "asc" },
    include: {
      rubric: { select: { id: true, code: true, name: true, totalPoints: true } },
      _count: { select: { exhibitors: true, assignments: true } },
    },
  });
  res.json(categories);
});

const categoryCreate = z.object({
  name: z.string().min(2),
  group: groupEnum,
  order: z.number().int().positive().optional(),
  rubricId: z.string().nullable().optional(),
  eventId: z.string().optional(),
});
router.post("/categories", async (req, res) => {
  const parsed = categoryCreate.safeParse(req.body);
  if (!parsed.success) return res.status(400).json({ error: parsed.error.flatten() });
  const { name, group, eventId = "ditf-2026" } = parsed.data;
  let order = parsed.data.order;
  if (order == null) {
    const max = await prisma.category.aggregate({ where: { eventId }, _max: { order: true } });
    order = (max._max.order ?? 0) + 1;
  }
  // explicit rubricId wins (incl. null); otherwise fall back to the group default
  const rubricId = "rubricId" in parsed.data ? parsed.data.rubricId ?? null : await defaultRubricIdForGroup(group);
  const cat = await prisma.category.create({ data: { name, group, order, eventId, rubricId } });
  res.status(201).json(cat);
});

const categoryUpdate = z.object({
  name: z.string().min(2).optional(),
  group: groupEnum.optional(),
  order: z.number().int().positive().optional(),
  rubricId: z.string().nullable().optional(),
});
router.patch("/categories/:id", async (req, res) => {
  const parsed = categoryUpdate.safeParse(req.body);
  if (!parsed.success) return res.status(400).json({ error: parsed.error.flatten() });
  const data: Record<string, unknown> = {};
  for (const k of ["name", "group", "order"] as const) if (parsed.data[k] !== undefined) data[k] = parsed.data[k];
  if ("rubricId" in parsed.data) data.rubricId = parsed.data.rubricId ?? null;
  try {
    const cat = await prisma.category.update({ where: { id: req.params.id }, data });
    res.json(cat);
  } catch (e: any) {
    if (e?.code === "P2025") return res.status(404).json({ error: "Category not found." });
    throw e;
  }
});

router.delete("/categories/:id", async (req, res) => {
  const id = req.params.id;
  const exhibitors = await prisma.exhibitor.count({ where: { categoryId: id } });
  if (exhibitors > 0)
    return res.status(409).json({ error: `Cannot delete: ${exhibitors} exhibitor(s) are in this category. Reassign or remove them first.` });
  try {
    await prisma.category.delete({ where: { id } }); // judge assignments + results cascade
    res.json({ ok: true });
  } catch (e: any) {
    if (e?.code === "P2025") return res.status(404).json({ error: "Category not found." });
    throw e;
  }
});

// --- Event phase ---
const phaseSchema = z.object({ phase: z.enum(["REGISTRATION", "SCORING", "R1_DONE", "SECOND_ROUND", "CLOSED"]) });
router.patch("/events/:id/phase", async (req, res) => {
  const parsed = phaseSchema.safeParse(req.body);
  if (!parsed.success) return res.status(400).json({ error: "Invalid phase" });
  const event = await prisma.event.update({ where: { id: req.params.id }, data: { phase: parsed.data.phase } });
  res.json(event);
});

// --- Sync exhibitors from SAMS ---
router.get("/sync/status", (_req, res) => {
  res.json({ configured: samsConfigured() });
});

router.post("/sync/exhibitors", async (req, res) => {
  if (!samsConfigured()) return res.status(400).json({ error: "SAMS_DATABASE_URL is not configured on the API" });
  const eventId = (req.query.event as string) || "ditf-2026";
  try {
    const report = await syncExhibitorsFromSams(eventId);
    res.json({ ok: true, ...report });
  } catch (e: any) {
    res.status(502).json({ error: "SAMS sync failed: " + e.message });
  }
});

// --- Compute results ---
router.post("/results/compute", async (req, res) => {
  const eventId = (req.query.event as string) || "ditf-2026";
  const round = Number(req.query.round || 1);
  const written = await computeResults(eventId, round);
  res.json({ ok: true, round, categoriesRanked: written.length });
});

// Candidates for hand-editing a category's podium: eligible exhibitors + their
// computed average, plus the current (possibly manual) podium.
router.get("/results/candidates", async (req, res) => {
  const eventId = (req.query.event as string) || "ditf-2026";
  const categoryId = req.query.category as string;
  const round = Number(req.query.round || 1);
  if (!categoryId) return res.status(400).json({ error: "category is required" });
  const candidates = await candidateAverages(eventId, categoryId, round);
  const current = await prisma.result.findMany({
    where: { categoryId, round },
    orderBy: { rank: "asc" },
    select: { exhibitorId: true, rank: true, average: true, manual: true },
  });
  res.json({ candidates, current });
});

// Manually set a category's podium (overrides the computed ranking). Flagged
// manual so Compute won't overwrite it. Replaces any existing podium for the round.
const manualSchema = z.object({
  categoryId: z.string(),
  round: z.number().int().min(1).max(2).default(1),
  places: z
    .array(z.object({ exhibitorId: z.string(), average: z.number().min(0) }))
    .min(1)
    .max(10),
});
router.post("/results/manual", async (req, res) => {
  const eventId = (req.query.event as string) || "ditf-2026";
  const parsed = manualSchema.safeParse(req.body);
  if (!parsed.success) return res.status(400).json({ error: parsed.error.flatten() });
  const { categoryId, round, places } = parsed.data;

  const category = await prisma.category.findFirst({ where: { id: categoryId, eventId } });
  if (!category) return res.status(404).json({ error: "Category not found for this event." });

  // No duplicate exhibitors.
  const exIds = new Set<string>();
  for (const p of places) {
    if (exIds.has(p.exhibitorId)) return res.status(400).json({ error: "An exhibitor can only hold one place." });
    exIds.add(p.exhibitorId);
  }

  // Every chosen exhibitor must belong to this event.
  const valid = await prisma.exhibitor.count({ where: { id: { in: [...exIds] }, eventId } });
  if (valid !== exIds.size) return res.status(400).json({ error: "One or more selected exhibitors are invalid." });

  // Rank is DERIVED from the entered score (highest = 1st), so the podium is always
  // internally consistent — the displayed number decides the placement.
  const ranked = [...places].sort((a, b) => b.average - a.average);

  await prisma.$transaction([
    prisma.result.deleteMany({ where: { categoryId, round } }),
    ...ranked.map((p, i) =>
      prisma.result.create({
        data: { categoryId, exhibitorId: p.exhibitorId, round, average: p.average, rank: i + 1, manual: true },
      })
    ),
  ]);
  res.json({ ok: true, saved: places.length });
});

// Revert a category's podium to the computed ranking (drops the manual override).
router.post("/results/revert", async (req, res) => {
  const eventId = (req.query.event as string) || "ditf-2026";
  const categoryId = req.query.category as string;
  const round = Number(req.query.round || 1);
  if (!categoryId) return res.status(400).json({ error: "category is required" });
  await prisma.result.deleteMany({ where: { categoryId, round } });
  await computeResults(eventId, round); // rebuilds this category (now non-manual); others preserved
  res.json({ ok: true });
});

// Clear the computed rankings for ONE round (e.g. to reverse an accidental compute).
// Only the Result table is touched — judges' scores and submissions are untouched,
// so a fresh Compute rebuilds the same rankings.
router.post("/results/clear", async (req, res) => {
  const eventId = (req.query.event as string) || "ditf-2026";
  const round = Number(req.query.round || 1);
  const r = await prisma.result.deleteMany({ where: { round, category: { eventId } } });
  res.json({ ok: true, round, cleared: r.count });
});

// --- Second round ---
// Preview the qualifying field: the round-1 first-winner of each ordinary category.
router.get("/second-round/finalists", async (req, res) => {
  const eventId = (req.query.event as string) || "ditf-2026";
  const grand = await prisma.category.findFirst({ where: { eventId, group: "GRAND" } });
  const finalists = await grandFinalists(eventId);
  const judgesAssigned = grand
    ? await prisma.judgeAssignment.count({ where: { categoryId: grand.id } })
    : 0;
  res.json({
    grandCategoryId: grand?.id ?? null,
    grandCategoryName: grand?.name ?? null,
    judgesAssigned,
    finalists: finalists.map((f) => ({
      exhibitorId: f.exhibitorId,
      companyName: f.exhibitor.companyName,
      sourceCategory: f.category.name,
      round1Average: f.average,
    })),
  });
});

// Open the second round: validate the derived field exists, then flip the event
// phase so judges assigned to the GRAND category start scoring round 2.
router.post("/second-round/start", async (req, res) => {
  const eventId = (req.query.event as string) || "ditf-2026";
  const grand = await prisma.category.findFirst({ where: { eventId, group: "GRAND" } });
  if (!grand) return res.status(400).json({ error: "No GRAND category is configured for this event." });
  if (!grand.rubricId) return res.status(400).json({ error: "The GRAND category has no rubric assigned." });

  const finalists = await grandFinalists(eventId);
  if (finalists.length === 0)
    return res.status(400).json({ error: "No round-1 first-winners found. Compute round-1 results first." });

  const judgesAssigned = await prisma.judgeAssignment.count({ where: { categoryId: grand.id } });
  await prisma.event.update({ where: { id: eventId }, data: { phase: "SECOND_ROUND" } });

  res.json({
    ok: true,
    phase: "SECOND_ROUND",
    grandCategoryId: grand.id,
    finalists: finalists.length,
    judgesAssigned,
  });
});

// --- Reset (danger zone) ---
// Granular wipe: the admin ticks exactly what to clear. Everything is scoped to
// the event. Deleting exhibitors cascades their scores+results, so we delete
// results/scores first to keep the per-part counts honest.
const resetSchema = z.object({
  scores: z.boolean().optional(),
  results: z.boolean().optional(),
  exhibitors: z.boolean().optional(),
  assignments: z.boolean().optional(),
  phase: z.enum(["REGISTRATION", "SCORING", "R1_DONE", "SECOND_ROUND", "CLOSED"]).nullable().optional(),
});
router.post("/reset", async (req, res) => {
  const eventId = (req.query.event as string) || "ditf-2026";
  const parsed = resetSchema.safeParse(req.body);
  if (!parsed.success) return res.status(400).json({ error: parsed.error.flatten() });
  const { scores, results, exhibitors, assignments, phase } = parsed.data;

  if (!scores && !results && !exhibitors && !assignments && !phase)
    return res.status(400).json({ error: "Nothing selected to reset." });

  const cleared: Record<string, number | string> = {};
  if (results) cleared.results = (await prisma.result.deleteMany({ where: { category: { eventId } } })).count;
  if (scores) {
    // Submissions are meaningless without their scores — clear them together.
    cleared.submissions = (await prisma.judgeSubmission.deleteMany({ where: { exhibitor: { eventId } } })).count;
    cleared.scores = (await prisma.score.deleteMany({ where: { exhibitor: { eventId } } })).count;
  }
  if (exhibitors) cleared.exhibitors = (await prisma.exhibitor.deleteMany({ where: { eventId } })).count;
  if (assignments) cleared.assignments = (await prisma.judgeAssignment.deleteMany({ where: { category: { eventId } } })).count;
  if (phase) {
    await prisma.event.update({ where: { id: eventId }, data: { phase } });
    cleared.phase = phase;
  }
  res.json({ ok: true, cleared });
});

// --- Self-test ---
// Runs the full judging flow (score -> rank -> second round -> grand winner) on a
// throwaway, fully isolated event, then tears it down. Touches no real data:
// computeResults/grandFinalists are all scoped by eventId, and the temp event id
// is unique per run. Returns pass/fail checks like the CLI smoke test.
router.post("/self-test", async (_req, res) => {
  const checks: { label: string; ok: boolean }[] = [];
  const check = (ok: unknown, label: string) => checks.push({ ok: !!ok, label });

  const stamp = Date.now();
  const evId = `__selftest_${stamp}`;
  let userId: string | null = null;

  try {
    const rubric = await prisma.rubric.findUnique({
      where: { code: "ORDINARY" },
      include: { groups: { include: { items: true } } },
    });
    check(rubric, "ORDINARY rubric exists");
    if (!rubric) throw new Error("ORDINARY rubric not found — seed the database first.");
    const items = rubric.groups.flatMap((g) => g.items);
    check(items.length > 0, "rubric has scorable items");

    await prisma.event.create({
      data: { id: evId, name: "Self-test", theme: "self-test", registrationDeadline: new Date(stamp + 86400000), phase: "SCORING" },
    });
    const ordinary = await prisma.category.create({ data: { eventId: evId, order: 1, name: "ST Ordinary", group: "ORDINARY", rubricId: rubric.id } });
    const grand = await prisma.category.create({ data: { eventId: evId, order: 2, name: "ST Grand", group: "GRAND", rubricId: rubric.id } });
    const exA = await prisma.exhibitor.create({ data: { eventId: evId, companyName: "ST Company A", categoryId: ordinary.id } });
    const exB = await prisma.exhibitor.create({ data: { eventId: evId, companyName: "ST Company B", categoryId: ordinary.id } });

    const user = await prisma.user.create({ data: { email: `__selftest_${stamp}@example.invalid`, name: "ST Judge", role: "JUDGE", password: "__selftest__" } });
    userId = user.id;
    const judge = await prisma.judge.create({ data: { userId: user.id, code: `ST_${stamp}` } });
    await prisma.judgeAssignment.createMany({ data: [{ judgeId: judge.id, categoryId: ordinary.id }, { judgeId: judge.id, categoryId: grand.id }] });

    // Round 1: A full marks, B partial — then submit (locks + makes them count).
    await prisma.score.createMany({ data: items.map((it) => ({ exhibitorId: exA.id, judgeId: judge.id, rubricItemId: it.id, value: it.maxMark, round: 1 })) });
    await prisma.score.createMany({ data: items.map((it) => ({ exhibitorId: exB.id, judgeId: judge.id, rubricItemId: it.id, value: Math.round(it.maxMark * 0.4), round: 1 })) });
    await prisma.judgeSubmission.createMany({ data: [
      { judgeId: judge.id, exhibitorId: exA.id, round: 1 },
      { judgeId: judge.id, exhibitorId: exB.id, round: 1 },
    ] });

    await computeResults(evId, 1);
    const r1 = await prisma.result.findMany({ where: { categoryId: ordinary.id, round: 1 }, orderBy: { rank: "asc" } });
    check(r1[0]?.exhibitorId === exA.id && r1[0]?.rank === 1, "round 1: Company A ranked 1st");
    check(r1[1]?.exhibitorId === exB.id && r1[1]?.rank === 2, "round 1: Company B ranked 2nd");
    check((r1[0]?.average ?? 0) > (r1[1]?.average ?? 0), "round 1: 1st-place average beats 2nd");

    const finalists = await grandFinalistIds(evId);
    check(finalists.length === 1 && finalists[0] === exA.id, "second-round field = the first-winner (A)");

    // Round 2: A re-scored in the grand final, then submitted.
    await prisma.score.createMany({ data: items.map((it) => ({ exhibitorId: exA.id, judgeId: judge.id, rubricItemId: it.id, value: Math.round(it.maxMark * 0.8), round: 2 })) });
    await prisma.judgeSubmission.create({ data: { judgeId: judge.id, exhibitorId: exA.id, round: 2 } });
    await computeResults(evId, 2);
    const r2 = await prisma.result.findMany({ where: { categoryId: grand.id, round: 2 }, orderBy: { rank: "asc" } });
    check(r2[0]?.exhibitorId === exA.id && r2[0]?.rank === 1, "round 2: Company A wins the grand final");

    const pass = checks.filter((c) => c.ok).length;
    res.json({ ok: pass === checks.length, pass, fail: checks.length - pass, checks });
  } catch (e: any) {
    const pass = checks.filter((c) => c.ok).length;
    res.status(500).json({ ok: false, pass, fail: checks.length - pass, checks, error: e.message });
  } finally {
    // Teardown — child → parent order. Exhibitor→Category is RESTRICT (no cascade),
    // so the event can't be deleted until its exhibitors and categories are gone.
    try {
      await prisma.result.deleteMany({ where: { category: { eventId: evId } } });
      await prisma.judgeSubmission.deleteMany({ where: { exhibitor: { eventId: evId } } });
      await prisma.score.deleteMany({ where: { exhibitor: { eventId: evId } } });
      await prisma.judgeAssignment.deleteMany({ where: { category: { eventId: evId } } });
      await prisma.exhibitor.deleteMany({ where: { eventId: evId } });
      await prisma.category.deleteMany({ where: { eventId: evId } });
      await prisma.event.deleteMany({ where: { id: evId } });
    } catch {
      /* best-effort cleanup */
    }
    if (userId) await prisma.user.deleteMany({ where: { id: userId } }).catch(() => {});
  }
});

export default router;
