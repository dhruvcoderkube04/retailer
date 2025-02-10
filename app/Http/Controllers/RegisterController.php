<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserDetail;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function showRegistrationForm() {
        return view('auth.register');
    }

    public function register(Request $request) {
        // User Register
        // Form Validation
        $request->validate([
            'email' => 'required|max:255|email|unique:users,email',
            'password' => 'required|min:6|max:20|confirmed',
            'terms' => 'accepted', // Must be checked
        ], [
            'terms.accepted' => 'You must accept the Terms & Conditions.',
        ]);

        // Create User
        User::create([
            'firstname'=>'srianth',
            'lastname'=>'pradhan',
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'ip_address' => $request->ip(),
        ]);

        // // Create User Details
        // UserDetail::create([
        //     'user_id' => $user->id,
        //     'source' => $request->source,
        //     'email_verification_time' => now(), // Email verification time
        // ]);

        // event(new Registered($user)); // Trigger email verification if enabled
        return redirect()->route('login')->with('success', 'Registration successful! Please verify your email.');
    }

    public function forgetPassword()
    {
        // forget password
        return view('auth.login');
    }
}
