const API = process.env.NEXT_PUBLIC_API_URL || "http://localhost:4000";

export async function submitSurvey(body: unknown): Promise<{ ok: boolean; id: string }> {
  const res = await fetch(`${API}/survey/responses`, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(body),
  });
  if (!res.ok) {
    const data = await res.json().catch(() => ({}));
    const msg = data?.error?.formErrors?.[0] || data?.error?.fieldErrors?.companyName?.[0] || "Imeshindikana kutuma. Tafadhali jaribu tena.";
    throw new Error(typeof msg === "string" ? msg : "Imeshindikana kutuma.");
  }
  return res.json();
}
