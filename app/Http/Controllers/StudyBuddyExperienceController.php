<?php

namespace App\Http\Controllers;

use App\Models\StudyBuddyMiniAppPlatform;
use App\Models\StudyBuddyContentItem;
use App\Models\StudyBuddyContentPage;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Schema;
use Throwable;

class StudyBuddyExperienceController extends Controller
{
    public function learningHub(): View
    {
        return $this->renderPage('learning-hub');
    }

    public function learningPaths(): View
    {
        return $this->renderPage('learning-paths');
    }

    public function rewards(): View
    {
        return $this->renderPage('rewards');
    }

    public function parentsCenter(): View
    {
        return $this->renderPage('parents-center');
    }

    public function teacherStudio(): View
    {
        return $this->renderPage('teacher-studio');
    }

    public function safetySupport(): View
    {
        return $this->renderPage('safety-support');
    }

    public function appEcosystem(): View
    {
        return $this->renderPage('app-ecosystem');
    }

    protected function renderPage(string $slug): View
    {
        $page = $this->page($slug);
        $items = $this->items($slug);
        $apps = $slug === 'app-ecosystem' ? $this->apps() : collect();

        return view('studybuddy.experience.dynamic-page', [
            'page' => $page,
            'items' => $items,
            'apps' => $apps,
            'slug' => $slug,
            'navPages' => $this->navPages(),
        ]);
    }

    protected function page(string $slug): object
    {
        try {
            if (Schema::hasTable('studybuddy_content_pages')) {
                $page = StudyBuddyContentPage::where('slug', $slug)->where('is_published', true)->first();
                if ($page) {
                    return $page;
                }
            }
        } catch (Throwable $e) {
            // Database may not be migrated yet. Fall back to safe content.
        }

        return (object) $this->fallbackPage($slug);
    }

    protected function items(string $slug)
    {
        try {
            if (Schema::hasTable('studybuddy_content_items')) {
                return StudyBuddyContentItem::where('page_slug', $slug)
                    ->where('is_active', true)
                    ->orderBy('sort_order')
                    ->orderBy('title')
                    ->get();
            }
        } catch (Throwable $e) {
            // Ignore and use empty collection.
        }

        return collect();
    }

    /**
     * Apps shown on the experience pages.
     *
     * Reads the unified catalogue (studybuddy_mini_app_platforms) — the same
     * table the Apps CMS writes and /apps renders — so there is one source of
     * truth. The old studybuddy_app_catalog_items copy is no longer consulted.
     *
     * An empty collection is a valid answer: the views render an empty state
     * rather than inventing placeholder apps.
     */
    protected function apps()
    {
        try {
            if (Schema::hasTable('studybuddy_mini_app_platforms')) {
                return StudyBuddyMiniAppPlatform::query()
                    ->active()
                    ->ordered()
                    ->get()
                    ->map(fn (StudyBuddyMiniAppPlatform $app): object => (object) [
                        'title' => $app->name,
                        'slug' => $app->slug,
                        'category' => $app->category,
                        'summary' => $app->tagline,
                        'description' => $app->description,
                        'icon' => $app->icon,
                        'launch_status' => $app->status,
                        'points_reward' => $app->points_reward,
                        'image_path' => $app->safeHeroImage(),
                    ]);
            }
        } catch (Throwable $e) {
            // A missing table must not take the page down.
        }

        return collect();
    }

    protected function navPages()
    {
        try {
            if (Schema::hasTable('studybuddy_content_pages')) {
                return StudyBuddyContentPage::where('is_published', true)
                    ->where('slug', '!=', 'app-ecosystem')
                    ->orderBy('sort_order')
                    ->get(['slug', 'title']);
            }
        } catch (Throwable $e) {
            // Ignore.
        }

        return collect([
            (object) ['slug' => 'learning-hub', 'title' => 'Learning Hub'],
            (object) ['slug' => 'learning-paths', 'title' => 'Learning Paths'],
            (object) ['slug' => 'rewards', 'title' => 'Rewards'],
            (object) ['slug' => 'parents-center', 'title' => 'Parents'],
            (object) ['slug' => 'teacher-studio', 'title' => 'Teachers'],
            (object) ['slug' => 'safety-support', 'title' => 'Safety'],
        ]);
    }

    protected function fallbackPage(string $slug): array
    {
        $title = str($slug)->replace('-', ' ')->title()->toString();

        // Visitors should never read about migrations or the CMS, so this
        // stand-in stays in the product's voice.
        return [
            'slug' => $slug,
            'title' => $title,
            'eyebrow' => 'StudyBuddy',
            'subtitle' => 'Find practical guidance, simple tools, and a clear next step.',
            'hero_badge' => null,
            'primary_cta_label' => 'See the apps',
            'primary_cta_url' => '/apps',
            'secondary_cta_label' => 'Back home',
            'secondary_cta_url' => '/',
            'content_blocks' => [],
        ];
    }
}
