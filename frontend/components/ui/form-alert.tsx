export function FormAlert({ message, tone = "danger" }: { message?: string; tone?: "danger" | "success" }) {
  if (!message) return null;

  return (
    <div
      className={`rounded-lg px-3 py-2 text-sm ${tone === "danger" ? "bg-danger-soft text-danger" : "bg-success-soft text-success"}`}
      role="alert"
    >
      {message}
    </div>
  );
}
