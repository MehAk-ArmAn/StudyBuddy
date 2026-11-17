{{-- Wave 10: real role-aware quick actions and a real My Quest POST form. No sample progress data. --}}
<section class="sbx-dash" aria-label="Your StudyBuddy shortcuts">
  <div class="sbx-dash__headline">
    <div><p class="sbx-dash__kicker">YOUR LEARNING SPACE</p><h2>Make today <em>your own.</em></h2><p>Pick one useful next step. Your work stays connected to your StudyBuddy account.</p></div>
    <span class="sbx-dash__mascot"><img src="{{ asset('assets/studybuddy-brand/pages/hero-dolphin-book.webp') }}" alt="" loading="lazy"></span>
  </div>
  <div class="sbx-dash__links">
    <a href="{{ url('/apps') }}"><span>✧</span><strong>Explore apps</strong><small>Find your next activity</small><b aria-hidden="true">↗</b></a>
    <a href="{{ url('/my-quest') }}"><span>◇</span><strong>My Quest</strong><small>Your saved learning goals</small><b aria-hidden="true">↗</b></a>
    <a href="{{ url('/learning-hub') }}"><span>✦</span><strong>Learning Hub</strong><small>Discover subjects & paths</small><b aria-hidden="true">↗</b></a>
    @if($role === 'teacher')
      <a href="{{ url('/teacher-studio') }}"><span>✎</span><strong>Teacher Studio</strong><small>Teaching tools and classes</small><b aria-hidden="true">↗</b></a>
    @elseif($role === 'parent')
      <a href="{{ url('/parents-center') }}"><span>♡</span><strong>Family Center</strong><small>Connect and support learning</small><b aria-hidden="true">↗</b></a>
    @else
      <a href="{{ url('/points-wallet') }}"><span>☆</span><strong>My achievements</strong><small>Your real activity history</small><b aria-hidden="true">↗</b></a>
    @endif
  </div>
  @if(in_array($role, ['student','independent_learner'],true))
    <form method="POST" action="{{ route('studybuddy.quests.store') }}" class="sbx-dash__composer">
      @csrf
      <div><label for="sbx-quest-title">Add a personal goal to My Quest</label><p>Your goal will be saved in your account, not just displayed on this page.</p></div>
      <div class="sbx-dash__formrow"><input id="sbx-quest-title" type="text" name="mission_title" maxlength="220" required placeholder="e.g. Practice fractions for 10 minutes" autocomplete="off"><button type="submit">Save goal ↗</button></div>
    </form>
  @endif
</section>
