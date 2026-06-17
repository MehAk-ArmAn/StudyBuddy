<?php

namespace Database\Seeders;

use App\Models\FooterItem;
use App\Models\HomepageSection;
use App\Models\HomepageSectionItem;
use App\Models\MediaAsset;
use App\Models\NavigationItem;
use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $assetBase = 'assets/studybuddy-imgs/';

        foreach ([
            ['site_name', 'StudyBuddy', 'text', 'identity'],
            ['brand_name', 'StudyBuddy', 'text', 'identity'],
            ['logo_path', $assetBase.'brand/logo-icon.png', 'image', 'identity'],
            ['favicon_path', $assetBase.'brand/logo-icon.png', 'image', 'identity'],
            ['seo_title', 'StudyBuddy | Learn. Play. Grow. Your Way.', 'text', 'seo'],
            ['seo_description', 'A magical cosmic learning universe for curious kids, calm routines, confident parents, and classroom-ready growth.', 'textarea', 'seo'],
            ['global_cta_label', 'Start Learning', 'text', 'navigation'],
            ['global_cta_url', '#apps', 'url', 'navigation'],
            ['footer_brand_text', 'StudyBuddy', 'text', 'footer'],
            ['footer_description', 'A safe, magical learning universe where kids practice, focus, read, and grow with their personal StudyBuddy.', 'textarea', 'footer'],
            ['footer_legal_text', '© 2026 StudyBuddy. Learn. Play. Grow. Your Way.', 'text', 'footer'],
        ] as [$key, $value, $type, $group]) {
            SiteSetting::updateOrCreate(['key' => $key], ['value' => $value, 'type' => $type, 'group' => $group]);
        }

        foreach ([
            ['StudyBuddy Logo', 'brand/logo-icon.png'], ['Hero Dolphin Book', 'hero/hero-dolphin-book.png'],
            ['Math Quest Icon', 'apps/app-math-quest.png'], ['Spelling Sprint Icon', 'apps/app-spelling-sprint.png'],
            ['Reading Garden Icon', 'apps/app-reading-garden.png'], ['Focus Forest Icon', 'apps/app-focus-forest.png'],
            ['Planner City Icon', 'apps/app-planner-city.png'], ['Quiz Galaxy Icon', 'apps/app-quiz-galaxy.png'],
            ['Shapes Lab Icon', 'apps/app-shapes-lab.png'], ['Flashcard Castle Icon', 'apps/app-flashcard-castle.png'],
            ['Purple Planet', 'decor/planets/planet-purple-lg.png'], ['Ringed Planet', 'decor/planets/planet-ringed-lg.png'],
        ] as [$title, $path]) {
            MediaAsset::updateOrCreate(['path' => $assetBase.$path], ['title' => $title, 'alt_text' => $title, 'type' => 'image', 'mime_type' => 'image/png', 'is_active' => true]);
        }

        foreach ([['Home','#top',1],['Apps','#apps',2],['For Parents','#parents',3],['For Teachers','#teachers',4],['Why StudyBuddy','#why',5],['Start','#cta',6]] as [$label,$url,$sort]) {
            NavigationItem::updateOrCreate(['label' => $label], ['url' => $url, 'sort_order' => $sort, 'is_enabled' => true, 'opens_new_tab' => false]);
        }

        foreach ([['Explore','Home','#top',1],['Explore','Apps','#apps',2],['Explore','Why StudyBuddy','#why',3],['Families','For Parents','#parents',1],['Classrooms','For Teachers','#teachers',1],['Support','Start Learning','#cta',1]] as [$group,$label,$url,$sort]) {
            FooterItem::updateOrCreate(['group' => $group, 'label' => $label], ['url' => $url, 'sort_order' => $sort, 'is_enabled' => true, 'opens_new_tab' => false]);
        }

        $this->section('hero', 'hero', 1, 'Magical learning universe', 'Learn. Play. Grow. Your Way.', 'A fun and safe learning universe where students can practice, focus, read, and grow with their personal StudyBuddy.', 'Built for curious kids, confident parents, and classrooms that need calm progress without boring screens.', $assetBase.'hero/hero-dolphin-book.png', 'Start Learning', '#apps', 'Explore Apps', '#apps', ['highlight' => 'Your Way']);

        $apps = $this->section('apps', 'apps', 2, 'Mini apps', 'Choose your next learning quest', 'Eight playful learning cards inspired by math, reading, focus, planning, quizzes, shapes, spelling, and flashcards.', 'Each card feels like a premium app-store moment.', null);
        foreach ([
            ['math-quest','Math Quest','Practice math in a fun way.','Numbers become magical quests with bright rewards and tiny wins.','apps/app-math-quest.png','4.8',1],
            ['spelling-sprint','Spelling Sprint','Improve spelling and vocabulary.','Rocket through words, letters, and confidence-building practice.','apps/app-spelling-sprint.png','4.7',2],
            ['reading-garden','Reading Garden','Read stories and build reading skills.','Grow reading habits with calm magical story moments.','apps/app-reading-garden.png','4.8',3],
            ['focus-forest','Focus Forest','Stay focused and calm.','A peaceful space for focus and study routines.','apps/app-focus-forest.png','4.8',4],
            ['planner-city','Planner City','Organize tasks and homework.','Turn reminders and goals into a tiny futuristic city.','apps/app-planner-city.png','4.6',5],
            ['quiz-galaxy','Quiz Galaxy','Test knowledge and earn stars.','Explore quiz planets and collect sparkly wins.','apps/app-quiz-galaxy.png','4.7',6],
            ['shapes-lab','Shapes Lab','Learn shapes and their world.','Play with geometry and bright shape experiments.','apps/app-shapes-lab.png','4.6',7],
            ['flashcard-castle','Flashcard Castle','Study anywhere with flashcards.','Build memory inside a cute castle of cards.','apps/app-flashcard-castle.png','4.8',8],
        ] as [$key,$title,$subtitle,$body,$image,$badge,$sort]) {
            $this->item($apps, $key, $title, $subtitle, $body, $assetBase.$image, $badge, 'Start', '#cta', $sort);
        }

        $why = $this->section('why', 'features', 3, 'Why kids keep coming back', 'Soft structure, shiny motivation, real progress', 'StudyBuddy keeps learning playful without losing focus.', 'Designed to feel magical, but organized enough for everyday routines.', null);
        foreach ([['playful-practice','Playful Practice','Lessons feel light, bright, and doable.','Kids practice without overwhelm.','Star',1],['calm-focus','Calm Focus','Gentle routines make study less stressful.','Focus tools support attention.','Leaf',2],['reading-growth','Reading Growth','Stories build confidence.','A cozy place to grow vocabulary.','Book',3],['parent-confidence','Parent Confidence','Parents understand progress quickly.','Clear sections support learning.','Heart',4],['classroom-ready','Classroom Ready','Polished learning identity for school use.','Teachers guide learners with structure.','Cap',5],['reward-motivation','Reward Motivation','Stars and badges keep goals exciting.','Progress feels earned and fun.','Trophy',6]] as [$key,$title,$subtitle,$body,$badge,$sort]) {
            $this->item($why, $key, $title, $subtitle, $body, null, $badge, null, null, $sort);
        }

        $this->section('parents', 'split', 4, 'For families', 'A learning space parents can trust', 'Simple routines, friendly progress signals, and a calm design that keeps kids excited without chaos.', 'StudyBuddy helps families turn daily study into a safe, bright habit.', $assetBase.'decor/planets/planet-ringed-lg.png', 'Build a routine', '#cta');
        $this->section('teachers', 'split reverse', 5, 'For teachers', 'Classroom-friendly learning energy', 'A polished learning universe that can support practice, review, focus, and rewards as the platform grows.', 'The homepage tells the product story while the CMS keeps every message under your control.', $assetBase.'apps/app-quiz-galaxy.png', 'Explore learning cards', '#apps');

        $stats = $this->section('stats', 'stats', 6, 'Learning signals', 'Small wins that feel big', 'Bright metrics make the page feel alive and trustworthy.', null, null);
        foreach ([['mini-apps','8+','Mini Apps','Practice worlds for different moods.','Grid',1],['learning-paths','50+','Learning Paths','Flexible routes for daily practice.','Users',2],['study-goals','100K+','Study Goals','A product vision built around steady wins.','Cap',3],['parent-rating','4.9','Parent Rating','A premium parent-friendly learning feel.','Star',4]] as [$key,$title,$subtitle,$body,$badge,$sort]) {
            $this->item($stats, $key, $title, $subtitle, $body, null, $badge, null, null, $sort);
        }

        $this->section('cta', 'cta', 7, 'Ready to glow up study time?', 'Start your cosmic learning journey', 'One homepage, one clear message, one magical StudyBuddy universe ready to grow.', 'Every public section stays editable from your admin CMS.', $assetBase.'hero/hero-dolphin-book.png', 'Start Learning', '#apps', 'Back to top', '#top');
    }

    private function section(string $key, string $type, int $sort, ?string $eyebrow, ?string $title, ?string $subtitle, ?string $body, ?string $image, ?string $button = null, ?string $url = null, ?string $second = null, ?string $secondUrl = null, ?array $settings = null): HomepageSection
    {
        return HomepageSection::updateOrCreate(['section_key' => $key], ['section_type' => $type, 'eyebrow' => $eyebrow, 'title' => $title, 'subtitle' => $subtitle, 'body' => $body, 'image_path' => $image, 'button_label' => $button, 'button_url' => $url, 'secondary_button_label' => $second, 'secondary_button_url' => $secondUrl, 'sort_order' => $sort, 'is_enabled' => true, 'settings' => $settings]);
    }

    private function item(HomepageSection $section, string $key, ?string $title, ?string $subtitle, ?string $body, ?string $image, ?string $badge, ?string $button, ?string $url, int $sort): void
    {
        HomepageSectionItem::updateOrCreate(['homepage_section_id' => $section->id, 'item_key' => $key], ['title' => $title, 'subtitle' => $subtitle, 'body' => $body, 'image_path' => $image, 'badge_text' => $badge, 'button_label' => $button, 'button_url' => $url, 'sort_order' => $sort, 'is_enabled' => true]);
    }
}
