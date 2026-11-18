@extends('layouts.app')
@section('title', 'School Staff Board')
@push('styles')
<link rel="stylesheet" href="{{ asset('assets/studybuddy-p11b/school-os.css') }}?v=1">
@endpush
@section('content')
<div class="sbp11b-shell" id="sbp11b-school-os">
  <header class="sbp11b-hero">
    <div>
      <p class="sbp11b-kicker">SCHOOL OS · VERIFIED PRIVATE WORKSPACE</p>
      <h1>{{ $organization->name }} <span>Staff Board</span></h1>
      <p>Organize staff work across four lanes. Only approved school members can see these cards. Never put private learner information here.</p>
      <a href="{{ route('schools.workspace', $organization) }}" class="sbp11b-link">← Back to School Workspace</a>
    </div>
    <div class="sbp11b-hero-counter"><b>{{ $tasks->count() }}</b><span>Active staff tasks</span></div>
  </header>

  @if(session('status'))<p class="sbp11b-alert" role="status">{{ session('status') }}</p>@endif
  @if($errors->any())<div class="sbp11b-alert sbp11b-error" role="alert"><strong>Couldn't save:</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif

  @if($manager)
  <details class="sbp11b-create" id="sbp11b-new-task" @if($errors->any()) open @endif>
    <summary><span><strong>New staff task</strong><small>Schedule a coordination item for verified colleagues</small></span><span aria-hidden="true">＋</span></summary>
    <form method="POST" action="{{ route('schools.board.tasks.store', $organization) }}" class="sbp11b-form">
      @csrf
      <label>Task title <input name="title" minlength="3" maxlength="160" required value="{{ old('title') }}" placeholder="Plan assessment meeting"></label>
      <label class="sbp11b-wide">Staff notes (no student-identifying data) <textarea name="details" maxlength="4000" rows="3" placeholder="Agenda, internal next steps, links to approved resources...">{{ old('details') }}</textarea></label>
      <label>Status <select name="lane">@foreach($lanes as $lane)<option value="{{ $lane }}" @selected(old('lane','planned') === $lane)>{{ ucfirst($lane === 'doing' ? 'In progress' : $lane) }}</option>@endforeach</select></label>
      <label>Priority <select name="priority"><option value="normal">Normal</option><option value="low">Low</option><option value="high">High</option></select></label>
      <label>Due date <input type="date" name="due_on" value="{{ old('due_on') }}"></label>
      <label>Assign to <select name="assigned_to"><option value="">Unassigned</option>@foreach($staff as $person)<option value="{{ $person->user_id }}" @selected(old('assigned_to') == $person->user_id)>{{ $person->user?->name }} · {{ str_replace('_', ' ', $person->role) }}</option>@endforeach</select></label>
      <button type="submit" class="sbp11b-action">Create task</button>
    </form>
  </details>
  @endif
  <p class="sbp11b-helper">Select a card to open its synced detail panel. Assigned teachers can change their own task status; school managers edit all fields. Archived tasks remain stored for audit.</p>
  <div class="sbp11b-board" aria-label="School staff work board">
    @foreach(['planned' => 'Planned', 'doing' => 'In progress', 'review' => 'Review', 'done' => 'Done'] as $lane => $label)
    @php($items = $tasks->where('lane', $lane))
    <section class="sbp11b-lane" aria-label="{{ $label }} tasks">
      <button type="button" class="sbp11b-lane-toggle" aria-expanded="true" aria-controls="sbp11b-cards-{{ $lane }}">
        <span class="sbp11b-lane-title"><span class="sbp11b-dot sbp11b-dot-{{ $lane }}"></span>{{ $label }}</span>
        <span class="sbp11b-count">{{ $items->count() }}</span><span aria-hidden="true" class="sbp11b-chevron">⌄</span>
      </button>
      <div class="sbp11b-cards" id="sbp11b-cards-{{ $lane }}">
        @forelse($items as $task)
        <button type="button" class="sbp11b-card" data-task-id="{{ $task->id }}" data-title="{{ $task->title }}"
          data-details="{{ $task->details }}" data-lane="{{ $task->lane }}" data-priority="{{ $task->priority }}"
          data-due="{{ $task->due_on?->format('Y-m-d') }}" data-assigned="{{ $task->assigned_to }}"
          data-assignee="{{ $task->assignee?->name ?? 'Unassigned' }}"
          data-creator="{{ $task->creator?->name ?? 'Former staff member' }}"
          data-editable="{{ ($manager || $task->assigned_to === auth()->id()) ? '1' : '0' }}"
          data-update-url="{{ route('schools.board.tasks.update', [$organization, $task]) }}"
          data-archive-url="{{ route('schools.board.tasks.archive', [$organization, $task]) }}"
          aria-label="Open {{ $task->title }} task details">
          <span class="sbp11b-card-top"><small>{{ ucfirst($task->priority) }} priority</small><span aria-hidden="true">↗</span></span>
          <strong>{{ $task->title }}</strong>
          <span class="sbp11b-card-bottom"><small>{{ $task->assignee?->name ?? 'Unassigned' }}</small><small>{{ $task->due_on?->format('M j') ?? 'No deadline' }}</small></span>
        </button>
        @empty
        <p class="sbp11b-empty">No tasks in this lane.</p>
        @endforelse
      </div>
    </section>
    @endforeach
  </div>
</div>
<div class="sbp11b-backdrop" id="sbp11b-backdrop" hidden></div>
<aside class="sbp11b-panel" id="sbp11b-detail" role="dialog" aria-modal="true" aria-labelledby="sbp11b-detail-title" hidden>
  <div class="sbp11b-panel-top"><p>STAFF TASK DETAILS</p><button type="button" id="sbp11b-close" aria-label="Close task details">✕</button></div>
  <h2 id="sbp11b-detail-title">Task details</h2>
  <p id="sbp11b-read-only-hint" class="sbp11b-helper" hidden>You can view this task, but only its assigned teacher or school managers can edit it.</p>
  <p class="sbp11b-meta">Created by <span id="sbp11b-detail-creator"></span></p>
  <form id="sbp11b-edit-form" method="POST" action="" class="sbp11b-form sbp11b-panel-form">
    @csrf @method('PUT')
    @if($manager)
      <label>Title <input id="sbp11b-field-title" name="title" required minlength="3" maxlength="160"></label>
      <label>Details <textarea id="sbp11b-field-details" name="details" rows="5" maxlength="4000"></textarea></label>
      <label>Priority <select id="sbp11b-field-priority" name="priority"><option value="low">Low</option><option value="normal">Normal</option><option value="high">High</option></select></label>
      <label>Due date <input id="sbp11b-field-due" type="date" name="due_on"></label>
      <label>Assigned staff <select id="sbp11b-field-assigned" name="assigned_to"><option value="">Unassigned</option>@foreach($staff as $person)<option value="{{ $person->user_id }}">{{ $person->user?->name }} · {{ str_replace('_',' ',$person->role) }}</option>@endforeach</select></label>
    @else
      <p class="sbp11b-panel-readonly" id="sbp11b-view-details"></p>
      <p class="sbp11b-meta">Assigned to <b id="sbp11b-view-assignee"></b></p>
    @endif
    <label>Lane <select id="sbp11b-field-lane" name="lane">@foreach(['planned'=>'Planned', 'doing'=>'In progress', 'review'=>'Review', 'done'=>'Done'] as $key => $label)<option value="{{ $key }}">{{ $label }}</option>@endforeach</select></label>
    <button type="submit" id="sbp11b-save" class="sbp11b-action">Save changes</button>
  </form>
  @if($manager)
  <form id="sbp11b-archive-form" method="POST" action="" onsubmit="return confirm('Archive this task? It will be hidden from the board but retained in the database.')">
    @csrf @method('DELETE')<button type="submit" class="sbp11b-archive">Archive task</button>
  </form>
  @endif
</aside>
<script src="{{ asset('assets/studybuddy-p11b/board.js') }}?v=1" defer></script>
@endsection
