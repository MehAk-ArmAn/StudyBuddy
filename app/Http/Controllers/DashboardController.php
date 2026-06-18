<?php

namespace App\Http\Controllers;

use App\Models\FooterItem;
use App\Models\NavigationItem;
use App\Models\SiteSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $role = $this->displayRole((string) $user->role);

        return view('dashboard.index', [
            'settings' => SiteSetting::query()->pluck('value', 'key')->toArray(),
            'navigationItems' => NavigationItem::query()->where('is_enabled', true)->orderBy('sort_order')->get(),
            'footerGroups' => FooterItem::query()->where('is_enabled', true)->orderBy('group')->orderBy('sort_order')->get()->groupBy('group'),
            'user' => $user,
            'role' => $role,
            'roleLabel' => $this->roleLabel($role),
            'metrics' => $this->metrics($role),
            'missions' => $this->missions($role),
            'quickActions' => $this->quickActions($role),
            'learningCards' => $this->learningCards($role),
        ]);
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'role' => ['required', Rule::in(['student', 'parent', 'teacher', 'professional'])],
            'learning_stage' => ['nullable', 'string', 'max:120'],
            'avatar_style' => ['nullable', 'string', 'max:120'],
        ]);

        $request->user()->update($data);
        return back()->with('status', 'Profile updated.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if (! Hash::check($data['current_password'], $request->user()->password)) {
            return back()->withErrors(['current_password' => 'Your current access key was not correct.']);
        }

        $request->user()->update(['password' => Hash::make($data['password'])]);
        return back()->with('status', 'Access key updated safely.');
    }

    private function displayRole(string $role): string
    {
        return match ($role) {
            'primary', 'secondary' => 'student',
            'parent' => 'parent',
            'teacher' => 'teacher',
            'professional' => 'professional',
            default => 'student',
        };
    }

    private function roleLabel(string $role): string
    {
        return match ($role) {
            'parent' => 'Parent Dashboard',
            'teacher' => 'Teacher Dashboard',
            'professional' => 'Professional Dashboard',
            default => 'Student Dashboard',
        };
    }

    private function metrics(string $role): array
    {
        return match ($role) {
            'parent' => [['Weekly Focus','3h 20m','🎯','Calm practice time'],['Lessons Completed','28','📚','Across all apps'],['Confidence Score','85%','💜','Growing steadily']],
            'teacher' => [['Active Classes','5','🏫','Ready to guide'],['Students','120','👥','Connected learners'],['Assignments','12','✅','This week']],
            'professional' => [['Learning Paths','8','🧭','Explore product areas'],['Saved Resources','16','⭐','For later review'],['Progress Health','92%','📈','Strong momentum']],
            default => [['Level','12','⭐','Star Learner'],['Buddy Coins','320','🪙','Spend in your world'],['Study Streak','7 days','🔥','Keep it going']],
        };
    }

    private function missions(string $role): array
    {
        return match ($role) {
            'parent' => ['Review weekly progress','Choose a calm routine','Read one support tip'],
            'teacher' => ['Plan one practice block','Review class strengths','Share an app with learners'],
            'professional' => ['Explore the apps library','Review safety and privacy pages','Save useful support info'],
            default => ['Complete 2 Math Quest lessons','Read a Reading Garden story','Try one Focus Forest session'],
        };
    }

    private function quickActions(string $role): array
    {
        return match ($role) {
            'parent' => [['Parents Guide','/for-parents'],['Support','/support'],['Privacy','/privacy-policy']],
            'teacher' => [['Teacher Page','/for-teachers'],['Apps','/apps'],['Contact','/contact-us']],
            'professional' => [['About','/about-us'],['Apps','/apps'],['Support','/support']],
            default => [['Explore Apps','/apps'],['Get Support','/support'],['Contact Us','/contact-us']],
        };
    }

    private function learningCards(string $role): array
    {
        return match ($role) {
            'parent' => [['Routine check','Make a gentle weekly routine for your learner.','💜'],['Progress glance','Spot strengths without overwhelming details.','📈'],['Safety center','Review privacy, support, and data choices.','🛡️']],
            'teacher' => [['Class energy','Plan a focused, friendly learning flow.','🏫'],['Assignments','Prepare practice tasks learners can finish.','✅'],['Resources','Keep apps and pages ready for classroom use.','📚']],
            'professional' => [['Product tour','Understand the StudyBuddy learning story.','🧭'],['Trust pages','Review privacy, data deletion, and support.','🛡️'],['Growth map','Use the pages to explore what comes next.','🚀']],
            default => [['Start small','Pick one mini app and complete a tiny win.','🎮'],['Stay calm','Use Focus Forest before a hard task.','🌿'],['Celebrate','Track your streak and collect buddy points.','⭐']],
        };
    }
}
