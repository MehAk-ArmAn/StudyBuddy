@php($brandName = \App\Support\Cms::setting('brand.name'))
@php($brandLogo = \App\Support\Cms::setting('brand.logo_path'))
@php($footerText = \App\Support\Cms::setting('footer.text'))
@php($columns = \App\Support\Cms::footerColumns())
<footer class="cosmic-footer reveal-on-load">
    <div class="footer-brand">
        <span class="brand-mark footer-mark">
            @include('partials.cms-image', ['path' => $brandLogo, 'alt' => $brandName, 'class' => ''])
        </span>
        <div>
            @if($brandName !== '')<h2>{{ $brandName }}</h2>@endif
            @if($footerText !== '')<p>{{ $footerText }}</p>@endif
        </div>
    </div>
    @if($columns->isNotEmpty())
        <div class="footer-columns">
            @foreach($columns as $column)
                <div>
                    @if($column->title !== '')<h3>{{ $column->title }}</h3>@endif
                    @foreach($column->links as $link)
                        @if($link->label !== '')
                            <a href="{{ \App\Support\CmsRoutes::url($link->route_name, $link->url) }}">{{ $link->label }}</a>
                        @endif
                    @endforeach
                </div>
            @endforeach
        </div>
    @endif
</footer>
