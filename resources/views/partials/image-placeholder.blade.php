@php
    $label = $label ?? 'IMAGE_PLACEHOLDER';
    $variant = $variant ?? 'orbital';
    $caption = $caption ?? 'Future image slot';
    $class = $class ?? '';
    $src = $src ?? null;
    $assetUrl = \App\Support\Cms::imageUrl($src);
    $hasAsset = filled($assetUrl);
@endphp

<div class="asset-frame frame-{{ $variant }} {{ $class }} {{ $hasAsset ? 'has-real-asset' : 'is-fallback-art' }}" data-placeholder="{{ $label }}">
    @if($hasAsset)
        <img src="{{ $assetUrl }}" alt="{{ $caption }}" loading="lazy" onerror="this.closest('.asset-frame')?.classList.add('is-fallback-art'); this.closest('.asset-frame')?.classList.remove('has-real-asset'); this.closest('.asset-frame')?.querySelectorAll('[data-cms-fallback]')?.forEach((node) => node.hidden = false); this.remove();">
    @endif
    <span class="frame-shine" data-cms-fallback @if($hasAsset) hidden @endif></span>
    <span class="frame-orbit" data-cms-fallback @if($hasAsset) hidden @endif></span>
    <span class="frame-spark spark-a" data-cms-fallback @if($hasAsset) hidden @endif></span>
    <span class="frame-spark spark-b" data-cms-fallback @if($hasAsset) hidden @endif></span>
    <strong data-cms-fallback @if($hasAsset) hidden @endif>{{ $label }}</strong>
    <small data-cms-fallback @if($hasAsset) hidden @endif>{{ $caption }}</small>
</div>
