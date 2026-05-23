const CACHE_NAME = 'studhub-v1';
const CACHE_MAX_ENTRIES = 100;

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            return cache.addAll([
                '/',
                '/manifest.json',
            ]);
        })
    );
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) => {
            return Promise.all(
                keys.filter((key) => key !== CACHE_NAME).map((key) => caches.delete(key))
            );
        })
    );
    self.clients.claim();
});

self.addEventListener('fetch', (event) => {
    if (event.request.method !== 'GET') return;

    const url = new URL(event.request.url);
    const isBuildAsset = url.pathname.startsWith('/build/');
    const isPublicStatic = url.pathname.startsWith('/favicon') || url.pathname === '/manifest.json';

    if (isPublicStatic || isBuildAsset) {
        event.respondWith(
            caches.match(event.request).then((cached) => {
                return cached || fetch(event.request).then((response) => {
                    if (response.status === 200) {
                        const clone = response.clone();
                        caches.open(CACHE_NAME).then((cache) => {
                            cache.put(event.request, clone);
                            trimCache(cache);
                        });
                    }
                    return response;
                });
            })
        );
    } else {
        event.respondWith(
            fetch(event.request).catch(() => caches.match(event.request))
        );
    }
});

function trimCache(cache) {
    cache.keys().then((keys) => {
        if (keys.length > CACHE_MAX_ENTRIES) {
            keys.slice(0, keys.length - CACHE_MAX_ENTRIES).forEach((key) => cache.delete(key));
        }
    });
}