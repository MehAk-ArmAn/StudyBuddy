@if((bool) auth()->user()?->is_admin)
<section class="sb-admin-shortcut">
    <div>
        <p class="sbx-kicker">Admin editable content</p>
        <h2>StudyBuddy Content Studio</h2>
        <p>Edit Learning Hub, paths, rewards, parent and teacher pages, and safety content in one workspace.</p>
    </div>
    <a class="sbx-btn sbx-btn--primary" href="{{ route('studybuddy.admin.content.index') }}">Open Content Studio</a>
</section>
@endif
