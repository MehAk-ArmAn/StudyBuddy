(() => {
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');

    document.querySelectorAll('[data-toast-dismiss]').forEach((button) => {
        button.addEventListener('click', () => {
            button.closest('[data-admin-toast]')?.remove();
        });
    });

    const successToast = document.querySelector('.sb-toast--success[data-admin-toast]');
    if (successToast && !reducedMotion.matches) {
        window.setTimeout(() => successToast.classList.add('is-leaving'), 6500);
        window.setTimeout(() => successToast.remove(), 6900);
    }

    const validationSummary = document.querySelector('[data-validation-summary]');
    if (validationSummary) {
        window.setTimeout(() => validationSummary.focus(), 0);
    }

    const editor = document.querySelector('[data-app-editor]');

    if (!editor) {
        return;
    }

    const form = editor.querySelector('[data-app-form]');
    const saveState = editor.querySelector('[data-save-state]');
    const saveBar = editor.querySelector('[data-save-bar]');
    let dirty = editor.dataset.hasValidationErrors === 'true';
    let submitted = false;

    saveBar?.classList.toggle('is-dirty', dirty);

    const setSaveState = (message, state = '') => {
        if (saveState) {
            saveState.textContent = message;
        }

        saveBar?.classList.toggle('is-dirty', state === 'dirty');
        saveBar?.classList.toggle('is-saving', state === 'saving');
    };

    const markDirty = () => {
        if (submitted) {
            return;
        }

        dirty = true;
        setSaveState('Unsaved changes', 'dirty');
    };

    const slugify = (value) => String(value || '')
        .normalize('NFKD')
        .replace(/[\u0300-\u036f]/g, '')
        .toLowerCase()
        .trim()
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/^-+|-+$/g, '');

    const nameInput = editor.querySelector('[data-app-name]');
    const slugInput = editor.querySelector('[data-app-slug]');
    const slugPreview = editor.querySelector('[data-slug-preview]');
    let slugWasEdited = Boolean(slugInput?.value.trim());

    const updateSlugPreview = () => {
        if (slugPreview) {
            slugPreview.textContent = slugify(slugInput?.value) || 'your-app-name';
        }
    };

    slugInput?.addEventListener('input', () => {
        slugWasEdited = Boolean(slugInput.value.trim());
        updateSlugPreview();
    });

    nameInput?.addEventListener('input', () => {
        if (!slugInput || slugWasEdited) {
            return;
        }

        slugInput.value = slugify(nameInput.value);
        updateSlugPreview();
    });

    const objectUrls = new Map();

    editor.querySelectorAll('[data-artwork-card]').forEach((card) => {
        const input = card.querySelector('[data-artwork-input]');
        const preview = card.querySelector('[data-artwork-preview]');
        const image = card.querySelector('[data-artwork-preview-image]');
        const fallback = card.querySelector('[data-artwork-fallback]');
        const fileName = card.querySelector('[data-upload-file-name]');
        const clearButton = card.querySelector('[data-upload-clear]');
        const removeInput = card.querySelector('input[name^="remove_"]');
        const savedSrc = image?.getAttribute('src') || '';
        const savedWasVisible = Boolean(savedSrc && !image?.hidden);
        const defaultFileHelp = fileName?.textContent || '';

        const releaseObjectUrl = () => {
            const objectUrl = objectUrls.get(input);

            if (objectUrl) {
                URL.revokeObjectURL(objectUrl);
                objectUrls.delete(input);
            }
        };

        const showFallback = () => {
            if (image) {
                image.hidden = true;
            }

            if (fallback) {
                fallback.hidden = false;
            }

            preview?.classList.add('is-missing');
        };

        const restoreSavedPreview = () => {
            releaseObjectUrl();

            if (image && savedWasVisible && !removeInput?.checked) {
                image.src = savedSrc;
                image.hidden = false;
                fallback?.setAttribute('hidden', '');
                preview?.classList.remove('is-missing');
            } else {
                showFallback();
            }

            if (fileName) {
                fileName.textContent = defaultFileHelp;
            }

            if (clearButton) {
                clearButton.hidden = true;
            }

            card.classList.remove('has-new-file');
        };

        const previewFile = (file) => {
            if (!file || !image || !preview) {
                return;
            }

            releaseObjectUrl();
            const objectUrl = URL.createObjectURL(file);
            objectUrls.set(input, objectUrl);
            image.src = objectUrl;
            image.hidden = false;
            fallback?.setAttribute('hidden', '');
            preview.classList.remove('is-missing');
            card.classList.add('has-new-file');

            if (removeInput) {
                removeInput.checked = false;
            }

            if (fileName) {
                fileName.textContent = file.name + ' · '
                    + Math.max(1, Math.round(file.size / 1024)) + ' KB';
            }

            if (clearButton) {
                clearButton.hidden = false;
            }
        };

        input?.addEventListener('change', () => {
            const file = input.files?.[0];

            if (file) {
                previewFile(file);
            } else {
                restoreSavedPreview();
            }
        });

        clearButton?.addEventListener('click', () => {
            if (input) {
                input.value = '';
            }

            restoreSavedPreview();
            markDirty();
        });

        removeInput?.addEventListener('change', () => {
            if (removeInput.checked) {
                if (input) {
                    input.value = '';
                }

                releaseObjectUrl();
                showFallback();
                clearButton?.setAttribute('hidden', '');
                card.classList.remove('has-new-file');
            } else {
                restoreSavedPreview();
            }
        });

        image?.addEventListener('error', showFallback);

        if (image?.complete && savedSrc && image.naturalWidth === 0) {
            showFallback();
        }

        ['dragenter', 'dragover'].forEach((eventName) => {
            card.addEventListener(eventName, (event) => {
                event.preventDefault();
                card.classList.add('is-dragging');
            });
        });

        ['dragleave', 'drop'].forEach((eventName) => {
            card.addEventListener(eventName, (event) => {
                event.preventDefault();
                card.classList.remove('is-dragging');
            });
        });

        card.addEventListener('drop', (event) => {
            const file = event.dataTransfer?.files?.[0];

            if (!file || !file.type.startsWith('image/') || !input) {
                return;
            }

            const transfer = new DataTransfer();
            transfer.items.add(file);
            input.files = transfer.files;
            input.dispatchEvent(new Event('change', { bubbles: true }));
        });
    });

    window.addEventListener('pagehide', () => {
        objectUrls.forEach((url) => URL.revokeObjectURL(url));
        objectUrls.clear();
    });

    const playUrl = editor.querySelector('[data-google-play-url]');
    const packageInput = editor.querySelector('[data-android-package]');
    const packageStatus = editor.querySelector('[data-package-status]');
    let packageWasAutomatic = false;

    const packageFromPlayUrl = () => {
        if (!playUrl?.value.trim()) {
            return '';
        }

        try {
            const url = new URL(playUrl.value.trim());

            if (
                url.protocol !== 'https:'
                || url.hostname !== 'play.google.com'
                || url.pathname !== '/store/apps/details'
            ) {
                return '';
            }

            return (url.searchParams.get('id') || '').trim();
        } catch (error) {
            return '';
        }
    };

    const fillPackage = () => {
        if (!packageInput) {
            return;
        }

        const parsed = packageFromPlayUrl();

        if (parsed && (!packageInput.value.trim() || packageWasAutomatic)) {
            packageInput.value = parsed;
            packageWasAutomatic = true;

            if (packageStatus) {
                packageStatus.textContent = 'Package ID detected: ' + parsed;
                packageStatus.classList.add('is-confirmed');
            }
        } else if (!parsed && packageWasAutomatic) {
            packageInput.value = '';
            packageWasAutomatic = false;
            packageStatus?.classList.remove('is-confirmed');
        }
    };

    playUrl?.addEventListener('input', fillPackage);
    playUrl?.addEventListener('change', fillPackage);
    packageInput?.addEventListener('input', (event) => {
        if (event.isTrusted) {
            packageWasAutomatic = false;
            packageStatus?.classList.remove('is-confirmed');
        }
    });

    const syncPlatformToggle = (toggle) => {
        const panelId = toggle.getAttribute('aria-controls');
        const panel = panelId ? document.getElementById(panelId) : null;
        const hasValidationError = Boolean(panel?.querySelector('[aria-invalid="true"]'));
        const open = toggle.checked || hasValidationError;

        if (panel) {
            panel.hidden = !open;
        }

        toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        toggle.closest('[data-platform-card]')?.classList.toggle('is-enabled', toggle.checked);
    };

    editor.querySelectorAll('[data-platform-toggle]').forEach((toggle) => {
        syncPlatformToggle(toggle);
        toggle.addEventListener('change', () => syncPlatformToggle(toggle));
    });

    const webEnabled = editor.querySelector('[data-web-enabled]');
    const webZip = editor.querySelector('[data-web-zip]');
    const webUrl = editor.querySelector('[data-web-url]');
    const removeWebApp = editor.querySelector('input[name="remove_web_app"]');

    const revealPlatform = (toggle) => {
        if (!toggle) {
            return;
        }

        toggle.checked = true;
        syncPlatformToggle(toggle);
    };

    webZip?.addEventListener('change', () => {
        if (!webZip.files?.length) {
            return;
        }

        revealPlatform(webEnabled);

        if (webUrl) {
            webUrl.value = '';
        }

        if (removeWebApp) {
            removeWebApp.checked = false;
        }

        webZip.closest('.sb-launch-choice')?.classList.add('is-selected');
        webUrl?.closest('.sb-launch-choice')?.classList.remove('is-selected');
    });

    webUrl?.addEventListener('input', () => {
        if (!webUrl.value.trim()) {
            webUrl.closest('.sb-launch-choice')?.classList.remove('is-selected');
            return;
        }

        revealPlatform(webEnabled);

        if (webZip?.files?.length) {
            webZip.value = '';
        }

        if (removeWebApp) {
            removeWebApp.checked = false;
        }

        webUrl.closest('.sb-launch-choice')?.classList.add('is-selected');
        webZip?.closest('.sb-launch-choice')?.classList.remove('is-selected');
    });

    const storeEnabled = editor.querySelector('[data-store-enabled]');
    editor.querySelectorAll('[data-store-url], [data-google-play-url]').forEach((input) => {
        input.addEventListener('input', () => {
            if (input.value.trim()) {
                revealPlatform(storeEnabled);
                input.closest('details')?.setAttribute('open', '');
            }
        });
    });

    const sectionLinks = Array.from(editor.querySelectorAll('[data-section-link]'));
    const sections = sectionLinks
        .map((link) => document.getElementById(link.dataset.sectionTarget || ''))
        .filter(Boolean);

    const setActiveSection = (id) => {
        sectionLinks.forEach((link) => {
            const active = link.dataset.sectionTarget === id;
            link.classList.toggle('is-active', active);

            if (active) {
                link.setAttribute('aria-current', 'step');
            } else {
                link.removeAttribute('aria-current');
            }
        });
    };

    sectionLinks.forEach((link) => {
        link.addEventListener('click', (event) => {
            const section = document.getElementById(link.dataset.sectionTarget || '');

            if (!section) {
                return;
            }

            event.preventDefault();
            section.scrollIntoView({
                behavior: 'auto',
                block: 'start',
            });
            setActiveSection(section.id);
            window.history.replaceState(null, '', '#' + section.id);
        });
    });

    if ('IntersectionObserver' in window && sections.length) {
        const observer = new IntersectionObserver((entries) => {
            const visible = entries
                .filter((entry) => entry.isIntersecting)
                .sort((a, b) => b.intersectionRatio - a.intersectionRatio)[0];

            if (visible) {
                setActiveSection(visible.target.id);
            }
        }, {
            rootMargin: '-18% 0px -62% 0px',
            threshold: [0.05, 0.25, 0.5],
        });

        sections.forEach((section) => observer.observe(section));
    } else if (sections[0]) {
        setActiveSection(sections[0].id);
    }

    form?.addEventListener('input', markDirty);
    form?.addEventListener('change', markDirty);
    form?.addEventListener('submit', (event) => {
        if (submitted) {
            event.preventDefault();
            return;
        }

        submitted = true;
        setSaveState('Saving…', 'saving');
        form.setAttribute('aria-busy', 'true');
        form.classList.add('is-submitting');
        event.submitter?.setAttribute('aria-busy', 'true');
        form.querySelectorAll('button[type="submit"]').forEach((button) => {
            button.setAttribute('aria-disabled', 'true');
        });
    });

    window.addEventListener('beforeunload', (event) => {
        if (!dirty || submitted) {
            return;
        }

        event.preventDefault();
        event.returnValue = '';
    });

    const deleteForm = editor.querySelector('[data-delete-form]');
    const deleteInput = deleteForm?.querySelector('[data-delete-confirm]');
    const deleteButton = deleteForm?.querySelector('[data-delete-button]');
    const expectedName = deleteForm?.dataset.confirmValue || '';

    if (deleteButton) {
        deleteButton.disabled = true;
    }

    deleteInput?.addEventListener('input', () => {
        if (deleteButton) {
            deleteButton.disabled = deleteInput.value.trim() !== expectedName;
        }
    });

    const dangerSection = editor.querySelector('.sb-card--danger');
    if (dangerSection && 'IntersectionObserver' in window) {
        const dangerObserver = new IntersectionObserver(([entry]) => {
            editor.classList.toggle('is-danger-visible', entry.isIntersecting);
        }, { threshold: 0.08 });

        dangerObserver.observe(dangerSection);
    }
})();
