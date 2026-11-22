// LingoUz Progressive Web App (PWA) Service Worker - v5
const CACHE_NAME = 'lingouz-pwa-v5';
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

// Activate: clean up older caches and claim clients
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

    // Only intercept same-origin requests
    if (url.origin !== location.origin) {
        return;
    }

    // Static assets: cache-first with network fallback
    if (url.pathname.startsWith('/assets/') || url.pathname.startsWith('/favicon') || url.pathname === '/manifest.webmanifest') {
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
                }).catch(() => {
                    return caches.match(req);
                });
            })
        );
        return;
    }

    // HTML Navigations: network-first with safe offline fallback
    if (req.mode === 'navigate') {
        event.respondWith(
            fetch(req).catch(async () => {
                const cached = await caches.match(req);
                if (cached) return cached;
                const root = await caches.match('/');
                if (root) return root;
                return new Response(
                    '<!DOCTYPE html><html lang="uz"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"><title>Oflayn - LingoUz</title><style>body{font-family:sans-serif;text-align:center;padding:48px 16px;background:#f8fafc;color:#1e293b;}.box{max-width:400px;margin:0 auto;background:#fff;padding:32px;border-radius:16px;box-shadow:0 4px 12px rgba(0,0,0,.08);}h1{color:#0284c7;font-size:20px;}p{color:#64748b;font-size:14px;line-height:1.5;}.btn{display:inline-block;background:#0284c7;color:#fff;padding:10px 20px;border-radius:10px;text-decoration:none;border:none;cursor:pointer;font-weight:bold;margin-top:12px;}</style></head><body><div class="box"><div style="font-size:40px;">📡</div><h1>Internetga ulanish yo‘q</h1><p>Internet tarmog‘ini tekshiring va sahifani qayta yuklang.</p><button class="btn" onclick="location.reload()">Qayta yuklash</button></div></body></html>',
                    {
                        status: 503,
                        statusText: 'Service Unavailable',
                        headers: { 'Content-Type': 'text/html; charset=utf-8' }
                    }
                );
            })
        );
        return;
    }

    // For any other GET request (API, dynamic endpoints), let browser handle natively
});
