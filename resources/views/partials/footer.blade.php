<footer id="footer" class="site-footer">
    <div class="footer-brand">
        <a class="brand" href="{{ route('home') }}">
            @if (!empty($settings['logo_path']))
                <img src="{{ asset($settings['logo_path']) }}" alt="{{ $settings['brand_name'] ?? 'StudyBuddy' }}">
            @endif

            <span>{{ $settings['footer_brand_text'] ?? $settings['brand_name'] ?? 'StudyBuddy' }}</span>
        </a>

        @if (!empty($settings['footer_description']))
            <p>{{ $settings['footer_description'] }}</p>
        @endif
    </div>

    @foreach ($footerGroups as $groupName => $items)
        <div class="footer-group">
            <h3>{{ str($groupName)->replace('-', ' ')->replace('_', ' ')->title() }}</h3>

            @foreach ($items as $item)
                <a
                    href="{{ $item->url }}"
                    @if ($item->opens_new_tab)
                        target="_blank"
                        rel="noopener"
                    @endif
                >
                    {{ $item->label }}
                </a>
            @endforeach
        </div>
    @endforeach

    @if (!empty($settings['footer_legal_text']))
        <div class="footer-bottom">
            <p>{{ $settings['footer_legal_text'] }}</p>
        </div>
    @endif
</footer>