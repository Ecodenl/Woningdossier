import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/frontend/app.css',
                'resources/css/admin/app.css',
                'resources/sass/pdf.scss',
                'resources/js/app.js',
                'resources/js/datatables.js',
            ],
            refresh: true,
        }),
    ],
});
