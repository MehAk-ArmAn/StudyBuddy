<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (
            ! Schema::hasTable('pages')
            || ! Schema::hasTable('page_sections')
            || ! Schema::hasTable('page_section_items')
        ) {
            return;
        }

        $pageId = DB::table('pages')->where('slug', 'privacy-policy')->value('id');
        if (! $pageId) {
            return;
        }

        $sectionId = DB::table('page_sections')
            ->where('page_id', $pageId)
            ->where('section_key', 'legal_content')
            ->value('id');

        if (! $sectionId) {
            return;
        }

        $legacy = [
            'item-0' => ['What we collect', 'Your name and email address when you create an account, the role you pick, and anything you send us through the contact form. If you use the learning apps we also store your progress and points.'],
            'item-1' => ['Why we collect it', 'To keep you signed in, show your own progress rather than someone else\'s, answer your messages, and keep younger accounts safe.'],
            'item-2' => ['What we do not do', 'We do not sell your information, and we do not use it to advertise to you or your child.'],
            'item-3' => ['Asking us to delete it', 'You can ask us to delete your account and everything attached to it at any time. The Data Deletion page explains how.'],
            'item-4' => ['Questions', 'If something here is unclear, ask us through the contact page and we will explain it properly.'],
        ];

        $current = DB::table('page_section_items')
            ->where('page_section_id', $sectionId)
            ->orderBy('item_key')
            ->get(['item_key', 'title', 'body']);

        // Preserve any policy that has been edited or extended since seeding.
        // Only replace the exact five paragraphs StudyBuddy previously shipped.
        if ($current->count() !== count($legacy)) {
            return;
        }

        foreach ($current as $item) {
            $expected = $legacy[$item->item_key] ?? null;

            if (! $expected || $item->title !== $expected[0] || $item->body !== $expected[1]) {
                return;
            }
        }

        $copy = [
            ['What we collect', 'We store the account and profile details you choose to provide, including your display and real name, email, sign-in credentials, role, date of birth, country, guardian or child email connections, learning stage, profile choices and uploads. Role-specific details can include learning goals, subjects, age or class level, organization details, teaching focus and preferred study time.'],
            ['Learning and account activity', 'We store learning progress, points, quests, account connections, and any class or assignment activity you use so StudyBuddy can show the right information to you.'],
            ['Verification information', 'A verification request may include the submitted name and country, verification method or reference, confirmations and consent, notes, status, and review history.'],
            ['Support, updates and device records', 'Contact messages store the name, email, role, topic and message you submit. If you join the updates list, we store your email, subscription status and dates, a one-way code derived from your internet protocol (IP) address, and browser or device information. Site sessions and support requests can also record your IP address, browser or device information and recent activity to keep you signed in, protect forms and investigate safety concerns.'],
            ['Why we collect it', 'We use this information to run accounts and learning features, show your own progress, support the role you choose, answer messages, review verification requests, and keep younger users and the service safe.'],
            ['What we do not do', 'We do not sell your information, and we do not use it to advertise to you or your child.'],
            ['Asking us to delete it', 'You can ask us to delete your account and everything attached to it at any time. The Data Deletion page explains how.'],
            ['Questions', 'If something here is unclear, ask us through the contact page and we will explain it properly.'],
        ];

        foreach ($copy as $index => [$title, $body]) {
            $identity = [
                'page_section_id' => $sectionId,
                'item_key' => 'item-'.$index,
            ];
            $values = [
                'title' => $title,
                'subtitle' => null,
                'body' => $body,
                'badge_text' => null,
                'sort_order' => $index + 1,
                'is_enabled' => true,
                'updated_at' => now(),
            ];

            if (DB::table('page_section_items')->where($identity)->exists()) {
                DB::table('page_section_items')->where($identity)->update($values);
            } else {
                DB::table('page_section_items')->insert($identity + $values + ['created_at' => now()]);
            }
        }
    }

    public function down(): void
    {
        // Policy copy is not rolled back over possible later editorial changes.
    }
};
