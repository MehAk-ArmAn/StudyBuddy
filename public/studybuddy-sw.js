const SB_CACHE = 'studybuddy-shell-v1';
const SHELL = ['/', '/app-launchpad', '/learning-hub', '/assets/css/site.css'];
self.addEventListener('install', event => {
  event.waitUntil(caches.open(SB_CACHE).then(cache => cache.addAll(SHELL)).catch(() => null));
  self.skipWaiting();
});
self.addEventListener('activate', event => {
  event.waitUntil(caches.keys().then(keys => Promise.all(keys.filter(key => key !== SB_CACHE).map(key => caches.delete(key)))));
  self.clients.claim();
});
self.addEventListener('fetch', event => {
  if (event.request.method !== 'GET') return;
  event.respondWith(fetch(event.request).then(response => {
    const copy = response.clone();
    caches.open(SB_CACHE).then(cache => cache.put(event.request, copy)).catch(() => null);
    return response;
  }).catch(() => caches.match(event.request).then(cached => cached || caches.match('/'))));
});
