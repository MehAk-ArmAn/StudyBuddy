@php($type = $field['type'] ?? 'text')
@php($label = $field['label'] ?? str($name ?? '')->headline())
<label class="admin-field">
    <span>{{ $label }}</span>
    @if($type === 'textarea')
        <textarea name="{{ $name }}">{{ $value ?? '' }}</textarea>
    @elseif($type === 'select')
        <select name="{{ $name }}">
            @foreach(($field['options'] ?? []) as $optionValue => $optionLabel)
                <option value="{{ $optionValue }}" @selected((string) ($value ?? '') === (string) $optionValue)>{{ $optionLabel }}</option>
            @endforeach
        </select>
    @elseif($type === 'boolean')
        <input type="hidden" name="{{ $name }}" value="0">
        <input type="checkbox" name="{{ $name }}" value="1" @checked((bool) $value)>
    @else
        <input type="{{ $type }}" name="{{ $name }}" value="{{ $type === 'password' ? '' : ($value ?? '') }}">
    @endif
    @error($name)<small class="admin-error">{{ $message }}</small>@enderror
</label>
