<?php

namespace App\Http\Controllers;

use App\Models\StudyBuddySecurityEvent;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\View\View;

class PasswordRecoveryController extends Controller
{
    public function forgot(): View { return view('auth.forgot-password'); }

    public function send(Request $request): RedirectResponse
    {
        $data = $request->validate(['email' => ['required', 'email:rfc', 'max:190']]);
        Password::sendResetLink(['email' => Str::lower(trim($data['email']))]);
        // Non-enumerating message prevents disclosing which adults/students have accounts.
        return back()->with('status', 'If this email has a StudyBuddy account, a password-reset link has been sent.');
    }

    public function resetForm(string $token, Request $request): View
    {
        return view('auth.reset-password', ['token' => $token, 'email' => $request->query('email')]);
    }

    public function reset(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'email:rfc', 'max:190'],
            'password' => ['required', 'confirmed', PasswordRule::min(12)->letters()->numbers()],
        ]);
        $status = Password::reset($data, function (User $user, string $password) {
            $user->forceFill(['password' => Hash::make($password), 'remember_token' => Str::random(60)])->save();
            if (config('session.driver') === 'database') {
                DB::table('sessions')->where('user_id', $user->id)->delete();
            }
            StudyBuddySecurityEvent::record('password_reset', 'warning', $user->id);
        });
        if ($status !== Password::PASSWORD_RESET) {
            return back()->withErrors(['email' => 'The reset link is invalid or expired. Please request a new link.']);
        }
        return redirect()->route('login')->with('status', 'Password updated. Sign in with your new password.');
    }
}
