"use client";

import { startTransition, useOptimistic } from "react";
import Link from "next/link";
import { Button, Popover } from "@heroui/react";
import { Bell } from "@gravity-ui/icons";
import clsx from "clsx";

import type { AppNotification } from "@/lib/types";

import { markNotificationsRead } from "@/server/actions";

const TONE: Record<string, string> = {
  payment: "text-warning",
  reminder: "text-danger",
  allocation: "text-brand-ink",
  application: "text-brand-ink",
  checkin: "text-success",
};

function ago(date: string) {
  const mins = Math.round((Date.now() - new Date(date).getTime()) / 60_000);

  if (mins < 60) return `${Math.max(1, mins)}m ago`;
  if (mins < 1440) return `${Math.round(mins / 60)}h ago`;

  return `${Math.round(mins / 1440)}d ago`;
}

/** FR-035..039: the in-system channel. Email and SMS copies are sent by the API. */
export function NotificationBell({ notifications }: { notifications: AppNotification[] }) {
  const [items, apply] = useOptimistic(notifications, (list: AppNotification[], id: number | "all") =>
    list.map((n) => (id === "all" || n.id === id ? { ...n, read_at: n.read_at ?? new Date().toISOString() } : n)),
  );
  const unread = items.filter((n) => !n.read_at).length;

  return (
    <Popover>
      <Button
        aria-label={unread ? `Notifications, ${unread} unread` : "Notifications"}
        isIconOnly
        size="sm"
        variant="ghost"
      >
        <span className="relative">
          <Bell />
          {unread > 0 && (
            <span className="absolute -top-1.5 -right-2 grid min-w-4 place-items-center rounded-full bg-danger px-1 text-[10px] font-bold text-white">
              {unread > 9 ? "9+" : unread}
            </span>
          )}
        </span>
      </Button>
      <Popover.Content className="w-[min(92vw,380px)] p-0" placement="bottom end">
        <div className="flex items-center justify-between border-b border-separator px-4 py-3">
          <p className="font-semibold">Notifications</p>
          {unread > 0 && (
            <button
              className="text-xs font-semibold text-brand-ink hover:underline"
              type="button"
              onClick={() =>
                startTransition(async () => {
                  apply("all");
                  await markNotificationsRead();
                })
              }
            >
              Mark all read
            </button>
          )}
        </div>
        {items.length === 0 ? (
          <p className="px-4 py-8 text-center text-sm text-muted">Nothing needs you right now.</p>
        ) : (
          <ul className="max-h-[60vh] divide-y divide-separator overflow-y-auto">
            {items.map((n) => (
              <li key={n.id}>
                <Link
                  className={clsx(
                    "block px-4 py-3 transition-colors hover:bg-default/60",
                    !n.read_at && "bg-brand-soft/40",
                  )}
                  href={n.href ?? "#"}
                  onClick={() =>
                    startTransition(async () => {
                      apply(n.id);
                      await markNotificationsRead(n.id);
                    })
                  }
                >
                  <div className="flex items-center gap-2">
                    <span className={clsx("text-xs font-bold uppercase tracking-wider", TONE[n.kind] ?? "text-muted")}>
                      {n.kind}
                    </span>
                    <span className="ms-auto text-xs text-muted">{ago(n.created_at)}</span>
                    {!n.read_at && <span aria-hidden className="size-2 rounded-full bg-brand" />}
                  </div>
                  <p className="mt-0.5 text-sm font-semibold leading-snug">{n.title}</p>
                  <p className="mt-0.5 line-clamp-2 text-xs leading-relaxed text-muted">{n.body}</p>
                </Link>
              </li>
            ))}
          </ul>
        )}
      </Popover.Content>
    </Popover>
  );
}
