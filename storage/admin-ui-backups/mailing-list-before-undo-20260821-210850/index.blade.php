@extends('layouts.admin')

@section('title', 'Mailing List')

@push('styles')
<link
    rel="stylesheet"
    href="{{ asset('assets/css/studybuddy-mailing-list.css') }}?v={{ file_exists(public_path('assets/css/studybuddy-mailing-list.css')) ? filemtime(public_path('assets/css/studybuddy-mailing-list.css')) : time() }}"
>
@endpush

@section('content')
<div class="sb-mail-admin">
    <header class="sb-mail-admin__header">
        <div>
            <p class="sb-mail-admin__eyebrow">
                StudyBuddy Admin
            </p>

            <h1>Mailing List</h1>

            <p>
                Manage users who joined for launch notes,
                new learning apps and StudyBuddy updates.
            </p>
        </div>

        <div class="sb-mail-admin__header-actions">
            <a
                href="{{ route('admin.control-room.mailing-list.export') }}"
                class="sb-mail-admin__button sb-mail-admin__button--primary"
            >
                Export CSV
            </a>

            <a
                href="{{ url('/admin/control-room') }}"
                class="sb-mail-admin__button"
            >
                Control Room
            </a>
        </div>
    </header>

    @if(session('mailing_list_status'))
        <div class="sb-mail-admin__notice">
            {{ session('mailing_list_status') }}
        </div>
    @endif

    <section class="sb-mail-admin__stats">
        <article>
            <span>Total subscribers</span>
            <strong>{{ number_format($stats['total']) }}</strong>
        </article>

        <article>
            <span>Active</span>
            <strong>{{ number_format($stats['active']) }}</strong>
        </article>

        <article>
            <span>Unsubscribed</span>
            <strong>{{ number_format($stats['unsubscribed']) }}</strong>
        </article>

        <article>
            <span>Joined today</span>
            <strong>{{ number_format($stats['today']) }}</strong>
        </article>
    </section>

    <form
        method="GET"
        action="{{ route('admin.control-room.mailing-list.index') }}"
        class="sb-mail-admin__filters"
    >
        <label>
            <span>Search email</span>

            <input
                type="search"
                name="q"
                value="{{ $search }}"
                placeholder="Search subscribers"
                autocomplete="off"
            >
        </label>

        <label>
            <span>Status</span>

            <select name="status">
                <option value="">All statuses</option>

                <option
                    value="active"
                    @selected($status === 'active')
                >
                    Active
                </option>

                <option
                    value="unsubscribed"
                    @selected($status === 'unsubscribed')
                >
                    Unsubscribed
                </option>
            </select>
        </label>

        <button
            type="submit"
            class="sb-mail-admin__button sb-mail-admin__button--primary"
        >
            Apply filters
        </button>

        <a
            href="{{ route('admin.control-room.mailing-list.index') }}"
            class="sb-mail-admin__button"
        >
            Reset
        </a>
    </form>

    <section class="sb-mail-admin__table-panel">
        <div class="sb-mail-admin__table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Source</th>
                        <th>Subscribed</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($subscribers as $subscriber)
                        <tr>
                            <td>
                                <strong>{{ $subscriber->email }}</strong>
                            </td>

                            <td>
                                <span
                                    class="sb-mail-admin__status {{ $subscriber->status === 'active' ? 'is-active' : 'is-inactive' }}"
                                >
                                    {{ ucfirst($subscriber->status) }}
                                </span>
                            </td>

                            <td>
                                {{ \Illuminate\Support\Str::headline($subscriber->source) }}
                            </td>

                            <td>
                                {{
                                    $subscriber->subscribed_at
                                        ? $subscriber->subscribed_at->format('d M Y, H:i')
                                        : 'Not recorded'
                                }}
                            </td>

                            <td>
                                <div class="sb-mail-admin__row-actions">
                                    <form
                                        method="POST"
                                        action="{{ route('admin.control-room.mailing-list.update', $subscriber) }}"
                                    >
                                        @csrf
                                        @method('PATCH')

                                        <input
                                            type="hidden"
                                            name="status"
                                            value="{{ $subscriber->status === 'active' ? 'unsubscribed' : 'active' }}"
                                        >

                                        <button type="submit">
                                            {{
                                                $subscriber->status === 'active'
                                                    ? 'Unsubscribe'
                                                    : 'Reactivate'
                                            }}
                                        </button>
                                    </form>

                                    <form
                                        method="POST"
                                        action="{{ route('admin.control-room.mailing-list.destroy', $subscriber) }}"
                                        onsubmit="return confirm('Remove this subscriber permanently?')"
                                    >
                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="is-danger"
                                        >
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td
                                colspan="5"
                                class="sb-mail-admin__empty"
                            >
                                No mailing-list subscribers matched
                                the current filters.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="sb-mail-admin__pagination">
            {{ $subscribers->links() }}
        </div>
    </section>
</div>
@endsection
