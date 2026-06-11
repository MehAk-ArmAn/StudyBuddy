<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function login(): View
    {
        return view('auth.login');
    }

    public function register(): View
    {
        return view('auth.register');
    }

    public function adminLogin(): View
    {
        return view('auth.admin-login');
    }

    public function loginSubmit(): RedirectResponse
    {
        return redirect()->route('student.dashboard');
    }

    public function registerSubmit(): RedirectResponse
    {
        return redirect()->route('student.dashboard');
    }

    public function adminLoginSubmit(): RedirectResponse
    {
        return redirect()->route('admin.dashboard');
    }
}
