/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./**/*.php",
    "./includes/**/*.php",
    "./assets/**/*.js"
  ],
  theme: {
    extend: {
      colors: {
        brand: {
          DEFAULT: '#15803d',
          light: '#22c55e',
          dark: '#166534'
        }
      },
      borderRadius: {
        lg: '12px',
        xl: '14px'
      }
    }
  },
  corePlugins: {
    container: false // Disable Tailwind's default container plugin
  },
  plugins: []
}

