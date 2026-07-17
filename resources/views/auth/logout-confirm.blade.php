@extends('layouts.app')

@section('title', 'Log out')

@push('styles')
<style>
    .sb-logout-confirm{width:min(620px,calc(100% - 24px));margin:clamp(44px,10vw,110px) auto;padding:clamp(24px,5vw,42px);border:1px solid rgba(148,163,184,.22);border-radius:22px;background:rgba(15,23,42,.78);color:#eef2ff;text-align:center;box-shadow:0 24px 70px rgba(2,6,23,.3)}
    .sb-logout-confirm h1{margin:0;color:#fff;font-size:clamp(2rem,5vw,3.4rem);line-height:1.05}
    .sb-logout-confirm p{color:#cbd5e1;line-height:1.65}
    .sb-logout-actions{display:flex;flex-wrap:wrap;justify-content:center;gap:10px;margin-top:22px}
    .sb-logout-actions button,.sb-logout-actions a{min-height:46px;display:inline-flex;align-items:center;justify-content:center;border-radius:12px;padding:10px 16px;font:inherit;font-weight:800;text-decoration:none;cursor:pointer}
    .sb-logout-actions button{border:0;background:#7c3aed;color:#fff}
    .sb-logout-actions a{border:1px solid rgba(148,163,184,.3);background:transparent;color:#fff}
</style>
@endpush

@section('content')
<section class="sb-logout-confirm" aria-labelledby="logout-title">
    <h1 id="logout-title">Log out of StudyBuddy?</h1>
    <p>Your progress is already saved. You can sign back in whenever you’re ready.</p>
    <div class="sb-logout-actions">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit">Yes, log me out</button>
        </form>
        <a href="{{ route('dashboard') }}">Stay signed in</a>
    </div>
</section>
@endsection
