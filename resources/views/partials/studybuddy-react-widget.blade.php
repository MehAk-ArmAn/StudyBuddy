{{-- Usage: @include('partials.studybuddy-react-widget', ['name'=>'CuriosityMeter', 'props'=>['initialLevel'=>60]]) --}}
@php
    $sbWidgetName = (string) ($name ?? 'CuriosityMeter');
    $sbWidgetProps = json_encode($props ?? [], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
@endphp
<div data-sb-react="{{ $sbWidgetName }}" data-sb-react-props="{{ $sbWidgetProps }}" class="sbreact-widget" aria-label="{{ $label ?? 'Interactive StudyBuddy element' }}">
    <span class="sbreact-widget__fallback">{{ $fallback ?? 'Loading interactive element…' }}</span>
</div>
