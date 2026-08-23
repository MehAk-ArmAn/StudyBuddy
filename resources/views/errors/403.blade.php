@include('errors._screen', [
    'code' => 403,
    'title' => 'That part is not open to you.',
    'message' => 'If you think it should be, sign in with the right account or send us a message.',
    'image' => 'assets/images/errors/session-expired.svg',
    'imageAlt' => 'StudyBuddy access protection',
])
