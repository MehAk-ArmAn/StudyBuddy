(() => {
    const preview = document.querySelector('[data-profile-preview]');
    if (!preview) return;

    const pfpInput = document.querySelector('[data-pfp-input]');
    const pfpPreview = document.querySelector('[data-pfp-preview]');
    const pfpLetter = document.querySelector('[data-pfp-letter]');
    const nameInput = document.querySelector('[data-name-input]');
    const headlineInput = document.querySelector('[data-headline-input]');
    const previewName = document.querySelector('[data-preview-name]');
    const previewHeadline = document.querySelector('[data-preview-headline]');
    const previewBadge = document.querySelector('[data-preview-badge]');

    const fields = ['profile_theme', 'profile_frame', 'profile_color', 'avatar_shape'];

    const syncClasses = () => {
        fields.forEach((field) => {
            const checked = document.querySelector(`[name="${field}"]:checked`);
            if (!checked) return;

            const prefix = field.replace('profile_', '').replace('avatar_', '');
            Array.from(preview.classList).forEach((className) => {
                if (className.startsWith(prefix + '-')) preview.classList.remove(className);
            });

            preview.classList.add(prefix + '-' + checked.value);
        });

        const badge = document.querySelector('[name="profile_badge"]:checked');
        if (badge && previewBadge) previewBadge.textContent = badge.dataset.customLabel || badge.value;
    };

    document.querySelectorAll('[data-custom-field]').forEach((input) => {
        input.addEventListener('change', syncClasses);
    });

    if (pfpInput && pfpPreview) {
        pfpInput.addEventListener('change', () => {
            const file = pfpInput.files?.[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = (event) => {
                pfpPreview.src = event.target.result;
                pfpPreview.hidden = false;
                if (pfpLetter) pfpLetter.hidden = true;
            };
            reader.readAsDataURL(file);
        });
    }

    if (nameInput && previewName) {
        nameInput.addEventListener('input', () => {
            previewName.textContent = nameInput.value || 'StudyBuddy Learner';
        });
    }

    if (headlineInput && previewHeadline) {
        headlineInput.addEventListener('input', () => {
            previewHeadline.textContent = headlineInput.value || 'Learning with StudyBuddy';
        });
    }

    syncClasses();
})();
