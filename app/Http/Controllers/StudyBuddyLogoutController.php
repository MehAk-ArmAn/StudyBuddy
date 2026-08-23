<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class StudyBuddyLogoutController extends Controller
{
    public function confirm(Request $request): View
    {
        if (! $request->user()) {
            return view('auth.goodbye', [
                'name' => null,
            ]);
        }

        return view('auth.logout-confirm', [
            'user' => $request->user(),
        ]);
    }

    public function destroy(Request $request): Response
    {
        $name = $request->user()?->name;

        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()
            ->view('auth.goodbye', [
                'name' => $name,
            ])
            ->header(
                'Cache-Control',
                'no-store, no-cache, must-revalidate, private'
            )
            ->header('Pragma', 'no-cache');
    }

    public function goodbye(): View
    {
        return view('auth.goodbye', [
            'name' => null,
        ]);
    }
}
