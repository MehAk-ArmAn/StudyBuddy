<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Database\Seeders\StudyBuddyAdminSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * The administrator account is the way in to the whole CMS, so its seeding and
 * sign-in behaviour are covered directly.
 */
class AdminAccountTest extends TestCase
{
    use RefreshDatabase;

    private function seedAdmin(): void
    {
        $this->seed(StudyBuddyAdminSeeder::class);
    }

    /**
     * Regression: StudyBuddyAdminSeeder existed but was never registered in
     * DatabaseSeeder, so a fresh install had no users at all and nobody could
     * sign in to /admin.
     */
    public function test_the_default_seeder_creates_an_administrator(): void
    {
        $this->seed(\Database\Seeders\DatabaseSeeder::class);

        $admin = User::firstWhere('email', StudyBuddyAdminSeeder::DEFAULT_EMAIL);

        $this->assertNotNull($admin, 'Seeding must leave a usable admin account behind.');
        $this->assertTrue((bool) $admin->is_admin);
    }

    public function test_the_seeded_password_is_hashed_and_never_stored_in_plain_text(): void
    {
        $this->seedAdmin();

        $admin = User::firstWhere('email', StudyBuddyAdminSeeder::DEFAULT_EMAIL);

        $this->assertNotSame(StudyBuddyAdminSeeder::DEFAULT_PASSWORD, $admin->password);
        $this->assertStringNotContainsString(StudyBuddyAdminSeeder::DEFAULT_PASSWORD, $admin->password);
        $this->assertTrue(Hash::check(StudyBuddyAdminSeeder::DEFAULT_PASSWORD, $admin->password));
    }

    public function test_seeding_twice_does_not_create_a_second_administrator(): void
    {
        $this->seedAdmin();
        $this->seedAdmin();
        $this->seedAdmin();

        $this->assertSame(1, User::where('email', StudyBuddyAdminSeeder::DEFAULT_EMAIL)->count());
        $this->assertSame(1, User::where('is_admin', true)->count());
    }

    /**
     * An admin created by an earlier build is moved to the current address
     * rather than left behind as a second administrator.
     */
    public function test_an_administrator_from_an_earlier_build_is_adopted_not_duplicated(): void
    {
        User::forceCreate([
            'name' => 'Old Admin',
            'email' => 'admin@studybuddy.fun',
            'password' => Hash::make('whatever-it-used-to-be'),
            'is_admin' => true,
            'role' => 'admin',
        ]);

        $this->seedAdmin();

        $this->assertSame(1, User::where('is_admin', true)->count());
        $this->assertNull(User::firstWhere('email', 'admin@studybuddy.fun'));
        $this->assertNotNull(User::firstWhere('email', StudyBuddyAdminSeeder::DEFAULT_EMAIL));
    }

    // ── Signing in ──────────────────────────────────────────────────────

    public function test_an_admin_can_sign_in_and_reach_the_dashboard(): void
    {
        $this->seedAdmin();

        $this->post('/admin/login', [
            'email' => StudyBuddyAdminSeeder::DEFAULT_EMAIL,
            'password' => StudyBuddyAdminSeeder::DEFAULT_PASSWORD,
        ])->assertRedirect('/admin/dashboard');

        $this->assertAuthenticated();
        $this->get('/admin/dashboard')->assertOk();
    }

    public function test_a_signed_in_admin_can_reach_every_main_admin_area(): void
    {
        $this->seedAdmin();
        $admin = User::firstWhere('email', StudyBuddyAdminSeeder::DEFAULT_EMAIL);

        foreach ([
            '/admin/dashboard',
            '/admin/control-room',
            '/admin/control-room/apps',
            '/admin/control-room/apps/create',
            '/admin/control-room/users',
            '/admin/control-room/site-settings',
            '/admin/control-room/shell',
            '/admin/control-room/homepage-cms',
        ] as $path) {
            $this->actingAs($admin)->get($path)->assertOk("Admin should be able to open {$path}");
        }
    }

    public function test_the_wrong_password_is_rejected(): void
    {
        $this->seedAdmin();

        $this->post('/admin/login', [
            'email' => StudyBuddyAdminSeeder::DEFAULT_EMAIL,
            'password' => 'definitely-not-the-password',
        ])->assertRedirect();

        $this->assertGuest();
    }

    public function test_a_non_admin_is_kept_out_of_the_admin_area(): void
    {
        $learner = User::forceCreate([
            'name' => 'Learner',
            'email' => 'learner@studybuddy.test',
            'password' => Hash::make('secret-password'),
            'is_admin' => false,
            'role' => 'student',
        ]);

        $this->actingAs($learner)->get('/admin/dashboard')->assertRedirect();
        $this->actingAs($learner)->get('/admin/control-room')->assertRedirect();
    }
}
