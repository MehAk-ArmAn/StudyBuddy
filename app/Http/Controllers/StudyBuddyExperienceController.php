<?php

namespace App\Http\Controllers;

use App\Models\StudyBuddyAppCatalogItem;
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

    protected function apps()
    {
        try {
            if (Schema::hasTable('studybuddy_app_catalog_items')) {
                return StudyBuddyAppCatalogItem::where('is_active', true)
                    ->orderBy('sort_order')
                    ->orderBy('title')
                    ->get();
            }
        } catch (Throwable $e) {
            // Ignore and use empty collection.
        }

        return collect($this->fallbackApps())->map(fn ($item) => (object) $item);
    }

    protected function navPages()
    {
        try {
            if (Schema::hasTable('studybuddy_content_pages')) {
                return StudyBuddyContentPage::where('is_published', true)
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
            (object) ['slug' => 'app-ecosystem', 'title' => 'App Ecosystem'],
        ]);
    }

    protected function fallbackPage(string $slug): array
    {
        $title = str($slug)->replace('-', ' ')->title()->toString();

        return [
            'slug' => $slug,
            'title' => $title,
            'eyebrow' => 'StudyBuddy Experience',
            'subtitle' => 'This page is ready for admin-editable content once migrations are run.',
            'hero_badge' => 'Admin editable',
            'primary_cta_label' => 'Open Command Center',
            'primary_cta_url' => '/command-center',
            'secondary_cta_label' => 'Open Apps',
            'secondary_cta_url' => '/apps',
            'content_blocks' => [
                ['type' => 'cards', 'title' => 'Editable content area', 'items' => [
                    ['icon' => '✨', 'title' => 'Admin controlled', 'description' => 'Update this page from the StudyBuddy Content Studio.'],
                    ['icon' => '🛠️', 'title' => 'Safe fallback', 'description' => 'This fallback appears only if the database content is missing.'],
                ]],
            ],
        ];
    }

    protected function fallbackApps(): array
    {
        return [
            ['title'=>'Math Quest','category'=>'Math','summary'=>'Math missions','description'=>'Planned mini app.','icon'=>'➗','launch_status'=>'planned','points_reward'=>25],
            ['title'=>'Spelling Sprint','category'=>'Language','summary'=>'Spelling missions','description'=>'Planned mini app.','icon'=>'✏️','launch_status'=>'planned','points_reward'=>20],
            ['title'=>'Reading Garden','category'=>'Reading','summary'=>'Reading missions','description'=>'Planned mini app.','icon'=>'🌱','launch_status'=>'planned','points_reward'=>20],
        ];
    }
}
