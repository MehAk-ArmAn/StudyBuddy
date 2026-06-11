@php($brandName = \App\Support\Cms::setting('brand.name'))
@php($brandLogo = \App\Support\Cms::setting('brand.logo_path'))
@php($menuItems = \App\Support\Cms::menu('primary'))
<header class="nav-shell reveal-on-load">
    <a class="brand" href="{{ route('home') }}" aria-label="{{ $brandName }}">
        <span class="brand-mark">
            @include('partials.cms-image', ['path' => $brandLogo, 'alt' => $brandName, 'class' => ''])
        </span>
        @if($brandName !== '')
            <span class="brand-copy"><strong>{{ $brandName }}</strong></span>
        @endif
    </a>
    @if($menuItems->isNotEmpty())
        <nav class="nav-links" aria-label="{{ $brandName }}">
            @foreach($menuItems as $item)
                @php($href = \App\Support\CmsRoutes::url($item->route_name, $item->url))
                @if($item->label !== '')
                    <a href="{{ $href }}" @if($item->opens_new_tab) target="_blank" rel="noopener" @endif>{{ $item->label }}</a>
                @endif
            @endforeach
        </nav>
    @endif
</header>
