@php
    $unreadContactCount = 0;

    try {
        if (\Illuminate\Support\Facades\Schema::hasTable('studybuddy_contact_messages')) {
            $unreadContactCount = \Illuminate\Support\Facades\DB::table('studybuddy_contact_messages')
                ->whereIn('status', ['new', 'read', 'in-progress'])
                ->count();
        }
    } catch (\Throwable $e) {
        $unreadContactCount = 0;
    }
@endphp

<nav class="sb-admin-quick-links" aria-label="Admin quick links">
    <a href="{{ url('/admin/control-room') }}">Control Room</a>
    <a href="{{ url('/admin/control-room/health') }}">Health Check</a>
    <a href="{{ url('/admin/control-room/homepage-cms') }}">Homepage CMS</a>
    <a class="messages-link" href="{{ url('/admin/control-room/messages') }}">
        Messages
        @if($unreadContactCount > 0)
            <span>{{ $unreadContactCount }}</span>
        @endif
    </a>
    <a href="{{ url('/admin/control-room/account') }}">Account</a>
    <a href="{{ url('/') }}" target="_blank" rel="noopener">Preview</a>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit">Logout</button>
    </form>
</nav>
