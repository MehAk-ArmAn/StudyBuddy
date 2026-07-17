@include('errors._screen', [
    'code' => 419,
    'title' => 'Your secure session has expired.',
    'message' => 'Refresh the page before trying the action again. Your saved StudyBuddy progress remains safe.',
    'image' => 'assets/images/errors/session-expired.svg',
    'imageAlt' => 'StudyBuddy secure session clock',
])
