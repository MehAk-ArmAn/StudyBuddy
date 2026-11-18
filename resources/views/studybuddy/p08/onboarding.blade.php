@extends('layouts.app')
@section('title', 'Make StudyBuddy Yours')
@push('styles')
<link rel="stylesheet" href="{{ asset('assets/studybuddy-p08/onboarding.css') }}?v=8.1">
@endpush
@push('scripts')
<script defer src="{{ asset('assets/studybuddy-p08/onboarding-preview.bundle.js') }}?v=8.1"></script>
@endpush

@section('content')
@php
    $labels = [1 => 'Your identity', 2 => 'Interests & goals', 3 => 'Your workspace', 4 => 'Privacy first', 5 => 'Where to next?', 6 => 'Ready to explore'];
    $done = array_map('intval', is_array($setup['steps'] ?? null) ? $setup['steps'] : []);
    $roleLabel = match ($role) {
        'parent' => 'Family guide', 'teacher' => 'Teacher', 'independent_learner' => 'Independent learner',
        'admin' => 'Platform administrator', default => 'Student'
    };
    $heroLine = match ($role) {
        'parent' => 'Shape a calmer family learning journey.',
        'teacher' => 'Build your own teaching workspace.',
        'admin' => 'Tune your private admin workspace.',
        default => 'Your learning universe starts with you.'
    };
    $roleTip = match ($role) {
        'parent' => 'Connect Codes and approved family links are managed separately in your family workspace.',
        'teacher' => 'Teaching tools and class access still require the existing verification and authorization checks.',
        'admin' => 'These appearance choices do not alter platform permissions.',
        default => 'Choose interests you enjoy. You can change your mind anytime.'
    };
    $subjectsSelected = array_filter(array_map('trim', explode(',', old('subjects_text', $profile['favorite_subjects'] ?? ''))));
    $defaultAction = $setup['next_destination'] ?? (in_array($role, ['teacher','parent','admin'], true) ? 'dashboard' : 'apps');
    // Create JSON data in PHP first; avoid nested expressions inside Blade @json(...).
    // Laravel's Blade directive parser can otherwise terminate at asset(...).
    $previewPayload = [
        'name' => $user->name,
        'goal' => $profile['learning_goal'] ?? '',
        'role' => $roleLabel,
        'image' => asset('assets/studybuddy-p07/icons/reading.webp'),
        'accent' => $setup['accent'] ?? 'violet',
    ];
@endphp
<main class="sb08" id="sb08" data-step="{{ $step }}">
  <header class="sb08-hero">
    <img class="sb08-banner" src="{{ asset('assets/studybuddy-p07/banners/learner.webp') }}" alt="" aria-hidden="true" decoding="async">
    <div class="sb08-hero-copy">
      <span class="sb08-eyebrow">✦ THE STUDYBUDDY UNIVERSE · YOUR SPACE</span>
      <h1>{{ $heroLine }}</h1>
      <p>Six friendly steps. Your choices are private, editable, and connected to your real account.</p>
      <div class="sb08-hero-meta"><span>{{ $roleLabel }}</span><span>{{ $progress }}% setup choices saved</span><a href="{{ route('dashboard') }}">Back to dashboard ↗</a></div>
    </div>
  </header>

  <div class="sb08-progress"><div role="progressbar" aria-valuenow="{{ $progress }}" aria-valuemin="0" aria-valuemax="100" aria-label="Setup progress" style="--progress:{{ $progress }}%"><span></span></div><span>{{ $finished ? 'Finished · edit your choices anytime' : 'Setup is optional · nothing is locked behind it' }}</span></div>
  @if(session('onboarding_notice'))<div class="sb08-notice" role="status">{{ session('onboarding_notice') }}</div>@endif
  @if($errors->any())<div class="sb08-errors" role="alert"><strong>Check these details:</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif

  <div class="sb08-layout">
    <aside class="sb08-nav" aria-label="Personalization steps">
      <div class="sb08-nav-intro"><span class="sb08-eyebrow">YOUR ROADMAP</span><strong>Little steps, big possibilities.</strong><p>No new account or subscription needed.</p></div>
      <nav>
        @foreach($labels as $number => $label)
        <a href="{{ route('studybuddy.onboarding.show', ['step' => $number]) }}" class="{{ $step === $number ? 'is-current' : '' }}" @if($step === $number) aria-current="step" @endif>
          <span class="sb08-step-number">{{ in_array($number, $done, true) ? '✓' : $number }}</span>
          <span><strong>{{ $label }}</strong><small>{{ in_array($number, $done, true) ? 'Saved' : ($step === $number ? 'You are here' : 'Explore step') }}</small></span>
        </a>
        @endforeach
      </nav>
      <div class="sb08-side-note"><img src="{{ asset('assets/studybuddy-p07/icons/reading.webp') }}" alt="" aria-hidden="true" loading="lazy"><p>{{ $roleTip }}</p></div>
    </aside>

    <section class="sb08-panel" aria-labelledby="sb08-step-title">
      <div class="sb08-panel-heading"><span class="sb08-eyebrow">STEP {{ $step }} OF 6</span><h2 id="sb08-step-title">{{ $labels[$step] }}</h2></div>
      <form id="sb08-form" method="POST" action="{{ route('studybuddy.onboarding.save') }}">
        @csrf
        <input type="hidden" name="step" value="{{ $step }}">
        @if($step === 1)
          <p class="sb08-desc">Pick the name and avatar you want to see in your personal space. Your login details and role stay the same.</p>
          <div class="sb08-fields">
            <label>Display name <input id="sb08-display-name" data-preview-field="name" name="display_name" type="text" minlength="2" maxlength="120" required autocomplete="nickname" value="{{ old('display_name', $user->name) }}"></label>
            <fieldset><legend>Choose an avatar</legend><div class="sb08-tiles">
              @foreach(['dolphin-cadet'=>'Dolphin', 'cosmic-explorer'=>'Explorer', 'parent-guide'=>'Guide', 'teacher-mentor'=>'Mentor', 'star-builder'=>'Star builder'] as $avatar => $label)
                <label class="sb08-radio-tile"><input type="radio" name="avatar_style" value="{{ $avatar }}" @checked(old('avatar_style', $user->avatar_style ?? 'dolphin-cadet') === $avatar) required><span>{{ $label }}</span></label>
              @endforeach
            </div></fieldset>
          </div>
        @elseif($step === 2)
          <p class="sb08-desc">Focus on what matters to you. These choices inform your personal setup; they do not make assessments or grades.</p>
          <div class="sb08-fields">
            <fieldset><legend>{{ $role === 'teacher' ? 'Subjects you teach or explore' : ($role === 'parent' ? 'Interests for your family' : 'Favorite subjects') }} <small>(up to six)</small></legend><div class="sb08-subjects">
              @foreach($subjects as $subject)
                <label><input type="checkbox" name="subjects[]" value="{{ $subject }}" @checked(in_array($subject, old('subjects', $subjectsSelected)))><span>{{ $subject }}</span></label>
              @endforeach
            </div></fieldset>
            <label>{{ $role === 'teacher' ? 'Your teaching goal' : ($role === 'parent' ? 'Your family learning goal' : 'Your learning goal') }}
              <input id="sb08-goal" name="learning_goal" data-preview-field="goal" type="text" maxlength="160" minlength="3" required placeholder="e.g. Build a steady 20-minute routine" value="{{ old('learning_goal', $profile['learning_goal'] ?? '') }}">
            </label>
          </div>
        @elseif($step === 3)
          <p class="sb08-desc">Choose how your dashboard feels. These are display preferences, not permission upgrades or paid profile unlocks.</p>
          <div class="sb08-fields sb08-half">
            <label>Dashboard spacing <select name="dashboard_density"><option value="comfortable" @selected(old('dashboard_density', $setup['dashboard_density'] ?? 'comfortable') === 'comfortable')>Comfortable</option><option value="compact" @selected(old('dashboard_density', $setup['dashboard_density'] ?? '') === 'compact')>Compact</option></select></label>
            <label>Daily learning time <select name="daily_minutes">
              @foreach(['5'=>'5 minutes', '10'=>'10 minutes', '20'=>'20 minutes', '30'=>'30 minutes', 'flexible'=>'Flexible'] as $value=>$label)
              <option value="{{ $value }}" @selected(old('daily_minutes', $setup['daily_minutes'] ?? '10') === (string) $value)>{{ $label }}</option>
              @endforeach
            </select></label>
            <label>Guidance <select name="guide_level"><option value="guided" @selected(old('guide_level', $setup['guide_level'] ?? 'guided') === 'guided')>Show helpful tips</option><option value="standard" @selected(old('guide_level', $setup['guide_level'] ?? '') === 'standard')>Keep things simple</option></select></label>
            <label>Accent <select name="accent" data-preview-field="accent"><option value="violet" @selected(old('accent', $setup['accent'] ?? 'violet') === 'violet')>Galaxy purple</option><option value="cyan" @selected(old('accent', $setup['accent'] ?? '') === 'cyan')>Ocean blue</option><option value="rose" @selected(old('accent', $setup['accent'] ?? '') === 'rose')>Star pink</option></select></label>
          </div>
        @elseif($step === 4)
          <p class="sb08-desc">Your safety matters more than appearances. Let's check what these choices really mean.</p>
          <div class="sb08-privacy-grid">
            <article><span aria-hidden="true">🔒</span><strong>Private by default</strong><p>Setup choices are stored with your signed-in account, not shown in the public community.</p></article>
            <article><span aria-hidden="true">🛡️</span><strong>Permissions stay protected</strong><p>Teachers and school managers still need their normal verification and approval.</p></article>
            <article><span aria-hidden="true">👨‍👩‍👧</span><strong>Family access is approved</strong><p>Parents need verified Connect Code relationships to view child activity.</p></article>
            <article><span aria-hidden="true">🌙</span><strong>You control your profile</strong><p>Public profile visibility remains managed separately in Profile Studio.</p></article>
          </div>
          <label class="sb08-confirm"><input type="checkbox" name="privacy_reviewed" value="1" required @checked(!empty($setup['privacy_reviewed_at']))> <span>I reviewed these privacy and permission notes. This does not publish my profile or grant permissions.</span></label>
          <p class="sb08-small">Read the <a href="{{ url('/privacy-policy') }}" target="_blank" rel="noopener">Privacy Policy</a> whenever you need it.</p>
        @elseif($step === 5)
          <p class="sb08-desc">Choose what you'd like to open after setup. We remember your choice, but don't claim you've already visited it.</p>
          @php
            $destinations = match ($role) {
              'teacher' => ['dashboard'=>'Create or manage a class','apps'=>'Browse learning apps','study-space'=>'Write private teaching notes','profile'=>'Edit my Profile Studio'],
              'parent' => ['dashboard'=>'Open family connections','apps'=>'Browse family-friendly apps','profile'=>'Review public profile settings','study-space'=>'Open private Study Space'],
              'admin' => ['dashboard'=>'Return to platform overview','profile'=>'Edit my display profile','apps'=>'Review public learning apps','study-space'=>'Open private Study Space'],
              default => ['apps'=>'Explore learning apps','subjects'=>'Find a subject world','study-space'=>'Continue my Study Space','dashboard'=>'View my dashboard','profile'=>'Customise my profile'],
            };
          @endphp
          <div class="sb08-destinations">
            @foreach($destinations as $value => $label)
              <label><input type="radio" name="next_destination" value="{{ $value }}" @checked(old('next_destination', $defaultAction) === $value) required><span><strong>{{ $label }}</strong><small>{{ $value === 'study-space' ? 'Private notes · no AI replies yet' : 'Open your existing StudyBuddy tools' }}</small></span></label>
            @endforeach
          </div>
        @else
          <p class="sb08-desc">Your account already has everything it needs. Once your five setup choices are saved, you can complete your tour and jump back into StudyBuddy.</p>
          <div class="sb08-summary">
            <div><span>Display name</span><strong>{{ $user->name }}</strong></div>
            <div><span>Goal</span><strong>{{ $profile['learning_goal'] ?? 'Choose a goal in Step 2' }}</strong></div>
            <div><span>Dashboard view</span><strong>{{ ucfirst($setup['dashboard_density'] ?? 'Comfortable') }}</strong></div>
            <div><span>Privacy notes</span><strong>{{ empty($setup['privacy_reviewed_at']) ? 'Not reviewed' : 'Reviewed' }}</strong></div>
            <div><span>Saved steps</span><strong>{{ count($done) }} of 5</strong></div>
          </div>
          @if($nextStep)<p class="sb08-missing">Before finishing, <a href="{{ route('studybuddy.onboarding.show', ['step'=>$nextStep]) }}">complete Step {{ $nextStep }}</a>.</p>@endif
        @endif
        <footer class="sb08-actions">
          @if($step > 1)<a class="sb08-back" href="{{ route('studybuddy.onboarding.show', ['step'=>$step-1]) }}">← Back</a>@endif
          <a class="sb08-skip" href="{{ route('dashboard') }}">Leave for now</a>
          <button class="sb08-next" type="submit" @if($step === 6 && $nextStep) disabled title="Complete all setup steps first" @endif>{{ $step === 6 ? ($finished ? 'Return to dashboard' : 'Finish setup') : 'Save & continue' }} <span aria-hidden="true">→</span></button>
        </footer>
      </form>
    </section>

    <aside class="sb08-preview-side" aria-label="Private profile preview">
      <div class="sb08-preview-shell">
        <span class="sb08-eyebrow">YOUR PRIVATE PREVIEW</span>
        <div id="sb08-react-preview"><div class="sb08-preview-fallback"><img src="{{ asset('assets/studybuddy-p07/icons/reading.webp') }}" alt="" aria-hidden="true"><strong>{{ $user->name }}</strong><p>{{ $profile['learning_goal'] ?? 'A universe of possibilities awaits.' }}</p></div></div>
        <p class="sb08-preview-note">Only a preview. Your public profile remains private unless you separately choose to share it.</p>
      </div>
      <script type="application/json" id="sb08-preview-data">@json($previewPayload)</script>
    </aside>
  </div>
</main>
@endsection
