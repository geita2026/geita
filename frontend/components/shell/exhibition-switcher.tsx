"use client";

import { startTransition } from "react";
import { Button, Dropdown, Label } from "@heroui/react";
import { ChevronDown, Gem } from "@gravity-ui/icons";

import type { Exhibition } from "@/lib/types";

import { setExhibitionAction } from "@/server/actions";
import { EXHIBITION_STATUS } from "@/lib/domain/labels";

/** Which exhibition the map, lists and reports are showing. Saved in a cookie. */
export function ExhibitionSwitcher({
  exhibitions,
  current,
}: {
  exhibitions: Exhibition[];
  current: Exhibition | null;
}) {
  if (!current) return null;

  return (
    <Dropdown>
      <Button
        aria-label="Switch exhibition"
        className="h-10 max-w-[220px] gap-2 rounded-full bg-default/70 px-3"
        size="sm"
        variant="ghost"
      >
        <Gem className="size-4 shrink-0 text-brand-ink" />
        <span className="hidden truncate font-mono text-xs font-bold sm:block">{current.code}</span>
        <ChevronDown className="size-3 shrink-0 text-muted" />
      </Button>
      <Dropdown.Popover className="w-80" placement="bottom end">
        <Dropdown.Menu
          aria-label="Exhibitions"
          selectedKeys={[String(current.id)]}
          selectionMode="single"
          onAction={(key) => startTransition(() => void setExhibitionAction(Number(key)))}
        >
          {exhibitions.map((e) => (
            <Dropdown.Item key={e.id} id={String(e.id)} textValue={e.name}>
              <div className="min-w-0">
                <Label className="block truncate">{e.name}</Label>
                <span className="text-xs text-muted">
                  {e.code} · {EXHIBITION_STATUS[e.status]?.label ?? e.status}
                </span>
              </div>
              <Dropdown.ItemIndicator />
            </Dropdown.Item>
          ))}
        </Dropdown.Menu>
      </Dropdown.Popover>
    </Dropdown>
  );
}
