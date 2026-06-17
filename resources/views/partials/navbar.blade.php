<nav class="nav">
    <a class="brand" href="{{ route('home') }}">
        @if (!empty($settings['logo_path']))
            <img src="{{ asset($settings['logo_path']) }}" alt="{{ $settings['brand_name'] ?? 'StudyBuddy' }}">
        @endif

        <span>{{ $settings['brand_name'] ?? 'StudyBuddy' }}</span>
    </a>

    <button class="nav-toggle" type="button" data-nav-toggle>
        Menu
    </button>

    <div class="nav-links" data-nav-links>
        @foreach ($navigationItems as $item)
            @if ($item->opens_new_tab)
                <a href="{{ $item->url }}" target="_blank" rel="noopener">
                    {{ $item->label }}
                </a>
            @else
                <a href="{{ $item->url }}">
                    {{ $item->label }}
                </a>
            @endif
        @endforeach

        @if (!empty($settings['global_cta_label']))
            <a class="nav-cta" href="{{ $settings['global_cta_url'] ?? '#top' }}">
                {{ $settings['global_cta_label'] }}
            </a>
        @endif
    </div>
</nav>
