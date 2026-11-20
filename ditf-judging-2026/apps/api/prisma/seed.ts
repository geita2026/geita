import { PrismaClient } from "@prisma/client";
import { readFileSync } from "node:fs";
import { fileURLToPath } from "node:url";
import { dirname, resolve } from "node:path";

const __dirname = dirname(fileURLToPath(import.meta.url));
const DATA_DIR = resolve(__dirname, "../../../data");

const criteria = JSON.parse(readFileSync(resolve(DATA_DIR, "criteria.json"), "utf8"));
const categories = JSON.parse(readFileSync(resolve(DATA_DIR, "categories.json"), "utf8"));

const prisma = new PrismaClient();

// Map a category group -> rubric code
const RUBRIC_FOR_GROUP: Record<string, string | null> = {
  ORDINARY: "ORDINARY",
  SPECIAL_EXHIBITOR: "BEST_EXHIBITOR",
  SPECIAL_PAVILION: "BEST_BRANDED_PAVILION",
  RECOGNITION: null,
  GRAND: "ORDINARY",
};

async function main() {
  console.log("Seeding DITF 2026 judging database…");

  // 1) Rubrics, groups, items
  const rubricIdByCode: Record<string, string> = {};
  for (const r of criteria.rubrics) {
    const rubric = await prisma.rubric.upsert({
      where: { code: r.rubricCode },
      update: { name: r.name, totalPoints: r.totalPoints },
      create: { code: r.rubricCode, name: r.name, totalPoints: r.totalPoints },
    });
    rubricIdByCode[r.rubricCode] = rubric.id;

    // wipe & recreate groups/items so re-seeding stays clean
    await prisma.rubricGroup.deleteMany({ where: { rubricId: rubric.id } });
    let gi = 0;
    for (const g of r.groups) {
      const group = await prisma.rubricGroup.create({
        data: { rubricId: rubric.id, code: g.code, title: g.title, maxMark: g.maxMark, sort: gi++ },
      });
      let ii = 0;
      for (const item of g.items) {
        await prisma.rubricItem.create({
          data: { groupId: group.id, code: item.code, description: item.description, maxMark: item.maxMark, sort: ii++ },
        });
      }
    }
  }
  console.log(`  rubrics: ${criteria.rubrics.length}`);

  // 2) Event
  const event = await prisma.event.upsert({
    where: { id: "ditf-2026" },
    update: {},
    create: {
      id: "ditf-2026",
      name: criteria.event,
      theme: criteria.theme,
      registrationDeadline: new Date(criteria.registrationDeadline),
      phase: "REGISTRATION",
    },
  });

  // 3) Categories — create any that are missing (matched by NAME), never overwrite existing.
  // Categories are editable in the admin UI, so re-seeding must preserve those edits and must
  // not create duplicates. We match on the normalized name (not id) because rows may already
  // exist with different ids than this file's deterministic ids.
  const normName = (s: string | null | undefined) => (s || "").toUpperCase().replace(/[^A-Z0-9]+/g, " ").trim();
  const existingCats = await prisma.category.findMany({ where: { eventId: event.id }, select: { name: true } });
  const haveByName = new Set(existingCats.map((c) => normName(c.name)));
  let createdCats = 0;
  for (const c of categories.categories) {
    if (haveByName.has(normName(c.name))) continue; // already present (possibly edited) — leave it
    const rubricCode = RUBRIC_FOR_GROUP[c.group];
    await prisma.category.create({
      data: {
        id: c.id, // deterministic shared id (also exposed as the SAMS feed uuid)
        eventId: event.id,
        order: c.order,
        name: c.name,
        group: c.group,
        samsCategoryId: c.id, // SAMS exhibitors store this id in exhibitors.judgingCategory
        rubricId: rubricCode ? rubricIdByCode[rubricCode] : null,
      },
    });
    haveByName.add(normName(c.name));
    createdCats++;
  }
  console.log(`  categories: ${createdCats} added, ${categories.categories.length - createdCats} already present (edits preserved)`);

  // 4) No demo users are seeded (avoids weak default credentials in production).
  // Admins sign in with username "admin" + the SAMS scanner PIN; judges are created
  // in the admin Users page.

  console.log("Seed complete.");
}

main()
  .catch((e) => {
    console.error(e);
    process.exit(1);
  })
  .finally(async () => {
    await prisma.$disconnect();
  });
