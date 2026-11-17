"use client";

import { useEffect } from "react";
import { Button } from "@heroui/react";

export default function Error({ error, reset }: { error: Error & { digest?: string }; reset: () => void }) {
  useEffect(() => {
    console.error(error);
  }, [error]);

  return (
    <main className="flex min-h-screen flex-col items-center justify-center gap-3 p-6 text-center">
      <h2 className="text-xl font-semibold">Something went wrong</h2>
      <p className="max-w-md text-muted">
        The page could not be loaded. If this keeps happening, contact the ICT Unit and quote reference{" "}
        <code className="font-mono text-xs">{error.digest ?? "n/a"}</code>.
      </p>
      <Button variant="secondary" onPress={() => reset()}>
        Try again
      </Button>
    </main>
  );
}
