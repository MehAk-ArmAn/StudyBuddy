@if(($button->label ?? '') !== '')
    <a class="button {{ $button->style ?? '' }}" href="{{ $button->url ?: '#' }}">{{ $button->label }}</a>
@endif
