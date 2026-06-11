@php
    $assetFrom = function (string $directory, ?string $preferred = null): ?string {
        $base = 'assets/StudyBuddy-Imgs/' . trim($directory, '/');

        if ($preferred && file_exists(public_path($base . '/' . $preferred))) {
            return asset($base . '/' . $preferred);
        }

        $matches = glob(public_path($base . '/*.{png,webp,jpg,jpeg,svg}'), GLOB_BRACE) ?: [];

        return $matches === [] ? null : asset($base . '/' . basename($matches[0]));
    };

    $logoAsset = $assetFrom('01_brand/logo', 'studybuddy-logo-icon.png');
    $mascotAsset = $assetFrom('01_brand/mascot', 'dolphin-book-hero-large.png');
    $singleStarAsset = $assetFrom('03_effects/01_single-stars');
    $orbAsset = $assetFrom('03_effects/02_orbs');
    $cometAsset = $assetFrom('03_effects/03_comets');
    $largePlanetAsset = $assetFrom('04_planets/01_large-planets');
    $ringedPlanetAsset = $assetFrom('04_planets/02_ringed-planets');
    $bubbleAsset = $assetFrom('05_ui/bubbles');
    $uiStarAsset = $assetFrom('05_ui/stars');
@endphp

<div class="sb-floating-assets" aria-hidden="true">
    @if($largePlanetAsset)
        <img class="sb-floating-asset sb-floating-planet-large" src="{{ $largePlanetAsset }}" alt="" loading="lazy">
    @endif
    @if($ringedPlanetAsset)
        <img class="sb-floating-asset sb-floating-planet-ringed" src="{{ $ringedPlanetAsset }}" alt="" loading="lazy">
    @endif
    @if($orbAsset)
        <img class="sb-floating-asset sb-floating-orb" src="{{ $orbAsset }}" alt="" loading="lazy">
    @endif
    @if($cometAsset)
        <img class="sb-floating-asset sb-floating-comet" src="{{ $cometAsset }}" alt="" loading="lazy">
    @endif
    @if($singleStarAsset)
        <img class="sb-floating-asset sb-floating-star-one" src="{{ $singleStarAsset }}" alt="" loading="lazy">
    @endif
    @if($uiStarAsset)
        <img class="sb-floating-asset sb-floating-star-two" src="{{ $uiStarAsset }}" alt="" loading="lazy">
    @endif
    @if($bubbleAsset)
        <img class="sb-floating-asset sb-floating-bubble" src="{{ $bubbleAsset }}" alt="" loading="lazy">
    @endif
    @if($mascotAsset)
        <img class="sb-floating-asset sb-floating-mascot" src="{{ $mascotAsset }}" alt="" loading="lazy">
    @endif
    @if($logoAsset)
        <img class="sb-floating-asset sb-floating-logo-glow" src="{{ $logoAsset }}" alt="" loading="lazy">
    @endif
</div>
