<article class="role-card consent-card" data-role-card>
    <p class="role-kicker">Safe connections</p>
    <h2>StudyBuddy Connect Code</h2>
    <p>Share this code only with a parent or teacher you trust. They need your email and this code before they can connect you to a family dashboard or class.</p>

    <div class="connect-code-box">
        <span>{{ $learnerData['connect_code'] ?? 'OPEN-DASH' }}</span>
    </div>

    <form method="POST" action="{{ route('studybuddy.learner.connect-code.regenerate') }}" class="role-form">
        @csrf
        <button type="submit">Regenerate code</button>
    </form>
</article>
