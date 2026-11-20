import { Router } from "express";
import { prisma } from "../db.js";

const router = Router();

// Public: list categories (with their rubric summary) for an event
router.get("/", async (req, res) => {
  const eventId = (req.query.event as string) || "ditf-2026";
  const categories = await prisma.category.findMany({
    where: { eventId },
    orderBy: { order: "asc" },
    include: { rubric: { select: { id: true, code: true, name: true, totalPoints: true } } },
  });
  res.json(categories);
});

// Public feed for SAMS — the judging app is the source of truth for categories.
// Shape matches what SAMS expects: { categories: [{ uuid, title }] }.
// Only self-selectable groups (exhibitors choose these); recognition/grand excluded.
router.get("/feed", async (req, res) => {
  const eventId = (req.query.event as string) || "ditf-2026";
  const categories = await prisma.category.findMany({
    where: { eventId, group: { in: ["ORDINARY", "SPECIAL_EXHIBITOR", "SPECIAL_PAVILION"] } },
    orderBy: { order: "asc" },
    select: { id: true, name: true },
  });
  res.json({ categories: categories.map((c) => ({ uuid: c.id, title: c.name })) });
});

// Public: full rubric (groups + items) by id
router.get("/rubric/:id", async (req, res) => {
  const rubric = await prisma.rubric.findUnique({
    where: { id: req.params.id },
    include: { groups: { orderBy: { sort: "asc" }, include: { items: { orderBy: { sort: "asc" } } } } },
  });
  if (!rubric) return res.status(404).json({ error: "Rubric not found" });
  res.json(rubric);
});

export default router;
