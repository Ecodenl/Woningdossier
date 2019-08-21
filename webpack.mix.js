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
            'resources/assets/js/datatables/jquery.dataTables.js',
            'resources/assets/js/datatables/datatables.js',
            'resources/assets/js/datatables/dataTables.responsive.js',
            'resources/assets/js/datatables/responsive.bootstrap.js',

        ], 'public/js/datatables.js'
    )
    .copy('resources/assets/js/tinymce/', 'public/js/tinymce', false)
    .scripts(
        [
            'resources/assets/js/select2.js'
        ], 'public/js/select2.js'
    )
    .scripts(
        [
            'resources/assets/js/disable-auto-fill.js'
        ], 'public/js/disable-auto-fill.js'
    )
    .scripts(
        [
            'resources/assets/js/hoomdossier.js'
        ], 'public/js/hoomdossier.js'
    )
    .copy('resources/assets/images', 'public/images')
    .sass('resources/assets/sass/app.scss', 'public/css')
    .sass('resources/assets/sass/datatables/_responsive_bootstrap.scss', 'public/css/datatables/responsive.bootstrap.min.css')
    .sass('resources/assets/sass/datatables/_responsive_datatables.scss', 'public/css/datatables/responsive.dataTables.min.css')
    .sass('resources/assets/sass/datatables/_dataTables_bootstrap.scss', 'public/css/datatables/dataTables.bootstrap.min.css');


