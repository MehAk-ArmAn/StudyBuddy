<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Support\StudyBuddyAssetVersion;
use Tests\TestCase;

class StudyBuddyAssetVersionTest extends TestCase
{
    public function test_missing_public_asset_does_not_throw_and_uses_stable_version(): void
    {
        $this->assertSame(0, StudyBuddyAssetVersion::for('assets/__studybuddy_missing_asset_version_test__.css'));
    }

    public function test_present_public_asset_uses_its_real_modified_time(): void
    {
        $path = 'assets/js/studybuddy-v5-theme-boot.js';
        $full = public_path($path);

        if (!is_file($full)) {
            $this->markTestSkipped('Theme boot asset unavailable in this environment.');
        }

        $this->assertSame((int) filemtime($full), StudyBuddyAssetVersion::for($path));
    }
}
