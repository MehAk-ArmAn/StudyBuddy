@extends('layouts.admin')

@section('page_title', 'Users')

@section('content')
    <div class="resource-header">
        <div>
            <h2>User Accounts</h2>
            <p>Manage students, parents, teachers, independent learners, and admin accounts.</p>
        </div>
        <a class="button" href="{{ route('admin.users.create') }}">Create User</a>
    </div>

    <div class="resource-grid users-grid">
        @foreach($users as $user)
            <article class="resource-card">
                <strong>{{ $user->name }}</strong>
                <span>{{ $user->email }}</span>
                <p>{{ ucfirst($user->role) }} @if($user->is_admin) · Admin @endif</p>
                <p>{{ $user->learning_stage ?: 'No learning stage yet' }}</p>
                <div class="row-actions">
                    <a href="{{ route('admin.users.edit', $user) }}">Edit</a>
                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}">
                        @csrf @method('DELETE')
                        <button onclick="return confirm('Delete this user?')">Delete</button>
                    </form>
                </div>
            </article>
        @endforeach
    </div>

    {{ $users->links() }}
@endsection
