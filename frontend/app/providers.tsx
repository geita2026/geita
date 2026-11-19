"use client";

import type { ThemeProviderProps } from "next-themes";

import * as React from "react";
import { ThemeProvider as NextThemesProvider } from "next-themes";
import { Toast } from "@heroui/react";
import { MotionConfig } from "motion/react";

export interface ProvidersProps {
  children: React.ReactNode;
  themeProps?: ThemeProviderProps;
  /** The portal's own "Reduce motion" setting; otherwise the computer's setting decides. */
  reduceMotion?: boolean;
}

export function Providers({ children, themeProps, reduceMotion }: ProvidersProps) {
  return (
    <MotionConfig reducedMotion={reduceMotion ? "always" : "user"}>
      <NextThemesProvider {...themeProps}>
        {children}
        <Toast.Provider placement="bottom" />
      </NextThemesProvider>
    </MotionConfig>
  );
}
