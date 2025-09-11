import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    server: {
        hmr: {
            host: 'localhost',
        },
        host: '0.0.0.0',
        port: 5173,
        cors: true,
    },
    build: {
        rollupOptions: {
            external: [],
            output: {
                assetFileNames: (assetInfo) => {
                    if (assetInfo.name.includes('owl.carousel')) {
                        return 'css/[name][extname]';
                    }
                    return 'assets/[name]-[hash][extname]';
                }
            }
        }
    }
});
