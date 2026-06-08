<?php

namespace App\Http\Middleware;

use App\Models\AdminUser;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        $adminId = $request->session()->get('admin_user_id');
        $admin = $adminId ? AdminUser::query()->where('is_active', true)->find($adminId) : null;

        if (! $admin) {
            $request->session()->forget('admin_user_id');
            return redirect()->route('admin.login');
        }

        $request->attributes->set('admin_user', $admin);

        return $next($request);
    }
}
