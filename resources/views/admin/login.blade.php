@extends('layouts.admin')

@section('title', 'Admin Login')

@section('content')
    <section class="admin-panel">
        <form method="post" action="{{ route('admin.login.attempt') }}">
            @csrf
            <input name="email" type="email" value="{{ old('email') }}">
            <input name="password" type="password">
            <button class="admin-button" type="submit">Login</button>
        </form>
    </section>
@endsection
