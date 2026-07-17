@include('errors._screen', [
    'code' => 503,
    'title' => 'StudyBuddy is preparing an update.',
    'message' => 'The platform is temporarily unavailable while maintenance or an update is completed.',
    'image' => 'assets/images/errors/repair.svg',
    'imageAlt' => 'StudyBuddy platform maintenance',
])
