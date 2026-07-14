<section class="role-grid">
    <article class="role-card wide" data-role-card>
        <div class="role-card-head">
            <div>
                <p class="role-kicker">Teacher overview</p>
                <h2>Classroom command centre</h2>
            </div>
            <a href="{{ url('/roles') }}">Role guide</a>
        </div>

        <div class="metric-grid">
            <article><span>Classes</span><strong>{{ $teacherData['metrics']['classes'] }}</strong></article>
            <article><span>Students</span><strong>{{ $teacherData['metrics']['students'] }}</strong></article>
            <article><span>Assignments</span><strong>{{ $teacherData['metrics']['assignments'] }}</strong></article>
            <article><span>Drafts</span><strong>{{ $teacherData['metrics']['drafts'] }}</strong></article>
        </div>
    </article>

    <article class="role-card" data-role-card>
        <p class="role-kicker">Organization</p>
        <h2>Teacher profile</h2>

        <form method="POST" action="{{ route('studybuddy.teacher.organization.update') }}" class="role-form">
            @csrf
            <label>
                <span>Organization</span>
                <input name="organization_name" value="{{ old('organization_name', $user->organization_name) }}" required placeholder="School, academy, studio...">
            </label>
            <label>
                <span>Organization email</span>
                <input type="email" name="organization_email" value="{{ old('organization_email', $user->organization_email) }}" placeholder="teacher@school.com">
            </label>
            <label>
                <span>Position title</span>
                <input name="position_title" value="{{ old('position_title', $user->position_title) }}" placeholder="Teacher, tutor, mentor...">
            </label>
            <button type="submit">Save organization</button>
        </form>
    </article>

    <article class="role-card" data-role-card>
        <p class="role-kicker">Create class</p>
        <h2>New classroom</h2>

        <form method="POST" action="{{ route('studybuddy.teacher.classes.store') }}" class="role-form">
            @csrf
            <label>
                <span>Class name</span>
                <input name="name" required placeholder="Year 8 Math Group">
            </label>
            <label>
                <span>Organization</span>
                <input name="organization_name" value="{{ $user->organization_name }}" placeholder="Optional">
            </label>
            <label>
                <span>Description</span>
                <textarea name="description" rows="3" placeholder="What this class is for..."></textarea>
            </label>
            <button type="submit">Create class</button>
        </form>
    </article>

    <article class="role-card wide" data-role-card>
        <div class="role-card-head">
            <div>
                <p class="role-kicker">Classes</p>
                <h2>Roster management</h2>
            </div>
        </div>

        <div class="class-grid">
            @forelse($teacherData['groups'] as $group)
                <article>
                    <strong>{{ $group->name }}</strong>
                    <small>{{ $group->organization_name ?? 'No organization' }} • Code {{ $group->invite_code }}</small>
                    <p>{{ $group->description }}</p>

                    <form method="POST" action="{{ route('studybuddy.teacher.students.store', $group->id) }}" class="mini-inline-form">
                        @csrf
                        <input name="student_name" placeholder="Student name">
                        <input type="email" name="student_email" required placeholder="student@email.com">
                        <button type="submit">Add</button>
                    </form>
                </article>
            @empty
                <div class="empty-role-panel">Create your first class to start assigning tasks.</div>
            @endforelse
        </div>
    </article>

    <article class="role-card wide" data-role-card>
        <div class="role-card-head">
            <div>
                <p class="role-kicker">Assign work</p>
                <h2>Create task, quiz, or app mission</h2>
            </div>
        </div>

        <form method="POST" action="{{ route('studybuddy.teacher.assignments.store') }}" class="assignment-form">
            @csrf

            <label>
                <span>Assign to class</span>
                <select name="group_id">
                    <option value="">Draft only</option>
                    @foreach($teacherData['groups'] as $group)
                        <option value="{{ $group->id }}">{{ $group->name }}</option>
                    @endforeach
                </select>
            </label>

            <label>
                <span>Type</span>
                <select name="type" required>
                    <option value="task">Task</option>
                    <option value="quiz">Quiz</option>
                    <option value="practice">App practice</option>
                    <option value="project">Project</option>
                </select>
            </label>

            <label>
                <span>Title</span>
                <input name="title" required placeholder="Fractions warm-up quiz">
            </label>

            <label>
                <span>App world</span>
                <select name="app_slug">
                    <option value="">No specific app</option>
                    @foreach($apps as $app)
                        <option value="{{ $app->slug }}">{{ $app->name }}</option>
                    @endforeach
                </select>
            </label>

            <label>
                <span>Due date</span>
                <input type="datetime-local" name="due_at">
            </label>

            <label>
                <span>Points reward</span>
                <input type="number" name="points_reward" value="50" min="0">
            </label>

            <label class="full">
                <span>Instructions</span>
                <textarea name="instructions" rows="4" placeholder="Explain exactly what students should do."></textarea>
            </label>

            <label class="full">
                <span>Quiz / question bank</span>
                <textarea name="question_bank" rows="5" placeholder="One question per line. Example: What is 7 x 8? | 56"></textarea>
            </label>

            <button type="submit">Create assignment</button>
        </form>
    </article>

    <article class="role-card wide" data-role-card>
        <div class="role-card-head">
            <div>
                <p class="role-kicker">Assigned</p>
                <h2>Recent teacher tasks</h2>
            </div>
        </div>

        <div class="assignment-list">
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
