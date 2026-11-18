{{-- W36 quick access. Does not replace role/permission-aware dashboard features. --}}
<section class="sb36-hub" aria-label="Quick learning actions">
 <div class="sb36-hub-head"><div><span class="sb36-hub-kicker">YOUR NEXT STEP</span><h2>Make room for a little progress.</h2></div><span aria-hidden="true">✦</span></div>
 <div class="sb36-hub-actions">
   <a href="{{ route('studybuddy.study-space') }}"><span class="sb36-hub-symbol" aria-hidden="true">✎</span><span><strong>Study Space</strong><small>Keep private questions and notes</small></span><span aria-hidden="true">↗</span></a>
   @if($role === 'teacher')
     <a href="{{ route('dashboard') }}#sb36-teacher-actions"><span class="sb36-hub-symbol" aria-hidden="true">▦</span><span><strong>Class tools</strong><small>Manage your classes and tasks</small></span><span aria-hidden="true">↗</span></a>
   @elseif($role === 'parent')
     <a href="{{ url('/profile') }}"><span class="sb36-hub-symbol" aria-hidden="true">♡</span><span><strong>Family profile</strong><small>Manage approved connections</small></span><span aria-hidden="true">↗</span></a>
   @else
     <a href="{{ url('/learning/math-sprint') }}"><span class="sb36-hub-symbol" aria-hidden="true">✧</span><span><strong>Math Sprint</strong><small>Play a real five-question round</small></span><span aria-hidden="true">↗</span></a>
   @endif
   <a href="{{ url('/apps') }}"><span class="sb36-hub-symbol" aria-hidden="true">◈</span><span><strong>Explore apps</strong><small>Discover your next learning activity</small></span><span aria-hidden="true">↗</span></a>
 </div>
</section>
