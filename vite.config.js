import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/css/filament/admin/themes/light.css',
                'resources/css/filament/admin/themes/dark.css',
            ],
            refresh: true,
        }),
    ],
});
