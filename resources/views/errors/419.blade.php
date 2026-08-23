@include('errors._screen', [
    'code' => 419,
    'title' => 'That page sat still for too long.',
    'message' => 'We ended the session to keep your account safe. Go back and try once more.',
    'image' => 'assets/images/errors/session-expired.svg',
    'imageAlt' => 'StudyBuddy secure session clock',
])
