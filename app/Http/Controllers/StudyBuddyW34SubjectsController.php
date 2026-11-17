<?php

namespace App\Http\Controllers;

use App\Models\StudyBuddyMiniAppPlatform;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\StudyBuddySubjectAssignment;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class StudyBuddyW34SubjectsController extends Controller
{
    private function subjects(): array
    {
        return config('studybuddy_w34_subjects.subjects', []);
    }

    public function index(Request $request): View
    {
        return $this->directory($request, null);
    }

    public function show(Request $request, string $slug): View
    {
        abort_unless(array_key_exists($slug, $this->subjects()), 404);
        return $this->directory($request, $slug);
    }

    private function directory(Request $request, ?string $selected): View
    {
        $subjectData = $this->subjects();
        $query = trim((string) $request->query('q', ''));
        $query = mb_substr($query, 0, 80);
        $stage = (string) $request->query('stage', '');
        if (! in_array($stage, ['', 'early-years', 'primary', 'secondary', 'all-ages'], true)) {
            $stage = '';
        }

        // Respect the same published and role visibility rules as StudyBuddy app pages.
        $visibleApps = StudyBuddyMiniAppPlatform::query()->active()->with('subjectAssignment')->get()
            ->filter(fn ($app) => $app->visibleForRole(auth()->user()?->role))
            ->values();

        $counts = [];
        foreach ($visibleApps as $app) {
            $item = $app->subjectAssignment;
            if ($item && isset($subjectData[$item->primary_subject])) {
                $counts[$item->primary_subject] = ($counts[$item->primary_subject] ?? 0) + 1;
            }
        }
        $cards = collect($subjectData)->filter(fn ($item, $slug) => ($counts[$slug] ?? 0) > 0)
            ->map(fn ($item, $slug) => ['slug' => $slug, 'label' => $item['label'], 'group' => $item['group'],
                'summary' => $item['summary'], 'count' => $counts[$slug]])->values();

        $results = $visibleApps->filter(function ($app) use ($subjectData, $selected, $query, $stage) {
            $tag = $app->subjectAssignment;
            if (! $tag || ! isset($subjectData[$tag->primary_subject])) return false;
            if ($selected !== null && $tag->primary_subject !== $selected) return false;
            if ($stage !== '' && $tag->learning_stage !== $stage) return false;
            return $query === '' || mb_stripos($app->name, $query) !== false
                || mb_stripos((string) $tag->topics, $query) !== false;
        })->map(fn ($app) => ['app' => $app, 'tag' => $app->subjectAssignment,
            'subject' => $subjectData[$app->subjectAssignment->primary_subject]['label']])->values();

        return view('studybuddy.w34.directory', compact('cards', 'results', 'query', 'stage', 'selected', 'subjectData'));
    }

    public function edit(StudyBuddyMiniAppPlatform $app): View
    {
        return view('studybuddy.w34.editor', [
            'app' => $app,
            'subjects' => $this->subjects(),
            'assignment' => $app->subjectAssignment,
        ]);
    }

    public function update(Request $request, StudyBuddyMiniAppPlatform $app): RedirectResponse
    {
        $data = $request->validate([
            'primary_subject' => ['nullable', 'string', Rule::in(array_keys($this->subjects()))],
            'learning_stage' => ['nullable', Rule::in(['early-years', 'primary', 'secondary', 'all-ages'])],
            'topics' => ['nullable', 'string', 'max:400'],
            'learning_outcome' => ['nullable', 'string', 'max:1200'],
        ]);
        $primary = $data['primary_subject'] ?? null;
        if (! $primary) {
            $app->subjectAssignment()->delete();
            return back()->with('status', 'Subject removed. The app will not appear in the public subject directory.');
        }
        // updateOrCreate preserves the original created_at on later edits.
        StudyBuddySubjectAssignment::query()->updateOrCreate(
            ['app_id' => $app->id],
            [
                'primary_subject' => $primary,
                'learning_stage' => $data['learning_stage'] ?? null,
                'topics' => $data['topics'] ?? null,
                'learning_outcome' => $data['learning_outcome'] ?? null,
            ]
        );
        return back()->with('status', 'Learning classification saved. Only published, visible apps appear publicly.');
    }
}
