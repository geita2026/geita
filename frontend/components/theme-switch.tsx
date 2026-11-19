"use client";

import { useRef, useSyncExternalStore } from "react";
import { useTheme } from "next-themes";
import { Button } from "@heroui/react";
import { Moon, Sun } from "@gravity-ui/icons";

import { nextFrame, playWave } from "@/lib/wave-effect";

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
  const ref = useRef<HTMLButtonElement>(null);

  if (!mounted) return <div aria-hidden className="size-8" />;
  const isLight = resolvedTheme === "light";
  const next = isLight ? "dark" : "light";

  return (
    <Button
      ref={ref}
      isIconOnly
      aria-label={`Switch to ${next} mode`}
      size="sm"
      variant="ghost"
      onPress={() =>
        // The page ripples from this button into the other theme (Settings → Distortion wave).
        void playWave(
          document.body,
          async () => {
            setTheme(next);
            // next-themes applies the class after React commits; wait until it shows.
            for (let i = 0; i < 10 && !document.documentElement.classList.contains(next); i++) await nextFrame();
          },
          { source: ref.current },
        )
      }
    >
      {isLight ? <Moon /> : <Sun />}
    </Button>
  );
}
