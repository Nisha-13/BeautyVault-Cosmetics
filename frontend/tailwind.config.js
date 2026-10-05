/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./index.html",
    "./src/**/*.{vue,js,ts,jsx,tsx}",
  ],
  darkMode: 'class',
  theme: {
    extend: {
      colors: {
        brand: {
          50:  '#fdf2f4',
          100: '#fce7eb',
          200: '#f9d2d9',
          300: '#f4abb9',
          400: '#ec7891',
          500: '#df486d',
          600: '#ca2c55',
          700: '#ab1f44',
          800: '#8e1d3b',
          900: '#771d36',
          950: '#420a1a',
        },
        rosewood: {
          DEFAULT: '#904E55',
          dark:    '#562C30',
          light:   '#BA7A81',
        },
        champagne: {
          DEFAULT: '#F7E7CE',
          light:   '#FBF4EB',
          dark:    '#E0C59F',
        },
        slatebg: {
          50:  '#f8fafc',
          900: '#0f172a',
          950: '#020617',
        },
      },
      fontFamily: {
        serif: ['Playfair Display', 'Georgia', 'serif'],
        sans:  ['Plus Jakarta Sans', 'Inter', 'system-ui', 'sans-serif'],
      },
      boxShadow: {
        glow: '0 0 25px -5px rgba(223, 72, 109, 0.35)',
        soft: '0 10px 30px -10px rgba(0, 0, 0, 0.06)',
      },
      animation: {
        'spin-slow': 'spin 5s linear infinite',
        'marquee':   'marquee 28s linear infinite',
      },
    },
  },
  plugins: [],
}

