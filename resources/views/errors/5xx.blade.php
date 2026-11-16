@include('errors._screen', [
    'code' => method_exists($exception, 'getStatusCode') ? $exception->getStatusCode() : 500,
    'title' => 'StudyBuddy needs a quick repair.',
    'message' => 'The platform could not complete this request. Your account and saved learning data remain protected.',
    'image' => 'assets/images/errors/repair.svg',
    'imageAlt' => 'StudyBuddy platform repair tools',
])
