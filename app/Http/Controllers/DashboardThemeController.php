<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class DashboardThemeController extends Controller
{
    public const THEMES = [
        'cosmic-dolphin',
        'bts-purple-galaxy',
        'ocean-focus',
        'candy-pop',
        'forest-calm',
        'night-study',
        'solar-gold',
        'neon-gamer',
    ];

    public function update(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'theme' => ['required', 'string', Rule::in(self::THEMES)],
        ]);

        $theme = $validated['theme'];
        $user = $request->user();

        $updates = [];

        if (Schema::hasColumn('users', 'avatar_style')) {
            $updates['avatar_style'] = $theme;
        }

        if (Schema::hasColumn('users', 'dashboard_style')) {
            $updates['dashboard_style'] = $theme;
        }

        if (Schema::hasColumn('users', 'theme')) {
            $updates['theme'] = $theme;
        }

        if ($updates) {
            $user->forceFill($updates)->save();
        }

        session(['studybuddy_theme' => $theme]);

        if ($request->expectsJson()) {
            return response()->json([
                'ok' => true,
                'message' => 'Dashboard style saved.',
                'theme' => $theme,
            ]);
        }

        return back()->with('status', 'Dashboard style saved.');
    }
}
