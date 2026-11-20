import type { Config } from "tailwindcss";
import { heroui } from "@heroui/react";

const config: Config = {
  content: [
    "./app/**/*.{js,ts,jsx,tsx,mdx}",
    "./components/**/*.{js,ts,jsx,tsx,mdx}",
    // npm workspaces hoist deps to the repo root, and npm nests @heroui/theme
    // inside other @heroui packages. These globs catch it in every location.
    "../../node_modules/@heroui/theme/dist/**/*.{js,ts,jsx,tsx}",
    "../../node_modules/@heroui/*/node_modules/@heroui/theme/dist/**/*.{js,ts,jsx,tsx}",
  ],
  theme: {
    extend: {},
  },
  // daisyUI + HeroUI both register plugins; HeroUI handles its own components,
  // daisyUI provides layout utility classes (navbar, card, stat, steps, badge…).
  plugins: [
    require("daisyui"),
    heroui({
      themes: {
        dark: {
          colors: {
            primary: { DEFAULT: "#FFBF00", foreground: "#0b0c10" },
            secondary: { DEFAULT: "#2a2113", foreground: "#FFBF00" },
            focus: "#FFBF00",
          },
        },
      },
    }),
  ],
  daisyui: {
    themes: [
      {
        // Dark amber gold theme — matches the DITF timetable (#FFBF00).
        tantrade: {
          "color-scheme": "dark",
          primary: "#FFBF00",
          "primary-content": "#0b0c10",
          secondary: "#2a2113",
          "secondary-content": "#FFBF00",
          accent: "#FFBF00",
          "accent-content": "#0b0c10",
          neutral: "#16131a",
          "neutral-content": "#ECECEC",
          "base-100": "#0f0d10",
          "base-200": "#16131a",
          "base-300": "#211c24",
          "base-content": "#ECECEC",
          info: "#38bdf8",
          success: "#22c55e",
          warning: "#FFBF00",
          error: "#ef4444",
        },
      },
      "light",
      "dark",
    ],
  },
};

export default config;
