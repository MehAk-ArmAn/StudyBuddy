@php
    $label = $label ?? 'IMAGE_PLACEHOLDER';
    $variant = $variant ?? 'orbital';
    $caption = $caption ?? 'Future image slot';
    $class = $class ?? '';
@endphp

<div class="image-placeholder placeholder-{{ $variant }} {{ $class }}" data-placeholder="{{ $label }}">
    <span class="placeholder-shine"></span>
    <span class="placeholder-orbit"></span>
    <span class="placeholder-spark spark-a"></span>
    <span class="placeholder-spark spark-b"></span>
    <strong>{{ $label }}</strong>
    <small>{{ $caption }}</small>
</div>
