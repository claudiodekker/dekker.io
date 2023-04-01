const colors = require('tailwindcss/colors')

module.exports = {
  content: require('fast-glob').sync([
    'source/**/*.html',
    'source/**/*.md',
    'source/**/*.js',
    'source/**/*.php',
    'source/**/*.vue',
  ]),
  theme: {
    extend: {
      colors: {
        cyan: colors.cyan,
        'slate': colors.slate,
        rose: colors.rose
      }
    },
  },
  variants: {
    extend: {
      borderWidth: ['last'],
    },
  },
  plugins: [],
};
