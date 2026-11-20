const { heroui } = require("@heroui/theme");

/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./app/**/*.{js,jsx,ts,tsx}",
    "./node_modules/@heroui/theme/dist/**/*.{js,ts,jsx,tsx}",
  ],
  theme: {
    extend: {
      colors: {
        ditf: { green: "#0b6e4f", green2: "#10985f", gold: "#f4b400" },
      },
      fontFamily: {
        sans: ["Montserrat", "Verdana", "sans-serif"],
      },
    },
  },
  darkMode: "class",
  plugins: [
    require("daisyui"),
    heroui(),
  ],
  daisyui: {
    themes: ["light"],
    base: false, // let HeroUI/Tailwind own base styles; use daisyUI components only
    logs: false,
  },
};
