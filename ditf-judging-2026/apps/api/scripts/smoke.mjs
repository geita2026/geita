// End-to-end smoke test for the DITF 2026 judging API.
// Requires: API running (npm run dev:api) + DB migrated + seeded.
// Run:  node apps/api/scripts/smoke.mjs   (or: npm run test:smoke --workspace api)
// Uses global fetch (Node 18+). No extra dependencies.

const BASE = process.env.API_URL || "http://localhost:4000";
const EVENT = "ditf-2026";

let pass = 0;
let fail = 0;
function check(cond, label) {
  if (cond) { pass++; console.log(`  ✓ ${label}`); }
  else { fail++; console.error(`  ✗ ${label}`); }
}

async function req(path, { method = "GET", token, body } = {}) {
  const res = await fetch(`${BASE}${path}`, {
    method,
    headers: { "Content-Type": "application/json", ...(token ? { Authorization: `Bearer ${token}` } : {}) },
    body: body ? JSON.stringify(body) : undefined,
  });
  const text = await res.text();
  let json;
  try { json = text ? JSON.parse(text) : null; } catch { json = text; }
  return { status: res.status, ok: res.ok, json };
}

async function main() {
  console.log(`\nDITF 2026 API smoke test → ${BASE}\n`);

  // 0) health
  const health = await req("/health");
  check(health.ok && health.json?.ok, "GET /health responds");

  // 1) admin login
  const adminLogin = await req("/auth/login", { method: "POST", body: { email: "admin@tantrade.go.tz", password: "admin123" } });
  check(adminLogin.ok && adminLogin.json?.token, "admin can log in");
  const admin = adminLogin.json?.token;
  if (!admin) return finish();

  // 2) reset phase to REGISTRATION so the test is repeatable
  const toReg = await req(`/admin/events/${EVENT}/phase`, { method: "PATCH", token: admin, body: { phase: "REGISTRATION" } });
  check(toReg.ok, "admin sets phase = REGISTRATION");

  // 3) pick an ordinary (scored) category
  const cats = await req("/categories");
  const ordinary = cats.json?.find((c) => c.group === "ORDINARY" && c.rubricId);
  check(!!ordinary, "found an ORDINARY category with a rubric");
  if (!ordinary) return finish();

  // 4) register three exhibitors in that category (C is left un-submitted on purpose)
  const stamp = Date.now();
  const names = [`Smoke Co A ${stamp}`, `Smoke Co B ${stamp}`, `Smoke Co C ${stamp}`];
  const exhibitors = [];
  for (const companyName of names) {
    const r = await req("/exhibitors", { method: "POST", body: { companyName, categoryId: ordinary.id, standLocation: "Hall 1" } });
    check(r.status === 201 && r.json?.id, `registered exhibitor "${companyName}"`);
    if (r.json?.id) exhibitors.push(r.json);
  }
  if (exhibitors.length < 3) {
    console.error("  ! Registration failed — is the event past its deadline? Bump registrationDeadline in data/criteria.json and re-seed.");
    return finish();
  }

  // 5) find judge J01 and assign to the category
  const judges = await req("/admin/judges", { token: admin });
  const j01 = judges.json?.find((j) => j.code === "J01");
  check(!!j01, "judge J01 exists");
  const assign = await req("/admin/assignments", { method: "POST", token: admin, body: { judgeId: j01.id, categoryId: ordinary.id } });
  check(assign.status === 201, "assigned J01 to the category");

  // 6) move to SCORING
  const toScoring = await req(`/admin/events/${EVENT}/phase`, { method: "PATCH", token: admin, body: { phase: "SCORING" } });
  check(toScoring.ok, "admin sets phase = SCORING");

  // 7) judge login
  const judgeLogin = await req("/auth/login", { method: "POST", body: { email: "judge1@tantrade.go.tz", password: "judge123" } });
  check(judgeLogin.ok && judgeLogin.json?.token, "judge J01 can log in");
  const judge = judgeLogin.json?.token;

  // 8) judge sees the assignment
  const assignments = await req("/judge/assignments", { token: judge });
  const myCat = assignments.json?.find((a) => a.category.id === ordinary.id);
  check(!!myCat && myCat.exhibitors.length >= 3, "judge sees assigned exhibitors");

  // helpers: autosave all items at a factor, and finalise (submit + lock)
  async function autosaveAll(exId, factor) {
    const sheet = await req(`/judge/exhibitors/${exId}/sheet`, { token: judge });
    const items = sheet.json.rubric.groups.flatMap((g) => g.items.map((it) => ({ rubricItemId: it.id, value: Math.round(it.maxMark * factor) })));
    const save = await req(`/judge/exhibitors/${exId}/scores`, { method: "POST", token: judge, body: { items } });
    return { save, items, total: items.reduce((a, b) => a + b.value, 0) };
  }
  const finalize = (exId) => req(`/judge/exhibitors/${exId}/submit`, { method: "POST", token: judge, body: {} });

  // 9) autosave A (full) and B (partial); both have every item scored
  const a = await autosaveAll(exhibitors[0].id, 1.0);
  const b = await autosaveAll(exhibitors[1].id, 0.4);
  check(a.save.ok && a.save.json.savedItems === a.items.length, `autosaved exhibitor A (${a.items.length} items)`);
  check(b.save.ok && b.save.json.savedItems === b.items.length, "autosaved exhibitor B");

  // 10) max-mark validation should reject an over-cap score
  const oneItem = (await req(`/judge/exhibitors/${exhibitors[0].id}/sheet`, { token: judge })).json.rubric.groups[0].items[0];
  const bad = await req(`/judge/exhibitors/${exhibitors[0].id}/scores`, { method: "POST", token: judge, body: { items: [{ rubricItemId: oneItem.id, value: oneItem.maxMark + 999 }] } });
  check(bad.status === 400, "API rejects a score above the item max");

  // 10b) C is scored on only the first group — an incomplete submit must be refused
  const cSheet = await req(`/judge/exhibitors/${exhibitors[2].id}/sheet`, { token: judge });
  const cItems = cSheet.json.rubric.groups[0].items.map((it) => ({ rubricItemId: it.id, value: it.maxMark }));
  await req(`/judge/exhibitors/${exhibitors[2].id}/scores`, { method: "POST", token: judge, body: { items: cItems } });
  const cSubmit = await finalize(exhibitors[2].id);
  check(cSubmit.status === 400, "incomplete sheet cannot be submitted (exhibitor C)");

  // 11) finalise A and B; once submitted the sheet is locked
  const aSub = await finalize(exhibitors[0].id);
  const bSub = await finalize(exhibitors[1].id);
  check(aSub.ok && aSub.json?.submitted, "exhibitor A submitted");
  check(bSub.ok && bSub.json?.submitted, "exhibitor B submitted");
  const editLocked = await req(`/judge/exhibitors/${exhibitors[0].id}/scores`, { method: "POST", token: judge, body: { items: a.items } });
  check(editLocked.status === 409, "judge cannot edit scores after submission (locked)");
  const reSubmit = await finalize(exhibitors[0].id);
  check(reSubmit.status === 409, "cannot submit twice");

  // 12) compute results
  const compute = await req(`/admin/results/compute?round=1`, { method: "POST", token: admin });
  check(compute.ok && compute.json?.categoriesRanked >= 1, "admin computes results");

  // 13) leaderboard: A 1st, B 2nd, and the un-submitted C is NOT counted
  const results = await req(`/results?round=1`);
  const catResult = results.json?.find((c) => c.category.id === ordinary.id);
  const first = catResult?.winners.find((w) => w.rank === 1);
  const second = catResult?.winners.find((w) => w.rank === 2);
  check(first?.exhibitor.id === exhibitors[0].id, "exhibitor A ranked 1st");
  check(second?.exhibitor.id === exhibitors[1].id, "exhibitor B ranked 2nd");
  check((first?.average ?? 0) > (second?.average ?? 0), "1st-place average > 2nd-place average");
  check(!catResult?.winners.some((w) => w.exhibitor.id === exhibitors[2].id), "un-submitted exhibitor C is not ranked");

  // --- Second round (grand final) ---

  // 13) assign J01 to the GRAND category so they can judge the final
  const grand = cats.json?.find((c) => c.group === "GRAND" && c.rubricId);
  check(!!grand, "found a GRAND category with a rubric");
  if (!grand) return finish();
  const grandAssign = await req("/admin/assignments", { method: "POST", token: admin, body: { judgeId: j01.id, categoryId: grand.id } });
  check(grandAssign.status === 201, "assigned J01 to the grand final");

  // 14) admin opens the second round → promotes round-1 first-winners
  const start = await req("/admin/second-round/start", { method: "POST", token: admin });
  check(start.ok && start.json?.phase === "SECOND_ROUND", "admin opens the second round");
  check((start.json?.finalists ?? 0) >= 1, "second round has at least one finalist (A)");

  // 15) judge now sees the grand final with the finalist, in round 2
  const r2assignments = await req("/judge/assignments", { token: judge });
  const grandCat = r2assignments.json?.find((a) => a.category.id === grand.id);
  check(!!grandCat && grandCat.round === 2, "judge sees the grand final as round 2");
  check(!!grandCat?.exhibitors.find((e) => e.id === exhibitors[0].id), "exhibitor A is in the second-round field");
  // round-1 ordinary category is hidden once the final opens
  check(!r2assignments.json?.find((a) => a.category.id === ordinary.id), "round-1 categories hidden in round 2");

  // 16) judge scores the finalist — sheet should report round 2 with no prior scores
  const r2sheet = await req(`/judge/exhibitors/${exhibitors[0].id}/sheet`, { token: judge });
  check(r2sheet.json?.round === 2 && Object.keys(r2sheet.json?.myScores || {}).length === 0, "round-2 sheet is fresh");
  const r2items = r2sheet.json.rubric.groups.flatMap((g) => g.items.map((it) => ({ rubricItemId: it.id, value: Math.round(it.maxMark * 0.8) })));
  const r2save = await req(`/judge/exhibitors/${exhibitors[0].id}/scores`, { method: "POST", token: judge, body: { items: r2items } });
  check(r2save.ok && r2save.json?.round === 2, "judge autosaves round-2 scores");
  const r2sub = await req(`/judge/exhibitors/${exhibitors[0].id}/submit`, { method: "POST", token: judge, body: {} });
  check(r2sub.ok && r2sub.json?.submitted, "judge submits the grand-final sheet");

  // 17) a non-finalist (exhibitor B) cannot be scored in round 2
  const r2bad = await req(`/judge/exhibitors/${exhibitors[1].id}/scores`, { method: "POST", token: judge, body: { items: r2items } });
  check(r2bad.status === 403, "non-finalist rejected in round 2");

  // 18) compute + publish round-2 results
  const r2compute = await req("/admin/results/compute?round=2", { method: "POST", token: admin });
  check(r2compute.ok && r2compute.json?.categoriesRanked >= 1, "admin computes round-2 results");
  const r2results = await req("/results?round=2");
  const grandResult = r2results.json?.find((c) => c.category.id === grand.id);
  check(grandResult?.winners.find((w) => w.rank === 1)?.exhibitor.id === exhibitors[0].id, "exhibitor A wins the grand final");

  finish();
}

function finish() {
  console.log(`\nResult: ${pass} passed, ${fail} failed\n`);
  process.exit(fail === 0 ? 0 : 1);
}

main().catch((e) => {
  console.error("\nSmoke test crashed — is the API running on", BASE, "?\n", e.message);
  process.exit(1);
});
