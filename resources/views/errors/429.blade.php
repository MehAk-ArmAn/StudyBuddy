@include('errors._screen', [
    'code' => 429,
    'title' => 'StudyBuddy needs a brief pause.',
    'message' => 'Several requests arrived very quickly. Wait a moment and try the action again.',
    'image' => 'assets/images/errors/session-expired.svg',
    'imageAlt' => 'StudyBuddy waiting before another request',
])
