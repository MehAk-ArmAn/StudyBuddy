<?php

namespace App\Http\Controllers;

use App\Models\StudyBuddySecurityEvent;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmailVerificationController extends Controller
{
    public function notice(Request $request): View|RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) { return redirect()->route('dashboard'); }
        return view('auth.verify-email');
    }
    public function verify(EmailVerificationRequest $request): RedirectResponse
    {
        $request->fulfill();
        StudyBuddySecurityEvent::record('email_verified', 'info', $request->user()->id);
        return redirect()->route('dashboard')->with('status', 'Your email address is verified.');
    }
    public function send(Request $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) { return redirect()->route('dashboard'); }
        $request->user()->sendEmailVerificationNotification();
        return back()->with('status', 'Verification link sent. Check your inbox and spam folder.');
    }
}
