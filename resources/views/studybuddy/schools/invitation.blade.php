@extends('layouts.app')
@section('title', 'School invitation')
@push('styles')<link rel="stylesheet" href="{{ asset('assets/css/studybuddy-next-chapter.css') }}?v={{ filemtime(public_path('assets/css/studybuddy-next-chapter.css')) }}">@endpush
@section('content')
<div class="sb-school-shell"><p class="sb-next-eyebrow">PRIVATE INVITATION</p><h1>You're invited to learn together.</h1><p class="school-intro">{{ $invite->organization->name }} has invited <strong>{{ $invite->email }}</strong> as {{ str_replace('_',' ', $invite->role) }}. This invitation expires {{ $invite->expires_at->format('j M Y') }}.</p>
@if($errors->any())<div class="sb-school-notice error" role="alert">@foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div>@endif
<form method="POST" action="{{ route('schools.invitations.accept', request()->route('token')) }}">@csrf<button class="sb-next-button primary" type="submit">Accept invitation →</button></form><p class="school-footnote">You must sign in with this exact verified email and complete required educator verification first. Never forward an invitation link.</p></div>
@endsection
