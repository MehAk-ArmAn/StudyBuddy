@php($resolved = \App\Support\CmsImages::url($path ?? null))
@if($resolved)
    <img src="{{ $resolved }}" alt="{{ $alt ?? '' }}" class="{{ $class ?? '' }}" loading="lazy" onerror="this.style.display='none';this.nextElementSibling.hidden=false;">
    <span class="image-placeholder premium-placeholder {{ $placeholderClass ?? '' }}" hidden></span>
@else
    <span class="image-placeholder premium-placeholder {{ $placeholderClass ?? '' }}" role="img" aria-label="{{ $alt ?? '' }}"></span>
@endif
