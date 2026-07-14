@php
    $assetUrl = function ($path) {
        if (!$path) return asset('assets/studybuddy-imgs/brand/logo-icon.png');
        if (preg_match('/^https?:\/\//i', $path)) return $path;
        return file_exists(public_path(ltrim($path, '/'))) ? asset(ltrim($path, '/')) : asset('assets/studybuddy-imgs/brand/logo-icon.png');
    };

    $livingReviews = [
        ['name' => 'Ayaan', 'role' => 'Student', 'text' => 'Math Quest made practice feel like a game instead of homework.', 'avatar' => 'A'],
        ['name' => 'Maya', 'role' => 'Parent', 'text' => 'The dashboard is clear enough to see progress without overwhelming my child.', 'avatar' => 'M'],
        ['name' => 'Sara', 'role' => 'Teacher', 'text' => 'I can quickly guide learners toward the right mini-apps and routines.', 'avatar' => 'S'],
    ];

    $livingStats = [
        ['value' => '8+', 'label' => 'learning worlds'],
        ['value' => '4', 'label' => 'role-based experiences'],
        ['value' => '24/7', 'label' => 'self-paced practice'],
        ['value' => 'Safe', 'label' => 'guided progress'],
    ];
@endphp

<section class="sb-living-section sb-living-results" data-animate-section>
    <div class="sb-living-section-head">
        <p class="sb-living-kicker">Platform pulse</p>
        <h2>Built like a real learning product, not just a pretty homepage.</h2>
        <p>StudyBuddy connects apps, dashboards, profiles, points, community showcases, and role-based controls in one universe.</p>
    </div>

    <div class="sb-living-stats">
        @foreach($livingStats as $stat)
            <article data-living-card>
                <strong>{{ $stat['value'] }}</strong>
                <span>{{ $stat['label'] }}</span>
            </article>
        @endforeach
    </div>
</section>

<section class="sb-living-section sb-avatar-cta-section" data-animate-section>
    <div class="sb-avatar-showcase">
        <div class="tiny-avatar-cloud" aria-hidden="true">
            <span>🎒</span><span>🛡️</span><span>🏫</span><span>🚀</span><span>⭐</span>
        </div>
        <p class="sb-living-kicker">Choose your vibe</p>
        <h2>Profiles, avatars, colours, badges, and public showcases.</h2>
        <p>Users can build a profile that shows their learning personality, favorite app worlds, and progress style.</p>
        <div class="sb-living-actions">
            <a href="{{ url('/profile') }}">Customize profile</a>
            <a class="soft" href="{{ url('/community') }}">See community</a>
        </div>
    </div>

    <div class="sb-app-popups">
        @foreach(($appUniverse ?? collect())->take(4) as $app)
            <a href="{{ url('/apps/'.$app->slug) }}" data-living-card>
                <img src="{{ $assetUrl($app->hero_image ?? $app->image_path ?? null) }}" alt="{{ $app->name }} artwork">
                <span>{{ $app->icon ?? '✨' }}</span>
                <strong>{{ $app->name }}</strong>
                <small>Play this world →</small>
            </a>
        @endforeach
    </div>
</section>

<section class="sb-living-section sb-living-reviews" data-animate-section>
    <div class="sb-living-section-head">
        <p class="sb-living-kicker">Learner love</p>
        <h2>Friendly, playful, and actually understandable.</h2>
        <p>Short missions, clean controls, and safe community-style progress make the platform easier to explore.</p>
    </div>

    <div class="sb-review-grid">
        @foreach($livingReviews as $review)
            <article data-living-card>
                <span>{{ $review['avatar'] }}</span>
                <p>“{{ $review['text'] }}”</p>
                <strong>{{ $review['name'] }}</strong>
                <small>{{ $review['role'] }}</small>
            </article>
        @endforeach
    </div>
</section>

<section class="sb-living-section sb-living-final-cta" data-animate-section>
    <div>
        <p class="sb-living-kicker">Ready when you are</p>
        <h2>Start with one tiny win today.</h2>
        <p>Pick an app, customize your profile, and turn learning into a space you actually want to come back to.</p>
        <div class="sb-living-actions">
            <a href="{{ url('/apps') }}">Explore apps</a>
            <a class="soft" href="{{ url('/register') }}">Create account</a>
        </div>
    </div>
</section>
