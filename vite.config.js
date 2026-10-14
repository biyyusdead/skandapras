import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css',
                    'resources/css/home.css',
                    'resources/css/public.css',       // ← TAMBAH
                    'resources/js/app.js',
                    'resources/js/home.js',
                    'resources/js/public.js',
                    'resources/css/peta-custom.css',
                    'resources/js/vendor-3d.js',
                    'resources/js/peta-handler.js',
                    'resources/js/route-planner.js',  
                ],
            refresh: true,
        }),
    ],
    server: {
        host: '0.0.0.0',           // ← listen di semua interface
        port: 5173,
        strictPort: true,
        hmr: {
            host: 'localhost',      // ← akan di-override oleh tunnel
        },
    },
    build: {
        chunkSizeWarningLimit: 2000,
    },
});
