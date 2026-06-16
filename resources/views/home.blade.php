@extends('layouts.app')
@section('content')
@foreach($sections as $section)
<section id="{{ $section->section_key }}" class="home-section section-{{ $section->section_type }}" @if($section->background_image_path) style="background-image:linear-gradient(135deg, rgba(13,10,40,.86), rgba(31,50,124,.7)), url('{{ asset($section->background_image_path) }}')" @endif>
  <div class="section-copy"><p class="eyebrow">{{ $section->eyebrow }}</p><h1>{{ $section->title }}</h1><p class="subtitle">{{ $section->subtitle }}</p><p>{{ $section->body }}</p><div class="actions">@if($section->button_label)<a class="btn primary" href="{{ $section->button_url ?: '#top' }}">{{ $section->button_label }}</a>@endif @if($section->secondary_button_label)<a class="btn ghost" href="{{ $section->secondary_button_url ?: '#footer' }}">{{ $section->secondary_button_label }}</a>@endif</div></div>
  @if($section->image_path)<img class="section-art" src="{{ asset($section->image_path) }}" alt="{{ $section->title }}">@endif
  @if($section->items->isNotEmpty())<div class="card-grid">@foreach($section->items as $item)<article class="glow-card">@if($item->badge_text)<span>{{ $item->badge_text }}</span>@endif @if($item->image_path)<img src="{{ asset($item->image_path) }}" alt="{{ $item->title }}">@endif<h3>{{ $item->title }}</h3><p>{{ $item->subtitle }}</p><p>{{ $item->body }}</p>@if($item->button_label)<a href="{{ $item->button_url ?: '#top' }}">{{ $item->button_label }}</a>@endif</article>@endforeach</div>@endif
</section>
@endforeach
@endsection
