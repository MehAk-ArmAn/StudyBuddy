@php
    $roleImage = $settings['role_image_teacher'] ?? 'https://raw.githubusercontent.com/MehAk-ArmAn/StudyBuddy-Imgs/main/homepage-paths/path-teachers.png';
@endphp

<section class="role-grid teacher-readable-dashboard">
    <article class="role-card wide role-art-card teacher-art" data-role-card>
        <div>
            <p class="role-kicker">Teacher command centre</p>
            <h2>Classes, students, assignments, and activity.</h2>
            <p>Students can only be added with their StudyBuddy Connect Code. This keeps class rosters consent-based and safe.</p>
        </div>
        <img src="{{ $roleImage }}" alt="Teacher StudyBuddy path" onerror="this.src='{{ asset('assets/studybuddy-imgs/brand/logo-icon.png') }}'">
    </article>

    <article class="role-card wide" data-role-card>
        <div class="role-card-head">
            <div>
                <p class="role-kicker">Teacher overview</p>
                <h2>Classroom metrics</h2>
            </div>
            <a href="{{ url('/roles') }}">Role guide</a>
        </div>

        <div class="metric-grid">
            <article><span>Classes</span><strong>{{ $teacherData['metrics']['classes'] }}</strong></article>
            <article><span>Students</span><strong>{{ $teacherData['metrics']['students'] }}</strong></article>
            <article><span>Assignments</span><strong>{{ $teacherData['metrics']['assignments'] }}</strong></article>
            <article><span>Activity</span><strong>{{ $teacherData['metrics']['activity'] }}</strong></article>
        </div>
    </article>

    <article class="role-card teacher-control-panel" data-role-card>
        <p class="role-kicker">Organization</p>
        <h2>Teacher profile</h2>

        <form method="POST" action="{{ route('studybuddy.teacher.organization.update') }}" class="role-form">
            @csrf
            <label><span>Organization</span><input name="organization_name" value="{{ old('organization_name', $user->organization_name) }}" required placeholder="School, academy, studio..."></label>
            <label><span>Organization email</span><input type="email" name="organization_email" value="{{ old('organization_email', $user->organization_email) }}" placeholder="teacher@school.com"></label>
            <label><span>Position title</span><input name="position_title" value="{{ old('position_title', $user->position_title) }}" placeholder="Teacher, tutor, mentor..."></label>
            <button type="submit">Save organization</button>
        </form>
    </article>

    <article class="role-card teacher-control-panel" data-role-card>
        <p class="role-kicker">Create class</p>
        <h2>New classroom</h2>

        <form method="POST" action="{{ route('studybuddy.teacher.classes.store') }}" class="role-form">
            @csrf
            <label><span>Class name</span><input name="name" required placeholder="Year 8 Math Group"></label>
            <label><span>Organization</span><input name="organization_name" value="{{ $user->organization_name }}" placeholder="Optional"></label>
            <label><span>Description</span><textarea name="description" rows="4" placeholder="What this class is for..."></textarea></label>
            <button type="submit">Create class</button>
        </form>
    </article>

    <article class="role-card ultra-wide" data-role-card>
        <div class="role-card-head">
            <div>
                <p class="role-kicker">Roster management</p>
                <h2>Classes and verified students</h2>
            </div>
        </div>

        <div class="class-list-readable">
            @forelse($teacherData['groups'] as $group)
                @php($classMembers = $teacherData['members']->where('group_id', $group->id))
                <article class="class-room-card">
                    <div class="class-room-head">
                        <div>
                            <strong>{{ $group->name }}</strong>
                            <small>{{ $group->organization_name ?? 'No organization' }} • Invite code {{ $group->invite_code }}</small>
                            @if($group->description)<p>{{ $group->description }}</p>@endif
                        </div>
                    </div>

                    <form method="POST" action="{{ route('studybuddy.teacher.students.store', $group->id) }}" class="add-student-form">
                        @csrf
                        <label><span>Student email</span><input type="email" name="student_email" required placeholder="student@email.com"></label>
                        <label><span>Student Connect Code</span><input name="student_connect_code" required placeholder="Example: A1B2C3D4"></label>
                        <button type="submit">Add verified student</button>
                    </form>

                    <div class="student-roster-table">
                        <h3>Students in this class</h3>
                        @forelse($classMembers as $member)
                            <div class="student-row">
                                <span>{{ strtoupper(substr($member->display_name ?? $member->email ?? 'S', 0, 1)) }}</span>
                                <div>
                                    <strong>{{ $member->display_name ?? 'Student' }}</strong>
                                    <small>{{ $member->email }} • {{ $member->status }}</small>
                                </div>
                            </div>
                        @empty
                            <p class="empty-role-panel">No verified students yet. Ask students for their Connect Code.</p>
                        @endforelse
                    </div>
                </article>
            @empty
                <div class="empty-role-panel">Create your first class to start assigning tasks.</div>
            @endforelse
        </div>
    </article>

    <article class="role-card ultra-wide" data-role-card>
        <div class="role-card-head">
            <div>
                <p class="role-kicker">Assign work</p>
                <h2>Create task, quiz, or app mission</h2>
            </div>
        </div>

        <form method="POST" action="{{ route('studybuddy.teacher.assignments.store') }}" class="assignment-form readable-assignment-form">
            @csrf

            <label><span>Assign to class</span><select name="group_id"><option value="">Draft only</option>@foreach($teacherData['groups'] as $group)<option value="{{ $group->id }}">{{ $group->name }}</option>@endforeach</select></label>
            <label><span>Type</span><select name="type" required><option value="task">Task</option><option value="quiz">Quiz</option><option value="practice">App practice</option><option value="project">Project</option></select></label>
            <label><span>Title</span><input name="title" required placeholder="Fractions warm-up quiz"></label>
            <label><span>App world</span><select name="app_slug"><option value="">No specific app</option>@foreach($apps as $app)<option value="{{ $app->slug }}">{{ $app->name }}</option>@endforeach</select></label>
            <label><span>Due date</span><input type="datetime-local" name="due_at"></label>
            <label><span>Points reward</span><input type="number" name="points_reward" value="50" min="0"></label>
            <label class="full"><span>Instructions</span><textarea name="instructions" rows="5" placeholder="Explain exactly what students should do."></textarea></label>
            <label class="full"><span>Quiz / question bank</span><textarea name="question_bank" rows="6" placeholder="One question per line. Example: What is 7 x 8? | 56"></textarea></label>

            <button type="submit">Create assignment</button>
        </form>
    </article>

    <article class="role-card wide" data-role-card>
        <p class="role-kicker">Student activity</p>
        <h2>Latest verified student progress</h2>

        <div class="role-list spacious-list">
            @forelse($teacherData['studentActivity'] as $activity)
                <article>
                    <span>{{ ($activity->points ?? 0) >= 0 ? '+' : '' }}{{ $activity->points ?? 0 }}</span>
                    <div>
                        <strong>{{ $activity->learner_name ?? 'Student' }}</strong>
                        <small>{{ $activity->label ?? $activity->reason ?? 'Learning activity' }} • {{ $activity->created_at ?? 'Recently' }}</small>
                    </div>
                </article>
            @empty
                <div class="empty-role-panel">Student activity will appear here after verified students use apps or earn points.</div>
            @endforelse
        </div>
    </article>

    <article class="role-card wide" data-role-card>
        <p class="role-kicker">Recent assignments</p>
        <h2>Teacher-created tasks</h2>

        <div class="assignment-list spacious-list">
            @forelse($teacherData['assignments'] as $assignment)
                <article>
                    <span>{{ strtoupper(substr($assignment->type ?? 'T', 0, 1)) }}</span>
                    <div>
                        <strong>{{ $assignment->title }}</strong>
                        <small>{{ ucfirst($assignment->status) }} • {{ $assignment->due_at ? 'Due '.$assignment->due_at : 'No deadline' }}</small>
                    </div>
                </article>
            @empty
                <div class="empty-role-panel">No assignments yet.</div>
            @endforelse
        </div>
    </article>
</section>
