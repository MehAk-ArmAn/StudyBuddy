@extends('layouts.admin')
@section('title', 'Confirm admin sign-in')
@section('content')
<section style="margin:8vh auto;padding:2.3rem;max-width:540px;background:#17203f;border:1px solid #49517e;border-radius:22px;color:white"><p style="font-size:.75rem;letter-spacing:.2em;color:#c4a8ff">ADMIN SECURITY</p><h1>One more step.</h1><p>A six-digit code was sent to the administrator's verified email. It expires in ten minutes.</p>
@if($errors->any())<div role="alert" style="padding:1rem;border-radius:10px;background:#5d263f">@foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div>@endif
<form method="POST" action="{{ route('admin.two-factor.verify') }}">@csrf<label style="display:block;margin:1.3rem 0">Six-digit security code<input name="code" type="text" inputmode="numeric" pattern="[0-9]{6}" minlength="6" maxlength="6" autocomplete="one-time-code" required autofocus style="width:100%;padding:1rem;border-radius:12px;border:1px solid #6e5db0;display:block;margin-top:.6rem;color:#222"></label><button type="submit" style="border:0;border-radius:12px;background:#8659f7;color:#fff;padding:.85rem 1.35rem;font-weight:800;cursor:pointer">Confirm secure sign-in</button></form><p><a href="{{ route('admin.login') }}">Cancel and sign in again</a></p>
</section>
@endsection
