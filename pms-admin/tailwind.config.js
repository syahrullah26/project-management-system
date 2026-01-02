/** @type {import('tailwindcss').Config} */
export default {
  content: [
    './index.html',
    './src/**/*.{vue,js,ts,jsx,tsx}',
  ],
  theme: {
    extend: {
      colors: {
        gold: '#aa8320',
        goldDark: '#7b5902',
        goldHover: '#aa8320ff',
        goldHoverend: '#7b5902ff'
      },
    },
  },
  plugins: [],
}
