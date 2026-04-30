import { defineConfig, loadEnv } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig(({ mode }) => {
    const env = loadEnv(mode, process.cwd(), '');
    const port = parseInt(env.VITE_PORT || '5173');
    const hmrHost = env.VITE_HMR_HOST || 'localhost';

    return {
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
            port: port,
            strictPort: true,
            cors: true,
            hmr: { host: hmrHost, protocol: 'ws', port: port },
            origin: `http://localhost:${port}`,
            proxy: {
                '/icons': 'http://localhost:80',
            }
        },
    };
});
