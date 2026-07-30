import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    /*server: {
        host: '0.0.0.0', // Permite acceso desde cualquier dispositivo
        port: 5173,
        strictPort: true,
        hmr: {
            host: '192.168.100.4',
            port: 5173,
            protocol: 'ws',
        },
    },*/
});
