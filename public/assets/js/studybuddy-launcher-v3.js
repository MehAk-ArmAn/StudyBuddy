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
    const failure = root.querySelector('[data-launcher-failed]');
    const state = root.querySelector('[data-launcher-state]');
    const reload = root.querySelector('[data-launcher-reload]');
    const retry = root.querySelector('[data-launcher-retry]');
    const fullscreen = root.querySelector(
        '[data-launcher-fullscreen]'
    );
    const complete = root.querySelector(
        '[data-launcher-complete]'
    );
    const message = root.querySelector(
        '[data-launcher-message]'
    );

    // How long a build gets to paint its first frame before the learner is
    // offered a retry. Generous: a cold cache pulls several megabytes.
    const READY_TIMEOUT = 65000;

    let phase = 'loading';
    let timer = null;

    const setState = (text, value) => {
        if (!state) {
            return;
        }

        state.textContent = text;
        state.dataset.state = value;
        state.classList.toggle('is-ready', value === 'ready');
    };

    const showLoading = () => {
        phase = 'loading';
        loading?.classList.remove('is-hidden');
        failure?.setAttribute('hidden', '');
        setState('Loading app…', 'loading');
    };

    const showReady = () => {
        // A late first frame always wins: an app that started slowly is still
        // a working app, so a timeout warning must not stick.
        phase = 'ready';
        window.clearTimeout(timer);
        loading?.classList.add('is-hidden');
        failure?.setAttribute('hidden', '');
        setState('Ready', 'ready');
    };

    const showFailure = () => {
        if (phase === 'ready') {
            return;
        }

        phase = 'failed';
        window.clearTimeout(timer);
        loading?.classList.add('is-hidden');
        failure?.removeAttribute('hidden');
        setState("Couldn't start", 'failed');
    };

    /**
     * Decide whether the embedded build is genuinely on screen.
     *
     * The iframe `load` event only says the HTML document arrived — a Flutter
     * build that failed to boot fires it too, which is how a blank rectangle
     * used to be reported as "App running". The build carries an injected
     * StudyBuddy bridge that posts back on its first painted frame; this is the
     * same-origin fallback for when a message is missed.
     */
    const inspectFrame = () => {
        if (phase === 'ready' || !frame) {
            return false;
        }

        try {
            const doc = frame.contentDocument;

            if (!doc) {
                return false;
            }

            if (doc.querySelector('flutter-view, flt-glass-pane, canvas')) {
                showReady();
                return true;
            }

            // A plain static build has no Flutter host element, so a rendered
            // body is the honest signal there.
            const painted =
                doc.readyState === 'complete'
                && !doc.querySelector(
                    'script[src*="flutter_bootstrap.js"],'
                    + 'script[src*="main.dart.js"],'
                    + 'script[src*="flutter.js"]'
                )
                && (doc.body?.childElementCount ?? 0) > 0;

            if (painted) {
                showReady();
                return true;
            }
        } catch (error) {
            // A cross-origin build cannot be inspected. The bridge message and
            // the timeout still cover it.
        }

        return false;
    };

    const watchFrame = () => {
        window.clearTimeout(timer);

        if (!frame) {
            return;
        }

        const started = Date.now();

        const poll = () => {
            if (phase === 'ready' || phase === 'failed') {
                return;
            }

            if (inspectFrame()) {
                return;
            }

            if (Date.now() - started >= READY_TIMEOUT) {
                showFailure();
                return;
            }

            timer = window.setTimeout(poll, 500);
        };

        poll();
    };

    const start = () => {
        showLoading();
        watchFrame();
    };

    frame?.addEventListener('load', () => {
        // Not "running" — only "the document arrived". Readiness is decided by
        // the bridge message or by inspecting what the document produced.
        inspectFrame();

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

    frame?.addEventListener('error', showFailure);

    const restart = () => {
        if (!frame) {
            return;
        }

        showLoading();
        frame.src = frame.src;
        watchFrame();
    };

    reload?.addEventListener('click', restart);
    retry?.addEventListener('click', restart);

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
            if (message) {
                message.textContent =
                    'Full screen is not available in this browser.';
            }
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

        if (data.type === 'studybuddy:app-ready') {
            showReady();
        }

        if (data.type === 'studybuddy:app-failed') {
            showFailure();
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

    if (frame) {
        start();
    }
})();
