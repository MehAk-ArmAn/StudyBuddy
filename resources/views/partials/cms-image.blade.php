@php($path = $path ?? null)
@if($path && \App\Support\Cms::assetExists($path))
    <img src="{{ asset($path) }}" alt="{{ $alt ?? '' }}">
@else
    <span class="cms-image-placeholder" aria-label="{{ $alt ?? 'Image placeholder' }}"><i></i></span>
@endif
