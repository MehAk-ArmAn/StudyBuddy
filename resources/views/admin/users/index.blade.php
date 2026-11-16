@extends('layouts.admin')

@section('title', 'Users')

@section('content')
<div class="sb-res" data-admin-skip-unified>

    <header class="sb-res__header">
        <div>
            <h1>People</h1>
            <p>Everyone with a StudyBuddy account: learners, parents, teachers and administrators.</p>
        </div>

        <a class="sb-res__btn sb-res__btn--primary" href="{{ route('admin.users.create') }}">
            <svg viewBox="0 0 24 24" width="17" height="17" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true">
                <path d="M12 5v14M5 12h14"></path>
            </svg>
            Add person
        </a>
    </header>

    @if(session('status'))
        <div class="sb-res__note sb-res__note--good" role="status">{{ session('status') }}</div>
    @endif

    @if($users->isEmpty())
        <div class="sb-res__empty">
            <h2>No accounts yet</h2>
            <p>Add the first person, or wait for someone to sign up on the website.</p>
            <a class="sb-res__btn sb-res__btn--primary" href="{{ route('admin.users.create') }}">Add person</a>
        </div>
    @else
        <div class="sb-res__card">
            <div class="sb-res__table-wrap">
                <table class="sb-res__table sb-people">
                    <caption class="sb-visually-hidden">StudyBuddy accounts</caption>
                    <thead>
                        <tr>
                            <th scope="col">Person</th>
                            <th scope="col">Role</th>
                            <th scope="col">Access</th>
                            <th scope="col">Learning stage</th>
                            <th scope="col"><span class="sb-visually-hidden">Actions</span></th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($users as $user)
                            <tr>
                                <th scope="row" data-label="Person">
                                    <div class="sb-people__identity">
                                        <span class="sb-people__avatar" aria-hidden="true">
                                            {{ \Illuminate\Support\Str::of($user->name)->trim()->substr(0, 1)->upper() ?: '?' }}
                                        </span>
                                        <span class="sb-people__names">
                                            <strong>{{ $user->name }}</strong>
                                            <small>{{ $user->email }}</small>
                                        </span>
                                    </div>
                                </th>

                                <td data-label="Role">
                                    <span class="sb-res__badge is-role">
                                        {{ \Illuminate\Support\Str::of($user->role ?: 'member')->replace('_', ' ')->title() }}
                                    </span>
                                </td>

                                <td data-label="Access">
                                    @if($user->is_admin)
                                        <span class="sb-res__badge is-on">Administrator</span>
                                    @else
                                        <span class="sb-res__badge is-off">Standard</span>
                                    @endif
                                </td>

                                <td data-label="Learning stage">
                                    @if($user->learning_stage)
                                        <span class="sb-res__cell">{{ $user->learning_stage }}</span>
                                    @else
                                        <span class="sb-res__muted">Not set</span>
                                    @endif
                                </td>

                                <td data-label="Actions">
                                    <div class="sb-res__actions">
                                        <a class="sb-res__btn sb-res__btn--small" href="{{ route('admin.users.edit', $user) }}">Edit</a>

                                        @if($user->id === auth()->id())
                                            {{-- Deleting your own account would lock you out mid-session. --}}
                                            <span class="sb-res__muted sb-res__self">This is you</span>
                                        @else
                                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                                  onsubmit="return confirm('Delete {{ $user->name }}? This cannot be undone.')">
                                                @csrf @method('DELETE')
                                                <button class="sb-res__btn sb-res__btn--small sb-res__btn--danger" type="submit">Delete</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="sb-res__pagination">{{ $users->links() }}</div>
    @endif
</div>
@endsection
