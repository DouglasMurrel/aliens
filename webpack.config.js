const Encore = require('@symfony/webpack-encore');

Encore
   // directory where compiled assets will be stored
    .setOutputPath('public/vue_build/')
    // public path used by the web server to access the output path
    .setPublicPath('/vue_build')

    .addEntry('app', './vue_app/main.js')
    .enableVueLoader()
;