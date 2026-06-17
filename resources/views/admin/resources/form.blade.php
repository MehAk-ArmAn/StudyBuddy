@extends('layouts.admin')

@section('content')
    @php
        $parentParams = $parentParams ?? (isset($parent) ? (is_array($parent) ? $parent : [$parent]) : []);
        $formAction = $method === 'POST'
            ? route($route.'.store', $parentParams)
            : route($route.'.update', array_merge($parentParams, [$item]));
    @endphp

    <h2>{{ $title }}</h2>

    @if($errors->any())
        <div class="error">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form class="editor" method="POST" action="{{ $formAction }}">
        @csrf
        @if($method !== 'POST')
            @method($method)
        @endif

        @foreach($fields as $field)
            <label>
                {{ $field }}
                @if(in_array($field, ['is_enabled', 'opens_new_tab', 'is_active']))
                    <input type="checkbox" name="{{ $field }}" value="1" @checked(old($field, $item->$field ?? true))>
                @elseif(str_contains($field, 'body') || str_contains($field, 'subtitle') || str_contains($field, 'description') || $field === 'value' || $field === 'settings')
                    <textarea name="{{ $field }}">{{ old($field, is_array($item->$field) ? json_encode($item->$field) : $item->$field) }}</textarea>
                @else
                    <input name="{{ $field }}" value="{{ old($field, $item->$field) }}" @if(str_contains($field, 'url')) type="url" @else type="text" @endif>
                @endif
            </label>
        @endforeach

        <button>Save</button>
        <a class="button" href="{{ route($route.'.index', $parentParams) }}">Cancel</a>
    </form>
@endsection
