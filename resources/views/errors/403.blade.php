@include('errors._screen', [
    'code' => 403,
    'title' => 'This area needs different access.',
    'message' => 'Your current role does not have permission to open this StudyBuddy area.',
    'image' => 'assets/images/errors/session-expired.svg',
    'imageAlt' => 'StudyBuddy access protection',
])
