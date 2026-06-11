<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminUser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function login(): View
    {
        return view('admin.login');
    }

    public function authenticate(Request $request): RedirectResponse
    {
        $credentials = $request->validate(['email' => ['required', 'email'], 'password' => ['required', 'string']]);
        $admin = AdminUser::query()->where('email', $credentials['email'])->where('is_active', true)->first();

        if ($admin && Hash::check($credentials['password'], $admin->password)) {
            $request->session()->regenerate();
            $request->session()->put('admin_user_id', $admin->id);
            $admin->forceFill(['last_login_at' => now()])->save();

            return redirect()->route('admin.dashboard');
        }

        return back()->withErrors(['email' => ''])->onlyInput('email');
    }

    public function logout(Request $request): RedirectResponse
    {
        $request->session()->forget('admin_user_id');
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
