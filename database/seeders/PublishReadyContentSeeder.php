<?php

namespace Database\Seeders;

use App\Models\FooterItem;
use App\Models\HomepageSection;
use App\Models\HomepageSectionItem;
use App\Models\MediaAsset;
use App\Models\NavigationItem;
use App\Models\Page;
use App\Models\PageSection;
use App\Models\PageSectionItem;
use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class PublishReadyContentSeeder extends Seeder
{
    private string $assetBase = 'https://raw.githubusercontent.com/MehAk-ArmAn/StudyBuddy-Imgs/main/';

    public function run(): void
    {
        $this->seedSettings();
        $this->seedMediaAssets();
        $this->seedNavigation();
        $this->seedFooter();
        $this->seedHomepage();
        $this->seedPages();
    }

    private function seedSettings(): void
    {
        $settings = [
            ['site_name', 'StudyBuddy.fun', 'text', 'identity'],
            ['brand_name', 'StudyBuddy', 'text', 'identity'],
            ['site_url', 'https://studybuddy.fun', 'url', 'identity'],
            ['support_email', 'support@studybuddy.fun', 'email', 'identity'],
            ['privacy_email', 'privacy@studybuddy.fun', 'email', 'identity'],
            ['asset_repo_url', 'https://github.com/MehAk-ArmAn/StudyBuddy-Imgs', 'url', 'identity'],
            ['asset_raw_base', $this->assetBase, 'url', 'identity'],
            ['logo_path', $this->assetBase.'brand/logo-icon.png', 'image', 'identity'],
            ['favicon_path', $this->assetBase.'brand/logo-icon.png', 'image', 'identity'],
            ['seo_title', 'StudyBuddy.fun | Learn. Play. Grow. Your Way.', 'text', 'seo'],
            ['seo_description', 'StudyBuddy.fun is a magical learning universe for students, parents, teachers, and independent learners, with playful mini apps, calm focus tools, trusted supervision, and clear progress support.', 'textarea', 'seo'],
            ['seo_keywords', 'StudyBuddy, study app, learning app, kids education, parent dashboard, teacher dashboard, independent learner, focus tools, reading practice, math practice', 'textarea', 'seo'],
            ['global_cta_label', 'Start Learning', 'text', 'navigation'],
            ['global_cta_url', '/apps', 'url', 'navigation'],
            ['login_label', 'Login', 'text', 'navigation'],
            ['register_label', 'Create Account', 'text', 'navigation'],
            ['dashboard_label', 'Dashboard', 'text', 'navigation'],
            ['logout_label', 'Logout', 'text', 'navigation'],
            ['homepage_announcement', 'A calmer way to practice, focus, read, plan, and grow.', 'text', 'homepage'],
            ['trust_statement', 'StudyBuddy.fun keeps supervision clear: learners approve connections, parent controls are protected, and teacher access is limited.', 'textarea', 'trust'],
            ['footer_brand_text', 'StudyBuddy.fun', 'text', 'footer'],
            ['footer_description', 'A safe, magical learning universe where students practice, focus, read, plan, and grow with trusted family and classroom support.', 'textarea', 'footer'],
            ['footer_legal_text', 'Copyright 2026 StudyBuddy.fun. Learn. Play. Grow. Your Way.', 'text', 'footer'],
        ];

        foreach ($settings as [$key, $value, $type, $group]) {
            SiteSetting::updateOrCreate(['key' => $key], ['value' => $value, 'type' => $type, 'group' => $group]);
        }
    }

    private function seedMediaAssets(): void
    {
        $assets = [
            ['StudyBuddy logo icon', 'brand/logo-icon.png', 'Brand logo used across the website.', 1],
            ['Hero dolphin and glowing book', 'hero/hero-dolphin-book.png', 'Main StudyBuddy hero illustration.', 2],
            ['Homepage apps path image', 'homepage-paths/path-apps.png', 'Illustration for the Apps page path.', 10],
            ['Homepage parents path image', 'homepage-paths/path-parents.png', 'Illustration for the Parents page path.', 11],
            ['Homepage teachers path image', 'homepage-paths/path-teachers.png', 'Illustration for the Teachers page path.', 12],
            ['Homepage support path image', 'homepage-paths/path-support.png', 'Illustration for the Support page path.', 13],
            ['Math Quest app icon', 'apps/app-math-quest.png', 'Math Quest mini app icon.', 20],
            ['Spelling Sprint app icon', 'apps/app-spelling-sprint.png', 'Spelling Sprint mini app icon.', 21],
            ['Reading Garden app icon', 'apps/app-reading-garden.png', 'Reading Garden mini app icon.', 22],
            ['Focus Forest app icon', 'apps/app-focus-forest.png', 'Focus Forest mini app icon.', 23],
            ['Planner City app icon', 'apps/app-planner-city.png', 'Planner City mini app icon.', 24],
            ['Quiz Galaxy app icon', 'apps/app-quiz-galaxy.png', 'Quiz Galaxy mini app icon.', 25],
            ['Shapes Lab app icon', 'apps/app-shapes-lab.png', 'Shapes Lab mini app icon.', 26],
            ['Flashcard Castle app icon', 'apps/app-flashcard-castle.png', 'Flashcard Castle mini app icon.', 27],
            ['Student dashboard illustration', 'dashboard/role-student.svg', 'Dashboard illustration for student accounts.', 30],
            ['Parent dashboard illustration', 'dashboard/role-parent.svg', 'Dashboard illustration for parent accounts.', 31],
            ['Teacher dashboard illustration', 'dashboard/role-teacher.svg', 'Dashboard illustration for teacher accounts.', 32],
            ['Trust email verification illustration', 'dashboard/trust-email.svg', 'Email verification trust card illustration.', 33],
            ['Trust age gate illustration', 'dashboard/trust-age.svg', 'Age gate trust card illustration.', 34],
            ['Trust role verification illustration', 'dashboard/trust-role.svg', 'Role verification trust card illustration.', 35],
            ['Trust parent connection illustration', 'dashboard/trust-parent.svg', 'Parent supervision connection illustration.', 36],
            ['Trust teacher connection illustration', 'dashboard/trust-teacher.svg', 'Teacher supervision connection illustration.', 37],
            ['Purple decorative planet', 'decor/planets/planet-purple-lg.png', 'Purple decorative planet.', 50],
            ['Ringed decorative planet', 'decor/planets/planet-ringed-lg.png', 'Ringed decorative planet.', 51],
        ];

        foreach ($assets as [$title, $path, $alt, $sort]) {
            $mime = str_ends_with($path, '.svg') ? 'image/svg+xml' : 'image/png';
            MediaAsset::updateOrCreate(['path' => $this->assetBase.$path], ['title' => $title, 'alt_text' => $alt, 'type' => 'image', 'mime_type' => $mime, 'sort_order' => $sort, 'is_active' => true]);
        }
    }

    private function seedNavigation(): void
    {
        NavigationItem::query()->delete();
        foreach ([['Home', '/', 1], ['Apps', '/apps', 2], ['Parents', '/for-parents', 3], ['Teachers', '/for-teachers', 4], ['About', '/about-us', 5], ['Support', '/support', 6], ['Contact', '/contact-us', 7]] as [$label, $url, $sort]) {
            NavigationItem::create(['label' => $label, 'url' => $url, 'sort_order' => $sort, 'is_enabled' => true, 'opens_new_tab' => false]);
        }
    }

    private function seedFooter(): void
    {
        FooterItem::query()->delete();
        foreach ([
            ['Explore', 'Home', '/', 1], ['Explore', 'Apps', '/apps', 2], ['Explore', 'For Parents', '/for-parents', 3], ['Explore', 'For Teachers', '/for-teachers', 4],
            ['Product', 'Independent Learners', '/apps', 1], ['Product', 'Focus Tools', '/apps', 2], ['Product', 'Reading Practice', '/apps', 3], ['Product', 'Planning Support', '/apps', 4],
            ['Company', 'About StudyBuddy', '/about-us', 1], ['Company', 'Support', '/support', 2], ['Company', 'Contact', '/contact-us', 3],
            ['Legal', 'Privacy Policy', '/privacy-policy', 1], ['Legal', 'Data Deletion', '/data-deletion', 2],
        ] as [$group, $label, $url, $sort]) {
            FooterItem::create(['group' => $group, 'label' => $label, 'url' => $url, 'sort_order' => $sort, 'is_enabled' => true, 'opens_new_tab' => false]);
        }
    }

    private function seedHomepage(): void
    {
        HomepageSection::query()->delete();

        $this->section('hero', 'hero', 1, 'Magical learning universe', 'Learn. Play. Grow. Your Way.', 'StudyBuddy.fun turns everyday study into a calmer, brighter learning journey for students, parents, teachers, and independent learners.', 'Practice math, spelling, reading, focus, planning, quizzes, shapes, and flashcards in one cosmic learning world built around clarity, trust, and tiny wins.', $this->assetBase.'hero/hero-dolphin-book.png', 'Explore Mini Apps', '/apps', 'Create Account', '/register', ['highlight' => 'Your Way', 'bubble_one' => 'Daily practice', 'bubble_two' => 'Calm focus', 'bubble_three' => 'Trusted support']);

        $what = $this->section('what_we_do', 'what', 2, 'What StudyBuddy does', 'A playful learning system with calm structure.', 'StudyBuddy.fun blends mini apps, focus support, reading growth, planning tools, parent confidence, teacher clarity, and independent learner freedom.', 'The website is built to explain the product clearly today and grow into a full learning platform tomorrow.', null);
        foreach ([
            ['practice', 'Practice without pressure', 'Short learning moments make study easier to start.', 'Students build confidence through bite-sized activities.', 'Open Apps', '/apps', 1],
            ['focus', 'Focus with less noise', 'Calm screens help learners stay steady.', 'Focus Forest and soft routines make study feel less overwhelming.', 'Try Focus Tools', '/apps', 2],
            ['family', 'Support families clearly', 'Parents see how routines, trust, and approval work.', 'Parent controls are designed around learner approval and safety.', 'For Parents', '/for-parents', 3],
            ['classroom', 'Support teachers safely', 'Teachers get structure without overreaching.', 'Teacher supervision stays limited and verification-based.', 'For Teachers', '/for-teachers', 4],
        ] as [$key, $title, $subtitle, $body, $button, $url, $sort]) {
            $this->item($what, $key, $title, $subtitle, $body, null, null, $button, $url, $sort);
        }

        $apps = $this->section('apps_preview', 'apps-preview', 3, 'Mini app universe', 'Eight learning worlds, one friendly dashboard.', 'Each mini app has a simple purpose: practice, focus, read, plan, review, explore, remember, and grow.', null, null, 'View All Apps', '/apps');
        foreach (array_slice($this->appData(), 0, 4) as $row) {
            [$key, $title, $subtitle, $body, $image, $badge, $sort] = $row;
            $this->item($apps, $key, $title, $subtitle, $body, $this->assetBase.$image, $badge, 'Open Apps', '/apps', $sort);
        }

        $paths = $this->section('page_paths', 'paths', 4, 'Choose your path', 'Clear pages for every type of learner.', 'Jump into the area that matches your role and understand exactly how StudyBuddy helps.', null);
        foreach ([
            ['apps', 'Apps', 'Explore every mini app in the StudyBuddy universe.', 'Practice, focus, read, plan, quiz, and grow.', $this->assetBase.'homepage-paths/path-apps.png', 'Explore Apps', '/apps', 1],
            ['parents', 'Parents', 'Understand routines, trust, and child approval.', 'Parent controls unlock only when learners approve the connection.', $this->assetBase.'homepage-paths/path-parents.png', 'For Parents', '/for-parents', 2],
            ['teachers', 'Teachers', 'See how classroom-friendly support can work.', 'Teacher access is limited, verified, and learner-approved.', $this->assetBase.'homepage-paths/path-teachers.png', 'For Teachers', '/for-teachers', 3],
            ['support', 'Support', 'Find help, privacy, deletion, and contact options.', 'Everything important is easy to reach before launch and after launch.', $this->assetBase.'homepage-paths/path-support.png', 'Get Support', '/support', 4],
        ] as [$key, $title, $subtitle, $body, $image, $button, $url, $sort]) {
            $this->item($paths, $key, $title, $subtitle, $body, $image, null, $button, $url, $sort);
        }

        $why = $this->section('why', 'features', 5, 'Why it feels different', 'Study support that feels calm, safe, and motivating.', 'The goal is not to make learning louder. The goal is to make it easier to begin, easier to repeat, and easier to trust.', null);
        foreach ([
            ['playful', 'Playful practice', 'Mini apps turn practice into clear learning moments.', 'Learners get motivation without feeling buried by a huge system.', 1],
            ['calm', 'Calm focus', 'Soft visuals and focused pages reduce clutter.', 'The learning experience feels friendly, not stressful.', 2],
            ['reading', 'Reading growth', 'Reading Garden supports vocabulary, confidence, and story habits.', 'Reading can feel cozy instead of forced.', 3],
            ['parent', 'Parent confidence', 'Parents understand what is happening and why.', 'Family support is clear, supervised, and approval-based.', 4],
            ['teacher', 'Teacher clarity', 'Teachers get a focused, limited support path.', 'Classroom support stays organized and respectful of learner privacy.', 5],
            ['independent', 'Independent learning', 'Self-guided learners get their own path.', 'Independent Learners can choose practice, set goals, and keep moving.', 6],
        ] as [$key, $title, $subtitle, $body, $sort]) {
            $this->item($why, $key, $title, $subtitle, $body, null, null, null, null, $sort);
        }

        $trust = $this->section('trust', 'trust', 6, 'Trust-first accounts', 'Supervision is clear by design.', 'StudyBuddy.fun separates learner, parent, teacher, and independent learner experiences so each role sees the right controls.', 'Parents request child connections. Teachers request limited student supervision. Learners approve connections. Independent Learners manage their own study path.', $this->assetBase.'dashboard/trust-role.svg', 'Create Account', '/register', 'Read Privacy', '/privacy-policy');
        foreach ([
            ['learner-approval', 'Learner approval', 'Learners approve or reject supervision requests.', 'No parent or teacher connection should feel hidden.', $this->assetBase.'dashboard/trust-role.svg', 1],
            ['parent-controls', 'Parent controls', 'Parent tools are for family routines and support.', 'Full parent controls unlock only after learner approval.', $this->assetBase.'dashboard/trust-parent.svg', 2],
            ['teacher-limits', 'Teacher limits', 'Teacher tools stay classroom-focused.', 'Teacher supervision is limited and verification-based.', $this->assetBase.'dashboard/trust-teacher.svg', 3],
        ] as [$key, $title, $subtitle, $body, $image, $sort]) {
            $this->item($trust, $key, $title, $subtitle, $body, $image, null, null, null, $sort);
        }

        $this->section('cta', 'cta', 7, 'Ready to make study feel lighter?', 'Start your StudyBuddy.fun journey.', 'Explore the mini app universe, create a role-based dashboard, and build a learning flow that feels clear from the first click.', 'Everything is connected to editable CMS content, so the site can keep growing without rebuilding the whole website.', $this->assetBase.'hero/hero-dolphin-book.png', 'Create Account', '/register', 'Contact Us', '/contact-us');
    }

    private function seedPages(): void
    {
        Page::query()->delete();
        $this->seedAppsPage();
        $this->simplePage('for-parents', 'For Parents', 'Parents', 'Support study time without adding pressure.', 'StudyBuddy.fun gives families a calmer way to understand routines, trust, and learning progress.', 'Parent accounts are built for support, not control overload. A parent can request a child connection, but learner approval is required before family supervision tools unlock.', $this->assetBase.'homepage-paths/path-parents.png', 2, [
            ['Routine support', 'Create calmer learning habits at home.', 'Short sessions and clear goals help learners build consistency without pressure.'],
            ['Trust-first connection', 'Parent controls require learner approval.', 'This keeps supervision clear and avoids hidden access.'],
            ['Progress clarity', 'Understand the learning journey.', 'Parents can support practice, focus, and confidence with a simpler view of what matters.'],
            ['Family-friendly design', 'Soft visuals and plain language.', 'StudyBuddy is designed to feel warm, safe, and easy to explain.'],
        ]);
        $this->simplePage('for-teachers', 'For Teachers', 'Teachers', 'A classroom-friendly learning layer.', 'StudyBuddy.fun can support practice, review, focus, planning, and learner confidence in a polished education-friendly environment.', 'Teacher accounts are verification-based and intentionally limited. A teacher can request student supervision after verification, and learners approve the connection.', $this->assetBase.'homepage-paths/path-teachers.png', 3, [
            ['Practice moments', 'Use mini apps for quick review.', 'Math, spelling, reading, quizzes, shapes, and flashcards can support class routines.'],
            ['Limited supervision', 'Teacher access stays focused.', 'Teacher tools are not parent controls and do not replace learner privacy.'],
            ['Focus support', 'Help learners begin calmly.', 'Focus Forest and gentle routines make study less overwhelming.'],
            ['Future classroom growth', 'Ready for expansion.', 'The CMS structure can grow into class pages, resources, announcements, and learning pathways.'],
        ]);
        $this->simplePage('about-us', 'About StudyBuddy', 'About', 'Learning should feel clear, calm, and magical.', 'StudyBuddy.fun is built around a simple idea: learners do better when practice feels approachable and support feels trustworthy.', 'The platform combines playful visuals, short learning moments, role-based dashboards, and safe supervision rules so students, parents, teachers, and independent learners each get the right experience.', $this->assetBase.'hero/hero-dolphin-book.png', 4, [
            ['Our mission', 'Make learning easier to start.', 'StudyBuddy helps learners build momentum through small, clear activities.'],
            ['Our tone', 'Playful but not chaotic.', 'The cosmic style is bright, friendly, and intentionally soft.'],
            ['Our trust model', 'Support should be visible.', 'Parent and teacher connections are designed around transparency and learner approval.'],
            ['Our future', 'A growing learning universe.', 'The website is ready for more apps, resources, dashboards, and educational tools.'],
        ]);
        $this->legalPage('privacy-policy', 'Privacy Policy', 'Privacy', 'Privacy Policy', 'StudyBuddy.fun is designed with trust, clarity, and safe account handling in mind.', 'This policy explains what information StudyBuddy.fun may collect, how it may be used, and how users can request support or deletion.', 5, [
            ['Information we collect', 'We may collect account details such as name, email address, role, learning stage, country, parent or guardian email where needed, teacher verification details where relevant, and support messages submitted through the website.'],
            ['How we use information', 'We use information to create accounts, show role-based dashboards, provide support, improve safety, manage verification, display CMS content, and keep the website working correctly.'],
            ['Parent and teacher connections', 'Parent and teacher supervision features are designed to be transparent. Learners approve or reject connection requests. Teacher access is limited and verification-based.'],
            ['Cookies and sessions', 'The website may use cookies and sessions to keep users logged in, protect forms, and support normal website security.'],
            ['Data sharing', 'StudyBuddy.fun does not sell personal data. Information may be shared only when needed to run the service, comply with law, or protect users and the platform.'],
            ['Contact for privacy', 'For privacy questions, contact privacy@studybuddy.fun.'],
        ]);
        $this->legalPage('data-deletion', 'Data Deletion', 'Data Deletion', 'Request account or data deletion', 'Users can request deletion of personal account data connected to StudyBuddy.fun.', 'This page explains the deletion request process in clear, simple language.', 6, [
            ['Step 1: Send a request', 'Email privacy@studybuddy.fun from the email address linked to your StudyBuddy.fun account. Include the subject line Data Deletion Request.'],
            ['Step 2: Tell us what to delete', 'Tell us whether you want your full account deleted, support messages deleted, or specific personal details removed.'],
            ['Step 3: Verification', 'We may ask for confirmation to make sure the request comes from the account owner or authorized guardian.'],
            ['Step 4: Processing', 'After verification, we will process the request as soon as reasonably possible and confirm when it is complete.'],
            ['Important note', 'Some technical logs, security records, or information required by law may be retained for a limited period where necessary.'],
        ]);
        $this->simplePage('contact-us', 'Contact StudyBuddy', 'Contact', 'Contact the StudyBuddy.fun team.', 'Questions, parent feedback, school interest, partnerships, and support requests can start here.', 'Use support@studybuddy.fun for general support, privacy@studybuddy.fun for privacy or deletion requests, and this page as the public contact hub for the product.', $this->assetBase.'homepage-paths/path-support.png', 7, [
            ['General support', 'For account, website, and app questions.', 'Email support@studybuddy.fun and include a clear subject line.'],
            ['Privacy and deletion', 'For privacy or deletion requests.', 'Email privacy@studybuddy.fun from the account email address.'],
            ['Schools and teachers', 'For classroom or school interest.', 'Share your school, role, and the kind of learning support you want to explore.'],
            ['Parents and families', 'For family account questions.', 'Ask about supervision, learner approval, safety, or routines.'],
        ]);
        $this->simplePage('support', 'Support', 'Support', 'Need help with StudyBuddy.fun?', 'Support is here for learners, parents, teachers, and independent learners.', 'Start with the topic that matches your question. The product is designed to keep help clear, safe, and easy to find.', $this->assetBase.'homepage-paths/path-support.png', 8, [
            ['Getting started', 'Create an account and choose your role.', 'Students, parents, teachers, and independent learners each get a dashboard made for their needs.'],
            ['Verification help', 'Email and role checks protect the platform.', 'If a verification link is missing, request another one from the dashboard or contact support.'],
            ['Connection help', 'Parent and teacher requests need learner approval.', 'Learners can approve, reject, or revoke connections from the dashboard.'],
            ['Media and content', 'Website images come from the external image repository.', 'The database stores image URLs so the website repo stays lighter.'],
        ]);
    }

    private function seedAppsPage(): void
    {
        $page = $this->page('apps', 'StudyBuddy Apps', 'Apps', 'Explore the mini app universe.', 'Each StudyBuddy mini app gives learners a focused way to practice, remember, plan, read, focus, or review.', 'The apps are designed as a friendly product universe: clear names, clear purpose, and a magical look that makes learning feel lighter.', $this->assetBase.'homepage-paths/path-apps.png', 'Create Account', '/register', 1);
        $overview = $this->pageSection($page, 'apps_intro', 'intro', 1, 'Mini apps', 'Small worlds with clear learning goals.', 'StudyBuddy apps are built for short, confidence-building study moments.', 'They can support students, independent learners, families, and classrooms.', $this->assetBase.'homepage-paths/path-apps.png');
        foreach ([['quick', 'Fast to start', 'Open an app and begin a small task without heavy setup.'], ['focused', 'Focused by design', 'Each app has one clear learning purpose.'], ['rewarding', 'Motivating progress', 'Tiny wins help learners keep going.']] as [$key, $title, $subtitle]) {
            $this->pageItem($overview, $key, $title, $subtitle, null, null, null, null, null, 1);
        }
        $section = $this->pageSection($page, 'all_apps', 'app-grid', 2, 'App library', 'All StudyBuddy learning worlds', 'Choose the app that matches the learning moment.', null, null);
        foreach ($this->appData() as $row) {
            [$key, $title, $subtitle, $body, $image, $badge, $sort] = $row;
            $this->pageItem($section, $key, $title, $subtitle, $body, $this->assetBase.$image, $badge, 'Learn More', '/contact-us', $sort);
        }
    }

    private function simplePage(string $slug, string $title, string $label, string $hero, string $subtitle, string $body, string $image, int $sort, array $cards): void
    {
        $page = $this->page($slug, $title, $label, $hero, $subtitle, $body, $image, 'Create Account', '/register', $sort);
        $section = $this->pageSection($page, 'main_cards', 'cards', 1, $label, 'What this page covers', 'Clear information written for real visitors, not placeholder content.', null, null);
        foreach ($cards as $i => $card) {
            $this->pageItem($section, str($card[0])->slug()->toString(), $card[0], $card[1], $card[2], null, null, null, null, $i + 1);
        }
    }

    private function legalPage(string $slug, string $title, string $label, string $hero, string $subtitle, string $body, int $sort, array $items): void
    {
        $page = $this->page($slug, $title, $label, $hero, $subtitle, $body, $this->assetBase.'brand/logo-icon.png', 'Contact Us', '/contact-us', $sort);
        $section = $this->pageSection($page, 'legal_content', 'legal', 1, $label, $title, $body, null, null);
        foreach ($items as $i => $item) {
            $this->pageItem($section, 'item-'.$i, $item[0], null, $item[1], null, null, null, null, $i + 1);
        }
    }

    private function appData(): array
    {
        return [
            ['math-quest', 'Math Quest', 'Build math confidence through quick challenges.', 'Numbers become simple quests with friendly wins, practice loops, and a playful learning rhythm.', 'apps/app-math-quest.png', 'Math', 1],
            ['spelling-sprint', 'Spelling Sprint', 'Practice spelling and vocabulary with momentum.', 'Learners move through words, patterns, and memory-building spelling rounds.', 'apps/app-spelling-sprint.png', 'Words', 2],
            ['reading-garden', 'Reading Garden', 'Grow reading habits in a calmer space.', 'A cozy reading world for story confidence, vocabulary growth, and gentle practice.', 'apps/app-reading-garden.png', 'Reading', 3],
            ['focus-forest', 'Focus Forest', 'Start study time with calm focus.', 'A soft focus space for routines, attention, and less stressful learning starts.', 'apps/app-focus-forest.png', 'Focus', 4],
            ['planner-city', 'Planner City', 'Organize tasks, goals, and homework.', 'Learners turn plans into a tiny city of reminders, steps, and study goals.', 'apps/app-planner-city.png', 'Planning', 5],
            ['quiz-galaxy', 'Quiz Galaxy', 'Review knowledge across bright quiz worlds.', 'Questions feel like planets to explore, with tiny wins that make review easier to repeat.', 'apps/app-quiz-galaxy.png', 'Quiz', 6],
            ['shapes-lab', 'Shapes Lab', 'Explore geometry and visual thinking.', 'Shapes, patterns, and spatial ideas become friendly experiments for curious learners.', 'apps/app-shapes-lab.png', 'Shapes', 7],
            ['flashcard-castle', 'Flashcard Castle', 'Remember facts through simple card practice.', 'A castle-themed flashcard world for memory, review, repetition, and self-study.', 'apps/app-flashcard-castle.png', 'Memory', 8],
        ];
    }

    private function page(string $slug, string $title, string $label, string $hero, string $subtitle, string $body, string $image, ?string $button, ?string $url, int $sort): Page
    {
        return Page::updateOrCreate(['slug' => $slug], ['template' => 'cosmic', 'title' => $title, 'nav_label' => $label, 'meta_title' => $title.' | StudyBuddy.fun', 'meta_description' => $subtitle, 'eyebrow' => $label, 'hero_title' => $hero, 'hero_subtitle' => $subtitle, 'hero_body' => $body, 'hero_image_path' => $image, 'button_label' => $button, 'button_url' => $url, 'secondary_button_label' => 'Back Home', 'secondary_button_url' => '/', 'sort_order' => $sort, 'is_enabled' => true, 'settings' => ['publish_ready' => true, 'editable_from_admin' => true]]);
    }

    private function pageSection(Page $page, string $key, string $type, int $sort, ?string $eyebrow, ?string $title, ?string $subtitle, ?string $body, ?string $image = null, ?string $button = null): PageSection
    {
        return PageSection::updateOrCreate(['page_id' => $page->id, 'section_key' => $key], ['section_type' => $type, 'eyebrow' => $eyebrow, 'title' => $title, 'subtitle' => $subtitle, 'body' => $body, 'image_path' => $image, 'button_label' => $button, 'button_url' => null, 'sort_order' => $sort, 'is_enabled' => true, 'settings' => ['publish_ready' => true, 'editable_from_admin' => true]]);
    }

    private function pageItem(PageSection $section, string $key, ?string $title, ?string $subtitle, ?string $body, ?string $image, ?string $badge, ?string $button, ?string $url, int $sort): void
    {
        PageSectionItem::updateOrCreate(['page_section_id' => $section->id, 'item_key' => $key], ['title' => $title, 'subtitle' => $subtitle, 'body' => $body, 'image_path' => $image, 'badge_text' => $badge, 'button_label' => $button, 'button_url' => $url, 'sort_order' => $sort, 'is_enabled' => true, 'settings' => ['publish_ready' => true, 'editable_from_admin' => true]]);
    }

    private function section(string $key, string $type, int $sort, ?string $eyebrow, ?string $title, ?string $subtitle, ?string $body, ?string $image = null, ?string $button = null, ?string $url = null, ?string $second = null, ?string $secondUrl = null, ?array $settings = null): HomepageSection
    {
        return HomepageSection::updateOrCreate(['section_key' => $key], ['section_type' => $type, 'eyebrow' => $eyebrow, 'title' => $title, 'subtitle' => $subtitle, 'body' => $body, 'image_path' => $image, 'button_label' => $button, 'button_url' => $url, 'secondary_button_label' => $second, 'secondary_button_url' => $secondUrl, 'sort_order' => $sort, 'is_enabled' => true, 'settings' => $settings ?: ['publish_ready' => true, 'editable_from_admin' => true]]);
    }

    private function item(HomepageSection $section, string $key, ?string $title, ?string $subtitle, ?string $body, ?string $image, ?string $badge, ?string $button, ?string $url, int $sort): void
    {
        HomepageSectionItem::updateOrCreate(['homepage_section_id' => $section->id, 'item_key' => $key], ['title' => $title, 'subtitle' => $subtitle, 'body' => $body, 'image_path' => $image, 'badge_text' => $badge, 'button_label' => $button, 'button_url' => $url, 'sort_order' => $sort, 'is_enabled' => true, 'settings' => ['publish_ready' => true, 'editable_from_admin' => true]]);
    }
}
