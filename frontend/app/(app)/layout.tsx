import type { AppNotification } from "@/lib/types";

import { api } from "@/lib/api";
import { requireSession } from "@/lib/auth/session";
import { currentExhibition, listExhibitions } from "@/lib/exhibition";
import { AppShell } from "@/components/shell/app-shell";

export default async function AppLayout({ children }: { children: React.ReactNode }) {
  const session = await requireSession();
  const [notifications, exhibitions, exhibition] = await Promise.all([
    api<AppNotification[]>("/notifications"),
    listExhibitions(),
    currentExhibition(),
  ]);

  return (
    <AppShell exhibition={exhibition} exhibitions={exhibitions} notifications={notifications} session={session}>
      {children}
    </AppShell>
  );
}
