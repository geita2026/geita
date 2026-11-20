import { Router } from "express";
import { z } from "zod";
import { prisma } from "../db.js";
import { auth, requireRole } from "../auth.js";

const router = Router();

const registerSchema = z.object({
  companyName: z.string().min(2),
  contactEmail: z.string().email().optional(),
  contactPhone: z.string().optional(),
  standLocation: z.string().optional(),
  isForeign: z.boolean().optional(),
  categoryId: z.string().min(1),
});

// Public registration (open only during REGISTRATION phase, before deadline)
router.post("/", async (req, res) => {
  const parsed = registerSchema.safeParse(req.body);
  if (!parsed.success) return res.status(400).json({ error: parsed.error.flatten() });

  const category = await prisma.category.findUnique({ where: { id: parsed.data.categoryId }, include: { event: true } });
  if (!category) return res.status(404).json({ error: "Category not found" });
  if (category.group === "RECOGNITION" || category.group === "GRAND")
    return res.status(400).json({ error: "This category is not open for self-registration" });

  const event = category.event;
  if (event.phase !== "REGISTRATION") return res.status(403).json({ error: "Registration is closed" });
  if (new Date() > event.registrationDeadline) return res.status(403).json({ error: "Registration deadline has passed" });

  const exhibitor = await prisma.exhibitor.create({
    data: { ...parsed.data, eventId: event.id, source: "MANUAL" },
  });
  res.status(201).json(exhibitor);
});

// Admin/Judge: list exhibitors (filterable)
router.get("/", auth(), requireRole("ADMIN", "JUDGE"), async (req, res) => {
  const where: any = { eventId: (req.query.event as string) || "ditf-2026" };
  if (req.query.category) where.categoryId = req.query.category as string;
  if (req.query.status) where.status = req.query.status as string;
  const exhibitors = await prisma.exhibitor.findMany({
    where,
    orderBy: { companyName: "asc" },
    include: {
      category: { select: { id: true, name: true, group: true, rubricId: true } },
      _count: { select: { scores: true, submissions: true, results: true } },
    },
  });
  res.json(exhibitors);
});

// Admin: edit / update status
router.put("/:id", auth(), requireRole("ADMIN"), async (req, res) => {
  const exhibitor = await prisma.exhibitor.update({ where: { id: req.params.id }, data: req.body });
  res.json(exhibitor);
});

// Admin: delete an exhibitor — refused if it carries any scores, submissions or
// results, so only a clean (e.g. duplicate, un-scored) row can be removed.
router.delete("/:id", auth(), requireRole("ADMIN"), async (req, res) => {
  const id = req.params.id;
  const [scores, submissions, results] = await Promise.all([
    prisma.score.count({ where: { exhibitorId: id } }),
    prisma.judgeSubmission.count({ where: { exhibitorId: id } }),
    prisma.result.count({ where: { exhibitorId: id } }),
  ]);
  if (scores || submissions || results)
    return res.status(409).json({
      error: `Cannot delete — this exhibitor has ${scores} score(s), ${submissions} submission(s) and appears in ${results} result(s). Only an un-scored exhibitor can be removed.`,
    });
  try {
    await prisma.exhibitor.delete({ where: { id } });
  } catch (e: any) {
    if (e?.code === "P2025") return res.status(404).json({ error: "Exhibitor not found." });
    throw e;
  }
  res.json({ ok: true });
});

export default router;
