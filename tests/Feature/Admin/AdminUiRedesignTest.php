<?php

namespace Tests\Feature\Admin;

use App\Models\StudyBuddyMiniAppPlatform;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AdminUiRedesignTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::forceCreate([
            'name' => 'Interface Admin',
            'email' => 'admin-ui@studybuddy.test',
            'password' => bcrypt('secret-password'),
            'is_admin' => true,
            'role' => 'admin',
        ]);
    }

    public function test_the_admin_shell_uses_grouped_canonical_navigation(): void
    {
        $this->actingAs($this->admin())
            ->get('/admin/control-room')
            ->assertOk()
            ->assertSeeInOrder(['Overview', 'Apps', 'Content', 'People', 'Website', 'System'])
            ->assertSee(route('admin.control-room.apps.index'), false)
            ->assertSee(route('admin.control-room.apps.create'), false)
            ->assertSee('All Apps')
            ->assertSee('Add App')
            ->assertSee('StudyBuddy')
            ->assertSee('Control Room');
    }

    public function test_dashboard_metrics_are_backed_by_real_database_values(): void
    {
        StudyBuddyMiniAppPlatform::forceCreate([
            'slug' => 'admin-ui-app',
            'name' => 'Admin UI App',
            'category' => 'Learning',
            'status' => 'live',
            'web_play_url' => 'https://example.com/admin-ui-app',
            'is_web_enabled' => true,
            'is_active' => true,
        ]);

        DB::table('studybuddy_contact_messages')->insert([
            'name' => 'StudyBuddy Parent',
            'email' => 'parent@example.com',
            'category' => 'general',
            'subject' => 'A real question',
            'message' => 'Could you help with this account?',
            'status' => 'new',
            'priority' => 'normal',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($this->admin())
            ->get('/admin/control-room')
            ->assertOk()
            ->assertSee('data-dashboard-metric="published-apps" data-value="1"', false)
            ->assertSee('data-dashboard-metric="browser-apps" data-value="1"', false)
            ->assertSee('data-dashboard-metric="new-messages" data-value="1"', false)
            ->assertSee('Published Apps')
            ->assertSee('Browser Apps')
            ->assertSee('New Messages');
    }

    public function test_add_app_is_a_five_step_editor_with_persistent_actions(): void
    {
        $response = $this->actingAs($this->admin())
            ->get('/admin/control-room/apps/create')
            ->assertOk()
            ->assertSee('data-editor-mode="create"', false)
            ->assertSee('data-editor-nav', false)
            ->assertSee('data-save-bar', false)
            ->assertSee('data-section-target="basic-info"', false)
            ->assertSee('data-section-target="branding"', false)
            ->assertSee('data-section-target="learning-details"', false)
            ->assertSee('data-section-target="availability"', false)
            ->assertSee('data-section-target="preview-publish"', false)
            ->assertSee('data-artwork-card="image_url"', false)
            ->assertSee('data-artwork-card="hero_image"', false)
            ->assertSee('data-platform-card="browser"', false)
            ->assertSee('data-platform-card="google-play"', false)
            ->assertSee('data-platform-card="app-store"', false)
            ->assertSee('data-platform-card="downloads"', false)
            ->assertSee('name="save_action" value="draft"', false)
            ->assertSee('name="save_action" value="publish"', false);

        $html = $response->getContent();
        $this->assertSame(1, substr_count($html, '<main '));
        $this->assertStringNotContainsString('name="is_active"', $html);

        foreach (['android_url', 'android_package_id', 'ios_url', 'windows_url', 'mac_url', 'support_url'] as $field) {
            $this->assertMatchesRegularExpression(
                '/<[^>]+name="'.preg_quote($field, '/').'"(?![^>]*\sdisabled(?:\s|=|>))[^>]*>/',
                $html
            );
        }
    }

    public function test_published_editor_uses_a_safe_default_submit_action(): void
    {
        $app = StudyBuddyMiniAppPlatform::forceCreate([
            'slug' => 'safe-enter-app',
            'name' => 'Safe Enter App',
            'category' => 'Maths',
            'status' => 'live',
            'is_active' => true,
        ]);

        $html = $this->actingAs($this->admin())
            ->get(route('admin.control-room.apps.edit', $app))
            ->assertOk()
            ->assertSee('Save & unpublish')
            ->assertSee('Save changes')
            ->getContent();

        $safeDefault = strpos($html, 'class="sb-visually-hidden" type="submit" name="save_action" value="publish"');
        $unpublishAction = strpos($html, 'Save &amp; unpublish');

        $this->assertNotFalse($safeDefault);
        $this->assertNotFalse($unpublishAction);
        $this->assertLessThan($unpublishAction, $safeDefault);
    }

    public function test_apps_library_has_premium_empty_and_product_row_states(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->get('/admin/control-room/apps')
            ->assertOk()
            ->assertSee('Your app shelf is ready')
            ->assertSee('Add your first app');

        $app = StudyBuddyMiniAppPlatform::forceCreate([
            'slug' => 'product-row-app',
            'name' => 'Product Row App',
            'category' => 'Maths',
            'tagline' => 'A real learning app.',
            'status' => 'live',
            'android_url' => 'https://play.google.com/store/apps/details?id=fun.studybuddy.productrow',
            'android_package_id' => 'fun.studybuddy.productrow',
            'is_web_enabled' => true,
            'is_download_enabled' => true,
            'is_active' => true,
        ]);

        $this->actingAs($admin)
            ->get('/admin/control-room/apps')
            ->assertOk()
            ->assertSee('Product Row App')
            ->assertSee('Browser needs attention')
            ->assertSee('Google Play')
            ->assertSee('Preview')
            ->assertSee('Edit')
            ->assertSee('More actions for Product Row App')
            ->assertSee(route('admin.control-room.apps.edit', $app), false)
            ->assertSee('studybuddy-admin-apps.js')
            ->assertSee('Delete permanently');
    }
}
