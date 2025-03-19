<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserDetail;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class RetailerAuthController extends Controller
{
    public function showRegistrationForm() {
        return view('auth.register');
    }

    public function register(Request $request) {

        $validator = Validator::make($request->all(), [
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'companyname' => 'required|string|max:255',
            'phonenumber' => 'required|string|max:20',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()->symbols()],
            'password_confirmation' => 'required',
            'toc' => 'required|accepted',
        ]);

        if ($validator->fails()) {
            return redirect('register')
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Create User
            $user = User::create([
                'firstname' => $request->firstname,
                'lastname' => $request->lastname,
                'phone_number' => $request->phone_number,
                'email' => $request->email,
                'user_type' => '3',
                'status' => '0',
                'password' => Hash::make($request->password),
                'ip_address' => $request->ip(),
                'email_verified_at' => now(), // Email verification time
            ]);

            // Create User Details
            UserDetail::create([
                'user_id' => $user->id,
                'company_name' => $request->companyname, // Use companyname
            ]);

            // event(new Registered($user)); // Trigger email verification if enabled
            return redirect()->route('retailer.login')->with('success', 'Registration successful! Please verify your email.');

        } catch (QueryException $e) {
            // Handle database errors (e.g., duplicate email)
            if ($e->errorInfo[1] === 1062) { // MySQL duplicate entry error code
                return redirect('retailer/register')
                    ->withErrors(['email' => 'This email address is already registered.'])
                    ->withInput();
            }

            // Log other database errors
            \Log::error('Database error during registration: ' . $e->getMessage());

            return redirect('retailer/register')
                ->withErrors(['error' => 'An error occurred during registration. Please try again later.'])
                ->withInput();
        } catch (\Exception $e) {
            // Handle other unexpected errors
            \Log::error('Unexpected error during registration: ' . $e->getMessage());

            return redirect('retailer/register')
                ->withErrors(['error' => 'An unexpected error occurred. Please try again later.'])
                ->withInput();
        }
    }

    public function forgetPassword()
    {
        // forget password
        return view('auth.login');
    }

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
            return redirect()->route('retailer.login');
        }

        // Password Check
        if ($user && Hash::check($request->password, $user->password)) {
            if (!$user->hasVerifiedEmail()) {
                session()->flash('error', 'Please verify your email before logging in');
                return redirect()->route('retailer.login');
            }

            Auth::login($user);
            $user->update(['login_attempt' => 0, 'locked_until' => null]); // Reset attempts

            if ($user->user_type == 3) {
                return redirect()->route('retailer.dashboard');
            } else {
                return redirect('/');
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
        return redirect()->route('retailer.login');
    }

    public function logout()
    {
        Auth::logout();
        return redirect('login');
    }
}
