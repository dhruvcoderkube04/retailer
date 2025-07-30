<?php

namespace App\Http\Controllers\API\Retailer;

use App\Http\Controllers\Controller;
use App\Mail\WelcomeCustomerMail;
use App\Models\Customer;
use App\Models\RetailerWebManagement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CustomerRegisterController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'user_token' => 'required|string',
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        $retailer = RetailerWebManagement::where('product_listing_key', $request->user_token)->where('is_active',1)->first();
        if (!$retailer) {
            return response()->json(['status' => false, 'message' => 'Invalid user token.'], 404);
        }

        $existing = Customer::where('user_id', $retailer->id)
            ->whereRaw('LOWER(email) = ?', [strtolower($request->email)])
            ->first();

        if ($existing) {
            return response()->json(['status' => false, 'message' => 'Email already registered.'], 409);
        }

        $verificationToken = Str::random(64);

        $customer = Customer::create([
            'user_id' => $retailer->id,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'user_token' => Str::random(60),
            'is_active' => false,
            'email_verification_token' => $verificationToken,
        ]);

        Mail::to($customer->email)->send(new WelcomeCustomerMail($customer));

        return response()->json([
            'status' => true,
            'message' => 'Customer registered. Please verify your email.',
        ], 201);
    }

    public function verifyEmail($token)
    {
        $customer = Customer::where('email_verification_token', $token)->first();

        if (!$customer) {
            return response()->json(['status' => false, 'message' => 'Invalid or expired token.'], 400);
        }

        $customer->email_verified_at = now();
        $customer->email_verification_token = null;
        $customer->is_active = true;
        $customer->save();

        return response()->json(['status' => true, 'message' => 'Email verified successfully!']);
    }

    public function login(Request $request)
    {
        $request->validate([
            'user_token' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        // Check valid retailer
        $retailer = RetailerWebManagement::where('product_listing_key', $request->user_token)
            ->where('is_active', 1)
            ->first();

        if (!$retailer) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid user token.'
            ], 404);
        }

        // Find customer
        $customer = Customer::where('user_id', $retailer->id)
            ->where('email', $request->email)
            ->where('is_active', true)
            ->first();
        
       
        if (!$customer) {
            return response()->json([
                'status' => false,
                'message' => 'Please Verify your email.'
            ], 404);
        }
       
        // Validate credentials
        if (!$customer || !Hash::check($request->password, $customer->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        if (is_null($customer->email_verified_at)) {
            return response()->json([
                'status' => false,
                'message' => 'Please verify your email before logging in.'
            ], 403);
        }

        if (!$customer->is_active) {
            return response()->json([
                'status' => false,
                'message' => 'Your account is not active. Please contact support.'
            ], 403);
        }
        // Create Sanctum token
        $token = $customer->createToken('customer-token')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'Login successful.',
            'token' => $token,
            'user_token' => $request->user_token,
            'data' => [
                'id' => $customer->id,
                'name' => $customer->name,
                'email' => $customer->email
            ]
        ]);
    }

    public function logout(Request $request)
    {
        $user = $request->user();

        if ($user) {
            $user->currentAccessToken()->delete();

            return response()->json([
                'status' => true,
                'message' => 'Logout successful'
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => 'User not authenticated'
        ], 401);
    }
}
