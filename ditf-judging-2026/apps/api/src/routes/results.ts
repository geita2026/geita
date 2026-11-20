import { Router } from "express";
import { prisma } from "../db.js";

const router = Router();

// Public leaderboard: results per category for a round (+ per-judge matrix optional)
router.get("/", async (req, res) => {
  const eventId = (req.query.event as string) || "ditf-2026";
  const round = Number(req.query.round || 1);
  const categoryId = req.query.category as string | undefined;

  const results = await prisma.result.findMany({
    where: { round, category: { eventId, ...(categoryId ? { id: categoryId } : {}) } },
    orderBy: [{ categoryId: "asc" }, { rank: "asc" }],
    include: {
      exhibitor: { select: { id: true, companyName: true, standLocation: true } },
      category: { select: { id: true, name: true, group: true } },
    },
  });

  // group by category
  const byCategory: Record<string, any> = {};
  for (const r of results) {
    const key = r.category.id;
    byCategory[key] ??= { category: r.category, manual: false, winners: [] };
    if (r.manual) byCategory[key].manual = true;
    byCategory[key].winners.push({
      rank: r.rank,
      average: r.average,
      manual: r.manual,
      exhibitor: r.exhibitor,
      medal: r.rank === 1 ? "1st" : r.rank === 2 ? "2nd" : r.rank === 3 ? "3rd" : `${r.rank}th`,
    });
  }
  res.json(Object.values(byCategory));
});

export default router;
