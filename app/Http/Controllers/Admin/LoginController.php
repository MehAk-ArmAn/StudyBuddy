<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function show(): View { return view('admin.login'); }
    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate(['email' => ['required','email'], 'password' => ['required','string']]);
        if (Auth::attempt($credentials + ['is_admin' => true], $request->boolean('remember'))) {
            $request->session()->regenerate(); return redirect()->intended(route('admin.dashboard'));
        }
        return back()->withErrors(['email' => 'The provided admin credentials were not accepted.'])->onlyInput('email');
    }
    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout(); $request->session()->invalidate(); $request->session()->regenerateToken(); return redirect()->route('admin.login');
    }
}
