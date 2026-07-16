@extends('layouts.admin')

@section('title', 'Contact Messages')

@section('content')
<section class="sb-control-resource pro-contact-admin">
    <div class="sb-control-panel">
        <div class="sb-control-panel-head wide">
            <div>
                <p class="sb-control-kicker">Support Inbox</p>
                <h2>Contact Messages</h2>
                <p>Messages sent from the public contact page appear here.</p>
            </div>
            <div class="sb-control-row-actions">
                <a href="{{ url('/contact') }}" target="_blank">Preview contact page</a>
            </div>
        </div>

        <div class="sb-control-stat-grid">
            <article><span>Total</span><strong>{{ $stats['total'] }}</strong><small>All messages</small></article>
            <article><span>New</span><strong>{{ $stats['new'] }}</strong><small>Unread</small></article>
            <article><span>Priority</span><strong>{{ $stats['priority'] }}</strong><small>Safety or data</small></article>
            <article><span>Resolved</span><strong>{{ $stats['resolved'] }}</strong><small>Closed</small></article>
        </div>
    </div>

    <div class="sb-control-panel">
        <div class="pro-admin-list">
            @forelse($messages as $message)
                <article class="message-row">
                    <div>
                        <strong>{{ $message->subject }}</strong>
                        <small>{{ $message->name }} • {{ $message->email }} • {{ $message->category }} • {{ $message->status }}</small>
                        <p>{{ Str::limit($message->message, 180) }}</p>
                    </div>
                    <a href="{{ route('admin.control-room.contact-messages.show', $message->id) }}">Open</a>
                </article>
            @empty
                <article>
                    <strong>No messages yet</strong>
                    <p>Contact messages will appear here.</p>
                </article>
            @endforelse
        </div>

        {{ $messages->links() }}
    </div>
</section>
@endsection
