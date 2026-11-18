<?php

/**
 * STUDYBUDDY-TEAM-HANDOVER-2026
 * Developer orientation: Auth boundary: this middleware does not replace per-action Head Admin grants or school organization checks. Review permission tests before extending staff access.
 *
 * Documentation-only annotation; no runtime behavior is changed here.
 */


namespace App\Http\Middleware;

use App\Support\StudyBuddyAdminAuthority as Authority;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        if (!$user || !$user->is_admin) {
            return redirect()->route('admin.login')->withErrors(['email' => 'Please sign in with an administrator account.']);
        }
        $permission = Authority::requiredPermission($request);
        if ($permission !== null) abort_unless(Authority::can($user, $permission), 403);
        return $next($request);
    }
}
