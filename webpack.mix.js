const mix = require('laravel-mix')
require('laravel-mix-jigsaw')

mix.disableSuccessNotifications()
mix.setPublicPath('source/assets/build')

mix.jigsaw()
    .postCss('source/_assets/css/app.css', 'css/app.css', [
      require('postcss-import'),
      require('tailwindcss/nesting'),
      require('tailwindcss'),
    ])
    .options({
        processCssUrls: false,
        // Remove block comments, such as those from TailwindCSS.
        // Laravel Mix currently doesn't auto-strip those.
        cssNano: {
            discardComments: {
                removeAll: true
            }
        }
    })
    .browserSync({
      server: 'build_local',
      files: ['build_local/**'],
    })
    .version()
