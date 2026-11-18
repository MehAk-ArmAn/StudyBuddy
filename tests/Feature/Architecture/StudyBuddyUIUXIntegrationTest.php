<?php

namespace Tests\Feature\Architecture;

use Tests\TestCase;

final class StudyBuddyUIUXIntegrationTest extends TestCase
{
    public function test_rewards_admin_navigation_is_discoverable_only_behind_head_check(): void
    {
        $layout = file_get_contents(resource_path('views/layouts/admin.blade.php'));
        $this->assertStringContainsString('admin.control-room.rewards.index', $layout);
        $this->assertStringContainsString('StudyBuddyRewardIntegrity::isHead', $layout);
        $this->assertStringContainsString('@if($canManageRewards)', $layout);
        $this->assertStringContainsString('SB_UIUX_REWARDS_NAV_V1', $layout);
    }

    public function test_rewards_screen_uses_shared_admin_layout_and_css_not_inline_styles(): void
    {
        $blade = file_get_contents(resource_path('views/admin/p11f/rewards.blade.php'));
        $css = file_get_contents(public_path('assets/studybuddy-p11f/rewards.css'));
        $this->assertStringContainsString("@extends('layouts.admin')", $blade);
        $this->assertStringContainsString("@push('styles-late')", $blade);
        $this->assertStringNotContainsString('style=', $blade);
        $this->assertStringContainsString('SB_UIUX_GOVERNANCE_V1_START', $css);
    }
}
