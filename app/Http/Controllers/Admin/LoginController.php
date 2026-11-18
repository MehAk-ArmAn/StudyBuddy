<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Str;
use App\Models\StudyBuddySecurityEvent;
use App\Support\StudyBuddyAdminAuthority as Authority;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function show(): View { return view('admin.login'); }
    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate(['email' => ['required', 'email'], 'password' => ['required', 'string']]);
        $credentials['email'] = Str::lower(trim($credentials['email']));
        $key = 'studybuddy-admin-login:'.hash('sha256', $credentials['email'].'|'.$request->ip());
        if (RateLimiter::tooManyAttempts($key, 5)) {
            throw ValidationException::withMessages(['email' => 'Too many attempts. Try again later.']);
        }
        if (Auth::attempt($credentials + ['is_admin' => true], false)) {
            RateLimiter::clear($key);
            $userId = Auth::id();
            if (!Authority::can(Auth::user(), 'platform.view')) {
                Auth::logout();
                return back()->withErrors(['email' => 'This administrator is not enabled for the new Control Room.'])->onlyInput('email');
            }
            if (config('studybuddy.admin_mfa_enabled')) {
                $code = (string) random_int(100000, 999999);
                Auth::logout();
                $request->session()->regenerate();
                $request->session()->put('studybuddy_admin_mfa', [
                    'user_id' => $userId,
                    'code_hash' => Hash::make($code),
                    'expires_at' => now()->addMinutes(10)->getTimestamp(),
                    'tries' => 0,
                    'remember' => $request->boolean('remember'),
                ]);
                try {
                    // Transactional email must be configured before enabling MFA.
                    $user = User::findOrFail($userId);
                    Mail::raw("Your StudyBuddy admin sign-in code is {$code}. It expires in 10 minutes. Never share this code.",
                        fn($message) => $message->to($user->email)->subject('StudyBuddy admin sign-in code'));
                } catch (\Throwable $error) {
                    $request->session()->forget('studybuddy_admin_mfa');
                    return redirect()->route('admin.login')->withErrors(['email' => 'Could not deliver the admin security code. Contact the system administrator.']);
                }
                return redirect()->route('admin.two-factor.show');
            }
            Auth::loginUsingId($userId, $request->boolean('remember'));
            $request->session()->regenerate();
            StudyBuddySecurityEvent::record('admin_login_success', 'info', $userId);
            return redirect()->intended(route('admin.dashboard'));
        }
        RateLimiter::hit($key, 900);
        $attempts = RateLimiter::attempts($key);
        if ($attempts === 3 || $attempts === 5) {
            StudyBuddySecurityEvent::record('admin_login_repeated_failure', 'critical', null, ['attempts' => $attempts]);
            $address = config('studybuddy.security_alert_email');
            if (filled($address)) {
                try {
                    Mail::raw('StudyBuddy detected repeated unsuccessful administrator login attempts. Review the Security Events panel and your application logs.',
                        fn ($message) => $message->to($address)->subject('StudyBuddy security alert'));
                } catch (\Throwable $error) { /* Avoid denying login just because SMTP is down. */ }
            }
        }
        return back()->withErrors(['email' => 'The provided admin credentials were not accepted.'])->onlyInput('email');
    }
    public function showTwoFactor(Request $request): View|RedirectResponse
    {
        $challenge = $request->session()->get('studybuddy_admin_mfa');
        if (!$challenge || ($challenge['expires_at'] ?? 0) < time()) {
            $request->session()->forget('studybuddy_admin_mfa');
            return redirect()->route('admin.login');
        }
        return view('admin.two-factor');
    }

    public function verifyTwoFactor(Request $request): RedirectResponse
    {
        $data = $request->validate(['code' => ['required', 'digits:6']]);
        $challenge = $request->session()->get('studybuddy_admin_mfa');
        if (!$challenge || ($challenge['expires_at'] ?? 0) < time() || ($challenge['tries'] ?? 0) >= 5) {
            $request->session()->forget('studybuddy_admin_mfa');
            return redirect()->route('admin.login')->withErrors(['email' => 'Security challenge expired. Sign in again.']);
        }
        if (!Hash::check($data['code'], $challenge['code_hash'])) {
            $challenge['tries']++;
            $request->session()->put('studybuddy_admin_mfa', $challenge);
            if ($challenge['tries'] >= 5) { $request->session()->forget('studybuddy_admin_mfa'); }
            StudyBuddySecurityEvent::record('admin_mfa_failure', 'warning', (int)$challenge['user_id']);
            return back()->withErrors(['code' => 'That code was not accepted. Check your email or sign in again.']);
        }
        $user = User::findOrFail($challenge['user_id']);
        abort_unless(Authority::can($user, 'platform.view'), 403);
        $request->session()->forget('studybuddy_admin_mfa');
        Auth::login($user, (bool)$challenge['remember']);
        $request->session()->regenerate();
        StudyBuddySecurityEvent::record('admin_mfa_success', 'info', $user->id);
        return redirect()->intended(route('admin.dashboard'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout(); $request->session()->invalidate(); $request->session()->regenerateToken(); return redirect()->route('admin.login');
    }
}
