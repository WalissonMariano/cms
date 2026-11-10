import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/css/layouts/menu.css', 'resources/css/components/home.css', 'resources/css/components/dashboard.css', 'resources/css/components/index-page.css', 'resources/css/components/account.css', 'resources/css/components/form-page.css', 'resources/js/app.js', 'resources/js/editors/post.js'],
            refresh: true,
        }),
    ],
});
