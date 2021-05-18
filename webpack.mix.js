const mix = require('laravel-mix')
require('laravel-mix-jigsaw')

mix.disableSuccessNotifications()
mix.setPublicPath('source/assets/build')

mix
    .jigsaw()
    .postCss('source/_assets/css/app.css', 'css', [
        require('postcss-import'),
        require('tailwindcss'),
        require('postcss-nesting')
    ])
    .options({
        // Remove block comments, such as those from TailwindCSS.
        // Laravel Mix currently doesn't auto-strip those.
        cssNano: {
            discardComments: {
                removeAll: true
            }
        }
    })
    .version()
