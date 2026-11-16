<?php

namespace App\Http\Controllers;

use App\Models\SavedQuest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SavedQuestController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $quests = SavedQuest::query()
            ->where('user_id', $user->id)
            ->latest()
            ->paginate(12);

        $stats = [
            'total' => SavedQuest::where('user_id', $user->id)->count(),
            'saved' => SavedQuest::where('user_id', $user->id)->where('status', 'saved')->count(),
            'in_progress' => SavedQuest::where('user_id', $user->id)->where('status', 'in_progress')->count(),
            'completed' => SavedQuest::where('user_id', $user->id)->where('status', 'completed')->count(),
        ];

        return view('dashboard.my-quest', [
            'quests' => $quests,
            'stats' => $stats,
        ]);
    }

    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'app_slug' => ['nullable', 'string', 'max:120'],
            'app_title' => ['nullable', 'string', 'max:180'],
            'mission_title' => ['required', 'string', 'max:220'],
            'mission_description' => ['nullable', 'string', 'max:3000'],
            'difficulty' => ['nullable', 'string', 'max:80'],
            'estimated_minutes' => ['nullable', 'integer', 'min:1', 'max:1440'],
            'source_url' => ['nullable', 'string', 'max:500'],
            'metadata' => ['nullable', 'array'],
        ]);

        $appSlug = $validated['app_slug']
            ?? Str::slug($validated['app_title'] ?? 'studybuddy');

        if (! $appSlug) {
            $appSlug = 'studybuddy';
        }

        $payload = array_merge($validated, [
            'user_id' => $request->user()->id,
            'app_slug' => $appSlug,
            'status' => 'saved',
            'progress' => 0,
        ]);

        $quest = SavedQuest::updateOrCreate(
            [
                'user_id' => $request->user()->id,
                'app_slug' => $appSlug,
                'mission_title' => $validated['mission_title'],
            ],
            $payload
        );

        if ($request->expectsJson()) {
            return response()->json([
                'ok' => true,
                'message' => 'Mission saved to My Quest.',
                'quest' => $quest,
            ], 201);
        }

        return redirect()
            ->route('studybuddy.quests.index')
            ->with('status', 'Mission saved to My Quest.');
    }

    public function update(Request $request, SavedQuest $savedQuest): JsonResponse|RedirectResponse
    {
        $quest = SavedQuest::where('user_id', $request->user()->id)
            ->findOrFail($savedQuest->id);

        $validated = $request->validate([
            'status' => ['nullable', 'string', 'in:saved,in_progress,completed,archived'],
            'progress' => ['nullable', 'integer', 'min:0', 'max:100'],
            'notes' => ['nullable', 'string', 'max:3000'],
        ]);

        if (($validated['status'] ?? null) === 'in_progress' && ! $quest->started_at) {
            $validated['started_at'] = now();
        }

        if (($validated['status'] ?? null) === 'completed') {
            $validated['progress'] = 100;
            $validated['completed_at'] = now();
        }

        if (($validated['status'] ?? null) !== 'completed' && array_key_exists('status', $validated)) {
            $validated['completed_at'] = null;
        }

        $quest->update($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'ok' => true,
                'message' => 'Quest updated.',
                'quest' => $quest->fresh(),
            ]);
        }

        return back()->with('status', 'Quest updated.');
    }

    public function destroy(Request $request, SavedQuest $savedQuest): JsonResponse|RedirectResponse
    {
        $quest = SavedQuest::where('user_id', $request->user()->id)
            ->findOrFail($savedQuest->id);

        $quest->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'ok' => true,
                'message' => 'Quest removed.',
            ]);
        }

        return back()->with('status', 'Quest removed.');
    }
}
