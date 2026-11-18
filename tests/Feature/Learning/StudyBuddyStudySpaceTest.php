<?php

namespace Tests\Feature\Learning;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudyBuddyStudySpaceTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_private_study_space(): void
    {
        $this->get('/study-space')->assertRedirect('/login');
    }

    public function test_signed_in_learner_can_open_react_shell_without_fake_ai_responses(): void
    {
        $user = User::query()->create([
            'name' => 'Private Study Learner',
            'email' => 'private-studyshell@example.test',
            'password' => 'ExamplePassword123!',
            'role' => 'student',
        ]);
        $this->actingAs($user)->get('/study-space')->assertOk()
            ->assertSee('sb36-react-root', false)
            ->assertSee('study-space.bundle.js', false)
            ->assertDontSee('Demo AI response')
            ->assertDontSee('Other learner private notes');
    }

    public function test_learner_dashboard_links_to_the_private_study_space(): void
    {
        $user = User::query()->create([
            'name' => 'Study Space Dashboard Learner',
            'email' => 'studyshell-dashboard@example.test',
            'password' => 'ExamplePassword123!',
            'role' => 'student',
        ]);
        $this->actingAs($user)->get('/dashboard')->assertOk()
            ->assertSee('Study Space');
    }
}
