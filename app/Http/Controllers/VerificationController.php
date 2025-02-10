<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    public function notice() {
        return view('auth.verify-email');
    }

    public function verify(EmailVerificationRequest $request) {
        $request->fulfill();
        return redirect('/dashboard');
    }

    public function resend(Request $request) {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect('/dashboard');
        }

        $request->user()->sendEmailVerificationNotification();
        return back()->with('message', 'Verification email sent!');
    }
}
