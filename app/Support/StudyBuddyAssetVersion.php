<?php

declare(strict_types=1);

namespace App\Support;

/**
 * Central cache-busting version resolver for local StudyBuddy public assets.
 *
 * Test suites can replace Laravel's public path with an isolated folder.
 * A missing file must not turn an ordinary response (including a 404)
 * into a 500 just because its optional JS/CSS is unavailable there.
 */
final class StudyBuddyAssetVersion
{
    public static function for(string $assetPath): int
    {
        $file = public_path(ltrim($assetPath, '/'));

        if (!is_file($file)) {
            return 0;
        }

        $mtime = @filemtime($file);

        return $mtime === false ? 0 : (int) $mtime;
    }
}
