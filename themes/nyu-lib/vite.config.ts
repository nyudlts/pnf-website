// vite.config.js
import {defineConfig} from 'vite';
import {resolve} from 'node:path';

export default defineConfig({
    build: {
        rollupOptions: {
            input: {
                main: resolve(__dirname, 'js/main.js')
            },
            output: {
                entryFileNames: 'js/[name].js',
                assetFileNames: 'assets/[name][extname]',
            }
        },
        minify: true,
        outDir: 'build',
        assetsInlineLimit: 0,
    },
    server: {
        watch: {
            ignored: ['node_modules/**/*', 'build/**/*', 'css/**/*']
        }
    },
});