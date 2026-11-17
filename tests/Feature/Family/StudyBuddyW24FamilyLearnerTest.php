<?php

namespace Tests\Feature\Family;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class StudyBuddyW24FamilyLearnerTest extends TestCase
{
    use RefreshDatabase;

    /** Family tests do not depend on a UserFactory (this project has none). */
    private int $userSequence = 0;

    private function makeUser(array $attributes = []): User
    {
        $this->userSequence++;

        return User::query()->create(array_replace([
            'name' => 'W24 Family Test User',
            'email' => 'w24-family-'.$this->userSequence.'@example.test',
            'password' => 'W24TestOnlyPassword123!',
            'role' => 'student',
            'date_of_birth' => now()->subYears(16)->toDateString(),
            'email_verified_at' => now(),
            'is_admin' => false,
        ], $attributes));
    }

    private function parent(bool $verified = true): User
    {
        return $this->makeUser([
            'name' => 'Family Guide', 'role' => 'parent',
            'date_of_birth' => now()->subYears(34)->toDateString(),
            'email_verified_at' => $verified ? now() : null,
            'role_verification_status' => 'pending_child_approval',
            'adult_verification_status' => 'pending_review',
            'is_admin' => false,
        ]);
    }

    private function childData(int $birthYear = 2024): array
    {
        return ['display_name' => 'Little Explorer', 'birth_year' => $birthYear,
            'favorite_subjects' => 'Shapes, music', 'current_focus' => 'Numbers',
            'learning_goal' => 'Play and discover'];
    }

    public function test_guests_cannot_access_private_family_profiles(): void
    {
        $this->get(route('studybuddy.family.learners'))->assertRedirect(route('login'));
    }

    public function test_verified_adult_parent_can_create_a_profile_for_a_very_young_child(): void
    {
        $parent = $this->parent();
        $this->actingAs($parent)->post(route('studybuddy.family.learners.store'), $this->childData())
            ->assertRedirect(route('studybuddy.family.learners'));
        $this->assertDatabaseHas('studybuddy_group_members', [
            'owner_id' => $parent->id, 'display_name' => 'Little Explorer',
            'email' => null, 'user_id' => null, 'status' => 'family_managed',
        ]);
        $member = DB::table('studybuddy_group_members')->first();
        $profile = json_decode($member->metrics_json, true);
        $this->assertSame(2024, $profile['birth_year']);
        $this->assertSame('Shapes, music', $profile['favorite_subjects']);
        $this->assertSame(0, $profile['moments']);
        $this->assertSame(1, DB::table('users')->count()); // No child login created.
        $this->actingAs($parent)->get(route('studybuddy.family.learners'))->assertOk()->assertSee('Little Explorer');
    }

    public function test_unverified_parent_and_teacher_cannot_manage_child_profiles(): void
    {
        $parent = $this->parent(false);
        $this->actingAs($parent)->post(route('studybuddy.family.learners.store'), $this->childData())->assertForbidden();
        $teacher = $this->makeUser(['role' => 'teacher', 'date_of_birth' => now()->subYears(29)->toDateString(), 'email_verified_at' => now()]);
        $this->actingAs($teacher)->post(route('studybuddy.family.learners.store'), $this->childData())->assertForbidden();
        $this->assertSame(0, DB::table('studybuddy_group_members')->count());
    }

    public function test_parent_cannot_access_another_parents_private_learner(): void
    {
        $first = $this->parent();
        $second = $this->parent();
        $this->actingAs($first)->post(route('studybuddy.family.learners.store'), $this->childData());
        $id = DB::table('studybuddy_group_members')->value('id');
        $this->actingAs($second)->patch(route('studybuddy.family.learners.update', $id), ['display_name' => 'Changed'])->assertNotFound();
        $this->actingAs($second)->post(route('studybuddy.family.learners.moment', $id))->assertNotFound();
        $this->assertSame('Little Explorer', DB::table('studybuddy_group_members')->value('display_name'));
    }

    public function test_family_logged_moments_change_journey_without_awarding_points(): void
    {
        $parent = $this->parent();
        $points = (int) $parent->cosmic_points;
        $this->actingAs($parent)->post(route('studybuddy.family.learners.store'), $this->childData());
        $id = DB::table('studybuddy_group_members')->value('id');
        $this->actingAs($parent)->post(route('studybuddy.family.learners.moment', $id), ['moment_note' => 'Counted red stars'])->assertRedirect();
        $json = json_decode(DB::table('studybuddy_group_members')->value('metrics_json'), true);
        $this->assertSame(1, $json['moments']);
        $this->assertSame('Counted red stars', $json['recent_moments'][0]['note']);
        $this->assertSame($points, (int) $parent->fresh()->cosmic_points);
        $this->assertSame(0, DB::table('studybuddy_point_transactions')->count());
    }

    public function test_verified_parent_can_connect_a_teen_without_losing_family_history(): void
    {
        $parent = $this->parent();
        $parent->update(['role_verification_status' => 'verified', 'adult_verification_status' => 'verified']);
        $teen = $this->makeUser([
            'role' => 'independent_learner', 'date_of_birth' => now()->subYears(15)->toDateString(),
            'email_verified_at' => now(), 'role_profile' => ['connect_code' => 'ABCD1234'],
        ]);
        $this->actingAs($parent)->post(route('studybuddy.family.learners.store'), $this->childData(2011));
        $id = DB::table('studybuddy_group_members')->value('id');
        $this->actingAs($parent)->post(route('studybuddy.family.learners.moment', $id), ['moment_note' => 'Practised planets']);
        $this->actingAs($parent)->post(route('studybuddy.family.learners.link', $id), [
            'learner_email' => $teen->email, 'connect_code' => 'ABCD1234',
        ])->assertRedirect();
        $row = DB::table('studybuddy_group_members')->where('id', $id)->first();
        $this->assertSame($teen->id, $row->user_id);
        $this->assertSame('connected_with_code', $row->status);
        $this->assertSame(1, json_decode($row->metrics_json, true)['moments']);
    }

    public function test_wrong_connect_code_cannot_claim_private_learning_history(): void
    {
        $parent = $this->parent();
        $parent->update(['role_verification_status' => 'verified', 'adult_verification_status' => 'verified']);
        $teen = $this->makeUser([
            'role' => 'student', 'date_of_birth' => now()->subYears(15)->toDateString(),
            'email_verified_at' => now(), 'role_profile' => ['connect_code' => 'GOOD1234'],
        ]);
        $this->actingAs($parent)->post(route('studybuddy.family.learners.store'), $this->childData());
        $id = DB::table('studybuddy_group_members')->value('id');
        $this->actingAs($parent)->post(route('studybuddy.family.learners.link', $id), [
            'learner_email' => $teen->email, 'connect_code' => 'BAD12345',
        ])->assertSessionHasErrors('connect_code');
        $this->assertNull(DB::table('studybuddy_group_members')->value('user_id'));
    }
}
