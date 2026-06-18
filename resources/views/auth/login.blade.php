@extends('layouts.app')

@section('content')
<section class="auth-wrap">
    <form class="auth-panel auth-form" method="POST" action="{{ route('login.store') }}">
        @csrf
        <h1>Login</h1>
        @if($errors->any()) <div class="auth-error">{{ $errors->first() }}</div> @endif
        <label>Email <input type="email" name="email" value="{{ old('email') }}" required></label>
        <label>Access key <input type="password" name="password" required></label>
        <button class="btn" type="submit">Open dashboard</button>
        <p><a href="{{ route('register') }}">Create account</a></p>
    </form>
</section>
@endsection
