@php
    $sb25Path = '/'.ltrim(request()->path(),'/');
    $sb25Current = match(true) {
        str_starts_with($sb25Path, '/apps'), str_starts_with($sb25Path, '/play') => 'apps',
        str_starts_with($sb25Path, '/roles') => 'roles',
        str_starts_with($sb25Path, '/for-schools'), str_starts_with($sb25Path, '/schools') => 'schools',
        default => 'none',
    };
    $sb25User = auth()->user();
    $sb25Avatar = null;
    if ($sb25User && !empty($sb25User->profile_photo_path)) {
        $sb25Photo = $sb25User->profile_photo_path;
        $sb25Avatar = preg_match('/^https?:\/\//i', $sb25Photo) ? $sb25Photo : asset('storage/'.ltrim($sb25Photo,'/'));
    }
@endphp
<header class="sb25-header" data-sb25-header data-sb25-current="{{ $sb25Current }}">
  <div class="sb25-header-inner">
    <a class="sb25-brand" href="{{ url('/') }}" aria-label="StudyBuddy — back to homepage">
      <span class="sb25-mark"><img src="{{ asset('assets/studybuddy-base44-exact/logo.png') }}" alt="" width="42" height="42"></span>
      <span class="sb25-brand-text"><strong>StudyBuddy</strong><small>Learn · Play · Grow</small></span>
    </a>
    <div class="sb25-primary" data-sb25-active="{{ $sb25Current }}">
      <nav class="sb25-links-fallback" aria-label="Main navigation">
        <a href="{{ url('/apps') }}" @if($sb25Current==='apps') aria-current="page" @endif>Apps</a>
        <a href="{{ url('/roles') }}" @if($sb25Current==='roles') aria-current="page" @endif>Roles</a>
        <a href="{{ url('/for-schools') }}" @if($sb25Current==='schools') aria-current="page" @endif>For schools</a>
        <a href="{{ url('/#story') }}">How it works</a>
      </nav>
      {{-- One visible navbar at a time: SSR links remain until the React enhancement is ready. --}}
      <div class="sb25-react-slot" data-sb25-react-host data-sb25-active="{{ $sb25Current }}" aria-hidden="true" inert></div>
    </div>
    <div class="sb25-actions">
      <button class="sb25-icon-button sb25-search-open" type="button" data-sb25-search-open aria-label="Search StudyBuddy" aria-expanded="false" aria-controls="sb25-search-box"><svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m16 16 5 5"/></svg></button>
      @include('partials.studybuddy-v5-theme-switch', ['sbAppearanceId' => 'sb25'])
      @guest
        <a href="{{ url('/login') }}" class="sb25-login">Log in</a><a href="{{ url('/register') }}" class="sb25-join">Start free</a>
      @else
        <div class="sb25-account">
          <button type="button" class="sb25-account-button" data-sb25-account-toggle aria-expanded="false" aria-controls="sb25-account-menu">
            <span class="sb25-avatar">@if($sb25Avatar)<img src="{{ $sb25Avatar }}" alt="">@else {{ strtoupper(mb_substr($sb25User->display_name ?? $sb25User->name ?? 'S',0,1)) }}@endif</span>
            <span class="sb25-account-copy"><strong>{{ $sb25User->display_name ?? $sb25User->name ?? 'My space' }}</strong><small>{{ ucwords(str_replace('_',' ',(string)($sb25User->role ?? 'learner'))) }}</small></span><span aria-hidden="true">⌄</span>
          </button>
          <div class="sb25-account-menu" id="sb25-account-menu" hidden>
            <a href="{{ url('/dashboard') }}">My dashboard</a>
            <a href="{{ url('/my-quest') }}">My Quest</a>
            <a href="{{ url('/profile') }}">Profile Studio</a>
            @if(($sb25User->is_admin ?? false))<a href="{{ url('/admin/control-room') }}">Control Room</a>@endif
            <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit">Log out</button></form>
          </div>
        </div>
      @endguest
    </div>
    <button type="button" class="sb25-mobile-toggle" data-sb25-mobile-toggle aria-expanded="false" aria-controls="sb25-mobile-menu" aria-label="Open menu"><span></span><span></span><span></span></button>
  </div>
  <form class="sb25-search-box" id="sb25-search-box" role="search" method="GET" action="{{ url('/search') }}" hidden>
    <label class="sb25-sr" for="sb25-search-input">Search StudyBuddy</label>
    <input id="sb25-search-input" type="search" name="q" value="{{ request('q','') }}" placeholder="Search apps, subjects, pages…" autocomplete="off"><button type="submit">Search</button>
  </form>
  <nav class="sb25-mobile-menu" id="sb25-mobile-menu" aria-label="Mobile navigation" hidden>
    <a href="{{ url('/apps') }}">Apps</a><a href="{{ url('/roles') }}">Roles</a><a href="{{ url('/for-schools') }}">For schools</a><a href="{{ url('/#story') }}">How it works</a>
    @auth <a href="{{ url('/dashboard') }}">My dashboard</a><a href="{{ url('/my-quest') }}">My Quest</a> @endauth
    @guest <a href="{{ url('/login') }}">Log in</a><a href="{{ url('/register') }}">Start free</a> @endguest
  </nav>
</header>
