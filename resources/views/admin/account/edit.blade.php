@extends('layouts.admin')

@section('title', 'Admin Account')

@section('content')
@php
    $photoUrl = null;
    if (!empty($adminUser->profile_photo_path)) {
        $photoUrl = preg_match('/^https?:\/\//i', $adminUser->profile_photo_path)
            ? $adminUser->profile_photo_path
            : asset('storage/' . ltrim($adminUser->profile_photo_path, '/'));
    }
@endphp

<section class="sb-control-resource pro-account-page">
    <div class="sb-control-panel pro-account-hero">
        <div>
            <p class="sb-control-kicker">Admin security</p>
            <h2>Account & password</h2>
            <p>Update the admin profile, profile picture, email, and password without leaving the Control Room.</p>
        </div>

        <div class="pro-admin-id-card">
            <span class="pro-avatar big">
                @if($photoUrl)
                    <img src="{{ $photoUrl }}" alt="{{ $adminUser->name }} profile picture">
                @else
                    {{ strtoupper(substr($adminUser->name ?? 'A', 0, 1)) }}
                @endif
            </span>
            <strong>{{ $adminUser->name }}</strong>
            <small>{{ $adminUser->email }}</small>
        </div>
    </div>

    <div class="pro-account-grid">
        <form class="sb-control-panel sb-control-form" method="POST" action="{{ route('admin.control-room.account.profile') }}" enctype="multipart/form-data">
            @csrf
            @method('PATCH')

            <div class="sb-control-panel-head wide">
                <div>
                    <p class="sb-control-kicker">Profile</p>
                    <h2>Admin identity</h2>
                </div>
                <button class="primary" type="submit">Save profile</button>
            </div>

            <div class="sb-control-form-grid">
                <label>
                    <span>Name</span>
                    <input name="name" value="{{ old('name', $adminUser->name) }}" required>
                </label>

                <label>
                    <span>Real name</span>
                    <input name="real_name" value="{{ old('real_name', $adminUser->real_name) }}">
                </label>

                <label>
                    <span>Email</span>
                    <input type="email" name="email" value="{{ old('email', $adminUser->email) }}" required>
                </label>

                <label>
                    <span>Profile picture</span>
                    <input type="file" name="profile_photo" accept="image/png,image/jpeg,image/jpg,image/webp,image/gif">
                </label>
            </div>
        </form>

        <form class="sb-control-panel sb-control-form" method="POST" action="{{ route('admin.control-room.account.password') }}">
            @csrf
            @method('PATCH')

            <div class="sb-control-panel-head wide">
                <div>
                    <p class="sb-control-kicker">Password</p>
                    <h2>Change access key</h2>
                    <p>Use a strong password. It will be hashed before storing.</p>
                </div>
                <button class="primary" type="submit">Update password</button>
            </div>

            <div class="sb-control-form-grid">
                <label class="wide">
                    <span>Current password</span>
                    <input type="password" name="current_password" required autocomplete="current-password">
                </label>

                <label>
                    <span>New password</span>
                    <input type="password" name="password" required autocomplete="new-password">
                </label>

                <label>
                    <span>Confirm new password</span>
                    <input type="password" name="password_confirmation" required autocomplete="new-password">
                </label>
            </div>
        </form>
    </div>
</section>
@endsection
