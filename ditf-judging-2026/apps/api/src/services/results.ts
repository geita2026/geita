import { prisma } from "../db.js";

/** Map an event phase to the round judges are currently scoring. */
export function phaseToRound(phase: string): number {
  return phase === "SECOND_ROUND" ? 2 : 1;
}

/**
 * The second-round field: the first-winner (rank 1) of every ORDINARY category
 * in round 1. Derived from round-1 Results, so there is nothing extra to persist
 * or keep in sync — recompute round 1 and the finalist set follows.
 */
export async function grandFinalists(eventId: string) {
  return prisma.result.findMany({
    where: { round: 1, rank: 1, category: { eventId, group: "ORDINARY" } },
    orderBy: { average: "desc" },
    include: {
      exhibitor: { select: { id: true, companyName: true, standLocation: true } },
      category: { select: { id: true, name: true } },
    },
  });
}

/** Just the exhibitor ids that qualify for the second round. */
export async function grandFinalistIds(eventId: string): Promise<string[]> {
  const rows = await grandFinalists(eventId);
  return rows.map((r) => r.exhibitorId);
}

/** Average (mean of submitted-judge totals) for one exhibitor in a round, or null. */
async function averageFor(exhibitorId: string, round: number): Promise<number | null> {
  const subs = await prisma.judgeSubmission.findMany({ where: { exhibitorId, round }, select: { judgeId: true } });
  if (subs.length === 0) return null;
  const counted = new Set(subs.map((s) => s.judgeId));
  const scores = await prisma.score.findMany({
    where: { exhibitorId, round, judgeId: { in: [...counted] } },
    select: { judgeId: true, value: true },
  });
  if (scores.length === 0) return null;
  const perJudge = new Map<string, number>();
  for (const s of scores) perJudge.set(s.judgeId, (perJudge.get(s.judgeId) || 0) + s.value);
  const totals = [...perJudge.values()];
  return Number((totals.reduce((a, b) => a + b, 0) / totals.length).toFixed(2));
}

/**
 * The pool an admin can pick from when hand-editing a category's podium: every
 * eligible exhibitor (registered in the category, or the finalists for a GRAND
 * round-2), each with their current computed average (null if not yet scored).
 */
export async function candidateAverages(eventId: string, categoryId: string, round: number) {
  const cat = await prisma.category.findUnique({ where: { id: categoryId }, select: { group: true } });
  let exhibitors: { id: string; companyName: string }[];
  if (round >= 2 && cat?.group === "GRAND") {
    const f = await grandFinalists(eventId);
    exhibitors = f.map((r) => ({ id: r.exhibitorId, companyName: r.exhibitor.companyName }));
  } else {
    exhibitors = await prisma.exhibitor.findMany({
      where: { categoryId },
      select: { id: true, companyName: true },
      orderBy: { companyName: "asc" },
    });
  }
  const out = [];
  for (const ex of exhibitors) out.push({ ...ex, average: await averageFor(ex.id, round) });
  return out;
}

/**
 * Compute results for every scored category in a round.
 * Each judge's score for an exhibitor = sum of that judge's item values.
 * Exhibitor average = mean of judge totals (judges who actually scored).
 * Ranks are assigned 1..n descending per category.
 *
 * Round 2: ordinary/special categories have no round-2 scores, so they fall out
 * naturally. The GRAND category has no exhibitors of its own — its field is the
 * derived set of round-1 first-winners, scored anew under round 2.
 */
export async function computeResults(eventId: string, round = 1) {
  const categories = await prisma.category.findMany({
    where: { eventId, rubricId: { not: null } },
    include: { exhibitors: { select: { id: true } } },
  });

  const finalists = round >= 2 ? await grandFinalistIds(eventId) : [];

  const written: { categoryId: string; ranked: number }[] = [];

  for (const cat of categories) {
    // A manually-overridden podium is preserved — never overwrite it on Compute.
    const manualCount = await prisma.result.count({ where: { categoryId: cat.id, round, manual: true } });
    if (manualCount > 0) continue;

    // The GRAND category re-judges the derived finalists; everyone else scores
    // their own registered exhibitors.
    const candidateIds =
      round >= 2 && cat.group === "GRAND" ? finalists : cat.exhibitors.map((e) => e.id);

    // clear existing results for this category+round
    await prisma.result.deleteMany({ where: { categoryId: cat.id, round } });

    const rows: { exhibitorId: string; average: number }[] = [];

    for (const exhibitorId of candidateIds) {
      // Only judges who FINALISED (submitted) their sheet count — a partial or
      // never-submitted sheet is ignored entirely.
      const subs = await prisma.judgeSubmission.findMany({
        where: { exhibitorId, round },
        select: { judgeId: true },
      });
      if (subs.length === 0) continue;
      const counted = new Set(subs.map((s) => s.judgeId));

      const scores = await prisma.score.findMany({
        where: { exhibitorId, round, judgeId: { in: [...counted] } },
        select: { judgeId: true, value: true },
      });
      if (scores.length === 0) continue;

      // sum per judge
      const perJudge = new Map<string, number>();
      for (const s of scores) perJudge.set(s.judgeId, (perJudge.get(s.judgeId) || 0) + s.value);
      const totals = [...perJudge.values()];
      const average = totals.reduce((a, b) => a + b, 0) / totals.length;
      rows.push({ exhibitorId, average: Number(average.toFixed(2)) });
    }

    rows.sort((a, b) => b.average - a.average);
    let rank = 0;
    for (const r of rows) {
      rank++;
      await prisma.result.create({
        data: { categoryId: cat.id, exhibitorId: r.exhibitorId, round, average: r.average, rank },
      });
    }
    if (rows.length) written.push({ categoryId: cat.id, ranked: rows.length });
  }

  return written;
}
