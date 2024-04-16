import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/auth.css',
                'resources/js/auth.js',
            ],
            refresh: true,
        }),
    ],
    build: {
        rollupOptions: {
            minify: false,
            input: {
                // Include both CSS and JS files as separate entry points
                'scripts': 'resources/js/auth.js',
                'styles': 'resources/css/auth.css',
            },
            output: {
                entryFileNames: `assets/[name].js`,
                assetFileNames: ({ name }) => {
                    if (name.endsWith('.css')) return 'assets/[name].css';
                    return 'assets/[name].[ext]';
                },
                // We can omit chunkFileNames if we are not expecting any chunks, but it's good to specify just in case
                chunkFileNames: 'assets/[name].js',
            }
        },
        // Optionally disable code splitting if it's not desired
        manualChunks: () => 'all-in-one',
    }
});
