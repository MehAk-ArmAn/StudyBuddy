@include('errors._screen', [
    'code' => 500,
    'title' => 'Something broke on our side.',
    'message' => 'Not your fault. We have been told about it, so try again shortly.',
    'image' => 'assets/images/errors/repair.svg',
    'imageAlt' => 'StudyBuddy platform repair tools',
])
