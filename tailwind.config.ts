import type { Config } from "tailwindcss";

const config: Config = {
  darkMode: ["class"],
  content: [
    "./app/**/*.{ts,tsx}",
    "./components/**/*.{ts,tsx}",
    "./lib/**/*.{ts,tsx}"
  ],
  theme: {
    extend: {
      colors: {
        brand: {
          DEFAULT: "#15803d",
          light: "#22c55e",
          dark: "#166534"
        }
      },
      borderRadius: {
        lg: "12px",
        xl: "14px"
      }
    }
  },
  plugins: [require("@tailwindcss/typography")]
};

export default config;

