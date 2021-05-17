const colors = require('tailwindcss/colors')

module.exports = {
  purge: [
    'source/**/*.blade.php',
    'source/**/*.md',
    'source/**/*.html',
  ],
  theme: {
    extend: {
      colors: {
        cyan: colors.cyan,
        'light-blue': colors.lightBlue,
        lime: colors.lime,
        rose: colors.rose,
        emerald: colors.emerald,
      }
    },
  },
  variants: {
    extend: {},
  },
  plugins: [],
};
