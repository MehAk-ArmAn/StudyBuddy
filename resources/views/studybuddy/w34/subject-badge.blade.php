@php
  $sb34Tag = $app->subjectAssignment;
  $sb34Subject = $sb34Tag ? config('studybuddy_w34_subjects.subjects.'.$sb34Tag->primary_subject) : null;
@endphp
@if($sb34Subject)
<div class="sb34-detail-badge" aria-label="Learning subject">
  <span>✦ Subject</span>
  <a href="{{ route('studybuddy.w34.subjects.show', $sb34Tag->primary_subject) }}">{{ $sb34Subject['label'] }} ↗</a>
  @if($sb34Tag->learning_outcome)<p>{{ $sb34Tag->learning_outcome }}</p>@endif
</div>
@endif
