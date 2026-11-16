@extends('layouts.admin')

@section('title', 'Contact Message')

@section('content')
<section class="sb-control-resource pro-contact-admin">
    <div class="sb-control-panel">
        <div class="sb-control-panel-head wide">
            <div>
                <p class="sb-control-kicker">Message Detail</p>
                <h2>{{ $message->subject }}</h2>
                <p>{{ $message->name }} • {{ $message->email }} • {{ $message->created_at }}</p>
            </div>
            <a href="{{ route('admin.control-room.contact-messages.index') }}">Back</a>
        </div>

        <div class="message-body-box">{!! nl2br(e($message->message)) !!}</div>
    </div>

    <form class="sb-control-panel sb-control-form" method="POST" action="{{ route('admin.control-room.contact-messages.update', $message->id) }}">
        @csrf
        @method('PATCH')

        <div class="sb-control-form-grid">
            <label>
                <span>Status</span>
                <select name="status">
                    @foreach(['new','read','in-progress','resolved','archived'] as $status)
                        <option value="{{ $status }}" @selected($message->status === $status)>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
            </label>

            <label>
                <span>Priority</span>
                <select name="priority">
                    @foreach(['low','normal','high','urgent'] as $priority)
                        <option value="{{ $priority }}" @selected($message->priority === $priority)>{{ ucfirst($priority) }}</option>
                    @endforeach
                </select>
            </label>
        </div>

        <button class="primary" type="submit">Save</button>
    </form>
</section>
@endsection
