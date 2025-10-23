import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/frontend/app.css',
                'resources/css/admin/app.css',
                'resources/css/pdf/pdf.css',
                'resources/js/app.js',
                'resources/js/datatables.js',
            ],
            refresh: true,
        }),
    ],
    server: {
        host: true,
        port: 5173,
        strictPort: true,
        cors: true, // <-- laat alle origins toe (alleen dev)
        hmr: { host: 'localhost', protocol: 'ws', port: 5173 },
        origin: 'http://localhost:5173',
        proxy: {
            '/icons': 'http://localhost:80',
        }
    },
});
