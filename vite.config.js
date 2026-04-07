import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/events/form.js',
                'resources/js/events/filter.js',
            ],
            refresh: true,
        }),
    ],
});
