<?php

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
