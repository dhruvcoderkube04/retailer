<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Illuminate\Database\QueryException;
use App\Models\User;
use App\Models\UserDetail;
use Carbon\Carbon;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

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
                'phone_number' => $request->phonenumber, // Fixed typo (phonenumber)
                'email' => $request->email,
                'user_type' => '3',
                'status' => '0',  //Inacitve during register
                'password' => Hash::make($request->password),
                'ip_address' => $request->ip(),
            ]);

            // Create User Details
            UserDetail::create([
                'user_id' => $user->id,
                'company_name' => $request->companyname,
            ]);

            // Attempt to send verification email
            try {
                $user->notify(new VerifyEmail);
            } catch (Exception $e) {
                \Log::error('Failed to send verification email: ' . $e->getMessage());

                return redirect()->route('retailer.login')
                    ->with('success', 'Registration successful! However, we could not send the verification email. Please try resending it from your account.');
            }

            return redirect()->route('retailer.login')->with('success', 'Registration successful! Please check your email to verify your account.');

        } catch (QueryException $e) {
            if ($e->errorInfo[1] === 1062) { // MySQL duplicate entry error
                return redirect('retailer/register')
                    ->withErrors(['email' => 'This email address is already registered.'])
                    ->withInput();
            }

            \Log::error('Database error during registration: ' . $e->getMessage());
            return redirect('retailer/register')
                ->withErrors(['error' => 'An error occurred during registration. Please try again later.'])
                ->withInput();
        } catch (Exception $e) {
            \Log::error('Unexpected error during registration: ' . $e->getMessage());
            return redirect('retailer/register')
                ->withErrors(['error' => 'An unexpected error occurred. Please try again later.'])
                ->withInput();
        }
    }


    public function forgetPassword()
    {
        // forget password
        return view('auth.forgotPassword');
    }
    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->with('error', 'This email is not registered with us.');
        }

        // Check if account is inactive
        if ($user->status == 0) {
            return back()->with('error', 'Your account is inactive. Please contact our support team.');
        }

        try {
            // Generate token
            $token = Str::random(60);

            // Store in password_reset_tokens table
            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $user->email],
                [
                    'token' => $token,
                    'created_at' => Carbon::now(),
                ]
            );

            // Send reset email
            Mail::send('emails.password_reset', ['token' => $token, 'email' => $user->email], function ($message) use ($user) {
                $message->to($user->email);
                $message->subject('Password Reset Request');
            });

            return back()->with('success', 'A password reset link has been sent to your email.');

        } catch (Exception $e) {
            Log::error('Password reset email failed: ' . $e->getMessage());
            return back()->with('error', 'An unexpected error occurred. Please try again later.');
        }
    }

    public function showResetPasswordForm($token)
    {

        $resetToken = DB::table('password_reset_tokens')->where('token', $token)->first();
        if (!$resetToken) {
            return redirect()->route('retailer.login')->with('error', 'Invalid or expired reset token.');
        }

        return view('auth.resetPassword', [
            'token' => $token,
            'email' => $resetToken->email
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        try {
            // Find token entry in password_reset_tokens table
            $resetEntry = DB::table('password_reset_tokens')->where('token', $request->token)->first();

            if (!$resetEntry) {
                return back()->withErrors(['error' => 'Invalid or expired reset token.']);
            }

            // Find user by email
            $user = User::where('email', $resetEntry->email)->first();

            if (!$user) {
                return back()->withErrors(['error' => 'No user found for this email.']);
            }

            // Update password
            $user->update([
                'password' => Hash::make($request->password),
            ]);

            // Delete reset token
            DB::table('password_reset_tokens')->where('email', $resetEntry->email)->delete();

            return redirect()->route('retailer.login')->with('success', 'Your password has been reset successfully.');
        } catch (Exception $e) {
            \Log::error('Password reset failed: ' . $e->getMessage());
            return back()->withErrors(['error' => 'An unexpected error occurred. Please try again later.']);
        }
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

        // First: Check if user exists and password is correct
        if (!$user || !Hash::check($request->password, $user->password)) {
            session()->flash('error', 'Invalid credentials');
            return redirect()->route('retailer.login');
        }

        // Check if account is locked
        if ($user->locked_until && $user->locked_until->isFuture()) {
            session()->flash('error', 'Account locked. Try again after ' . $user->locked_until->diffForHumans());
            return redirect()->route('retailer.login');
        }

        // Check if account is inactive
        if ($user->status == 0) {
            session()->flash('error', 'Your account is currently inactive. Please contact support for assistance.');
            return redirect()->route('retailer.login');
        }

        // Check user type (must be retailer)
        if ($user->user_type != 3 ) {
            session()->flash('error', 'Invalid credentials');
            return redirect()->route('retailer.login');
        }

        // Check email verification if needed
        if (!$user->hasVerifiedEmail()) {
            session()->flash('error', 'Please verify your email before logging in');
            return redirect()->route('retailer.login');
        }

        // Successful login
        Auth::login($user);
        $user->update(['login_attempt' => 0, 'locked_until' => null]); // Reset login attempts
        return redirect()->route('retailer.dashboard');
    }


    public function logout()
    {
        Auth::logout();
        return redirect('login');
    }
}
