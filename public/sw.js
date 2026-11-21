// LingoUz Progressive Web App (PWA) Service Worker
const CACHE_NAME = 'lingouz-pwa-v1';
const PRECACHE_ASSETS = [
    '/',
    '/assets/style.css',
    '/assets/app-v3.css',
    '/assets/mascot.css',
    '/assets/app.js',
    '/assets/lesson.js',
    '/assets/mascot-owl.svg',
    '/assets/icon-192.png',
    '/assets/icon-512.png',
    '/favicon.svg',
    '/manifest.webmanifest'
];

// Install: precache essential static assets
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            return cache.addAll(PRECACHE_ASSETS).catch((err) => {
                console.warn('[LingoUz SW] Precache warning:', err);
            });
        }).then(() => self.skipWaiting())
    );
});

// Activate: clean up older caches
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) => {
            return Promise.all(
                keys.map((key) => {
                    if (key !== CACHE_NAME) {
                        return caches.delete(key);
                    }
                })
            );
        }).then(() => self.clients.claim())
    );
});

// Fetch: strategy based on request type
self.addEventListener('fetch', (event) => {
    const req = event.request;
    if (req.method !== 'GET') {
        return;
    }

    const url = new URL(req.url);

    // Static assets: cache-first with network fallback
    if (url.origin === location.origin && (url.pathname.startsWith('/assets/') || url.pathname.startsWith('/favicon'))) {
        event.respondWith(
            caches.match(req).then((cached) => {
                if (cached) {
                    // Update cache in background
                    fetch(req).then((res) => {
                        if (res && res.status === 200) {
                            caches.open(CACHE_NAME).then((cache) => cache.put(req, res));
                        }
                    }).catch(() => {});
                    return cached;
                }
                return fetch(req).then((res) => {
                    if (res && res.status === 200) {
                        const copy = res.clone();
                        caches.open(CACHE_NAME).then((cache) => cache.put(req, copy));
                    }
                    return res;
                });
            })
        );
        return;
    }

    // HTML navigations: network-first, fallback to cache
    if (req.mode === 'navigate') {
        event.respondWith(
            fetch(req).catch(() => {
                return caches.match(req).then((cached) => {
                    return cached || caches.match('/');
                });
            })
        );
        return;
    }

    // Default: network with cache fallback
    event.respondWith(
        fetch(req).catch(() => caches.match(req))
    );
});
