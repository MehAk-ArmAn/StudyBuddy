self.addEventListener('install', () => {
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        Promise.all([
            caches.keys().then((keys) => {
                return Promise.all(
                    keys.map((key) => caches.delete(key))
                );
            }),

            self.registration.unregister(),

            self.clients.matchAll({
                type: 'window',
                includeUncontrolled: true,
            }).then((clients) => {
                clients.forEach((client) => {
                    client.navigate(client.url);
                });
            }),
        ])
    );
});
