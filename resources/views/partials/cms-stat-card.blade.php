@if(($stat->is_enabled ?? false) && (($stat->value ?? '') !== '' || ($stat->label ?? '') !== ''))
    <article class="stat-card">
        @if(($stat->display_type ?? '') === 'rating')
            <span class="rating-dots" aria-hidden="true"><i></i><i></i><i></i><i></i><i></i></span>
        @endif
        @if(($stat->value ?? '') !== '')<strong>{{ $stat->value }}</strong>@endif
        @if(($stat->label ?? '') !== '')<span>{{ $stat->label }}</span>@endif
        @if(($stat->helper_text ?? '') !== '')<small>{{ $stat->helper_text }}</small>@endif
    </article>
@endif
