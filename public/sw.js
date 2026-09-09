const CACHE_NAME = 'laravel-crud-v1'
const urlsToCache = ['/', '/offline']

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            return cache.addAll(urlsToCache)
        })
    )
})

self.addEventListener('fetch', (event) => {
    if (event.request.method !== 'GET') {
        return
    }

    event.respondWith(
        caches.match(event.request).then((response) => {
            if (response) {
                return response
            }

            return fetch(event.request).catch(() => {
                return caches.match('/offline')
            })
        })
    )
})

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) => {
            return Promise.all(
                keys
                    .filter((key) => key !== CACHE_NAME)
                    .map((key) => caches.delete(key))
            )
        })
    )
})
