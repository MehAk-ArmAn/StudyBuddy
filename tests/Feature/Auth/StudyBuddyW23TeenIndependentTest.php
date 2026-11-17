<?php
namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class StudyBuddyW23TeenIndependentTest extends TestCase
{
    use RefreshDatabase;

    private function fields(string $role, int $age): array
    {
        return [
            'name' => 'Learning Explorer',
            'email' => 'explorer@example.test',
            'role' => $role,
            'date_of_birth' => now()->subYears($age)->subDays(2)->toDateString(),
            'independent_goal' => 'Skill building',
            'learning_stage' => 'Year 10',
            'password' => 'Learning12345',
            'password_confirmation' => 'Learning12345',
            'safe_use_confirmed' => '1',
            'accuracy_confirmed' => '1',
        ];
    }

    public function test_a_15_year_old_can_register_as_an_independent_learner_without_adult_privileges(): void
    {
        Notification::fake();
        $this->post(route('register.store'), $this->fields('independent_learner', 15))
            ->assertRedirect(route('verification.notice'));
        $u = User::where('email', 'explorer@example.test')->firstOrFail();
        $this->assertSame('independent_learner', $u->role);
        $this->assertSame('not_required', $u->role_verification_status);
        $this->assertSame('not_required', $u->adult_verification_status);
        $this->assertNull($u->adult_verification_consent_at);
        $this->assertNull($u->verification_submitted_at);
        $this->assertSame('Skill building', $u->role_profile['learning_goal']);
        $this->assertFalse($u->isAdult());
        $this->assertFalse($u->isPowerRole());
        $this->assertFalse($u->canUseAdultControls());
        $this->assertFalse($u->needsAdultVerification());
        $this->assertFalse($u->needsRoleVerification());
        $this->assertFalse((bool) $u->is_admin);
    }

    public function test_13_year_old_independent_learners_are_eligible(): void
    {
        Notification::fake();
        $this->post(route('register.store'), $this->fields('independent_learner', 13))
            ->assertRedirect(route('verification.notice'));
        $this->assertSame('independent_learner', User::firstOrFail()->role);
    }

    public function test_under_13_independent_registration_is_rejected_without_creating_an_account(): void
    {
        $this->post(route('register.store'), $this->fields('independent_learner', 12))
            ->assertSessionHasErrors('date_of_birth');
        $this->assertDatabaseCount('users', 0);
    }

    public function test_adult_learner_still_requires_existing_adult_verification(): void
    {
        Notification::fake();
        $this->post(route('register.store'), $this->fields('independent_learner', 19))
            ->assertRedirect(route('verification.notice'));
        $u = User::firstOrFail();
        $this->assertSame('pending_admin_review', $u->role_verification_status);
        $this->assertSame('pending_review', $u->adult_verification_status);
        $this->assertTrue($u->needsAdultVerification());
        $this->assertFalse($u->canUseAdultControls());
    }

    public function test_underage_parent_and_teacher_accounts_remain_blocked(): void
    {
        foreach (['parent','teacher'] as $role) {
            $this->post(route('register.store'), $this->fields($role, 16))
                ->assertSessionHasErrors('date_of_birth');
        }
        $this->assertDatabaseCount('users', 0);
    }

    public function test_register_page_says_teens_can_learn_independently(): void
    {
        $this->get(route('register'))->assertOk()
            ->assertSee('Independent Learner (13+)')
            ->assertSee('teenagers aged 13–17');
    }
}
