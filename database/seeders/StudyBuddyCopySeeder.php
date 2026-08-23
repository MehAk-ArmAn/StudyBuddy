<?php

namespace Database\Seeders;

use App\Models\HomepageSection;
use App\Models\HomepageSectionItem;
use App\Models\Page;
use App\Models\PageSection;
use App\Models\PageSectionItem;
use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

/**
 * The public copy StudyBuddy ships with.
 *
 * Earlier builds seeded the website with notes written for whoever was
 * building it — "What you can edit", "Every card, title, body, icon, button
 * and image can be changed in admin", "Replace this starter content with your
 * final legal wording". Visitors were reading all of it.
 *
 * This seeder is idempotent and targets records by their stable keys, so it is
 * safe to re-run on a live site:
 *
 *     php artisan db:seed --class=StudyBuddyCopySeeder
 *
 * Only the fields listed here are touched. Anything an admin has since edited
 * in another field is left alone.
 */
class StudyBuddyCopySeeder extends Seeder
{
    public function run(): void
    {
        $this->homepage();
        $this->pages();
        $this->settings();
    }

    private function homepage(): void
    {
        $copy = [
            'hero' => [
                'eyebrow' => 'Small games, real progress',
                'subtitle' => 'Short games that quietly teach maths, spelling, reading and focus. Ten minutes at a time.',
                'body' => 'Built for kids who would rather play than revise, and for the grown-ups keeping an eye on both.',
            ],
            'what_we_do' => [
                'title' => 'Turn study time into a tiny adventure',
                'subtitle' => 'Practice that sticks, screens that stay calm, and progress a parent can read in one glance.',
            ],
            'page_paths' => [
                'eyebrow' => 'Start somewhere',
                'title' => 'Where do you want to begin?',
                'subtitle' => 'Four ways in. Pick whichever one sounds like you.',
            ],
            'why' => [
                'eyebrow' => 'Why it works',
                'title' => 'Short enough to finish. Good enough to come back to.',
                'subtitle' => 'Playful on the surface, properly structured underneath.',
            ],
            'cta' => [
                'eyebrow' => 'Ready when you are',
                'title' => 'Pick a game and start tapping.',
                'subtitle' => 'We will sneak the learning part in.',
                'body' => 'Nothing to install, and nothing to sit through first.',
            ],
        ];

        foreach ($copy as $key => $fields) {
            HomepageSection::where('section_key', $key)->update($fields);
        }

        $this->homepageCards();
    }

    /**
     * Homepage cards. The originals described the product in the abstract
     * ("classroom-friendly learning energy") and every button just said "Open".
     */
    private function homepageCards(): void
    {
        // Keyed by section, because both sections contain a "support" card.
        $cards = [
            'what_we_do' => [
                'practice' => ['Practice', 'A few minutes a day beats an hour on Sunday.', 'See the apps'],
                'focus' => ['Focus', 'One screen, one task, no autoplay pulling them somewhere else.', 'See the apps'],
                'growth' => ['Progress', 'Every finished round is a small, visible win.', 'How it works'],
                'support' => ['Help', 'Straight answers for parents and teachers, not a knowledge-base maze.', 'Get help'],
            ],
            'page_paths' => [
                'apps' => ['Apps', 'Every game we have made, in one place.', 'Browse apps'],
                'parents' => ['Parents', 'What your child is playing, and how long it takes.', 'Read the parent guide'],
                'teachers' => ['Teachers', 'Short activities that fit the ten minutes you actually have.', 'Read the teacher guide'],
                'support' => ['Help', 'Accounts, safety, privacy, and how to reach a human.', 'Get help'],
            ],
        ];

        foreach ($cards as $sectionKey => $items) {
            $section = HomepageSection::where('section_key', $sectionKey)->first();

            if (! $section) {
                continue;
            }

            foreach ($items as $key => [$title, $subtitle, $button]) {
                HomepageSectionItem::where('homepage_section_id', $section->id)
                    ->where('item_key', $key)
                    ->update([
                        'title' => $title,
                        'subtitle' => $subtitle,
                        'button_label' => $button,
                    ]);
            }
        }

        // Why it works — the six feature cards.
        foreach ([
            'playful' => ['Short by design', 'Built to be finished in one sitting, not abandoned halfway.'],
            'calm' => ['Quiet screens', 'No autoplay, no streak guilt, no countdown pressure.'],
            'reading' => ['Reading that sticks', 'Words come back later, so they actually settle.'],
            'parent' => ['Nothing hidden', 'You can see what your child played and for how long.'],
            'classroom' => ['Classroom sized', 'Long enough to be worth it, short enough for a starter.'],
            'reward' => ['Wins worth having', 'Points for finishing, never for spending longer on a screen.'],
        ] as $key => [$title, $subtitle]) {
            HomepageSectionItem::where('item_key', $key)->update([
                'title' => $title,
                'subtitle' => $subtitle,
            ]);
        }
    }

    private function pages(): void
    {
        // hero_body on each public page.
        $heroes = [
            'apps' => 'Each one does a small thing properly, then gets out of the way.',
            'for-parents' => 'Short sessions, clear progress, and screens that stay calm.',
            'for-teachers' => 'Quick enough for a starter activity, structured enough to be worth the time.',
            'about-us' => 'We build small learning games and try very hard not to make them boring.',
            'privacy-policy' => 'What we collect, why we collect it, and how to ask us to delete it.',
            'data-deletion' => 'How to ask us to delete your StudyBuddy account and the data attached to it.',
            'contact-us' => 'Questions, ideas, bug reports, or a school that wants a word. All welcome.',
            'support' => 'Stuck on something? Start here.',
        ];

        foreach ($heroes as $slug => $body) {
            Page::where('slug', $slug)->update(['hero_body' => $body]);
        }

        // Section headings that used to describe the CMS instead of the page.
        $sections = [
            'for-parents' => ['What it looks like at home', 'Three things parents tell us actually matter.'],
            'for-teachers' => ['How it fits a lesson', 'Built to slot into the time you already have.'],
            'about-us' => ['What we believe', 'Three ideas behind everything we make.'],
            'privacy-policy' => ['The short version', 'Plain English, kept as short as we can make it.'],
            'data-deletion' => ['How to request deletion', 'Three steps, and we handle the rest.'],
            'contact-us' => ['What can we help with?', 'Pick whichever fits and we will point it the right way.'],
            'support' => ['Common questions', 'The things people ask us most.'],
        ];

        foreach ($sections as $slug => [$title, $subtitle]) {
            $page = Page::where('slug', $slug)->first();

            if (! $page) {
                continue;
            }

            PageSection::where('page_id', $page->id)
                ->whereIn('section_key', ['main_cards', 'legal_content'])
                ->update(['title' => $title, 'subtitle' => $subtitle]);
        }

        $this->aboutCards();
        $this->supportCards();
        $this->legalCards();
    }

    /**
     * The support and contact cards used to be notes to whoever was filling
     * the site in ("Add account instructions and contact details here").
     */
    private function supportCards(): void
    {
        foreach ([
            'getting-started' => ['New here?', 'Make an account, open Apps, and pick whatever looks fun. That is genuinely the whole setup.'],
            'account-help' => ['Need help signing in?', 'Check that the email address is right, then message us if you still cannot get in and we will help.'],
            'safety-&-privacy' => ['Safety and privacy', 'What we store, why, and how to have it deleted. All of it in plain English.'],
            'general-questions' => ['General questions', 'Anything about the apps, the site, or an account.'],
            'schools-&-teachers' => ['Schools and teachers', 'Using StudyBuddy with a class, or thinking about it.'],
            'parents-&-families' => ['Parents and families', 'Questions about safety, screen time, or what your child is actually learning.'],
        ] as $key => [$title, $subtitle]) {
            PageSectionItem::where('item_key', $key)
                ->update(['title' => $title, 'subtitle' => $subtitle, 'body' => null]);
        }
    }

    private function aboutCards(): void
    {
        foreach ([
            'learning-can-be-playful' => 'A quest beats a worksheet, every time.',
            'learning-can-be-calm' => 'Not every screen needs to shout to hold attention.',
            'learning-can-grow' => 'Small wins add up faster than big ones.',
        ] as $key => $body) {
            PageSectionItem::where('item_key', $key)->update(['body' => $body]);
        }
    }

    /**
     * Factual, plain-language policy content describing what StudyBuddy
     * actually stores. Written to be readable; have it reviewed by a lawyer
     * before it stands as your final published policy.
     */
    private function legalCards(): void
    {
        $privacy = Page::where('slug', 'privacy-policy')->first();
        $deletion = Page::where('slug', 'data-deletion')->first();

        if ($privacy) {
            $this->replaceItems($privacy, [
                ['What we collect', 'We store the account and profile details you choose to provide, including your display and real name, email, sign-in credentials, role, date of birth, country, guardian or child email connections, learning stage, profile choices and uploads. Role-specific details can include learning goals, subjects, age or class level, organization details, teaching focus and preferred study time.'],
                ['Learning and account activity', 'We store learning progress, points, quests, account connections, and any class or assignment activity you use so StudyBuddy can show the right information to you.'],
                ['Verification information', 'A verification request may include the submitted name and country, verification method or reference, confirmations and consent, notes, status, and review history.'],
                ['Support, updates and device records', 'Contact messages store the name, email, role, topic and message you submit. If you join the updates list, we store your email, subscription status and dates, a one-way code derived from your internet protocol (IP) address, and browser or device information. Site sessions and support requests can also record your IP address, browser or device information and recent activity to keep you signed in, protect forms and investigate safety concerns.'],
                ['Why we collect it', 'We use this information to run accounts and learning features, show your own progress, support the role you choose, answer messages, review verification requests, and keep younger users and the service safe.'],
                ['What we do not do', 'We do not sell your information, and we do not use it to advertise to you or your child.'],
                ['Asking us to delete it', 'You can ask us to delete your account and everything attached to it at any time. The Data Deletion page explains how.'],
                ['Questions', 'If something here is unclear, ask us through the contact page and we will explain it properly.'],
            ]);
        }

        if ($deletion) {
            $this->replaceItems($deletion, [
                ['1. Send us the request', 'Message us from the contact page using the email address on the account you want deleted.'],
                ['2. Tell us how far to go', 'Say whether you want the whole account removed, just your messages to us, or only your learning history.'],
                ['3. We confirm when it is done', 'We check the request came from the account holder, delete what you asked for, and reply to confirm.'],
            ]);
        }
    }

    /**
     * @param array<int, array{0:string, 1:string}> $items
     */
    private function replaceItems(Page $page, array $items): void
    {
        $section = PageSection::where('page_id', $page->id)
            ->where('section_key', 'legal_content')
            ->first();

        if (! $section) {
            return;
        }

        foreach ($items as $index => [$title, $body]) {
            PageSectionItem::updateOrCreate(
                ['page_section_id' => $section->id, 'item_key' => 'item-'.$index],
                [
                    'title' => $title,
                    'body' => $body,
                    'subtitle' => null,
                    'badge_text' => null,
                    'sort_order' => $index + 1,
                    'is_enabled' => true,
                ]
            );
        }

        // Drop any leftover starter paragraphs beyond the ones written above.
        PageSectionItem::where('page_section_id', $section->id)
            ->whereNotIn('item_key', array_map(
                static fn (int $i): string => 'item-'.$i,
                range(0, count($items) - 1)
            ))
            ->delete();
    }

    private function settings(): void
    {
        foreach ([
            'seo_title' => 'StudyBuddy | Learn. Play. Grow. Your Way.',
            'seo_description' => 'Short learning games for maths, spelling, reading and focus. Built for kids, and sane for the parents watching over their shoulder.',
            'footer_description' => 'Small learning games that treat kids like they are clever, because they are.',
        ] as $key => $value) {
            SiteSetting::where('key', $key)->update(['value' => $value]);
        }
    }
}
