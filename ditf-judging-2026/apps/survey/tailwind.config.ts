import type { Config } from "tailwindcss";

const config: Config = {
  content: ["./app/**/*.{js,ts,jsx,tsx,mdx}", "./components/**/*.{js,ts,jsx,tsx,mdx}"],
  theme: { extend: {} },
  plugins: [require("daisyui")],
  daisyui: {
    themes: [
      {
        // Dark amber gold theme — matches the DITF judging app (#FFBF00).
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
    ],
  },
};

export default config;
