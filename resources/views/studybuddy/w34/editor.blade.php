@extends('layouts.admin')
@section('title', 'Learning Subjects · '.$app->name)
@section('content')
<div class="sb34-editor" data-admin-skip-unified>
  <div class="sb34-header"><span class="sb34-eyebrow">STUDYBUDDY / W34 CONTENT STANDARD</span>
    <h1>📚 Classify {{ $app->name }}</h1>
    <p>Choose the <strong>actual learning objective</strong>, not the game style. Classifications do not change existing app slugs, status, or downloads.</p>
    <a href="{{ route('studybuddy.w33.admin.edit', $app) }}">← Back to Showcase & Desktop</a>
  </div>
  @if(session('status'))<p class="sb34-notice" role="status">{{ session('status') }}</p>@endif
  @if($errors->any())<div class="sb34-errors" role="alert"><strong>Check these fields:</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
  <form method="POST" action="{{ route('studybuddy.w34.admin.update', $app) }}" class="sb34-form">@csrf
    <label>Primary academic subject <small>(one per app)</small>
      <select name="primary_subject"><option value="">Not assigned yet</option>
        @foreach($subjects as $slug => $subject)
          <option value="{{ $slug }}" @selected(old('primary_subject', $assignment->primary_subject ?? '') === $slug)>{{ $subject['label'] }} · {{ $subject['group'] }}</option>
        @endforeach
      </select>
    </label>
    <label>Learning stage <small>(optional, not a certified age rating)</small>
      <select name="learning_stage">
        <option value="">Not specified</option>
        @foreach(['early-years' => 'Early Years', 'primary' => 'Primary', 'secondary' => 'Secondary', 'all-ages' => 'All ages'] as $slug => $title)
          <option value="{{ $slug }}" @selected(old('learning_stage', $assignment->learning_stage ?? '') === $slug)>{{ $title }}</option>
        @endforeach
      </select>
    </label>
    <label>Topic tags <small>(comma-separated, e.g. fractions, addition)</small>
      <input type="text" name="topics" maxlength="400" value="{{ old('topics', $assignment->topics ?? '') }}" placeholder="addition, number sense, mental maths">
    </label>
    <label>What does the learner practise? <small>(specific and verifiable, no exaggerated claims)</small>
      <textarea name="learning_outcome" rows="4" maxlength="1200" placeholder="After playing, the learner can practise…">{{ old('learning_outcome', $assignment->learning_outcome ?? '') }}</textarea>
    </label>
    <div class="sb34-actions"><button type="submit">Save subject classification</button><a href="{{ route('studybuddy.w34.subjects.index') }}" target="_blank" rel="noopener">Preview public subjects ↗</a></div>
  </form>
  <p class="sb34-footnote">Only categories with assigned, published, role-visible games are shown publicly. No changes to existing student data or verified scores.</p>
</div>
@endsection
@push('styles')
<link rel="stylesheet" href="{{ asset('assets/studybuddy-w34/subjects.css') }}?v=34.1">
@endpush
