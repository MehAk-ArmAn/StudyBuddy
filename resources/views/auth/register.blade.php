@extends('layouts.app')

@section('title', 'Create your StudyBuddy account')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/studybuddy-w22/register.css') }}?v={{ filemtime(public_path('assets/studybuddy-w22/register.css')) }}">
@endpush

@section('content')
<section class="sb-auth-stage sb22-stage" data-auth-page="register">
    <div class="sb-auth-card sb-auth-card-wide sb22-card">
        <aside class="sb-auth-intro sb22-intro" aria-label="Choose your StudyBuddy journey">
            <p class="eyebrow">LEARN · PLAY · GROW</p>
            <h1>Learn your way.<br><em>Make it yours.</em></h1>
            <p>Your own space in the StudyBuddy universe. Choose how you learn, discover your favorite subjects, and keep every milestone together.</p>

            <div class="sb22-route-choices" role="group" aria-label="Account type">
                <button type="button" data-role-preview="student" class="sb22-role" aria-pressed="false">
                    <span class="sb22-role-icon" aria-hidden="true">✦</span>
                    <span><strong>Student</strong><small>Apps, quests and learning goals</small></span>
                </button>
                <button type="button" data-role-preview="parent" class="sb22-role" aria-pressed="false">
                    <span class="sb22-role-icon" aria-hidden="true">♡</span>
                    <span><strong>Parent / Family</strong><small>Support and approved connections</small></span>
                </button>
                <button type="button" data-role-preview="teacher" class="sb22-role" aria-pressed="false">
                    <span class="sb22-role-icon" aria-hidden="true">▣</span>
                    <span><strong>Teacher</strong><small>Teaching tools, pending verification</small></span>
                </button>
                <button type="button" data-role-preview="independent_learner" class="sb22-role" aria-pressed="false">
                    <span class="sb22-role-icon" aria-hidden="true">✧</span>
                    <span><strong>Independent Learner</strong><small>Learn at your pace — teens welcome</small></span>
                </button>
            </div>
            <p class="sb22-age-callout"><span aria-hidden="true">✧</span> Independent learners can be teenagers too. Your age helps us apply the right protections.</p>
            <p class="sb22-trust"><span aria-hidden="true">✳</span> Your data and learning progress stay in the real StudyBuddy account system. School access requires separate approval.</p>
        </aside>

        <form method="POST" action="{{ route('register.store') }}" class="sb-auth-form sb22-form" data-sb22-register>
            @csrf
            <div class="sb22-step-header">
                <p class="sb22-kicker">Your learning profile</p>
                <span data-sb22-step-count aria-live="polite">Step 1 of 3</span>
            </div>
            <div class="sb22-progress" aria-hidden="true"><span data-sb22-progress></span></div>

            @if ($errors->any())
                <div class="sb-auth-error-summary" role="alert">
                    <div class="sb-auth-error-icon">!</div>
                    <div>
                        <strong>Almost there — please check:</strong>
                        <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                    </div>
                </div>
            @endif

            <section class="sb22-step" data-sb22-step="0" aria-label="Account basics">
                <h2>First, the essentials.</h2>
                <p class="sb22-description">Your display name is what others see. Keep private details out of it.</p>
                <div class="sb-form-grid">
                    <label><span>Display name *</span><input name="name" value="{{ old('name') }}" placeholder="Your learning nickname" autocomplete="nickname" maxlength="120" required>@error('name')<small>{{ $message }}</small>@enderror</label>
                    <label><span>Real name (private)</span><input name="real_name" value="{{ old('real_name') }}" placeholder="For account verification" maxlength="160" autocomplete="name">@error('real_name')<small>{{ $message }}</small>@enderror</label>
                    <label class="full"><span>Email *</span><input type="email" name="email" value="{{ old('email') }}" placeholder="you@example.com" maxlength="190" autocomplete="email" required>@error('email')<small>{{ $message }}</small>@enderror</label>
                    <label><span>I am a *</span>
                        <select name="role" id="sb-auth-role" required>
                            <option value="student" @selected(old('role','student') === 'student')>Student</option>
                            <option value="parent" @selected(old('role') === 'parent')>Parent</option>
                            <option value="teacher" @selected(old('role') === 'teacher')>Teacher</option>
                            <option value="independent_learner" @selected(old('role') === 'independent_learner')>Independent Learner (13+)</option>
                        </select>
                        @error('role')<small>{{ $message }}</small>@enderror
                    </label>
                    <label><span>Date of birth *</span><input type="date" name="date_of_birth" value="{{ old('date_of_birth') }}" min="1900-01-02" max="{{ now()->subDay()->toDateString() }}" autocomplete="bday" required>@error('date_of_birth')<small>{{ $message }}</small>@enderror</label>
                    <label class="full"><span>Country (optional)</span><input name="country" value="{{ old('country') }}" maxlength="90" placeholder="Where you are learning from">@error('country')<small>{{ $message }}</small>@enderror</label>
                </div>
                <p class="sb22-policy" data-sb22-age-notice aria-live="polite">Students under 13 need a guardian email. Independent learning is open from age 13; Parent and Teacher roles require age 18+.</p>
            </section>

            <section class="sb22-step" data-sb22-step="1" aria-label="Learning preferences">
                <h2>Shape your journey.</h2>
                <p class="sb22-description">These choices are saved to your account and can be changed in Profile Studio.</p>

                <section class="sb-role-dynamic-panel" data-panel="student" aria-label="Student setup">
                    <div class="sb-panel-icon" aria-hidden="true">✦</div><div><h3>Student setup</h3><p>Start with the subjects and goals that matter to you.</p></div>
                    <div class="sb-form-grid compact">
                        <label><span>Learning stage or focus</span><input name="learning_stage" value="{{ old('learning_stage') }}" maxlength="120" placeholder="Year 10, reading, science..."></label>
                        <label><span>Main goal</span><select name="study_goal"><option value="">Choose a goal</option>@foreach (['Build confidence','Improve focus','Practice daily','Have fun learning'] as $goal)<option value="{{ $goal }}" @selected(old('study_goal') === $goal)>{{ $goal }}</option>@endforeach</select></label>
                        <label class="full"><span>Favorite subjects</span><input name="favorite_subjects" value="{{ old('favorite_subjects') }}" maxlength="190" placeholder="Math, reading, languages..." data-sb22-subjects></label>
                    </div>
                    <div class="sb22-subject-chips" aria-label="Quick-select subjects" data-sb22-subject-buttons>
                        @foreach (['Math','English','Science','Geography','History','Languages'] as $subject)
                            <button type="button" data-subject="{{ $subject }}" aria-pressed="false">{{ $subject }}</button>
                        @endforeach
                    </div>
                    <label class="sb22-guardian" data-sb22-guardian><span>Parent or guardian email <span data-sb22-guardian-required>(needed for learners under 13)</span></span><input type="email" name="guardian_email" value="{{ old('guardian_email') }}" maxlength="190" placeholder="guardian@example.com">@error('guardian_email')<small>{{ $message }}</small>@enderror</label>
                </section>

                <section class="sb-role-dynamic-panel is-hidden" data-panel="parent" aria-label="Parent setup">
                    <div class="sb-panel-icon" aria-hidden="true">♡</div><div><h3>Parent setup</h3><p>Children's accounts are connected only through the platform's existing approval process.</p></div>
                    <label class="sb-block-label"><span>Child account email addresses (optional)</span><textarea name="child_emails_text" rows="2" maxlength="1200" placeholder="child@example.com, another@example.com">{{ old('child_emails_text') }}</textarea>@error('child_emails_text')<small>{{ $message }}</small>@enderror</label>
                    <div class="sb-form-grid compact">
                        <label><span>Parent goal</span><select name="parent_goal"><option value="">Choose a goal</option>@foreach (['Support homework','Track safe progress','Find learning apps','Encourage routine'] as $goal)<option @selected(old('parent_goal') === $goal)>{{ $goal }}</option>@endforeach</select></label>
                        <label><span>Child age range</span><select name="child_age_range"><option value="">Choose one</option>@foreach (['Under 7','7-10','11-13','14-16','Mixed ages'] as $range)<option @selected(old('child_age_range') === $range)>{{ $range }}</option>@endforeach</select></label>
                    </div>
                </section>

                <section class="sb-role-dynamic-panel is-hidden" data-panel="teacher" aria-label="Teacher setup">
                    <div class="sb-panel-icon" aria-hidden="true">▣</div><div><h3>Teacher setup</h3><p>Teaching and school privileges still require admin verification.</p></div>
                    <div class="sb-form-grid compact">
                        <label><span>Class level</span><select name="class_level"><option value="">Choose one</option>@foreach (['Primary','Middle school','High school','Mixed level','Tutoring'] as $level)<option @selected(old('class_level') === $level)>{{ $level }}</option>@endforeach</select></label>
                        <label><span>Teaching focus</span><input name="teaching_focus" value="{{ old('teaching_focus') }}" maxlength="190" placeholder="Reading, science, quiz prep..."></label>
                        <label><span>Organization name (optional)</span><input name="organization_name" value="{{ old('organization_name') }}" maxlength="190" placeholder="Your school or organization"></label>
                        <label><span>Position (optional)</span><input name="position_title" value="{{ old('position_title') }}" maxlength="140" placeholder="Class teacher, tutor..."></label>
                        <label class="full"><span>Organization email (optional)</span><input type="email" name="organization_email" value="{{ old('organization_email') }}" maxlength="190" placeholder="you@school.edu"></label>
                    </div>
                </section>

                <section class="sb-role-dynamic-panel is-hidden" data-panel="independent_learner" aria-label="Independent learner setup">
                    <div class="sb-panel-icon" aria-hidden="true">✧</div><div><h3>Independent learning</h3><p>Your learning, your pace. Open to teenagers aged 13–17 and adults 18+.</p></div>
                    <div class="sb-form-grid compact">
                        <label><span>Learning focus</span><input name="learning_stage" value="{{ old('learning_stage') }}" maxlength="120" placeholder="Study, professional skills, languages..."></label>
                        <label><span>Main goal</span><select name="independent_goal"><option value="">Choose a goal</option>@foreach (['Daily focus','Skill building','Revision routine','Creative learning','Career prep'] as $goal)<option @selected(old('independent_goal') === $goal)>{{ $goal }}</option>@endforeach</select></label>
                        <label class="full"><span>Daily study time</span><select name="daily_time"><option value="">Choose a routine</option>@foreach (['5-10 minutes','15-20 minutes','30 minutes','1 hour+','Flexible'] as $duration)<option @selected(old('daily_time') === $duration)>{{ $duration }}</option>@endforeach</select></label>
                    </div>
                </section>
            </section>

            <section class="sb22-step" data-sb22-step="2" aria-label="Account security">
                <h2>Make it yours. Keep it safe.</h2>
                <p class="sb22-description">Choose a password of at least 8 characters containing both letters and numbers.</p>
                <div class="sb-form-grid">
                    <label><span>Password *</span><div class="sb22-password-wrap"><input type="password" name="password" required minlength="8" autocomplete="new-password" aria-describedby="sb22-password-tip"><button type="button" data-sb22-toggle-password aria-label="Show password">Show</button></div>@error('password')<small>{{ $message }}</small>@enderror</label>
                    <label><span>Confirm password *</span><div class="sb22-password-wrap"><input type="password" name="password_confirmation" required minlength="8" autocomplete="new-password"><button type="button" data-sb22-toggle-password aria-label="Show confirmation">Show</button></div></label>
                </div>
                <p id="sb22-password-tip" class="sb22-password-tip" aria-live="polite">Strength: not entered</p>
                <label class="sb-check-row"><input type="checkbox" name="safe_use_confirmed" value="1" required @checked(old('safe_use_confirmed'))><span>I agree to use StudyBuddy safely and respectfully.</span></label>
                <label class="sb-check-row"><input type="checkbox" name="accuracy_confirmed" value="1" required @checked(old('accuracy_confirmed'))><span>I confirm the information I entered is accurate.</span></label>
                <p class="sb22-policy">Email verification is required. Parent, teacher and adult independent accounts retain their existing verification rules. Teen independent accounts have learner-only permissions. <a href="{{ url('/privacy-policy') }}">Privacy information</a></p>
            </section>

            <div class="sb22-navigation" data-sb22-controls>
                <button type="button" class="sb22-back" data-sb22-back hidden>Back</button>
                <button type="button" class="sb-auth-submit sb22-next" data-sb22-next>Continue <span aria-hidden="true">→</span></button>
                <button type="submit" class="sb-auth-submit sb22-submit" data-sb22-submit>Create my StudyBuddy account</button>
            </div>
            <p class="sb-auth-switch">Already a member? <a href="{{ route('login') }}">Log in instead</a></p>
        </form>
    </div>
</section>
@endsection

@push('scripts')
<script src="{{ asset('assets/studybuddy-w22/register.js') }}?v={{ filemtime(public_path('assets/studybuddy-w22/register.js')) }}" defer></script>
@endpush
