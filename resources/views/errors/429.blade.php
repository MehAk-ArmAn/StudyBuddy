@include('errors._screen', [
    'code' => 429,
    'title' => 'That was a lot of clicks.',
    'message' => 'Give it a few seconds, then try again.',
    'image' => 'assets/images/errors/session-expired.svg',
    'imageAlt' => 'StudyBuddy waiting before another request',
])
