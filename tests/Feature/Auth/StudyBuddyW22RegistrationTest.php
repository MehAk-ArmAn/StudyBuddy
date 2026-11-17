<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class StudyBuddyW22RegistrationTest extends TestCase
{
    use RefreshDatabase;

    private function validPayload(string $role = 'student'): array
    {
        return [
            'name' => 'Test Learner',
            'real_name' => 'Test Person',
            'email' => 'learner@example.test',
            'role' => $role,
            'date_of_birth' => now()->subYears(19)->subMonth()->toDateString(),
            'country' => 'UAE',
            'password' => 'Learning12345',
            'password_confirmation' => 'Learning12345',
            'safe_use_confirmed' => '1',
            'accuracy_confirmed' => '1',
        ];
    }

    public function test_registration_page_displays_all_roles_and_learning_setup(): void
    {
        $this->get(route('register'))
            ->assertOk()
            ->assertSee('Learn your way.')
            ->assertSee('Parent / Family')
            ->assertSee('Independent Learner')
            ->assertSee('family-managed')
            ->assertSee('studybuddy-w22/register.js');
    }

    public function test_student_date_of_birth_is_required_to_enforce_child_safeguards(): void
    {
        $payload = $this->validPayload();
        unset($payload['date_of_birth']);
        $this->post(route('register.store'), $payload)->assertSessionHasErrors('date_of_birth');
        $this->assertSame(0, User::count());
    }

    public function test_student_preferences_are_saved_under_dashboard_profile_keys(): void
    {
        Notification::fake();
        $payload = array_merge($this->validPayload(), [
            'study_goal' => 'Practice daily',
            'favorite_subjects' => 'Math, Science',
            'learning_stage' => 'Year 10',
            'parent_goal' => 'Track safe progress', // Cross-role field must be ignored.
        ]);
        $this->post(route('register.store'), $payload)->assertRedirect(route('verification.notice'));
        $user = User::where('email', 'learner@example.test')->firstOrFail();
        $this->assertAuthenticatedAs($user);
        $this->assertSame('student', $user->role);
        $this->assertSame('Practice daily', $user->role_profile['learning_goal']);
        $this->assertSame('Math, Science', $user->role_profile['favorite_subjects']);
        $this->assertSame('Year 10', $user->role_profile['current_focus']);
        $this->assertArrayNotHasKey('parent_goal', $user->role_profile);
        $this->assertFalse((bool) $user->is_admin);
    }

    public function test_under_13_direct_registration_requires_parent_managed_setup(): void
    {
        $payload = $this->validPayload();
        $payload['date_of_birth'] = now()->subYears(11)->toDateString();
        $payload['guardian_email'] = 'guardian@example.test';
        $this->post(route('register.store'), $payload)->assertSessionHasErrors('date_of_birth');
        $this->assertDatabaseCount('users', 0);
    }

    public function test_minor_cannot_select_adult_role(): void
    {
        $payload = $this->validPayload('teacher');
        $payload['date_of_birth'] = now()->subYears(16)->toDateString();
        $this->post(route('register.store'), $payload)->assertSessionHasErrors('date_of_birth');
        $this->assertSame(0, User::count());
    }

    public function test_teacher_preferences_do_not_bypass_verification(): void
    {
        Notification::fake();
        $payload = array_merge($this->validPayload('teacher'), [
            'class_level' => 'High school',
            'teaching_focus' => 'Science',
            'organization_name' => 'Example school',
            'study_goal' => 'Have fun learning',
        ]);
        $this->post(route('register.store'), $payload)->assertRedirect(route('verification.notice'));
        $user = User::where('email', $payload['email'])->firstOrFail();
        $this->assertSame('teacher', $user->role);
        $this->assertSame('pending_admin_review', $user->role_verification_status);
        $this->assertSame('pending_review', $user->adult_verification_status);
        $this->assertFalse($user->canUseAdultControls());
        $this->assertSame('High school', $user->role_profile['class_level']);
        $this->assertArrayNotHasKey('study_goal', $user->role_profile);
    }
}
