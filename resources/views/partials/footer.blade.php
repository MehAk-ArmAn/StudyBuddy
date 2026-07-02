@php
    $settings = $settings ?? [];
    $footerGroups = collect($footerGroups ?? []);

    $siteName = $settings['site_name'] ?? 'StudyBuddy';
    $footerText = $settings['footer_text'] ?? $settings['brand_promise'] ?? 'StudyBuddy is a playful learning universe built for students, parents, teachers, and independent learners. Learn with confidence, play with purpose, and grow at your own pace.';
    $contactEmail = $settings['contact_email'] ?? $settings['support_email'] ?? null;
    $creatorName = $settings['creator_name'] ?? 'PixelCraftsLab Studio';
    $creatorUrl = $settings['creator_url'] ?? 'https://www.pixelcraftslab.com';

    $pillOne = $settings['footer_pill_one'] ?? '📚 Explore apps';
    $pillTwo = $settings['footer_pill_two'] ?? '🧠 Build skills';
    $pillThree = $settings['footer_pill_three'] ?? '🏆 Earn points';

    $normalizeGroups = function ($groups) {
        if ($groups instanceof \Illuminate\Support\Collection && $groups->keys()->filter(fn($k) => is_string($k))->isNotEmpty()) {
            return $groups;
        }
        return collect($groups)->groupBy(fn($item) => $item->group ?? $item->group_name ?? 'Explore');
    };

    $groups = $normalizeGroups($footerGroups);

    if ($groups->isEmpty()) {
        $groups = collect([
            'Explore' => collect([
                (object)['label' => '✦ Apps', 'url' => url('/apps')],
                (object)['label' => '✦ Learning Hub', 'url' => url('/learning-hub')],
                (object)['label' => '✦ Quizzes', 'url' => url('/quizzes')],
                (object)['label' => '✦ Rewards', 'url' => url('/rewards')],
                (object)['label' => '✦ Profile', 'url' => url('/profile')],
                (object)['label' => '✦ Updates', 'url' => url('/updates')],
            ]),
            'Roles' => collect([
                (object)['label' => '🎒 Students', 'url' => url('/apps?role=student')],
                (object)['label' => '🛡️ Parents', 'url' => url('/parents-center')],
                (object)['label' => '🏫 Teachers', 'url' => url('/teacher-studio')],
                (object)['label' => '🚀 Independent Learners', 'url' => url('/apps?role=independent_learner')],
            ]),
            'Learning Worlds' => collect([
                (object)['label' => '🐬 Math Quest', 'url' => url('/apps/math-quest')],
                (object)['label' => '🌳 Focus Forest', 'url' => url('/apps/focus-forest')],
                (object)['label' => '🌌 Quiz Galaxy', 'url' => url('/apps/quiz-galaxy')],
                (object)['label' => '🏰 Flashcard Castle', 'url' => url('/apps/flashcard-castle')],
            ]),
            'Support' => collect([
                (object)['label' => 'About', 'url' => url('/about')],
                (object)['label' => 'Contact', 'url' => url('/contact')],
                (object)['label' => 'Privacy Policy', 'url' => url('/privacy-policy')],
                (object)['label' => 'Terms of Use', 'url' => url('/terms')],
                (object)['label' => 'Data Deletion', 'url' => url('/data-deletion')],
            ]),
        ]);
    }

    $linkUrl = function ($item) {
        $url = $item->url ?? $item->href ?? '#';
        return \Illuminate\Support\Str::startsWith($url, ['http://', 'https://', '/']) ? $url : url($url);
    };

    $linkLabel = fn ($item) => $item->label ?? $item->title ?? $item->name ?? 'Link';

    $socials = collect([
        ['label' => 'Instagram', 'url' => $settings['instagram_url'] ?? null, 'icon' => '◎'],
        ['label' => 'YouTube', 'url' => $settings['youtube_url'] ?? null, 'icon' => '▶'],
        ['label' => 'TikTok', 'url' => $settings['tiktok_url'] ?? null, 'icon' => '♪'],
        ['label' => 'X', 'url' => $settings['x_url'] ?? $settings['twitter_url'] ?? null, 'icon' => '𝕏'],
        ['label' => 'LinkedIn', 'url' => $settings['linkedin_url'] ?? null, 'icon' => 'in'],
    ])->filter(fn($social) => filled($social['url']));
@endphp

<footer class="sb-universe-footer">
    <div class="sb-footer-stars" aria-hidden="true"></div>

    <section class="sb-footer-top">
        <div class="sb-footer-brand-card">
            <div class="sb-footer-logo-row">
                <span class="sb-footer-logo" aria-hidden="true">
                    @if(!empty($settings['logo_image']))
                        <img src="{{ $settings['logo_image'] }}" alt="">
                    @else
                        🐬
                    @endif
                </span>
                <div>
                    <span class="sb-footer-logo-label">{{ $siteName }} logo</span>
                    <h2>{{ $siteName }}</h2>
                    <p class="sb-footer-tagline">{{ $settings['site_tagline'] ?? 'A learning universe made for every kind of learner' }}</p>
                </div>
            </div>

            <p class="sb-footer-promise">{{ $footerText }}</p>

            <div class="sb-footer-pills" aria-label="StudyBuddy promise shortcuts">
                <a href="{{ url('/apps') }}">{{ $pillOne }}</a>
                <a href="{{ url('/learning-hub') }}">{{ $pillTwo }}</a>
                <a href="{{ url('/points-wallet') }}">{{ $pillThree }}</a>
            </div>
        </div>

        <div class="sb-footer-connect-card">
            <p class="eyebrow">Connect with the StudyBuddy hub</p>
            <h3>Find us across the learning universe</h3>
            <p>Follow, subscribe, share, and stay connected with every bright corner of StudyBuddy.</p>

            @if($socials->isNotEmpty())
                <div class="sb-footer-socials">
                    @foreach($socials as $social)
                        <a href="{{ $social['url'] }}" target="_blank" rel="noopener noreferrer" aria-label="{{ $social['label'] }}">
                            <span>{{ $social['icon'] }}</span>
                            <strong>{{ $social['label'] }}</strong>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="sb-footer-empty-socials">
                    <strong>No social links added yet.</strong>
                    <span>We will give learners a way to connect soon. For now, use the Contact page.</span>
                </div>
            @endif
        </div>
    </section>

    <section class="sb-footer-link-grid" aria-label="StudyBuddy footer links">
        @foreach($groups as $groupName => $items)
            <div class="sb-footer-column">
                <h3>{{ $groupName }}</h3>
                <nav aria-label="{{ $groupName }} links">
                    @foreach(collect($items)->take(14) as $item)
                        <a href="{{ $linkUrl($item) }}">{{ $linkLabel($item) }}</a>
                    @endforeach
                </nav>
            </div>
        @endforeach

        <div class="sb-footer-column sb-footer-community">
            <h3>Community</h3>
            @if($contactEmail)
                <a href="mailto:{{ $contactEmail }}">{{ $contactEmail }}</a>
            @else
                <span>support@studybuddy.fun</span>
            @endif
            <a href="{{ url('/community') }}">StudyBuddy Hub</a>
            <a href="{{ url('/leaderboard') }}">Join the leaderboard</a>
        </div>
    </section>

    <section class="sb-footer-bottom">
        <span>© {{ date('Y') }} {{ $siteName }}.</span>
        <span>
            Created by
            <a href="{{ $creatorUrl }}" target="_blank" rel="noopener noreferrer">{{ $creatorName }}</a>
            · Learning-first · Safety-aware
        </span>
    </section>
</footer>
