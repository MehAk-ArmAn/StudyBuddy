<label>
    <span>{{ $label ?? '' }}</span>
    @if(($type ?? 'text') === 'textarea')
        <textarea name="{{ $name ?? '' }}">{{ $value ?? '' }}</textarea>
    @else
        <input type="{{ $type ?? 'text' }}" name="{{ $name ?? '' }}" value="{{ $value ?? '' }}">
    @endif
</label>
