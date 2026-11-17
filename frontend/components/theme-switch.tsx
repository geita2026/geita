"use client";

import { useSyncExternalStore } from "react";
import { useTheme } from "next-themes";
import { Button } from "@heroui/react";
import { Moon, Sun } from "@gravity-ui/icons";

// next-themes only knows the resolved theme on the client; render a placeholder during SSR.
const subscribe = () => () => {};
const useMounted = () =>
  useSyncExternalStore(
    subscribe,
    () => true,
    () => false,
  );

export function ThemeSwitch() {
  const mounted = useMounted();
  const { setTheme, resolvedTheme } = useTheme();

  if (!mounted) return <div aria-hidden className="size-8" />;
  const isLight = resolvedTheme === "light";

  return (
    <Button
      isIconOnly
      aria-label={`Switch to ${isLight ? "dark" : "light"} mode`}
      size="sm"
      variant="ghost"
      onPress={() => setTheme(isLight ? "dark" : "light")}
    >
      {isLight ? <Moon /> : <Sun />}
    </Button>
  );
}
