<?php

namespace App\Http\Controllers;

use App\Models\FooterItem;
use App\Models\NavigationItem;
use App\Models\SiteSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $role = match ((string) $user->role) {
            'primary', 'secondary' => 'student',
            'parent' => 'parent',
            'teacher' => 'teacher',
            'professional' => 'professional',
            default => 'student',
        };

        return view('dashboard.index', [
            'settings' => SiteSetting::query()->pluck('value', 'key')->toArray(),
            'navigationItems' => NavigationItem::query()->where('is_enabled', true)->orderBy('sort_order')->get(),
            'footerGroups' => FooterItem::query()->where('is_enabled', true)->orderBy('group')->orderBy('sort_order')->get()->groupBy('group'),
            'user' => $user,
            'role' => $role,
            'metrics' => $this->metrics($role),
            'missions' => $this->missions($role),
            'quickActions' => $this->quickActions($role),
        ]);
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'role' => ['required', 'in:student,parent,teacher,professional'],
            'learning_stage' => ['nullable', 'string', 'max:120'],
            'avatar_style' => ['nullable', 'string', 'max:120'],
        ]);

        $request->user()->update($data);

        return back()->with('status', 'Profile updated.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        return back()->with('status', 'Account safety changes are handled by an administrator for now.');
    }

    private function metrics(string $role): array
    {
        return match ($role) {
            'parent' => [['Weekly Focus','3h 20m','🎯'],['Lessons Completed','28','📚'],['Confidence Score','85%','💜']],
            'teacher' => [['Active Classes','5','🏫'],['Students','120','👥'],['Assignments','12','✅']],
            'professional' => [['Learning Paths','8','🧭'],['Saved Resources','16','⭐'],['Progress Health','92%','📈']],
            default => [['Level','12','⭐'],['Buddy Coins','320','🪙'],['Study Streak','7 days','🔥']],
        };
    }

    private function missions(string $role): array
    {
        return match ($role) {
            'parent' => ['Review weekly progress', 'Choose a calm routine', 'Read one support tip'],
            'teacher' => ['Plan one practice block', 'Review class strengths', 'Share an app with learners'],
            'professional' => ['Explore apps', 'Save useful support info', 'Check account settings'],
            default => ['Complete 2 Math Quest lessons', 'Read a Reading Garden story', 'Try one Focus Forest session'],
        };
    }

    private function quickActions(string $role): array
    {
        return match ($role) {
            'parent' => [['Parents Guide','/for-parents'],['Support','/support'],['Privacy','/privacy-policy']],
            'teacher' => [['Teacher Page','/for-teachers'],['Apps','/apps'],['Contact','/contact-us']],
            'professional' => [['About','/about-us'],['Apps','/apps'],['Support','/support']],
            default => [['Explore Apps','/apps'],['Get Support','/support'],['Contact','/contact-us']],
        };
    }
}
