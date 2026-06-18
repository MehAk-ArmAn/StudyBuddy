<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserAccessController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login', ['pageTitle' => 'Welcome back to StudyBuddy']);
    }

    public function showRegister(): View
    {
        return view('auth.register', ['pageTitle' => 'Create your StudyBuddy account']);
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'))->with('status', 'Welcome back. Your dashboard is ready.');
        }

        return back()->withErrors(['email' => 'We could not match those login details. Please check your email and access key.'])->onlyInput('email');
    }

    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:190', 'unique:users,email'],
            'role' => ['required', Rule::in(['student', 'parent', 'teacher', 'professional'])],
            'learning_stage' => ['nullable', 'string', 'max:120'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'],
            'learning_stage' => $data['learning_stage'] ?? null,
            'avatar_style' => $this->defaultAvatar($data['role']),
            'cosmic_points' => $this->startingPoints($data['role']),
            'is_admin' => false,
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard')->with('status', 'Account created. Welcome to your StudyBuddy space.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('status', 'You have been logged out safely.');
    }

    private function defaultAvatar(string $role): string
    {
        return match ($role) {
            'parent' => 'parent-guide',
            'teacher' => 'teacher-mentor',
            'professional' => 'cosmic-explorer',
            default => 'dolphin-cadet',
        };
    }

    private function startingPoints(string $role): int
    {
        return match ($role) {
            'parent', 'teacher' => 120,
            'professional' => 80,
            default => 50,
        };
    }
}
