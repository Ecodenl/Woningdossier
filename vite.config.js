import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/frontend/app.css',
                'resources/sass/admin/app.scss',
                'resources/sass/admin/datatables/datatables.scss',
                'resources/sass/pdf.scss',
                'resources/js/app.js',
                'resources/js/hoomdossier.js',
            ],
            refresh: true,
        }),
    ],
});
