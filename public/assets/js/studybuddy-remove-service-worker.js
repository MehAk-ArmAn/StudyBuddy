(() => {
    const cleanup = async () => {
        try {
            if ('serviceWorker' in navigator) {
                const registrations =
                    await navigator.serviceWorker.getRegistrations();

                await Promise.all(
                    registrations.map((registration) => {
                        return registration.unregister();
                    })
                );
            }

            if ('caches' in window) {
                const cacheNames = await caches.keys();

                await Promise.all(
                    cacheNames.map((cacheName) => {
                        return caches.delete(cacheName);
                    })
                );
            }

            const key = 'studybuddy-service-worker-cleaned-v1';

            if (!sessionStorage.getItem(key)) {
                sessionStorage.setItem(key, '1');

                const url = new URL(window.location.href);
                url.searchParams.set(
                    'studybuddy_refresh',
                    Date.now().toString()
                );

                window.location.replace(url.toString());
            }
        } catch (error) {
            console.warn(
                'StudyBuddy service worker cleanup failed.',
                error
            );
        }
    };

    cleanup();
})();
