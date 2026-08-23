@extends('layouts.admin')

@section('title', 'Role Tools')

@section('content')
<section class="sb-control-resource pro-role-tools">
    <div class="sb-control-panel">
        <div class="sb-control-panel-head wide">
            <div>
                <p class="sb-control-kicker">Role Tools</p>
                <h2>Parents, teachers, learners, classes, and assignments</h2>
                <p>Review the DB-powered role systems. Parent child connections, teacher classes, and assignments live here.</p>
            </div>

            <div class="sb-control-row-actions">
                <a href="{{ url('/roles') }}" target="_blank" rel="noopener">Preview roles</a>
                <a href="{{ url('/admin/control-room/users') }}">Users</a>
            </div>
        </div>

        <div class="sb-control-stat-grid">
            <article class="purple"><span>Students</span><strong>{{ number_format($stats['students'] ?? 0) }}</strong><small>Learner accounts</small></article>
            <article class="blue"><span>Parents</span><strong>{{ number_format($stats['parents'] ?? 0) }}</strong><small>Family accounts</small></article>
            <article class="cyan"><span>Teachers</span><strong>{{ number_format($stats['teachers'] ?? 0) }}</strong><small>Educator accounts</small></article>
            <article class="pink"><span>Assignments</span><strong>{{ number_format($stats['assignments'] ?? 0) }}</strong><small>Teacher-created tasks</small></article>
        </div>
    </div>

    <div class="pro-account-grid">
        <section class="sb-control-panel">
            <div class="sb-control-panel-head">
                <h2>Learning groups</h2>
                <span>{{ $groups->count() }} shown</span>
            </div>

            <div class="pro-admin-list">
                @forelse($groups as $group)
                    <article>
                        <strong>{{ $group->name }}</strong>
                        <small>{{ $group->type }} • {{ $group->organization_name ?? 'No organization' }} • {{ $group->invite_code ?? 'No code' }}</small>
                        <p>{{ $group->description ?: 'No description.' }}</p>
                    </article>
                @empty
                    <article><strong>No groups yet</strong><p>Parent family hubs and teacher classes will appear here.</p></article>
                @endforelse
            </div>
        </section>

        <section class="sb-control-panel">
            <div class="sb-control-panel-head">
                <h2>Assignments</h2>
                <span>{{ $assignments->count() }} shown</span>
            </div>

            <div class="pro-admin-list">
                @forelse($assignments as $assignment)
                    <article>
                        <strong>{{ $assignment->title }}</strong>
                        <small>{{ $assignment->type }} • {{ $assignment->status }} • {{ $assignment->due_at ?: 'No deadline' }}</small>
                        <p>{{ $assignment->instructions ?: 'No instructions.' }}</p>
                    </article>
                @empty
                    <article><strong>No assignments yet</strong><p>Teacher-created quizzes/tasks will appear here.</p></article>
                @endforelse
            </div>
        </section>
    </div>

    <section class="sb-control-panel">
        <div class="sb-control-panel-head">
            <h2>Group members</h2>
            <span>{{ $members->count() }} shown</span>
        </div>

        <div class="sb-control-table-wrap">
            <table class="sb-control-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Group</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($members as $member)
                        <tr>
                            <td>{{ $member->display_name ?: '—' }}</td>
                            <td>{{ $member->email ?: '—' }}</td>
                            <td>{{ $member->member_role ?: '—' }}</td>
                            <td>{{ $member->status ?: '—' }}</td>
                            <td>{{ $member->group_id }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">No members yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</section>
@endsection
