<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class VerificationController extends Controller
{
    // Show the verification notice page
    public function show()
    {
        return view('auth.verify-email');
    }

    // Handle email verification when user clicks the link
    public function verify(Request $request, $id, $hash)
    {
        // ✅ Get user by ID
        $user = User::findOrFail($id);

        // ✅ Verify the email hash manually (bypass Laravel's auth requirement)
        if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            return redirect('/')->with('error', 'Invalid verification link.');
        }

        // ✅ If user is already verified, just redirect
        if ($user->hasVerifiedEmail()) {
            return redirect()->route('retailer.login')->with('success', 'Email already verified.');
        }

        // ✅ Mark email as verified
        $user->markEmailAsVerified();
        $user->email_verified = 1; // Manually update `email_verified` field
        $user->save(); // Save changes

        // ✅ Redirect user after successful verification
        return redirect()->route('retailer.login')->with('success', 'Email verified successfully!');
    }

    // Resend verification email
    public function resend(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect('/home')->with('message', 'Your email is already verified.');
        }

        try {
            $request->user()->sendEmailVerificationNotification();
            return back()->with('message', 'Verification email resent!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to send verification email. Try again later.');
        }
    }
}
