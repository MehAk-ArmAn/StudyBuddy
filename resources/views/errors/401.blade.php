@include('errors._screen', [
    'code' => 401,
    'title' => 'You need to be signed in.',
    'message' => 'Sign in and we will bring you straight back here.',
    'image' => 'assets/images/errors/session-expired.svg',
    'imageAlt' => 'Secure StudyBuddy account access',
])
