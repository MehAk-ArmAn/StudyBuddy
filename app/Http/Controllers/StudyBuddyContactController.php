<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class StudyBuddyContactController extends Controller
{
    public function show(): View
    {
        $settings = Schema::hasTable('site_settings')
            ? DB::table('site_settings')->pluck('value', 'key')->all()
            : [];

        return view('studybuddy.contact.show', [
            'settings' => $settings,
            'categories' => [
                'general' => 'General question',
                'account' => 'Account or login help',
                'apps' => 'Learning apps',
                'parent' => 'Parent dashboard',
                'teacher' => 'Teacher dashboard',
                'safety' => 'Safety concern',
                'data-deletion' => 'Data deletion request',
                'feedback' => 'Feedback or suggestion',
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:160'],
            'email' => ['required', 'email', 'max:190'],
            'role' => ['nullable', 'string', 'max:80'],
            'category' => ['required', 'string', 'max:120'],
            'subject' => ['required', 'string', 'max:190'],
            'message' => ['required', 'string', 'min:10', 'max:5000'],
            'consent' => ['accepted'],
        ]);

        if (Schema::hasTable('studybuddy_contact_messages')) {
            DB::table('studybuddy_contact_messages')->insert([
                'name' => $data['name'],
                'email' => $data['email'],
                'role' => $data['role'] ?? null,
                'category' => $data['category'],
                'subject' => $data['subject'],
                'message' => $data['message'],
                'status' => 'new',
                'priority' => in_array($data['category'], ['safety', 'data-deletion'], true) ? 'high' : 'normal',
                'user_id' => $request->user()?->id,
                'ip_address' => $request->ip(),
                'user_agent' => substr((string) $request->userAgent(), 0, 500),
                'meta' => json_encode(['source' => 'contact_page'], JSON_UNESCAPED_SLASHES),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return back()->with('status', 'Message sent. StudyBuddy support can review it from the admin Control Room.');
    }
}
