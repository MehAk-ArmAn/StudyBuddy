{{-- Compact local display preference. The existing V5 script persists light/dark/system. --}}
<div class="sb-v6-appearance" data-sb-v6-appearance>
  <button type="button" class="sb-v6-appearance__trigger" data-sb-v6-appearance-trigger aria-expanded="false" aria-haspopup="true" aria-controls="sb-v6-appearance-{{ isset($sbAppearanceId) ? $sbAppearanceId : 'global' }}" title="Website appearance">
    <span class="sb-v6-appearance__glyph" aria-hidden="true">◐</span><span class="sb-v6-appearance__label">Appearance</span><span aria-hidden="true">⌄</span>
  </button>
  <div class="sb-v6-appearance__menu" id="sb-v6-appearance-{{ isset($sbAppearanceId) ? $sbAppearanceId : 'global' }}" data-sb-v6-appearance-menu hidden>
    <span class="sb-v6-appearance__caption">COLOR THE UNIVERSE</span>
    <div class="sb-v5-theme-switch" role="group" aria-label="Website appearance" data-sb-v5-theme-controls>
      <button type="button" data-sb-v5-theme="light" aria-label="Use light theme" aria-pressed="false"><span aria-hidden="true">☀</span><span>Light</span></button>
      <button type="button" data-sb-v5-theme="dark" aria-label="Use dark theme" aria-pressed="false"><span aria-hidden="true">☾</span><span>Dark</span></button>
      <button type="button" data-sb-v5-theme="system" aria-label="Match device theme" aria-pressed="true"><span aria-hidden="true">◐</span><span>Auto</span></button>
    </div>
  </div>
</div>
