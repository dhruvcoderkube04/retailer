<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email|max:255',
            'password' => 'required|min:8|max:20',
        ]);

        $user = User::where('email', $request->email)->first();

        // Account Lock Check
        if ($user && $user->locked_until && $user->locked_until->isFuture()) {
            session()->flash('error', 'Account locked. Try again after ' . $user->locked_until->diffForHumans());
            return redirect()->route('login');
        }

        // Password Check
        if ($user && Hash::check($request->password, $user->password)) {
            if (!$user->hasVerifiedEmail()) {
                session()->flash('error', 'Please verify your email before logging in');
                return redirect()->route('login');
            }

            Auth::login($user);
            $user->update(['login_attempt' => 0, 'locked_until' => null]); // Reset attempts

            if ($user->user_type == 1) {
                return redirect()->route('admin.dashboard'); // Admin (user_type = 1)
            } elseif ($user->user_type == 2) {
                return redirect()->route('wholesaler.dashboard'); // Wholesaler (user_type = 2)
            } elseif ($user->user_type == 3) {
                return redirect()->route('retailer.dashboard'); // Normal User (user_type = 3)
            } else {
                return redirect('/'); // Fallback route
            }
        }

        // Failed Login - Increment Attempts
        if ($user) {
            $user->increment('login_attempt');

            if ($user->login_attempt >= 5) {
                $user->update(['locked_until' => Carbon::now()->addHours(24)]); // Lock for 24 hours
            }
        }

        session()->flash('error', 'Invalid credentials');
        return redirect()->route('login');
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/login');
    }
}
