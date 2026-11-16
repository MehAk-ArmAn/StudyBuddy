<article class="role-card consent-card" data-role-card>
    <p class="role-kicker">Safe connections</p>
    <h2>Your Connect Code</h2>
    <p>Only share this with a parent or teacher you trust. They need your email and this code before they can connect to you.</p>

    <div class="connect-code-box">
        <span data-connect-code>{{ $learnerData['connect_code'] ?? 'OPEN-DASH' }}</span>

        <div class="connect-code-actions">
            <button type="button" class="sb-code-copy" data-copy-connect-code>Copy code</button>

            <form method="POST" action="{{ route('studybuddy.learner.connect-code.regenerate') }}">
                @csrf
                <button type="submit" class="sb-code-reset">New code</button>
            </form>
        </div>
    </div>

    <p class="role-fineprint">Making a new code stops anyone using the old one.</p>
</article>
