@extends('admin.layouts.admin')
@section('title', 'Dashboard')
@section('heading', 'Admin Dashboard')
@section('content')
<section class="admin-cms-grid">
    @foreach($stats as $label => $value)<article><small>{{ $label }}</small><strong>{{ $value }}</strong></article>@endforeach
</section>
<section class="admin-cms-panel"><h2>Quick Links</h2><div class="admin-cms-actions"><a href="{{ route('admin.pages') }}">Edit Page Content</a><a href="{{ route('admin.apps') }}">Manage Apps</a><a href="{{ route('admin.rewards') }}">Manage Rewards</a><a href="{{ route('admin.assets') }}">Review Assets</a></div></section>
@endsection
