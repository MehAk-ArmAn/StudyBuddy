(() => {
    const root = document.querySelector(
        '[data-studybuddy-launcher]'
    );

    if (!root) {
        return;
    }

    const frame = root.querySelector('[data-launcher-frame]');
    const stage = root.querySelector('[data-launcher-stage]');
    const loading = root.querySelector('[data-launcher-loading]');
    const state = root.querySelector('[data-launcher-state]');
    const reload = root.querySelector('[data-launcher-reload]');
    const fullscreen = root.querySelector(
        '[data-launcher-fullscreen]'
    );
    const complete = root.querySelector(
        '[data-launcher-complete]'
    );
    const message = root.querySelector(
        '[data-launcher-message]'
    );

    const setState = (text, ready = false) => {
        if (!state) {
            return;
        }

        state.textContent = text;
        state.classList.toggle('is-ready', ready);
    };

    frame?.addEventListener('load', () => {
        loading?.classList.add('is-hidden');
        setState('App running', true);

        try {
            frame.contentWindow?.postMessage(
                {
                    type: 'studybuddy:host-ready',
                    appSlug: root.dataset.appSlug,
                },
                '*'
            );
        } catch (error) {
            // The embedded app can still run without messaging.
        }
    });

    reload?.addEventListener('click', () => {
        if (!frame) {
            return;
        }

        loading?.classList.remove('is-hidden');
        setState('Reloading app');

        frame.src = frame.src;
    });

    fullscreen?.addEventListener('click', async () => {
        if (!stage) {
            return;
        }

        try {
            if (document.fullscreenElement) {
                await document.exitFullscreen();
                return;
            }

            if (stage.requestFullscreen) {
                await stage.requestFullscreen();
                return;
            }

            if (stage.webkitRequestFullscreen) {
                stage.webkitRequestFullscreen();
            }
        } catch (error) {
            setState('Full screen unavailable');
        }
    });

    const csrf =
        document
            .querySelector('meta[name="csrf-token"]')
            ?.getAttribute('content')
        || '';

    const saveCompletion = async () => {
        if (!complete || complete.disabled) {
            return;
        }

        const url = root.dataset.completeUrl;

        if (!url) {
            window.location.href = '/login';
            return;
        }

        complete.disabled = true;
        complete.textContent = 'Saving points…';

        if (message) {
            message.textContent =
                'Saving this completed session to your profile.';
        }

        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({
                    app_slug: root.dataset.appSlug,
                }),
            });

            const data = await response.json().catch(() => ({}));

            if (!response.ok && response.status !== 429) {
                throw new Error(
                    data.message
                    || 'StudyBuddy could not save this session.'
                );
            }

            complete.textContent =
                response.status === 429
                    ? 'Already saved today'
                    : `${data.points ?? root.dataset.points} points saved`;

            if (message) {
                message.textContent =
                    data.message
                    || 'Your completed session was saved.';
            }

            setState('Session complete', true);
        } catch (error) {
            complete.disabled = false;
            complete.textContent =
                `Save ${root.dataset.points || 0} points`;

            if (message) {
                message.textContent =
                    error.message
                    || 'Could not save points. Try again.';
            }
        }
    };

    complete?.addEventListener('click', saveCompletion);

    window.addEventListener('message', (event) => {
        if (!frame || event.source !== frame.contentWindow) {
            return;
        }

        let expectedOrigin = '';

        try {
            expectedOrigin = new URL(
                frame.src,
                window.location.href
            ).origin;
        } catch (error) {
            expectedOrigin = '';
        }

        if (
            expectedOrigin
            && event.origin
            && event.origin !== expectedOrigin
        ) {
            return;
        }

        const data = event.data;

        if (!data || typeof data !== 'object') {
            return;
        }

        if (
            data.type === 'studybuddy:complete'
            || data.type === 'studybuddy:session-complete'
        ) {
            saveCompletion();
        }

        if (
            data.type === 'studybuddy:status'
            && typeof data.message === 'string'
            && message
        ) {
            message.textContent = data.message.slice(0, 300);
        }

        if (
            data.type === 'studybuddy:title'
            && typeof data.title === 'string'
        ) {
            document.title =
                `${data.title.slice(0, 100)} · StudyBuddy`;
        }
    });
})();
