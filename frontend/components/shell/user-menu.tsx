"use client";

import { useRouter } from "next/navigation";
import { Avatar, Button, Dropdown, Label } from "@heroui/react";
import { ArrowRightFromSquare, ChevronDown, Gear } from "@gravity-ui/icons";

import type { User } from "@/lib/types";

import { siteConfig } from "@/config/site";
import { logoutAction } from "@/server/actions";

export function UserMenu({ user }: { user: User }) {
  const router = useRouter();
  const initials = user.name
    .split(" ")
    .map((s) => s[0])
    .slice(0, 2)
    .join("")
    .toUpperCase();

  return (
    <Dropdown>
      <Button
        aria-label="Account menu"
        className="h-10 gap-2.5 rounded-full ps-1 pe-2.5 data-[hovered]:bg-default"
        size="sm"
        variant="ghost"
      >
        <Avatar className="size-8" size="sm">
          <Avatar.Fallback className="bg-brand-soft font-bold text-brand-ink">{initials}</Avatar.Fallback>
        </Avatar>
        <span className="hidden text-sm font-semibold sm:block">{user.name}</span>
        <ChevronDown className="size-3 text-muted" />
      </Button>
      <Dropdown.Popover className="w-72 p-0" placement="bottom end">
        <div className="flex items-center gap-3 border-b border-separator px-4 py-3.5">
          <Avatar className="size-11" size="md">
            <Avatar.Fallback className="bg-brand font-display text-sm font-extrabold text-brand-foreground">
              {initials}
            </Avatar.Fallback>
          </Avatar>
          <div className="min-w-0 leading-tight">
            <p className="truncate font-semibold">{user.name}</p>
            <p className="truncate text-sm text-muted">{user.email}</p>
            <p className="mt-1 truncate text-xs font-semibold text-muted">
              {user.role_label} · {user.exhibitor?.organization_name ?? siteConfig.organisation}
            </p>
          </div>
        </div>
        <Dropdown.Menu
          className="p-1.5"
          onAction={(key) => {
            if (key === "logout") void logoutAction();
            if (key === "settings") router.push("/settings");
          }}
        >
          <Dropdown.Item id="settings" textValue="Settings">
            <Gear className="size-4" />
            <Label>Settings</Label>
          </Dropdown.Item>
          <Dropdown.Item id="logout" textValue="Sign out" variant="danger">
            <ArrowRightFromSquare className="size-4" />
            <Label>Sign out</Label>
          </Dropdown.Item>
        </Dropdown.Menu>
      </Dropdown.Popover>
    </Dropdown>
  );
}
