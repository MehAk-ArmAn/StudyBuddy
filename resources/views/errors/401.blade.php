@include('errors._screen', [
    'code' => 401,
    'title' => 'Please sign in to continue.',
    'message' => 'This StudyBuddy area is connected to a personal account. Sign in and open the page again.',
    'image' => 'assets/images/errors/session-expired.svg',
    'imageAlt' => 'Secure StudyBuddy account access',
])
