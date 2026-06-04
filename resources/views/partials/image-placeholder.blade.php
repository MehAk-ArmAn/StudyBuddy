@php
    $label = $label ?? 'IMAGE_PLACEHOLDER';
    $variant = $variant ?? 'orbital';
    $caption = $caption ?? 'Future image slot';
    $class = $class ?? '';
    $src = $src ?? null;
    $hasAsset = $src && file_exists(public_path($src));
@endphp

<div class="asset-frame frame-{{ $variant }} {{ $class }} {{ $hasAsset ? 'has-real-asset' : 'is-fallback-art' }}" data-placeholder="{{ $label }}">
    @if($hasAsset)
        <img src="{{ asset($src) }}" alt="{{ $caption }}" loading="lazy">
    @else
        <span class="frame-shine"></span>
        <span class="frame-orbit"></span>
        <span class="frame-spark spark-a"></span>
        <span class="frame-spark spark-b"></span>
        <strong>{{ $label }}</strong>
        <small>{{ $caption }}</small>
    @endif
</div>
