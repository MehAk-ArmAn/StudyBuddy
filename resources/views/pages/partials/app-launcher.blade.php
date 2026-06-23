@php
    $imageUrl = $imageUrl ?? function (?string $path): ?string {
        if (blank($path)) {
            return null;
        }

        return preg_match('/^https?:\/\//i', $path) ? $path : asset($path);
    };

    $appStoreUrl = $settings['app_store_url'] ?? '#';
    $playStoreUrl = $settings['google_play_url'] ?? '#';
    $apps = $appSection?->items ?? collect();
    $categories = $apps->map(fn ($item) => $item->badge_text ?: 'Explore')->unique()->values();
@endphp

<section class="launcher-shell" data-app-launcher>
    <div class="launcher-console">
        <div>
            <p class="eyebrow">Game launcher</p>
            <h2>Choose your learning world.</h2>
            <p>Pick a mini app, preview the mission, filter by skill, or download the StudyBuddy app when mobile builds go live.</p>
        </div>

        <div class="store-buttons" aria-label="App download links">
            <a class="store-btn" href="{{ $playStoreUrl }}" @if($playStoreUrl !== '#') target="_blank" rel="noopener" @endif>
                <span>Get it on</span>
                <strong>Google Play</strong>
            </a>
            <a class="store-btn" href="{{ $appStoreUrl }}" @if($appStoreUrl !== '#') target="_blank" rel="noopener" @endif>
                <span>Download on the</span>
                <strong>App Store</strong>
            </a>
        </div>
    </div>

    <div class="launcher-personalizer" data-launcher-recommender>
        <div>
            <p class="eyebrow">Personalized picks</p>
            <h3>Tell StudyBuddy your vibe.</h3>
            <p data-recommendation-copy>Choose a role and time target to preview your recommended first quest.</p>
        </div>

        <div class="choice-cloud">
            <button type="button" data-recommend-role="student">Student</button>
            <button type="button" data-recommend-role="parent">Parent</button>
            <button type="button" data-recommend-role="teacher">Teacher</button>
            <button type="button" data-recommend-role="independent">Independent Learner</button>
        </div>

        <div class="time-cloud">
            <button type="button" data-recommend-time="5">5 min</button>
            <button type="button" data-recommend-time="10">10 min</button>
            <button type="button" data-recommend-time="20">20 min</button>
        </div>
    </div>

    <div class="launcher-tabs">
        <button class="active" type="button" data-app-filter="all">All worlds</button>
        @foreach ($categories as $category)
            <button type="button" data-app-filter="{{ Str::slug($category) }}">{{ $category }}</button>
        @endforeach
    </div>

    <div class="launcher-grid">
        @foreach ($apps as $item)
            @php
                $category = Str::slug($item->badge_text ?: 'Explore');
                $image = $imageUrl($item->image_path);
            @endphp

            <article
                class="launcher-card"
                data-app-card
                data-category="{{ $category }}"
                data-title="{{ e($item->title) }}"
                data-subtitle="{{ e($item->subtitle) }}"
                data-body="{{ e($item->body) }}"
                data-image="{{ e($image) }}"
                data-url="{{ e($item->button_url ?: '/apps') }}"
            >
                <div class="launcher-card-art">
                    @if ($image)
                        <img src="{{ $image }}" alt="{{ $item->title }}">
                    @endif

                    @if ($item->badge_text)
                        <span>{{ $item->badge_text }}</span>
                    @endif
                </div>

                <div class="launcher-card-copy">
                    <h3>{{ $item->title }}</h3>

                    @if ($item->subtitle)
                        <p>{{ $item->subtitle }}</p>
                    @endif

                    <div class="launcher-card-actions">
                        <button type="button" data-open-app>Preview Mission</button>
                        @if ($item->button_label)
                            <a href="{{ $item->button_url ?: '#top' }}">{{ $item->button_label }}</a>
                        @endif
                    </div>
                </div>
            </article>
        @endforeach
    </div>
</section>

<div class="mission-modal" data-mission-modal hidden>
    <div class="mission-dialog" role="dialog" aria-modal="true" aria-labelledby="mission-title">
        <button class="mission-close" type="button" data-close-mission aria-label="Close mission">×</button>
        <img data-mission-image alt="" hidden>
        <p class="eyebrow">Mission preview</p>
        <h2 id="mission-title" data-mission-title></h2>
        <p data-mission-subtitle></p>
        <p data-mission-body></p>
        <div class="mission-actions">
            <button class="btn btn-primary" type="button" data-save-mission>Save to My Quest</button>
            <button class="btn btn-ghost" type="button" data-close-mission>Keep Browsing</button>
        </div>
        <p class="mission-save-note" data-mission-save-note hidden></p>
    </div>
</div>

{{-- StudyBuddy Mission Modal Fallback --}}

<script>
(function () {
    const QUEST_KEY = 'studybuddy_saved_quest';

    function toast(message) {
        let el = document.querySelector('[data-studybuddy-toast]');
        if (!el) {
            el = document.createElement('div');
            el.className = 'studybuddy-toast show';
            el.setAttribute('data-studybuddy-toast', '');
            document.body.appendChild(el);
        }
        el.textContent = message;
        el.classList.add('show');
        clearTimeout(el._timer);
        el._timer = setTimeout(() => el.classList.remove('show'), 2400);
    }

    function closeMission() {
        const modal = document.querySelector('[data-mission-modal]');
        if (modal) modal.hidden = true;
        document.body.classList.remove('modal-open');
    }

    document.addEventListener('click', function (event) {
        const closeButton = event.target.closest('[data-close-mission]');
        if (closeButton) {
            event.preventDefault();
            closeMission();
            return;
        }

        const saveButton = event.target.closest('[data-save-mission]');
        if (saveButton) {
            event.preventDefault();
            const modal = document.querySelector('[data-mission-modal]');
            const quest = {
                title: modal?.querySelector('[data-mission-title]')?.textContent?.trim() || 'StudyBuddy Mission',
                subtitle: modal?.querySelector('[data-mission-subtitle]')?.textContent?.trim() || 'Saved StudyBuddy quest',
                body: modal?.querySelector('[data-mission-body]')?.textContent?.trim() || '',
                url: '/apps',
                savedAt: new Date().toISOString()
            };
            localStorage.setItem(QUEST_KEY, JSON.stringify(quest));
            saveButton.textContent = 'Saved to My Quest';
            saveButton.disabled = true;
            const note = modal?.querySelector('[data-mission-save-note]');
            if (note) {
                note.hidden = false;
                note.textContent = 'Saved. Open your dashboard to see this quest.';
            }
            toast(quest.title + ' saved to My Quest.');
        }
    });
})();
</script>
