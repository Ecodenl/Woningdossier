let mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('resources/assets/js/app.js', 'public/js')
    .scripts(
        [
            'resources/assets/js/ays-beforeunload-shim.js',
            'resources/assets/js/jquery.are-you-sure.js',

        ], 'public/js/are-you-sure.js')
    .scripts(
        [
            'resources/assets/js/datatables.js'
        ], 'public/js/datatables.js'
    )
    .scripts(
        [
            'resources/assets/js/select2.js'
        ], 'public/js/select2.js'
    )
    .copy('resources/assets/images', 'public/images')
    .sass('resources/assets/sass/app.scss', 'public/css');
