import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/frontend/app.css',
                'resources/css/frontend/tinymce.css',
                'resources/sass/admin/app.scss',
                'resources/sass/admin/datatables/_dataTables_bootstrap.scss',
                'resources/sass/admin/datatables/_responsive_bootstrap.scss',
                'resources/sass/admin/datatables/_responsive_datatables.scss',
                'resources/sass/pdf.scss',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
    ],
});
