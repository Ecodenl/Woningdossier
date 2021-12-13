const mix = require('laravel-mix');

const tailwindcss = require("tailwindcss");

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

mix.js('resources/js/app.js', 'public/js')
    .scripts(
        [
            'resources/js/ays-beforeunload-shim.js',
            'resources/js/jquery.are-you-sure.js',

        ], 'public/js/are-you-sure.js')
    .scripts(
        [
            'resources/js/datatables/jquery.dataTables.js',
            'resources/js/datatables/datatables.js',
            'resources/js/datatables/dataTables.responsive.js',
            'resources/js/datatables/responsive.bootstrap.js',

        ], 'public/js/datatables.js'
    )
    .copy('resources/js/tinymce/', 'public/js/tinymce', false)
    .scripts(
        [
            'resources/js/select2.js'
        ], 'public/js/select2.js'
    )
    .scripts(
        [
            'resources/js/disable-auto-fill.js'
        ], 'public/js/disable-auto-fill.js'
    )
    .scripts(
        [
            'resources/js/hoomdossier.js'
        ], 'public/js/hoomdossier.js'
    )
    .copy('resources/images', 'public/images')
    .copy('resources/icons', 'public/icons')
    .sass('resources/sass/admin/app.scss', 'public/css/admin')
    .sass('resources/sass/pdf.scss', 'public/css')
    .sass('resources/sass/admin/datatables/_responsive_bootstrap.scss', 'public/css/admin/datatables/responsive.bootstrap.min.css')
    .sass('resources/sass/admin/datatables/_responsive_datatables.scss', 'public/css/admin/datatables/responsive.dataTables.min.css')
    .sass('resources/sass/admin/datatables/_dataTables_bootstrap.scss', 'public/css/admin/datatables/dataTables.bootstrap.min.css')
    .postCss("resources/css/frontend/app.css", "public/css/frontend", [
        tailwindcss,
    ])
    .options({
        // Whether to re-write added URLs based on the new css file location
        processCssUrls: false,
    })
    .version();