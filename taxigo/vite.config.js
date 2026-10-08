import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { viteStaticCopy } from 'vite-plugin-static-copy'
import { VitePWA } from 'vite-plugin-pwa';
import fs from 'node:fs';
import path from 'node:path';

// The vendored Laravel framework in this repo predates Vite 5's move of the
// manifest to `<outDir>/.vite/manifest.json` — its Vite.php still looks for
// a flat `<outDir>/manifest.json`. Rather than patching vendor/ or requiring
// a composer update (composer isn't available in this environment), mirror
// the manifest to the flat path Laravel actually reads from.
function mirrorManifestForLegacyLaravel() {
    return {
        name: 'mirror-manifest-for-legacy-laravel',
        writeBundle() {
            const from = path.resolve(__dirname, 'public/build/.vite/manifest.json');
            const to = path.resolve(__dirname, 'public/build/manifest.json');
            if (fs.existsSync(from)) fs.copyFileSync(from, to);
        },
    };
}

export default defineConfig({
    build: {
        manifest: true,
        rtl: true,
        outDir: 'public/build/',
        cssCodeSplit: true,
        rollupOptions: {
            output: {
                assetFileNames: (css) => {
                    if (css.name.split('.').pop() == 'css') {
                        return 'css/' + `[name]` + '.css';
                    } else {
                        return 'icons/' + css.name;
                    }
                },
                entryFileNames: 'js/' + `[name]` + `.js`,
            },
        },
    },
    plugins: [
        laravel({
            input: [
                'resources/scss/landing.scss',
                'resources/scss/style-preset.scss',
                'resources/scss/style.scss',
                'resources/scss/uikit.scss',
                'resources/css/customer.css',
                'resources/js/customer.js',
                // Real jQuery, bundled from npm — layouts/footerjs.blade.php
                // already expects it at exactly this build/js/jquery.min.js
                // path (a pre-existing reference the file itself was missing
                // for), which is why the source lives outside resources/js/:
                // that whole folder is also raw-copied by viteStaticCopy
                // below, which would otherwise clobber this bundled output
                // with the unresolved import() source.
                'resources/vendor-entries/jquery.min.js',
            ],
            refresh: true,
        }),
        mirrorManifestForLegacyLaravel(),
        VitePWA({
            // Only the customer routes are an installable app; the admin
            // panel/API must never be touched by the service worker.
            injectRegister: null,
            manifest: {
                id: '/app/',
                name: 'Seema Cabs Goa',
                short_name: 'Seema Cabs',
                description: 'Book local taxis, airport transfers and Goa sightseeing rides.',
                start_url: '/app/',
                scope: '/app/',
                display: 'standalone',
                background_color: '#FBF7F1',
                theme_color: '#FEDC33',
                icons: [
                    { src: '/app-icons/icon-192.png', sizes: '192x192', type: 'image/png', purpose: 'any' },
                    { src: '/app-icons/icon-512.png', sizes: '512x512', type: 'image/png', purpose: 'any' },
                    { src: '/app-icons/icon-512.png', sizes: '512x512', type: 'image/png', purpose: 'maskable' },
                ],
            },
            workbox: {
                navigateFallback: null,
                globPatterns: [],
                runtimeCaching: [
                    // Dynamic JSON action endpoints must never be touched by the SW —
                    // always go straight to network. (Confirmed via testing: Workbox's
                    // NetworkFirst handler on these caused fetch() to hang indefinitely
                    // with no resolve/reject, silently breaking every actions/* call.)
                    {
                        urlPattern: ({ url }) => url.pathname.startsWith('/app/actions/'),
                        handler: 'NetworkOnly',
                    },
                    {
                        urlPattern: ({ url }) => url.pathname.startsWith('/app'),
                        handler: 'NetworkFirst',
                        options: { cacheName: 'seema-app-shell' },
                    },
                    {
                        urlPattern: ({ url }) => url.pathname.startsWith('/build/') || url.pathname.startsWith('/app-icons/'),
                        handler: 'CacheFirst',
                        options: { cacheName: 'seema-app-assets' },
                    },
                ],
            },
        }),
        viteStaticCopy({
            targets: [
                {
                    src: 'resources/plugins',
                    dest: 'css'
                },
                {
                    src: 'resources/fonts',
                    dest: ''
                },
                {
                    src: 'resources/images',
                    dest: ''
                },
                {
                    src: 'resources/js',
                    dest: ''
                },
                {
                    src: 'resources/json',
                    dest: ''
                },
            ],
        })
    ],
});
