@extends('layouts.app')

@section('title', $resourceTitle)
@section('body_class', 'page-shell page-admin-resource')

@section('content')
<section class="rewards-shell reveal-on-load" aria-labelledby="resource-title">
    <aside class="reward-sidebar glass-panel">
        <strong>CMS</strong>
        <a href="{{ route('admin.dashboard') }}">Dashboard</a>
        @foreach($resources as $slug => $label)
            <a @class(['active' => $slug === $resource]) href="{{ route('admin.resources.index', $slug) }}">{{ $label }}</a>
        @endforeach
    </aside>
    <div class="buddy-customizer glass-panel">
        <div class="customizer-top">
            <div>
                <p class="eyebrow">Admin Resource</p>
                <h1 id="resource-title">{{ $resourceTitle }}</h1>
            </div>
            <a class="button button-compact" href="{{ route('admin.dashboard') }}">Back to Dashboard</a>
        </div>
        <div class="costume-grid">
            <article class="costume-card unlocked"><span>✦</span><h3>Content</h3><p>CMS-backed records stay editable from this resource area.</p></article>
            <article class="costume-card unlocked"><span>◎</span><h3>Status</h3><p>Review, publish, and organize StudyBuddy platform content.</p></article>
            <article class="costume-card unlocked"><span>◆</span><h3>Preview</h3><p>Frontend pages keep consuming CMS data without route regressions.</p></article>
        </div>
    </div>
</section>
@endsection
