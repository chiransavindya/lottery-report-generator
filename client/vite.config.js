import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react';
import path from 'path';

export default defineConfig(() => {
    const hmrHost = process.env.VITE_HMR_HOST || 'localhost';
    const hmrPort = Number(process.env.VITE_HMR_PORT || 5173);

    return {
        root: '.',
        server: {
            host: true,
            port: hmrPort,
            strictPort: true,
            hmr: {
                host: hmrHost,
                port: hmrPort,
            },
            watch: {
                usePolling: true,
            },
        },
        resolve: {
            alias: {
                '@': path.resolve(__dirname, './src'),
            },
        },
        plugins: [
            laravel({
                input: 'src/app.jsx',
                publicDirectory: '../server/public',
                refresh: ['../server/resources/views/**'],
            }),
            react(),
        ],
    };
});
