<?php

namespace App\Services;

use App\Models\StudyBuddyMiniAppPlatform;
use Illuminate\Http\Response;

/** App-scoped installability only. Never controls StudyBuddy's main-site scope. */
class StudyBuddyW33Pwa
{
    public static function manifest(StudyBuddyMiniAppPlatform $app): Response
    {
        $slug = rawurlencode((string) $app->slug);
        $base = '/app-builds/'.$slug.'/';
        $data = [
            'id' => $base, 'name' => $app->name, 'short_name' => mb_substr($app->name, 0, 24),
            'description' => $app->tagline ?: $app->description,
            'start_url' => $base.'index.html?source=desktop',
            'scope' => $base, 'display' => 'standalone',
            'background_color' => '#090d24', 'theme_color' => '#6f4de8',
            'icons' => [
                ['src' => '/assets/studybuddy-w33/icon-192.png', 'sizes' => '192x192', 'type' => 'image/png'],
                ['src' => '/assets/studybuddy-w33/icon-512.png', 'sizes' => '512x512', 'type' => 'image/png', 'purpose' => 'any maskable'],
            ],
        ];
        return response(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR), 200, [
            'Content-Type' => 'application/manifest+json',
            'X-Content-Type-Options' => 'nosniff', 'Cache-Control' => 'private, no-store',
        ]);
    }

    public static function serviceWorker(StudyBuddyMiniAppPlatform $app): Response
    {
        $scope = '/app-builds/'.rawurlencode((string) $app->slug).'/';
        $cache = 'studybuddy-w33-'.preg_replace('/[^a-z0-9-]+/', '', strtolower($app->slug)).'-'.($app->web_app_uploaded_at?->timestamp ?? 0);
        $js = 'const SCOPE='.json_encode($scope).';const CACHE='.json_encode($cache).';'.<<<'JS'
self.addEventListener('install', event => { self.skipWaiting(); });
self.addEventListener('activate', event => {
  event.waitUntil((async () => {
    for (const key of await caches.keys()) {
      if (key.startsWith('studybuddy-w33-') && key !== CACHE && key.includes(CACHE.split('-').slice(0, -1).join('-'))) await caches.delete(key);
    }
    await self.clients.claim();
  })());
});
self.addEventListener('fetch', event => {
  const request = event.request;
  if (request.method !== 'GET') return;
  const url = new URL(request.url);
  if (url.origin !== self.location.origin || !url.pathname.startsWith(SCOPE)) return;
  if (!/\.(html|js|mjs|css|wasm|png|jpe?g|webp|gif|ico|woff2?|json)$/.test(url.pathname)) return;
  event.respondWith((async () => {
    try {
      const response = await fetch(request);
      if (response.ok && response.type === 'basic') {
        const cache = await caches.open(CACHE);
        await cache.put(request, response.clone());
      }
      return response;
    } catch (error) {
      const cached = await caches.match(request);
      if (cached) return cached;
      throw error;
    }
  })());
});
JS;
        return response($js, 200, [
            'Content-Type' => 'text/javascript; charset=UTF-8',
            'Cache-Control' => 'private, no-store', 'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    public static function decorate(string $html, bool $existingServiceWorker): string
    {
        if (str_contains($html, 'data-sb33-pwa')) { return $html; }
        $registration = $existingServiceWorker ? '' : "navigator.serviceWorker.register('studybuddy-w33-sw.js',{scope:'./'}).catch(()=>{});";
        $script = <<<'JS'
<script data-sb33-pwa>
(() => {
  if ('serviceWorker' in navigator && (location.protocol === 'https:' || location.hostname === 'localhost' || location.hostname === '127.0.0.1')) {
    __REGISTRATION__
  }
  if (!new URLSearchParams(location.search).has('sb33install')) return;
  const bar = document.createElement('div');
  bar.setAttribute('role','status');
  bar.style.cssText = 'position:fixed;bottom:18px;left:50%;transform:translateX(-50%);z-index:2147483646;width:min(94vw,540px);display:flex;align-items:center;gap:12px;flex-wrap:wrap;padding:14px 18px;border:1px solid #9f81ff;border-radius:18px;background:#12102bf0;color:white;font:14px system-ui;box-shadow:0 16px 44px #0008';
  const message = document.createElement('span');
  message.textContent = 'Install this learning app on your desktop';
  message.style.cssText = 'flex:1;min-width:170px';
  const button = document.createElement('button');
  button.type='button';button.textContent='Install';button.disabled=true;
  button.style.cssText='background:linear-gradient(100deg,#a86afa,#70d6ef);border:0;border-radius:10px;padding:10px 16px;cursor:pointer;color:#121128;font-weight:700';
  const close=document.createElement('button');close.type='button';close.textContent='×';
  close.setAttribute('aria-label','Dismiss desktop install tips');
  close.style.cssText='border:0;color:#fff;background:transparent;font-size:24px;cursor:pointer';
  close.addEventListener('click',()=>bar.remove());
  let pending=null;
  window.addEventListener('beforeinstallprompt',event=>{event.preventDefault();pending=event;button.disabled=false;message.textContent='Ready to install on this desktop';});
  button.addEventListener('click',async()=>{if(!pending)return;const p=pending;pending=null;button.disabled=true;await p.prompt();});
  window.addEventListener('appinstalled',()=>{message.textContent='Desktop installation started';button.remove();});
  bar.append(message,button,close);
  const attach=()=>{
    (document.body||document.documentElement).appendChild(bar);
    setTimeout(()=>{if(!pending&&bar.isConnected){message.textContent='In Chrome/Edge, use the browser menu → Install app, if available. A secure HTTPS site is required outside localhost.';button.style.display='none';}},2200);
  };
  if(document.readyState==='loading')document.addEventListener('DOMContentLoaded',attach,{once:true});else attach();
})();
</script>
JS;
        $script = str_replace('__REGISTRATION__', $registration, $script);
        $manifest = '<link rel="manifest" href="studybuddy-w33-manifest.webmanifest" data-sb33-pwa-manifest><meta name="theme-color" content="#6f4de8">';
        // Replace Flutter's default manifest rather than adding a competing second manifest.
        // The source ZIP remains untouched; only the served public HTML is decorated.
        $manifestPattern = '~<link\b(?=[^>]*\brel\s*=\s*["\']?manifest["\']?)[^>]*>~i';
        if (preg_match($manifestPattern, $html)) {
            $html = preg_replace($manifestPattern, $manifest, $html, 1);
        } elseif (stripos($html, '</head>') !== false) {
            $html = preg_replace('/<\/head>/i', $manifest.'</head>', $html, 1);
        } else {
            $html = $manifest.$html;
        }
        return str_ireplace('</body>', $script.'</body>', $html);
    }
}
