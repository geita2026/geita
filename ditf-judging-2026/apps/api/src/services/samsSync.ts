import { prisma } from "../db.js";
import { fetchConfirmedExhibitors } from "../sams.js";

function norm(s: string | null | undefined): string {
  return (s || "")
    .toUpperCase()
    .replace(/[^A-Z0-9]+/g, " ") // collapse punctuation/whitespace
    .trim();
}

// SAMS category names that were renamed in the 2026 list.
// Maps normalized SAMS name -> our 2026 Category.name.
const CATEGORY_ALIASES: Record<string, string> = {
  [norm("BEST MEDIA SERVICES EXHIBITOR")]: "Best Media Exhibitor",
  [norm("BEST WOMEN ENTERPRINEUR EXHIBITOR")]: "Best Women in Trade Exhibitor",
  [norm("HIGHER LEARNING EDUCATION, SKILLS DEVELOPMENT AND RESEARCH INSTITUTION EXHIBITOR")]:
    "Higher Learning, Skills Development and Research Institution Exhibitor",
  [norm("INNOVATIONS, ICT EQUIPMENT AND SOLUTIONS EXHIBITOR")]: "Innovation and Solutions Exhibitor",
};

export type SyncReport = {
  fetched: number;
  created: number;
  updated: number;
  skippedNoCategory: number;
  unmatchedCategories: string[];
};

/**
 * Sync confirmed exhibitors from SAMS into the judging DB.
 * Category resolution order:
 *   1) Category.samsCategoryId == exhibitor.judgingCategory
 *   2) normalized name match (SAMS categoryName == our Category.name)
 * Exhibitors whose category can't be resolved are skipped and reported.
 */
export async function syncExhibitorsFromSams(eventId = "ditf-2026"): Promise<SyncReport> {
  const rows = await fetchConfirmedExhibitors();

  const categories = await prisma.category.findMany({ where: { eventId } });
  const bySamsId = new Map<string, string>();
  const byName = new Map<string, string>();
  for (const c of categories) {
    if (c.samsCategoryId) bySamsId.set(c.samsCategoryId, c.id);
    byName.set(norm(c.name), c.id);
  }

  const report: SyncReport = { fetched: rows.length, created: 0, updated: 0, skippedNoCategory: 0, unmatchedCategories: [] };
  const unmatched = new Set<string>();

  for (const r of rows) {
    // resolve our category id
    let categoryId: string | undefined;
    const samsName = norm(r.categoryName);
    const aliasTarget = CATEGORY_ALIASES[samsName];
    const lookupName = aliasTarget ? norm(aliasTarget) : samsName;
    if (r.judgingCategory && bySamsId.has(r.judgingCategory)) {
      categoryId = bySamsId.get(r.judgingCategory);
    } else if (r.categoryName && byName.has(lookupName)) {
      categoryId = byName.get(lookupName);
      // opportunistically remember the SAMS id for next time
      if (r.judgingCategory && categoryId && !bySamsId.has(r.judgingCategory)) {
        await prisma.category.update({ where: { id: categoryId }, data: { samsCategoryId: r.judgingCategory } }).catch(() => {});
        bySamsId.set(r.judgingCategory, categoryId);
      }
    }

    if (!categoryId) {
      report.skippedNoCategory++;
      if (r.categoryName) unmatched.add(r.categoryName);
      continue;
    }

    const data = {
      eventId,
      externalId: r.companyID,
      source: "SAMS",
      companyName: r.companyName,
      contactPhone: r.companyPhone || undefined,
      standLocation: r.judging_location || undefined,
      categoryId,
      syncedAt: new Date(),
    };

    const existing = await prisma.exhibitor.findUnique({ where: { externalId: r.companyID } });
    if (existing) {
      await prisma.exhibitor.update({ where: { externalId: r.companyID }, data });
      report.updated++;
    } else {
      await prisma.exhibitor.create({ data });
      report.created++;
    }
  }

  report.unmatchedCategories = [...unmatched];
  return report;
}
