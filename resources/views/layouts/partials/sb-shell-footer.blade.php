@php
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Schema;
    use Illuminate\Support\Str;

    $settings = $settings ?? (Schema::hasTable('site_settings') ? DB::table('site_settings')->pluck('value', 'key')->toArray() : []);
    $siteName = $settings['site_name'] ?? 'StudyBuddy';
    $logoText = $settings['logo_text'] ?? $siteName;
    $tagline = $settings['site_tagline'] ?? 'Fan-made learning universe';
    $brandPromise = $settings['brand_promise'] ?? $settings['footer_text'] ?? 'StudyBuddy is a safe, playful learning space created to help students, parents, teachers, and independent learners build confidence through apps, quests, points, and guided practice.';
    $supportEmail = $settings['support_email'] ?? $settings['contact_email'] ?? null;
    $creatorName = $settings['creator_name'] ?? 'PixelCraftsLab Studio';
    $creatorUrl = $settings['creator_url'] ?? 'https://pixelcraftslab.com';

    $logoSetting = $settings['logo_image'] ?? $settings['logo_path'] ?? $settings['site_logo'] ?? null;
    $logoSrc = $logoSetting
        ? (Str::startsWith($logoSetting, ['http://', 'https://', '/']) ? $logoSetting : asset($logoSetting))
        : asset('assets/studybuddy-brand/studybuddy-logo-mark.svg');

    $decode = function ($json, $fallback = []) {
        $decoded = json_decode((string) $json, true);
        return is_array($decoded) ? $decoded : $fallback;
    };

    $groups = $decode($settings['shell_footer_groups_json'] ?? '', [
        'Explore' => [
            ['label' => 'Apps', 'url' => '/apps'],
            ['label' => 'Learning Hub', 'url' => '/learning-hub'],
            ['label' => 'Quests', 'url' => '/my-quest'],
            ['label' => 'Points Wallet', 'url' => '/points-wallet'],
        ],
        'Roles' => [
            ['label' => 'Students', 'url' => '/apps?role=student'],
            ['label' => 'Parents', 'url' => '/parents-center'],
            ['label' => 'Teachers', 'url' => '/teacher-studio'],
            ['label' => 'Independent Learners', 'url' => '/apps?role=independent_learner'],
        ],
        'Learning Worlds' => [
            ['label' => 'Math Quest', 'url' => '/apps/math-quest'],
            ['label' => 'Reading Garden', 'url' => '/apps/reading-garden'],
            ['label' => 'Focus Forest', 'url' => '/apps/focus-forest'],
            ['label' => 'Quiz Galaxy', 'url' => '/apps/quiz-galaxy'],
        ],
        'Community' => [
            ['label' => 'About', 'url' => '/about'],
            ['label' => 'Contact', 'url' => '/contact'],
            ['label' => 'Privacy Policy', 'url' => '/privacy-policy'],
            ['label' => 'Terms of Use', 'url' => '/terms'],
        ],
    ]);

    $socials = collect($decode($settings['shell_social_links_json'] ?? '', []))->filter(fn($link) => filled($link['url'] ?? null));

    $pills = [
        ['icon' => asset('assets/studybuddy-brand/icon-apps.svg'), 'label' => $settings['footer_pill_one'] ?? 'Explore apps', 'url' => '/apps'],
        ['icon' => asset('assets/studybuddy-brand/icon-skills.svg'), 'label' => $settings['footer_pill_two'] ?? 'Build skills', 'url' => '/learning-hub'],
        ['icon' => asset('assets/studybuddy-brand/icon-points.svg'), 'label' => $settings['footer_pill_three'] ?? 'Earn points', 'url' => '/points-wallet'],
    ];

    $linkUrl = fn($url) => Str::startsWith(($url ?: '#'), ['http://', 'https://', '/']) ? ($url ?: '#') : url($url);
@endphp

<footer class="sb-consistent-footer-wrap">
    <div class="sb-consistent-footer">
        <section class="sb-consistent-footer-top">
            <div class="sb-consistent-footer-brand">
                <img src="{{ $logoSrc }}" alt="{{ $siteName }} logo">
                <div>
                    <p class="sb-footer-kicker">{{ $tagline }}</p>
                    <h2>{{ $logoText }}</h2>
                    <p>{{ $brandPromise }}</p>
                </div>
            </div>

            <div class="sb-footer-pills">
                @foreach($pills as $pill)
                    <a href="{{ $linkUrl($pill['url']) }}">
                        <img src="{{ $pill['icon'] }}" alt="">
                        <span>{{ $pill['label'] }}</span>
                    </a>
                @endforeach
            </div>
        </section>

        <section class="sb-footer-columns">
            @foreach($groups as $groupName => $items)
                <div class="sb-footer-column">
                    <h3>{{ $groupName }}</h3>
                    <nav aria-label="{{ $groupName }} footer links">
                        @foreach(collect($items)->take(14) as $item)
                            <a href="{{ $linkUrl($item['url'] ?? '#') }}"><span></span>{{ $item['label'] ?? 'Link' }}</a>
                        @endforeach
                    </nav>
                </div>
            @endforeach
        </section>

        <section class="sb-footer-connect">
            <div>
                <p class="sb-footer-kicker">Connect with the StudyBuddy learning hub</p>
                <h3>Find us across the learning universe</h3>
                <p>Follow, subscribe, share, and stay connected with every creative corner of StudyBuddy.</p>
            </div>

            <div class="sb-footer-socials">
                @forelse($socials as $social)
                    <a href="{{ $social['url'] }}" target="_blank" rel="noopener">{{ $social['label'] ?? 'Social' }}</a>
                @empty
                    <div class="sb-footer-social-empty">
                        <img src="{{ asset('assets/studybuddy-brand/icon-community.svg') }}" alt="">
                        <p>No social links added yet. Add them from the admin shell settings when the StudyBuddy hub is ready.</p>
                        @if($supportEmail)<a href="mailto:{{ $supportEmail }}">{{ $supportEmail }}</a>@endif
                    </div>
                @endforelse
            </div>
        </section>

        <section class="sb-footer-bottom">
            <span>© {{ date('Y') }} {{ $siteName }}. Learning-first · Safety-aware.</span>
            <span>Created by <a href="{{ $creatorUrl }}" target="_blank" rel="noopener">{{ $creatorName }}</a></span>
        </section>
    </div>
</footer>
