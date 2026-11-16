@extends('layouts.app')

@section('title', 'Create an account')

@section('content')
<section class="sb-auth-stage" data-auth-page="register">
    <div class="sb-auth-card sb-auth-card-wide">
        <div class="sb-auth-intro">
            <p class="eyebrow">Create account</p>
            <h1>Build your StudyBuddy dashboard.</h1>
            <p>Tell us who you are and we will set up the right kind of account. Parents link to their child's account instead of picking a learning stage.</p>

            <div class="sb-auth-flip-grid">
                <article data-role-preview="student" class="is-active"><span></span><strong>Students</strong><p>Apps, quests, points, and learning stages.</p></article>
                <article data-role-preview="parent"><span></span><strong>Parents</strong><p>Secure child links and progress support.</p></article>
                <article data-role-preview="teacher"><span></span><strong>Teachers</strong><p>Classroom tools and activity planning.</p></article>
                <article data-role-preview="independent_learner"><span></span><strong>Independent</strong><p>Self-paced goals and focus routines.</p></article>
            </div>
        </div>

        <form method="POST" action="{{ route('register') }}" class="sb-auth-form" novalidate>
            @csrf

            @if ($errors->any())
                <div class="sb-auth-error-summary" role="alert" aria-live="polite">
                    <div class="sb-auth-error-icon">!</div>
                    <div>
                        <strong>Almost there — fix these first:</strong>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif


            <div class="sb-form-grid">
                <label>
                    <span>Display name</span>
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="Example: Mehak" required autocomplete="name">
                    @error('name')<small>{{ $message }}</small>@enderror
                </label>

                <label>
                    <span>Real name</span>
                    <input type="text" name="real_name" value="{{ old('real_name') }}" placeholder="Used for trust and safety" autocomplete="name">
                    @error('real_name')<small>{{ $message }}</small>@enderror
                </label>

                <label class="full">
                    <span>Email</span>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="you@example.com" required autocomplete="email">
                    @error('email')<small>{{ $message }}</small>@enderror
                </label>

                <label>
                    <span>I am a</span>
                    <select name="role" id="sb-auth-role" required>
                        <option value="student" @selected(old('role','student')==='student')>Student</option>
                        <option value="parent" @selected(old('role')==='parent')>Parent</option>
                        <option value="teacher" @selected(old('role')==='teacher')>Teacher</option>
                        <option value="independent_learner" @selected(old('role')==='independent_learner')>Independent Learner</option>
                    </select>
                    @error('role')<small>{{ $message }}</small>@enderror
                </label>

                <label>
                    <span>Date of birth</span>
                    <input type="date" name="date_of_birth" value="{{ old('date_of_birth') }}">
                    @error('date_of_birth')<small>{{ $message }}</small>@enderror
                </label>

                <label>
                    <span>Country</span>
                    <input type="text" name="country" value="{{ old('country') }}" placeholder="Example: UAE">
                    @error('country')<small>{{ $message }}</small>@enderror
                </label>

                <label data-student-field>
                    <span>Learning stage or focus</span>
                    <input type="text" name="learning_stage" value="{{ old('learning_stage') }}" placeholder="Year 10, GCSE, reading, math, focus">
                    @error('learning_stage')<small>{{ $message }}</small>@enderror
                </label>
            </div>

            <section class="sb-role-dynamic-panel" data-panel="student">
                <div class="sb-panel-icon"></div>
                <div><h2>Student setup</h2><p>Choose what StudyBuddy should help with first.</p></div>
                <div class="sb-form-grid compact">
                    <label><span>Main goal</span><select name="study_goal"><option value="">Choose one</option><option>Build confidence</option><option>Improve focus</option><option>Practice daily</option><option>Have fun learning</option></select></label>
                    <label><span>Favorite subjects</span><input name="favorite_subjects" value="{{ old('favorite_subjects') }}" placeholder="Math, reading, spelling..."></label>
                </div>
            </section>

            <section class="sb-role-dynamic-panel is-hidden" data-panel="parent">
                <div class="sb-panel-icon"></div>
                <div><h2>Parent setup</h2><p>Add your child account emails. They are used for secure approved connections.</p></div>
                <label class="sb-block-label">
                    <span>Children's email addresses</span>
                    <textarea name="child_emails_text" rows="3" placeholder="child1@email.com, child2@email.com">{{ old('child_emails_text') }}</textarea>
                    @error('child_emails_text')<small>{{ $message }}</small>@enderror
                </label>
                <div class="sb-form-grid compact">
                    <label><span>Parent goal</span><select name="parent_goal"><option value="">Choose one</option><option>Support homework</option><option>Track safe progress</option><option>Find learning apps</option><option>Encourage routine</option></select></label>
                    <label><span>Child age range</span><select name="child_age_range"><option value="">Choose one</option><option>Under 7</option><option>7-10</option><option>11-13</option><option>14-16</option><option>Mixed ages</option></select></label>
                </div>
            </section>

            <section class="sb-role-dynamic-panel is-hidden" data-panel="teacher">
                <div class="sb-panel-icon"></div>
                <div><h2>Teacher setup</h2><p>Set the classroom style so your dashboard shows professional teaching tools.</p></div>
                <div class="sb-form-grid compact">
                    <label><span>Class level</span><select name="class_level"><option value="">Choose one</option><option>Primary</option><option>Middle school</option><option>High school</option><option>Mixed level</option><option>Tutoring</option></select></label>
                    <label><span>Teaching focus</span><input name="teaching_focus" value="{{ old('teaching_focus') }}" placeholder="Reading, quiz prep, focus skills..."></label>
                </div>
            </section>

            <section class="sb-role-dynamic-panel is-hidden" data-panel="independent_learner">
                <div class="sb-panel-icon"></div>
                <div><h2>Independent learner setup</h2><p>Choose a personal learning routine that fits your pace and style.</p></div>
                <div class="sb-form-grid compact">
                    <label><span>Learning goal</span><select name="independent_goal"><option value="">Choose one</option><option>Daily focus</option><option>Skill building</option><option>Revision routine</option><option>Creative learning</option><option>Career prep</option></select></label>
                    <label><span>Daily study time</span><select name="daily_time"><option value="">Choose one</option><option>5-10 minutes</option><option>15-20 minutes</option><option>30 minutes</option><option>1 hour+</option><option>Flexible</option></select></label>
                </div>
            </section>

            <div class="sb-form-grid">
                <label>
                    <span>Password</span>
                    <input type="password" name="password" required autocomplete="new-password">
                    @error('password')<small>{{ $message }}</small>@enderror
                </label>
                <label>
                    <span>Confirm access key</span>
                    <input type="password" name="password_confirmation" required autocomplete="new-password">
                </label>
            </div>

            <label class="sb-check-row">
                <input type="checkbox" name="safe_use_confirmed" value="1" required @checked(old('safe_use_confirmed'))>
                <span>I agree to use StudyBuddy safely and respectfully.</span>
            </label>

            <label class="sb-check-row">
                <input type="checkbox" name="accuracy_confirmed" value="1" required @checked(old('accuracy_confirmed'))>
                <span>I confirm the information I entered is accurate.</span>
            </label>

            <button class="sb-auth-submit" type="submit">Create dashboard</button>

            <p class="sb-auth-switch">
                Already have an account? <a class="sb-auth-switch" href="{{ route('login') }}">Log in instead</a>
            </p>
        </form>
    </div>
</section>
@endsection
