<article class="role-card ultra-wide sb-class-progress" data-role-card aria-labelledby="sb-class-progress-title">
    <div class="sb-class-progress__heading">
        <div>
            <p class="role-kicker">Verified classes · live assignments</p>
            <h2 id="sb-class-progress-title">Class progress, without guesswork.</h2>
            <p>Only work assigned to your Connect Code–verified learners counts here.</p>
        </div>
        @if($overview['state'] === 'ready')
            <span class="sb-class-progress__pill">Private to your teaching account</span>
        @endif
    </div>

    @if($overview['state'] === 'locked')
        <p class="sb-class-progress__empty">Complete email, adult and educator verification to view class progress. Your classroom tools remain governed by the existing approval rules.</p>
    @elseif($overview['state'] === 'unavailable')
        <p class="sb-class-progress__empty">Class progress data is not available on this installation. Existing classes and assignments have not been changed.</p>
    @elseif($overview['classes']->isEmpty())
        <p class="sb-class-progress__empty">No active classes yet. Create a class below, then connect learners with their codes and assign work.</p>
    @else
        <div class="sb-class-progress__summary" aria-label="Class task progress summary">
            <div><strong>{{ $overview['totals']['classes'] }}</strong><span>Active classes</span></div>
            <div><strong>{{ $overview['totals']['connected'] }}</strong><span>Connected class places</span></div>
            <div><strong>{{ $overview['totals']['assigned'] }}</strong><span>Assigned learner tasks</span></div>
            <div><strong>{{ $overview['totals']['completed'] }}</strong><span>Marked complete</span></div>
        </div>
        <div class="sb-class-progress__classes">
            @foreach($overview['classes'] as $class)
                <section class="sb-class-progress__class" aria-label="{{ $class['name'] }} assignment progress">
                    <div class="sb-class-progress__class-top">
                        <strong>{{ $class['name'] }}</strong>
                        <span>{{ $class['completed'] }} / {{ $class['assigned'] }} tasks completed</span>
                    </div>
                    <div class="sb-class-progress__bar" role="progressbar" aria-label="{{ $class['name'] }} completion percentage" aria-valuenow="{{ $class['percent'] }}" aria-valuemin="0" aria-valuemax="100">
                        <span style="width: {{ $class['percent'] }}%"></span>
                    </div>
                    <p>{{ $class['connected'] }} connected learners · {{ $class['assignments'] }} class assignments · {{ $class['pending'] }} pending learner tasks</p>
                </section>
            @endforeach
        </div>
    @endif
    <p class="sb-class-progress__note">“Completed” means a learner marked an assigned task finished. It is not a teacher-approved grade, Math Sprint score, or proof of mastery. No private learning journal is shared here.</p>
</article>
