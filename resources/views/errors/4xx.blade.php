@include('errors._screen', [
    'code' => method_exists($exception, 'getStatusCode') ? $exception->getStatusCode() : 400,
    'title' => 'StudyBuddy could not open this request.',
    'message' => 'The requested action is unavailable in its current form. Return to the platform and try another path.',
    'image' => 'assets/images/errors/lost-path.svg',
    'imageAlt' => 'A StudyBuddy path leading back to the platform',
])
