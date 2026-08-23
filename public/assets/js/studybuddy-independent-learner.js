(() => {
    const IMAGE_PATH =
        '/assets/images/roles/independent-learner.svg';

    const replaceImage = (image) => {
        if (
            !image
            || image.dataset.independentLearnerImage === '1'
        ) {
            return;
        }

        image.src = IMAGE_PATH;
        image.dataset.independentLearnerImage = '1';
        image.classList.add(
            'sb-independent-learner-image'
        );

        image.alt =
            'Independent learner studying at their own pace';
    };

    const apply = (root = document) => {
        root.querySelectorAll([
            '[data-role="independent"] img',
            '[data-role="independent_learner"] img',
            '[data-role="independent-learner"] img',
            '[data-role*="independent"] img',
            '[data-user-role*="independent"] img',
            '[data-audience-role*="independent"] img',
            '.independent-learner img',
            '.role-independent img',
        ].join(',')).forEach(replaceImage);

        root
            .querySelectorAll(
                'h1, h2, h3, h4, strong, button, a'
            )
            .forEach((label) => {
                const text =
                    label.textContent?.trim() || '';

                if (
                    !/\bindependent learner\b/i.test(text)
                ) {
                    return;
                }

                const container = label.closest(
                    [
                        'article',
                        'section',
                        'li',
                        '[data-role-card]',
                        '.role-card',
                        '.profile-card',
                        '.dashboard-card',
                    ].join(',')
                );

                replaceImage(
                    container?.querySelector('img')
                );
            });
    };

    apply();

    const observer = new MutationObserver(
        (mutations) => {
            mutations.forEach((mutation) => {
                mutation.addedNodes.forEach((node) => {
                    if (node instanceof Element) {
                        apply(node);
                    }
                });
            });
        }
    );

    observer.observe(
        document.documentElement,
        {
            childList: true,
            subtree: true,
        }
    );
})();
