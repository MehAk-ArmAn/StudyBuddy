@extends('layouts.app')

@section('title', ($page->meta_title ?? $page->title ?? 'StudyBuddy Experience') . ' | StudyBuddy')

@section('content')
@php
    $blocks = $page->content_blocks ?? [];
    if (is_string($blocks)) {
        $decoded = json_decode($blocks, true);
        $blocks = is_array($decoded) ? $decoded : [];
    }
@endphp

<div class="sbx-page" data-sbx-page="{{ $slug }}">
    <section class="sbx-hero">
        <div class="sbx-hero__copy">
            <p class="sbx-kicker">{{ $page->eyebrow ?? 'StudyBuddy Experience' }}</p>
            <h1>{{ $page->title ?? 'StudyBuddy Experience' }}</h1>
            <p>{{ $page->subtitle ?? 'Premium admin-editable learning content.' }}</p>
            <div class="sbx-hero__actions">
                @if(!empty($page->primary_cta_label) && !empty($page->primary_cta_url))
                    <a class="sbx-btn sbx-btn--primary" href="{{ $page->primary_cta_url }}">{{ $page->primary_cta_label }}</a>
                @endif
                @if(!empty($page->secondary_cta_label) && !empty($page->secondary_cta_url))
                    <a class="sbx-btn sbx-btn--ghost" href="{{ $page->secondary_cta_url }}">{{ $page->secondary_cta_label }}</a>
                @endif
            </div>
        </div>
        <aside class="sbx-hero__panel">
            <span>{{ $page->hero_badge ?? 'Admin editable' }}</span>
            <strong>One dashboard. Multiple apps. Connected progress.</strong>
            <p>Every major text block, card, app listing, and CTA in this experience layer can be edited from the admin Content Studio.</p>
            @auth
                <a href="{{ route('studybuddy.admin.content.index') }}">Open Content Studio →</a>
            @endauth
        </aside>
    </section>

    @include('studybuddy.experience.partials.experience-nav')

    @if($items->count())
        <section class="sbx-section">
            <div class="sbx-section__head">
                <p class="sbx-kicker">Featured shortcuts</p>
                <h2>Quick actions</h2>
            </div>
            <div class="sbx-card-grid">
                @foreach($items as $item)
                    <article class="sbx-card">
                        <span class="sbx-card__icon">{{ $item->icon ?: '✨' }}</span>
                        @if($item->badge)<span class="sbx-pill">{{ $item->badge }}</span>@endif
                        <h3>{{ $item->title }}</h3>
                        @if($item->subtitle)<p class="sbx-muted">{{ $item->subtitle }}</p>@endif
                        <p>{{ $item->description }}</p>
                        @if($item->button_url && $item->button_label)
                            <a class="sbx-link" href="{{ $item->button_url }}">{{ $item->button_label }} →</a>
                        @endif
                    </article>
                @endforeach
            </div>
        </section>
    @endif

    @foreach($blocks as $block)
        @php $type = $block['type'] ?? 'cards'; @endphp

        @if($type === 'stats')
            <section class="sbx-stats" aria-label="{{ $block['title'] ?? 'Stats' }}">
                @foreach(($block['items'] ?? []) as $stat)
                    <div>
                        <strong>{{ $stat['value'] ?? '—' }}</strong>
                        <span>{{ $stat['label'] ?? 'Metric' }}</span>
                    </div>
                @endforeach
            </section>
        @elseif($type === 'interactive_plan')
            <section class="sbx-section sbx-interactive" id="study-session-builder">
                <div class="sbx-section__head">
                    <p class="sbx-kicker">Interactive</p>
                    <h2>{{ $block['title'] ?? 'Build your study session' }}</h2>
                    <p>{{ $block['description'] ?? '' }}</p>
                </div>
                <div class="sbx-builder" data-sbx-builder="study-plan">
                    <label>Subject <input type="text" value="Math" data-sbx-plan="subject"></label>
                    <label>Minutes <input type="number" min="5" max="180" value="25" data-sbx-plan="minutes"></label>
                    <label>Mood
                        <select data-sbx-plan="mood">
                            <option>Focused</option><option>Sleepy</option><option>Excited</option><option>Stressed</option>
                        </select>
                    </label>
                    <button type="button" class="sbx-btn sbx-btn--primary" data-sbx-generate-plan>Generate plan</button>
                    <div class="sbx-output" data-sbx-plan-output></div>
                </div>
            </section>
        @elseif($type === 'interactive_points')
            <section class="sbx-section sbx-interactive" id="points-simulator">
                <div class="sbx-section__head">
                    <p class="sbx-kicker">Interactive</p>
                    <h2>{{ $block['title'] ?? 'Points simulator' }}</h2>
                    <p>{{ $block['description'] ?? '' }}</p>
                </div>
                <div class="sbx-builder" data-sbx-builder="points">
                    <label>Missions <input type="number" min="0" value="3" data-sbx-points="missions"></label>
                    <label>Quizzes <input type="number" min="0" value="2" data-sbx-points="quizzes"></label>
                    <label>Focus sessions <input type="number" min="0" value="1" data-sbx-points="focus"></label>
                    <button type="button" class="sbx-btn sbx-btn--primary" data-sbx-calc-points>Calculate points</button>
                    <div class="sbx-output" data-sbx-points-output></div>
                </div>
            </section>
        @elseif($type === 'interactive_lesson')
            <section class="sbx-section sbx-interactive" id="lesson-builder">
                <div class="sbx-section__head">
                    <p class="sbx-kicker">Interactive</p>
                    <h2>{{ $block['title'] ?? 'Mini lesson builder' }}</h2>
                    <p>{{ $block['description'] ?? '' }}</p>
                </div>
                <div class="sbx-builder" data-sbx-builder="lesson">
                    <label>Topic <input type="text" value="Fractions" data-sbx-lesson="topic"></label>
                    <label>Age group <input type="text" value="10-12" data-sbx-lesson="age"></label>
                    <button type="button" class="sbx-btn sbx-btn--primary" data-sbx-build-lesson>Build outline</button>
                    <div class="sbx-output" data-sbx-lesson-output></div>
                </div>
            </section>
        @elseif($type === 'role_tabs')
            <section class="sbx-section" id="role-paths">
                <div class="sbx-section__head"><p class="sbx-kicker">Paths</p><h2>{{ $block['title'] ?? 'Choose your path' }}</h2></div>
                <div class="sbx-tabs" data-sbx-tabs>
                    <div class="sbx-tabs__buttons">
                        @foreach(($block['items'] ?? []) as $i => $role)
                            <button type="button" class="{{ $i === 0 ? 'is-active' : '' }}" data-sbx-tab-button="{{ $i }}">{{ $role['icon'] ?? '✨' }} {{ $role['role'] ?? $role['title'] ?? 'Path' }}</button>
                        @endforeach
                    </div>
                    @foreach(($block['items'] ?? []) as $i => $role)
                        <article class="sbx-tab-panel {{ $i === 0 ? 'is-active' : '' }}" data-sbx-tab-panel="{{ $i }}">
                            <h3>{{ $role['role'] ?? $role['title'] ?? 'Path' }}</h3>
                            <p>{{ $role['description'] ?? '' }}</p>
                        </article>
                    @endforeach
                </div>
            </section>
        @elseif($type === 'faq')
            <section class="sbx-section">
                <div class="sbx-section__head"><p class="sbx-kicker">FAQ</p><h2>{{ $block['title'] ?? 'Questions' }}</h2></div>
                <div class="sbx-faq">
                    @foreach(($block['items'] ?? []) as $faq)
                        <details>
                            <summary>{{ $faq['question'] ?? 'Question' }}</summary>
                            <p>{{ $faq['answer'] ?? '' }}</p>
                        </details>
                    @endforeach
                </div>
            </section>
        @elseif($type === 'support_template')
            <section class="sbx-section" id="support-template">
                <div class="sbx-section__head"><p class="sbx-kicker">Template</p><h2>{{ $block['title'] ?? 'Support template' }}</h2><p>{{ $block['description'] ?? '' }}</p></div>
                <textarea class="sbx-template" data-sbx-copy-source>Hello StudyBuddy team, I need help with my account / learning path / app progress. My issue is:</textarea>
                <button type="button" class="sbx-btn sbx-btn--primary" data-sbx-copy-template>Copy template</button>
            </section>
        @elseif($type === 'app_catalog')
            <section class="sbx-section" id="app-catalog">
                <div class="sbx-section__head"><p class="sbx-kicker">App catalog</p><h2>{{ $block['title'] ?? 'Mini apps' }}</h2><p>{{ $block['description'] ?? '' }}</p></div>
                <div class="sbx-app-grid">
                    @forelse($apps as $app)
                        <article class="sbx-app-card">
                            <span class="sbx-card__icon">{{ $app->icon ?: '🎮' }}</span>
                            <span class="sbx-pill">{{ ucfirst($app->launch_status ?? 'planned') }}</span>
                            <h3>{{ $app->title }}</h3>
                            <p class="sbx-muted">{{ $app->category }} • +{{ $app->points_reward ?? 0 }} pts</p>
                            <p>{{ $app->summary ?: $app->description }}</p>
                            <div class="sbx-platforms">
                                <span class="{{ $app->available_web ? 'is-on' : '' }}">Web</span>
                                <span class="{{ $app->available_ios ? 'is-on' : '' }}">iOS</span>
                                <span class="{{ $app->available_android ? 'is-on' : '' }}">Android</span>
                                <span class="{{ $app->available_windows ? 'is-on' : '' }}">Windows</span>
                            </div>
                            @if($app->available_web && $app->web_play_url)
                                <a class="sbx-link" href="{{ $app->web_play_url }}">Play on web →</a>
                            @else
                                <span class="sbx-muted">Web play planned</span>
                            @endif
                        </article>
                    @empty
                        <p>No apps are active yet.</p>
                    @endforelse
                </div>
            </section>
        @elseif(in_array($type, ['cards', 'steps', 'split', 'checklist'], true))
            <section class="sbx-section">
                <div class="sbx-section__head">
                    <p class="sbx-kicker">{{ ucfirst($type) }}</p>
                    <h2>{{ $block['title'] ?? 'Section' }}</h2>
                    @if(!empty($block['description']))<p>{{ $block['description'] }}</p>@endif
                </div>
                <div class="{{ $type === 'checklist' ? 'sbx-checklist' : 'sbx-card-grid' }}">
                    @foreach(($block['items'] ?? []) as $entry)
                        @if(is_string($entry))
                            <div class="sbx-check">✓ {{ $entry }}</div>
                        @else
                            <article class="sbx-card">
                                <span class="sbx-card__icon">{{ $entry['icon'] ?? '✨' }}</span>
                                <h3>{{ $entry['title'] ?? 'Item' }}</h3>
                                <p>{{ $entry['description'] ?? '' }}</p>
                            </article>
                        @endif
                    @endforeach
                </div>
            </section>
        @endif
    @endforeach
</div>
@endsection
