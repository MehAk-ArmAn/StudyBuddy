<?php

namespace App\Http\Controllers;

use App\Models\SavedQuest;
use Carbon\Carbon;
use Illuminate\Http\Request;

class StudyBuddyCommandCenterController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = $request->user();
        $base = SavedQuest::query()->where('user_id', $user->id);
        $total = (clone $base)->count();
        $saved = (clone $base)->where('status', 'saved')->count();
        $active = (clone $base)->whereIn('status', ['active','started','in_progress'])->count();
        $done = (clone $base)->whereIn('status', ['done','completed','finished'])->count();
        $recent = (clone $base)->latest()->take(6)->get();
        $completion = $total ? (int) round(($done / $total) * 100) : 0;

        return view('dashboard.command-center', [
            'user' => $user,
            'totalQuests' => $total,
            'savedQuests' => $saved,
            'activeQuests' => $active,
            'completedQuests' => $done,
            'completionRate' => $completion,
            'recentQuests' => $recent,
            'streak' => $this->streak($user->id),
            'todayMission' => $this->todayMission((string) ($user->role ?? 'independent_learner'), $total, $done),
            'profileChecklist' => $this->profileChecklist($user),
            'weeklyFocus' => $this->weeklyFocus($total, $done),
            'rolePanel' => $this->rolePanel((string) ($user->role ?? 'independent_learner')),
        ]);
    }

    private function streak(int $userId): int
    {
        $days = SavedQuest::query()
            ->where('user_id', $userId)
            ->whereIn('status', ['done','completed','finished'])
            ->latest('updated_at')
            ->get(['updated_at'])
            ->map(fn ($q) => Carbon::parse($q->updated_at)->toDateString())
            ->unique()
            ->values();

        if ($days->isEmpty()) return 0;
        $cursor = Carbon::today();
        if (! $days->contains($cursor->toDateString())) $cursor = Carbon::yesterday();
        $count = 0;
        while ($days->contains($cursor->toDateString())) { $count++; $cursor->subDay(); }
        return $count;
    }

    private function todayMission(string $role, int $total, int $done): array
    {
        if ($total === 0) return ['tag'=>'First Mission','title'=>'Save your first quest','focus'=>'Open Apps, preview one mission, and save it into your Quest Vault.'];
        if ($done === 0) return ['tag'=>'First Win','title'=>'Complete your first saved quest','focus'=>'Open My Quest, start one mission, and mark it done after focused study.'];
        return match (strtolower($role)) {
            'student' => ['tag'=>'Student Boost','title'=>'Complete one mini-app warmup','focus'=>'Pick a tiny 10-minute mission and keep your learning streak alive.'],
            'parent' => ['tag'=>'Parent Guide','title'=>'Review one learning routine','focus'=>'Check saved quests and choose one simple study win for today.'],
            'teacher' => ['tag'=>'Teacher Prep','title'=>'Prepare one classroom activity','focus'=>'Explore Apps and save one classroom-ready learning mission.'],
            default => ['tag'=>'Self-Learner','title'=>'Build your learning streak','focus'=>'Save, start, or finish one StudyBuddy mission today.'],
        };
    }

    private function profileChecklist($user): array
    {
        $items = [
            ['label'=>'Display name added','done'=>filled($user->name ?? null)],
            ['label'=>'Email connected','done'=>filled($user->email ?? null)],
            ['label'=>'Role selected','done'=>filled($user->role ?? null)],
            ['label'=>'Real name added','done'=>filled($user->real_name ?? null)],
            ['label'=>'Country added','done'=>filled($user->country ?? null)],
            ['label'=>'Learning stage added','done'=>filled($user->learning_stage ?? null)],
        ];
        $done = collect($items)->where('done', true)->count();
        return ['items'=>$items, 'done'=>$done, 'total'=>count($items), 'percent'=>(int) round(($done / max(count($items),1)) * 100)];
    }

    private function weeklyFocus(int $total, int $done): array
    {
        return [
            ['title'=>'Save 3 quests','current'=>min($total,3),'target'=>3,'hint'=>'Build your mission vault from the Apps page.'],
            ['title'=>'Complete 2 quests','current'=>min($done,2),'target'=>2,'hint'=>'Turn saved missions into finished learning wins.'],
            ['title'=>'Open Command Center','current'=>1,'target'=>1,'hint'=>'Use this page as your learning control room.'],
        ];
    }

    private function rolePanel(string $role): array
    {
        return match (strtolower($role)) {
            'student' => ['title'=>'Student Learning Path','message'=>'Tiny daily wins matter: save a mission, complete it, and build your streak.','actions'=>['Open Apps','Check My Quest','Finish Today’s Mission']],
            'parent' => ['title'=>'Parent Support Path','message'=>'Guide routines, saved quests, and safe learning progress from one place.','actions'=>['Review quests','Pick today’s focus','Check consistency']],
            'teacher' => ['title'=>'Teacher Planning Path','message'=>'Use missions as classroom-ready activity ideas and reusable learning flows.','actions'=>['Browse activities','Save missions','Prepare learning sets']],
            default => ['title'=>'Independent Learner Path','message'=>'Design your own routine with saved quests, streaks, and weekly goals.','actions'=>['Save a quest','Start focused study','Mark progress']],
        };
    }
}
