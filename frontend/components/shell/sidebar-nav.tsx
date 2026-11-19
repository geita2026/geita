"use client";

import Link from "next/link";
import { usePathname } from "next/navigation";
import clsx from "clsx";

import type { NavItem } from "@/config/site";

import { NavIcon } from "@/components/shell/nav-icons";
import { AnimatedBackground } from "@/components/core/animated-background";
import { startNav, useNavPending } from "@/components/shell/nav-progress";

export function SidebarNav({ items, onNavigate }: { items: NavItem[]; onNavigate?: () => void }) {
  const pathname = usePathname();
  const pending = useNavPending();

  /* The longest matching href wins, so a nested route lights its own section
     rather than every ancestor of it. */
  const current = items
    .filter((i) => pathname === i.href || pathname.startsWith(`${i.href}/`))
    .sort((a, b) => b.href.length - a.href.length)[0]?.href;
  /* The pill moves to the clicked item at once, before the server answers:
     the click registered, and this is where you are going. */
  const shown = pending && items.some((i) => i.href === pending) ? pending : current;

  return (
    /* Two highlights, because they say two different things: the brand pill is
       the page you are on, and the grey one slides to whatever you are
       pointing at. The brand fill paints above the grey (.nav-item::after),
       so the grey can rest under the active item rather than being switched
       off — switching it off is a pop, right on the item just clicked. */
    <AnimatedBackground
      enableHover
      className="rounded-xl bg-default"
      containerClassName="flex flex-col gap-3"
      contentClassName="flex items-center gap-3"
      /* Position only, and no bounce: the rail animates its own width on the
         same gesture that moves this pill, and a pill that also tweens its
         width judders the whole 200ms the rail is opening. */
      layoutMode="position"
      transition={{ type: "spring", bounce: 0, duration: 0.18 }}
    >
      {items.map((item) => {
        const active = item.href === shown;
        const loading = item.href === pending;

        return (
          <Link
            key={item.href}
            aria-busy={loading || undefined}
            aria-current={item.href === current ? "page" : undefined}
            className={clsx(
              "nav-item flex h-11 items-center rounded-xl px-[17px] text-[15px] font-semibold",
              active ? "text-brand-foreground" : "text-foreground/80 hover:text-foreground",
            )}
            /* All visual state is CSS on these attributes (.nav-item in
               globals.css), so every change between them is a transition. */
            data-active={active || undefined}
            data-id={item.href}
            href={item.href}
            title={item.label}
            onClick={(e) => {
              startNav(e, item.href, current);
              onNavigate?.();
            }}
          >
            <NavIcon className="size-[18px] shrink-0" icon={item.icon} />
            <span className="rail-label whitespace-nowrap">{item.label}</span>
          </Link>
        );
      })}
    </AnimatedBackground>
  );
}
