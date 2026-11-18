<?php

namespace Tests\Feature\Learning;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudyBuddyPack08OnboardingTest extends TestCase
{
    use RefreshDatabase;

    private function user(string $role = 'student', string $email = 'p08-learner@example.test'): User
    {
        return User::forceCreate([
            'name' => 'Private Learner',
            'email' => $email,
            'email_verified_at' => now(),
            'password' => bcrypt('ExamplePassword123!'),
            'role' => $role,
            'role_profile' => ['profile_theme' => 'cosmic', 'public_profile_enabled' => false, 'unlocked_profile_items' => ['profile_theme:cosmic']],
            'role_verification_status' => 'pending',
            'is_admin' => false,
        ]);
    }

    public function test_private_onboarding_requires_login(): void
    {
        $this->get('/getting-started')->assertRedirect('/login');
        $this->post('/getting-started', ['step' => 1])->assertRedirect('/login');
    }

    public function test_a_student_sees_six_steps_real_visuals_and_private_preview(): void
    {
        $this->actingAs($this->user())->get('/getting-started')->assertOk()
            ->assertSee('Your learning universe starts with you.')
            ->assertSee('STEP 1 OF 6')
            ->assertSee('sb08-react-preview', false)
            ->assertSee('studybuddy-p07/banners/learner.webp', false)
            ->assertSee('onboarding-preview.bundle.js', false)
            ->assertSee('Your identity');
    }

    public function test_saving_preferences_keeps_prior_profile_customizations_and_authorization(): void
    {
        $user = $this->user('teacher', 'p08-teacher@example.test');
        $this->actingAs($user)->post('/getting-started', [
            'step' => 3,
            'dashboard_density' => 'compact',
            'daily_minutes' => '20',
            'guide_level' => 'guided',
            'accent' => 'cyan',
        ])->assertRedirect(route('studybuddy.onboarding.show', ['step' => 4]));

        $user->refresh();
        $this->assertSame('teacher', $user->role);
        $this->assertSame('pending', $user->role_verification_status);
        $this->assertSame('cosmic', $user->role_profile['profile_theme']);
        $this->assertFalse($user->role_profile['public_profile_enabled']);
        $this->assertSame(['profile_theme:cosmic'], $user->role_profile['unlocked_profile_items']);
        $this->assertSame('compact', $user->role_profile['sb_p08_setup']['dashboard_density']);
        $this->assertSame('cyan', $user->role_profile['sb_p08_setup']['accent']);
        $this->actingAs($user)->get('/dashboard')->assertOk()->assertSee('data-p08-density="compact"', false);
    }

    public function test_subjects_and_goal_save_only_to_the_signed_in_account(): void
    {
        $one = $this->user();
        $other = $this->user('student', 'other-student@example.test');
        $this->actingAs($one)->post('/getting-started', [
            'step' => 2,
            'subjects' => ['Math', 'Reading', 'Science'],
            'learning_goal' => 'Practice fifteen minutes daily',
        ])->assertRedirect(route('studybuddy.onboarding.show', ['step' => 3]));
        $one->refresh(); $other->refresh();
        $this->assertSame('Math, Reading, Science', $one->role_profile['favorite_subjects']);
        $this->assertSame('Practice fifteen minutes daily', $one->role_profile['learning_goal']);
        $this->assertArrayNotHasKey('favorite_subjects', $other->role_profile);
        $this->actingAs($other)->get('/getting-started?step=6')->assertOk()
            ->assertDontSee('Practice fifteen minutes daily');
    }

    public function test_bad_subject_and_missing_privacy_review_are_rejected(): void
    {
        $this->actingAs($this->user())->post('/getting-started', [
            'step' => 2, 'subjects' => ['Not a real subject'], 'learning_goal' => 'Daily focus',
        ])->assertSessionHasErrors('subjects.0');
        $this->post('/getting-started', ['step' => 4])->assertSessionHasErrors('privacy_reviewed');
    }

    public function test_complete_requires_all_five_steps_and_only_updates_private_setup(): void
    {
        $user = $this->user();
        $this->actingAs($user)->post('/getting-started', ['step' => 6])
            ->assertRedirect(route('studybuddy.onboarding.show', ['step' => 1]));
        $steps = [
            ['step' => 1, 'display_name' => 'New Cosmic Learner', 'avatar_style' => 'dolphin-cadet'],
            ['step' => 2, 'subjects' => ['Math'], 'learning_goal' => 'Keep learning'],
            ['step' => 3, 'dashboard_density' => 'comfortable', 'daily_minutes' => '10', 'guide_level' => 'guided', 'accent' => 'violet'],
            ['step' => 4, 'privacy_reviewed' => '1'],
            ['step' => 5, 'next_destination' => 'apps'],
        ];
        foreach ($steps as $step) $this->post('/getting-started', $step)->assertRedirect();
        $this->post('/getting-started', ['step' => 6])->assertRedirect(route('dashboard'));
        $user->refresh();
        $this->assertSame('New Cosmic Learner', $user->name);
        $this->assertSame([1, 2, 3, 4, 5], $user->role_profile['sb_p08_setup']['steps']);
        $this->assertNotEmpty($user->role_profile['sb_p08_setup']['finished_at']);
        $this->assertFalse($user->role_profile['public_profile_enabled']);
        $this->get('/dashboard')->assertOk()->assertSee('Edit my setup');
    }
}
