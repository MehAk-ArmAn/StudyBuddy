<?php

namespace Tests\Feature\Learning;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class StudyBuddyPack10ResourceStudioTest extends TestCase
{
    use RefreshDatabase;

    private function learner(string $email): User
    {
        return User::forceCreate([
            'name' => 'StudyBuddy Learner', 'email' => $email,
            'email_verified_at' => now(), 'password' => bcrypt('SomeTestPass123!'),
            'role' => 'student', 'is_admin' => false,
            'role_profile' => [
                'sb_p08_setup' => ['accent' => 'cyan'],
                'sb_p09_plans' => [['id' => (string) \Illuminate\Support\Str::uuid(), 'title' => 'Keep this plan']],
            ],
        ]);
    }

    public function test_guests_cannot_read_write_or_upload_resources(): void
    {
        $this->get('/resources')->assertRedirect('/login');
        $this->post('/resources/notes', ['title' => 'Private'])->assertRedirect('/login');
        $this->post('/resources/files', [])->assertRedirect('/login');
    }

    public function test_markdown_notes_are_saved_only_for_the_owner_and_existing_preferences_survive(): void
    {
        $owner = $this->learner('p10-owner@example.test');
        $stranger = $this->learner('p10-stranger@example.test');
        $this->actingAs($owner)->get('/resources')->assertOk()->assertSee('Your personal library');
        $this->post('/resources/notes', [
            'title' => 'My private lesson', 'body' => "# Secret science\n- One step at a time",
        ])->assertRedirect();
        $owner->refresh();
        $this->assertCount(1, $owner->role_profile['sb_p10_notes']);
        $this->assertSame('cyan', $owner->role_profile['sb_p08_setup']['accent']);
        $this->assertSame('Keep this plan', $owner->role_profile['sb_p09_plans'][0]['title']);
        $id = $owner->role_profile['sb_p10_notes'][0]['id'];
        $this->get('/resources?note='.$id)->assertOk()->assertSee('Secret science');
        $this->actingAs($stranger)->get('/resources?note='.$id)->assertOk()->assertDontSee('Secret science');
        $this->put('/resources/notes/'.$id, ['title' => 'Edited', 'body' => 'No access'])->assertNotFound();
        $this->delete('/resources/notes/'.$id)->assertNotFound();
        $this->actingAs($owner)->put('/resources/notes/'.$id, [
            'title' => 'My revised lesson', 'body' => '# First unit',
        ])->assertRedirect();
        $this->assertSame('My revised lesson', $owner->fresh()->role_profile['sb_p10_notes'][0]['title']);
        $this->delete('/resources/notes/'.$id)->assertRedirect();
        $this->assertCount(0, $owner->fresh()->role_profile['sb_p10_notes']);
    }

    public function test_bad_input_and_markup_remain_non_executable(): void
    {
        $owner = $this->learner('p10-markdown@example.test');
        $this->actingAs($owner)->post('/resources/notes', ['title' => 'A'])->assertSessionHasErrors('title');
        $this->post('/resources/notes', ['title' => 'Markup lesson', 'body' => '<script>alert(1)</script>'])->assertRedirect();
        $id = $owner->fresh()->role_profile['sb_p10_notes'][0]['id'];
        $this->get('/resources?note='.$id)->assertOk()
            ->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false)
            ->assertDontSee('<script>alert(1)</script>', false);
    }

    public function test_file_is_on_private_disk_with_owner_only_download_and_preview_access(): void
    {
        Storage::fake('local');
        $owner = $this->learner('p10-upload-owner@example.test');
        $stranger = $this->learner('p10-upload-other@example.test');
        $this->actingAs($owner)->post('/resources/files', [
            'upload' => UploadedFile::fake()->create('lesson.pdf', 16, 'application/pdf'),
        ])->assertRedirect();
        $files = $owner->fresh()->role_profile['sb_p10_files'];
        $this->assertCount(1, $files);
        $id = $files[0]['id'];
        $path = $files[0]['path'];
        Storage::disk('local')->assertExists($path);
        $this->assertStringContainsString('/u'.$owner->id.'/', $path);
        $this->get('/resources/files/'.$id.'/download')->assertOk();
        $this->get('/resources/files/'.$id.'/preview')->assertNotFound();
        $this->actingAs($stranger)->get('/resources/files/'.$id.'/download')->assertNotFound();
        $this->get('/resources/files/'.$id.'/preview')->assertNotFound();
        $this->delete('/resources/files/'.$id)->assertNotFound();
        $this->actingAs($owner)->delete('/resources/files/'.$id)->assertRedirect();
        Storage::disk('local')->assertMissing($path);
    }

    public function test_rejects_unsafe_file_extensions_and_preserves_other_account_data(): void
    {
        Storage::fake('local');
        $owner = $this->learner('p10-file-filter@example.test');
        $this->actingAs($owner)->post('/resources/files', [
            'upload' => UploadedFile::fake()->create('web-script.html', 4, 'text/html'),
        ])->assertSessionHasErrors('upload');
        $this->assertArrayNotHasKey('sb_p10_files', $owner->fresh()->role_profile);
        $this->post('/resources/files', [
            'upload' => UploadedFile::fake()->create('wrong.txt', 4, 'application/pdf'),
        ])->assertSessionHasErrors('upload');
    }
}
