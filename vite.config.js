import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/frontend/app.css',
                'resources/css/admin/app.css',
                // 'resources/sass/admin/app.scss',
                'resources/sass/pdf.scss',
                'resources/js/app.js',
                'resources/js/hoomdossier.js',
                'resources/js/datatables.js',
                'resources/js/select2.js',
            ],
            refresh: true,
        }),
    ],
});
