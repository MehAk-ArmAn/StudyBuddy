@extends('layouts.admin')

@section('title', $method === 'POST' ? 'Create User' : 'Edit User')

@section('content')
@php
    $action = $method === 'POST' ? route('admin.users.store') : route('admin.users.update', $user);
@endphp

<section class="sb-control-resource">
    <form class="sb-control-panel sb-control-form" method="POST" action="{{ $action }}">
        @csrf
        @if($method !== 'POST') @method('PUT') @endif

        <div class="sb-control-panel-head wide">
            <div>
                <p class="sb-control-kicker">Users & Roles</p>
                <h2>{{ $method === 'POST' ? 'Create user' : 'Edit user' }}</h2>
                <p>Manage role, profile path, points, admin access, and optional password reset.</p>
            </div>

            <div class="sb-control-row-actions">
                <a href="{{ route('admin.users.index') }}">Cancel</a>
                <button class="primary" type="submit">Save user</button>
            </div>
        </div>

        <div class="sb-control-form-grid">
            <label>
                <span>Name</span>
                <input name="name" value="{{ old('name', $user->name) }}" required>
            </label>

            <label>
                <span>Real name</span>
                <input name="real_name" value="{{ old('real_name', $user->real_name) }}">
            </label>

            <label>
                <span>Email</span>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" required>
            </label>

            <label>
                <span>Role</span>
                <select name="role">
                    @foreach(['student'=>'Student','parent'=>'Parent','teacher'=>'Teacher','independent_learner'=>'Independent Learner','admin'=>'Admin'] as $value => $label)
                        <option value="{{ $value }}" @selected(old('role', $user->role ?: 'student') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </label>

            <label>
                <span>Learning Stage</span>
                <input name="learning_stage" value="{{ old('learning_stage', $user->learning_stage) }}">
            </label>

            <label>
                <span>Avatar Style</span>
                <input name="avatar_style" value="{{ old('avatar_style', $user->avatar_style ?: 'dolphin-cadet') }}">
            </label>

            <label>
                <span>Global PFP path</span>
                <input name="profile_photo_path" value="{{ old('profile_photo_path', $user->profile_photo_path) }}" placeholder="profile-photos/example.webp">
            </label>

            <label>
                <span>Cosmic Points</span>
                <input type="number" min="0" name="cosmic_points" value="{{ old('cosmic_points', $user->cosmic_points ?? 0) }}">
            </label>

            <label class="wide">
                <span>{{ $method === 'POST' ? 'Temporary Access Key' : 'New password / leave blank to keep current' }}</span>
                <input name="password" value="{{ $method === 'POST' ? 'ChangeMe12345!' : '' }}">
            </label>

            <label class="wide pro-check-row">
                <input type="checkbox" name="is_admin" value="1" @checked(old('is_admin', $user->is_admin))>
                <span>Admin access</span>
            </label>
        </div>
    </form>
</section>
@endsection
