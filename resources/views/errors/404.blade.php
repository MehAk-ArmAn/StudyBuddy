@include('errors._screen', [
    'code' => 404,
    'title' => 'This StudyBuddy path could not be found.',
    'message' => 'The page may have moved, changed or been removed. Return to the platform and choose another path.',
    'image' => 'assets/images/errors/lost-path.svg',
    'imageAlt' => 'A StudyBuddy map leading back to the platform',
])
