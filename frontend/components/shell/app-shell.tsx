"use client";

import { useState } from "react";
import Link from "next/link";
import clsx from "clsx";
import { Button, Drawer } from "@heroui/react";
import { ArrowRightFromSquare, Bars } from "@gravity-ui/icons";

import type { AppNotification, Exhibition, Session } from "@/lib/types";

import { navItems, siteConfig } from "@/config/site";
import { logoutAction } from "@/server/actions";
import { versionLabel } from "@/lib/version";
import { Aura } from "@/components/ui/aura";
import { NotificationBell } from "@/components/shell/notification-bell";
import { IdleGuard } from "@/components/shell/idle-guard";
import { SidebarNav } from "@/components/shell/sidebar-nav";
import { NavProgress } from "@/components/shell/nav-progress";
import { SearchBox } from "@/components/shell/search-box";
import { UserMenu } from "@/components/shell/user-menu";
import { ExhibitionSwitcher } from "@/components/shell/exhibition-switcher";
import { ThemeSwitch } from "@/components/theme-switch";

function Brand() {
  return (
    <Link className="flex h-12 items-center gap-3 px-2" href="/dashboard">
      <span className="grid size-9 shrink-0 place-items-center rounded-xl bg-brand font-display text-xs font-extrabold text-brand-foreground">
        Ge
      </span>
      <span className="rail-label min-w-0 leading-none">
        <span className="block font-display text-[17px] font-extrabold tracking-tight">{siteConfig.shortName}</span>
        <span className="mt-1 block text-[11px] font-bold uppercase tracking-[0.18em] whitespace-nowrap text-muted">
          Mineral exhibition
        </span>
      </span>
    </Link>
  );
}

function SidebarBody({ items, onNavigate }: { items: typeof navItems; onNavigate?: () => void }) {
  return (
    <>
      <div className="min-h-0 shrink overflow-y-auto overflow-x-hidden [scrollbar-width:none]">
        <SidebarNav items={items} onNavigate={onNavigate} />
      </div>
      <div className="mt-auto grid shrink-0 gap-3 border-t border-separator pt-3">
        <button
          className="flex h-11 items-center gap-3 rounded-xl px-[17px] text-sm font-semibold text-muted transition-colors hover:bg-default hover:text-foreground"
          title="Log out"
          type="button"
          onClick={() => void logoutAction()}
        >
          <ArrowRightFromSquare className="size-[18px] shrink-0" />
          <span className="rail-label whitespace-nowrap">Log out</span>
        </button>
        <p className="rail-label px-[17px] pb-1 text-[11px] whitespace-nowrap text-muted tabular-nums">
          {versionLabel}
        </p>
      </div>
    </>
  );
}

export function AppShell({
  session,
  notifications,
  exhibitions,
  exhibition,
  children,
}: {
  session: Session;
  notifications: AppNotification[];
  exhibitions: Exhibition[];
  exhibition: Exhibition | null;
  children: React.ReactNode;
}) {
  const [open, setOpen] = useState(false);
  const [expanded, setExpanded] = useState(false);
  const isExhibitor = session.user.role === "exhibitor";
  /* Hiding a link is a courtesy, not the control: the API refuses what the role may not do. */
  const items = navItems.filter((i) =>
    i.exhibitorOnly ? isExhibitor : !i.needs || i.needs.some((p) => session.permissions.includes(p)),
  );

  return (
    <div className="safe-shell mx-auto flex min-h-screen w-full max-w-[1600px] gap-4 p-3 sm:p-4">
      <NavProgress />
      <IdleGuard minutes={session.idle_minutes ?? 30} />
      <div
        aria-hidden
        className={clsx(
          "pointer-events-none fixed inset-0 z-[35] hidden bg-canvas/40 backdrop-blur-[3px] lg:block",
          "transition-opacity duration-200 ease-out motion-reduce:transition-none",
          expanded ? "opacity-100" : "opacity-0",
        )}
      />
      <aside className="sticky top-4 z-40 hidden h-[calc(100vh-2rem)] w-[76px] shrink-0 lg:block">
        <div
          className={clsx(
            "absolute inset-y-0 left-0 flex transition-[width] duration-200 ease-out",
            expanded ? "w-[264px]" : "w-[76px]",
          )}
          onBlur={(e) => {
            if (!e.currentTarget.contains(e.relatedTarget as Node | null)) setExpanded(false);
          }}
          onFocus={() => setExpanded(true)}
          onPointerEnter={() => setExpanded(true)}
          onPointerLeave={() => setExpanded(false)}
        >
          <Aura className="w-full" isActive={expanded} radius="22px" size="md" variant="gold">
            <div
              className={clsx(
                "flex h-full flex-col overflow-hidden rounded-[22px] border border-separator bg-surface p-3",
                "[&_.rail-label]:transition-opacity [&_.rail-label]:duration-150",
                expanded
                  ? "shadow-[0_24px_60px_-20px_rgb(31_29_58/0.35)] [&_.rail-label]:opacity-100"
                  : "soft-shadow [&_.rail-label]:opacity-0",
              )}
            >
              <div className="pb-4">
                <Brand />
              </div>
              <SidebarBody items={items} />
            </div>
          </Aura>
        </div>
      </aside>

      <div className="flex min-w-0 flex-1 flex-col gap-4">
        <div className="pointer-events-none fixed inset-x-0 top-0 z-30">
          <div className="safe-shell mx-auto max-w-[1600px] px-3 pt-3 sm:px-4 sm:pt-4 lg:ps-[calc(1rem+76px+1rem)]">
            <header className="pointer-events-auto flex items-center gap-3 rounded-full border border-separator/60 bg-surface/90 py-2 ps-5 pe-2 shadow-[0_12px_40px_-12px_rgb(31_29_58/0.25)] backdrop-blur-md">
              <div className="lg:hidden">
                <Drawer isOpen={open} onOpenChange={setOpen}>
                  <Button isIconOnly aria-label="Open menu" size="sm" variant="ghost">
                    <Bars />
                  </Button>
                  <Drawer.Backdrop>
                    <Drawer.Content className="safe-drawer w-[280px] p-4" placement="left">
                      <Drawer.Dialog className="flex h-full flex-col">
                        <div className="pb-5">
                          <Brand />
                        </div>
                        <SidebarBody items={items} onNavigate={() => setOpen(false)} />
                      </Drawer.Dialog>
                    </Drawer.Content>
                  </Drawer.Backdrop>
                </Drawer>
              </div>
              <SearchBox />
              <div className="flex items-center gap-1">
                <ExhibitionSwitcher current={exhibition} exhibitions={exhibitions} />
                <NotificationBell notifications={notifications} />
                <ThemeSwitch />
                <UserMenu user={session.user} />
              </div>
            </header>
          </div>
        </div>
        <div aria-hidden className="h-[52px] shrink-0" />
        <main className="safe-bottom min-w-0 flex-1">{children}</main>
      </div>
    </div>
  );
}
