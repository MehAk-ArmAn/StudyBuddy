@php
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Schema;

    $shellSettings = [];
    if (Schema::hasTable('site_settings')) {
        $shellSettings = DB::table('site_settings')->pluck('value', 'key')->all();
    }

    $brandName = $shellSettings['brand_name'] ?? $shellSettings['site_name'] ?? config('studybuddy.brand.name');
    $tagline = $shellSettings['brand_slogan'] ?? $shellSettings['site_tagline'] ?? config('studybuddy.brand.slogan');
    $promise = $shellSettings['brand_promise'] ?? $shellSettings['footer_text'] ?? config('studybuddy.brand.description');

    // The canonical StudyBuddy mark leads the list. An admin override still
    // wins, but the generic control-room glyph is only ever a last resort.
    $logoCandidates = [
        $shellSettings['logo_image'] ?? null,
        config('studybuddy.icons.logo'),
        config('studybuddy.icons.mark'),
        'assets/studybuddy-control/logo.svg',
    ];

    $logoPath = null;
    foreach ($logoCandidates as $candidate) {
        if (!$candidate) continue;
        $clean = ltrim($candidate, '/');
        if (str_starts_with($clean, 'http') || file_exists(public_path($clean))) {
            $logoPath = $candidate;
            break;
        }
    }

    $safeJson = function ($value, $fallback) {
        if (!is_string($value) || trim($value) === '') return $fallback;
        $decoded = json_decode($value, true);
        return is_array($decoded) ? $decoded : $fallback;
    };

    $fallbackFooter = [
        'Explore' => [
            ['label' => 'Apps', 'url' => '/apps'],
            ['label' => 'Roles', 'url' => '/roles'],
            ['label' => 'Community', 'url' => '/community'],
            ['label' => 'Search', 'url' => '/search'],
        ],
        'For Every Role' => [
            ['label' => 'Students', 'url' => '/roles'],
            ['label' => 'Parents', 'url' => '/roles'],
            ['label' => 'Teachers', 'url' => '/roles'],
            ['label' => 'Independent Learners', 'url' => '/roles'],
        ],
        'Trust & Support' => [
            ['label' => 'Community Guidelines', 'url' => '/community-guidelines'],
            ['label' => 'Privacy First', 'url' => '/privacy-policy'],
            ['label' => 'Terms of Use', 'url' => '/terms'],
            ['label' => 'Contact Support', 'url' => '/contact'],
        ],
    ];

    $footerGroups = $safeJson($shellSettings['shell_footer_groups_json'] ?? null, $fallbackFooter);

    // Never list apps that do not exist. The column is built from the live
    // catalogue and disappears entirely while the catalogue is empty.
    unset($footerGroups['Learning Worlds']);

    $footerApps = \App\Models\StudyBuddyMiniAppPlatform::query()
        ->active()->ordered()->take(5)
        ->get(['slug', 'name']);

    if ($footerApps->isNotEmpty()) {
        $footerGroups = array_merge(
            ['Learning Worlds' => $footerApps->map(fn ($a) => [
                'label' => $a->name,
                'url' => '/apps/'.$a->slug,
            ])->all()],
            $footerGroups
        );
    }

    $socials = $safeJson($shellSettings['shell_social_links_json'] ?? null, [
        ['label' => 'Instagram', 'url' => ''],
        ['label' => 'YouTube', 'url' => ''],
        ['label' => 'LinkedIn', 'url' => ''],
    ]);

    $pillOne = $shellSettings['footer_pill_one'] ?? 'Explore apps';
    $pillTwo = $shellSettings['footer_pill_two'] ?? 'Build skills';
    $pillThree = $shellSettings['footer_pill_three'] ?? 'Earn points';
@endphp

<footer class="sb-advanced-footer">
    <div class="sb-footer-glow one" aria-hidden="true"></div>
    <div class="sb-footer-glow two" aria-hidden="true"></div>

    <div class="sb-footer-inner">
        <section class="sb-footer-top-section">
            <div class="sb-footer-brand-block">
                <a class="sb-footer-brand" href="{{ url('/') }}">
                    @if($logoPath)
                        <img src="{{ str_starts_with($logoPath, 'http') ? $logoPath : asset(ltrim($logoPath, '/')) }}" alt="{{ $brandName }}">
                    @endif
                    <span>
                        <strong>{{ $brandName }}</strong>
                        <em>{{ $tagline }}</em>
                    </span>
                </a>

                <p>{{ $promise }}</p>
            </div>

            <div class="sb-footer-action-card">
                <span>Ready to start?</span>
                <h2>Make an account and start playing.</h2>
                <p>It takes a minute. Pick who you are and we will set the rest up.</p>
                <div>
                    <a href="{{ url('/register') }}">Create account</a>
                    <a class="soft" href="{{ url('/apps') }}">Explore apps</a>
                </div>
            </div>
        </section>

        <section class="sb-footer-value-strip" aria-label="StudyBuddy values">
            <article>
                <strong>{{ $pillOne }}</strong>
                <span>Short games, one skill at a time.</span>
            </article>
            <article>
                <strong>{{ $pillTwo }}</strong>
                <span>Get it wrong, try again, no fuss.</span>
            </article>
            <article>
                <strong>{{ $pillThree }}</strong>
                <span>Points for finishing, not for lingering.</span>
            </article>
        </section>

        <section class="sb-footer-link-section">
            <div class="sb-footer-section-heading">
                <span>Find your way</span>
                <h2>Everything on StudyBuddy, in one list.</h2>
            </div>

            <div class="sb-footer-grid" aria-label="StudyBuddy footer navigation">
                @foreach($footerGroups as $group => $links)
                    <div class="sb-footer-group">
                        <h3>{{ $group }}</h3>
                        <ul>
                            @foreach($links as $link)
                                @php
                                    $label = $link['label'] ?? 'Link';
                                    $url = $link['url'] ?? '#';
                                    $isExternal = str_starts_with($url, 'http') || str_starts_with($url, 'mailto:');
                                @endphp
                                <li>
                                    <a href="{{ $isExternal ? $url : url($url) }}" @if($isExternal && str_starts_with($url, 'http')) target="_blank" rel="noopener" @endif>
                                        <span>{{ $label }}</span>
                                        <i>→</i>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach

                <div class="sb-footer-group sb-footer-newsletter">
                    <h3>Updates</h3>
                    <p>We will tell you when a new game is ready. Nothing else.</p>

                    <form action="{{ route('newsletter.subscribe') }}" method="POST">
    @csrf

    <label class="sb-newsletter-honeypot" aria-hidden="true">
        Website
        <input
            type="text"
            name="website"
            tabindex="-1"
            autocomplete="off"
        >
    </label>

                        <input name="email" type="email" placeholder="Your email" required autocomplete="email" value="{{ old('email') }}">
                        <button type="submit">Join</button>
                    
    @if(session('newsletter_success'))
        <p
            class="sb-newsletter-feedback is-success"
            role="status"
        >
            {{ session('newsletter_success') }}
        </p>
    @endif

    @error('email')
        <p
            class="sb-newsletter-feedback is-error"
            role="alert"
        >
            {{ $message }}
        </p>
    @enderror

</form>

                    <div class="sb-footer-socials">
                        @foreach($socials as $social)
                            @php
                                $url = $social['url'] ?? '';
                                $label = $social['label'] ?? 'Social';
                            @endphp
                            @if($url)
                                <a href="{{ $url }}" target="_blank" rel="noopener">{{ $label }}</a>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        <section class="sb-footer-bottom">
            <p>© {{ date('Y') }} {{ $brandName }}. All rights reserved.</p>
            <p>Created by <a href="{{ $shellSettings['creator_url'] ?? 'https://pixelcraftslab.com' }}" target="_blank" rel="noopener">{{ $shellSettings['creator_name'] ?? 'PixelCraftsLab Studio' }}</a></p>
        </section>
    </div>
</footer>