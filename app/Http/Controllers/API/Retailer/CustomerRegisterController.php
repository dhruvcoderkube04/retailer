<?php

namespace App\Http\Controllers\API\Retailer;

use App\Http\Controllers\Controller;
use App\Mail\WelcomeCustomerMail;
use App\Models\Customer;
use App\Models\CustomerCart;
use App\Models\CustomerDetails;
use App\Models\CustomerOrders;
use App\Models\OrderProductDetails;
use App\Models\Product;
use App\Models\RetailerCloneProduct;
use App\Models\RetailerWebManagement;
use App\Models\StoreCustomers;
use App\Models\StoreCustomersDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use App\Mail\WelcomeVerifyCustomerMail;
use App\Services\OtpService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Carbon;


class CustomerRegisterController extends Controller
{
    public function register(Request $request)
    {
        // Step 1: Validate input
        $request->validate([
            'user_token' => [
                'required',
                'string',
                function ($attribute, $value, $fail) use ($request) {
                    $existing = DB::table('store_customers_details')
                        ->where('email', $request->email)
                        ->where('user_token', $value)
                        ->first();

                    if ($existing) {
                        $fail('This email is already registered.');
                    }
                },
            ],
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'phone_number' => [
                'required',
                'regex:/^[6-9]\d{9}$/',
                function ($attribute, $value, $fail) {
                    if (preg_match('/^(\d)\1{9}$/', $value)) {
                        return $fail('The :attribute should not have all repeated digits.');
                    }

                    if (strpos('1234567890', $value) !== false || strpos('9876543210', $value) !== false) {
                        return $fail('The :attribute should not be a sequential or reverse-sequential number.');
                    }
                },
            ],
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        // Step 2: Validate token (Retailer exists & active)
        $retailer = RetailerWebManagement::where('product_listing_key', $request->user_token)
            ->where('is_active', 1)
            ->first();

        if (!$retailer) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid user token.'
            ], 404);
        }

        // Step 3: Register user, catch DB duplicate errors
        try {
            $verificationToken = Str::random(64);

            $customer = StoreCustomersDetails::create([
                'user_id' => $retailer->retailer_id,
                'firstname' => $request->firstname,
                'lastname' => $request->lastname,
                'phone_number' => $request->phone_number,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'user_token' => $request->user_token, // ✅ Save user-provided token
                'is_active' => false,
                'email_verification_token' => $verificationToken,
            ]);

            Mail::to($customer->email)->send(new WelcomeVerifyCustomerMail($customer));

            return response()->json([
                'status' => true,
                'message' => 'Customer registered. Please verify your email.'
            ], 201);
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() === '23000') {
                return response()->json([
                    'status' => false,
                    'message' => 'This email is already registered.'
                ], 409);
            }

            throw $e; // or log the unexpected error
        }
    }

    public function verifyEmail($token)
    {
        DB::beginTransaction();

        try {
            $customer = StoreCustomersDetails::where('email_verification_token', $token)->first();

            if (!$customer) {
                return response()->json(['status' => false, 'message' => 'Invalid or expired token.'], 400);
            }

            // Step 1: Update email verification status
            $customer->email_verified_at = now();
            $customer->email_verification_token = null;
            $customer->is_active = true;
            $customer->save();

            // Step 2: Check if customer_details exists
            $existing = CustomerDetails::where('user_id', $customer->user_id)
                ->where('email', $customer->email)
                ->first();

            // Step 3: Create if not exists, and update store table with its ID
            if (!$existing) {
                $newCustomerDetail = CustomerDetails::create([
                    'user_id' => $customer->user_id,
                    'firstname' => $customer->firstname,
                    'lastname' => $customer->lastname,
                    'phone_number' => $customer->phone_number,
                    'email' => $customer->email,
                ]);

                // Step 4: Store FK in store_customers_details
                $customer->customer_id = $newCustomerDetail->id;
                $customer->save();
            } else {
                // If already exists, still update FK if not set
                if (!$customer->customer_id) {
                    $customer->customer_id = $existing->id;
                    $customer->save();
                }
            }

            DB::commit();

            return response()->json(['status' => true, 'message' => 'Email verified successfully!']);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong during verification.',
                'error' => $e->getMessage()
            ], 500);
        }
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
        $customer = StoreCustomersDetails::where('user_id', $retailer->retailer_id)
            ->where('email', $request->email)
            ->where('is_active', true)
            ->first();

        if (!$customer) {
            return response()->json([
                'status' => false,
                'message' => 'Please verify your email.'
            ], 404);
        }

        // Check if email is verified
        if (is_null($customer->email_verified_at)) {
            return response()->json([
                'status' => false,
                'message' => 'Please verify your email before logging in.'
            ], 403);
        }

        // Check if account is active
        if (!$customer->is_active) {
            return response()->json([
                'status' => false,
                'message' => 'Your account is not active. Please contact support.'
            ], 403);
        }

        // Validate password
        if (!Hash::check($request->password, $customer->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Optional: Fetch full customer details if needed
        $customerDetails = CustomerDetails::where('id', $customer->customer_id)->first();

        $filteredCustomer = $customerDetails ? collect($customerDetails)->except([
            'id',
            'user_id',
            'created_at',
            'updated_at'
        ]) : collect($customer)->except([
                        'id',
                        'user_id',
                        'created_at',
                        'updated_at'
                    ]);


        $customerCart = CustomerCart::where('customer_id', $customerDetails->id)->get();

        $cartItems = [];
        $wishlistItems = [];

        foreach ($customerCart as $item) {
            if ($item->type === 'wishlist') {
                if (!is_null($item->product_id) && is_null($item->retailer_product_id)) {
                    $product = Product::select('name', 'slug', 'new_price')->find($item->product_id);
                } elseif (!is_null($item->retailer_product_id) && is_null($item->product_id)) {
                    $product = RetailerCloneProduct::select('name', 'slug', 'new_price')->find($item->retailer_product_id);
                } else {
                    $product = null;
                }

                if ($product) {
                    $wishlistItems[] = $product;
                }
            } elseif ($item->type === 'cart') {
                if (!is_null($item->product_id) && is_null($item->retailer_product_id)) {
                    $product = Product::select('name', 'slug', 'new_price')->find($item->product_id);
                } elseif (!is_null($item->retailer_product_id) && is_null($item->product_id)) {
                    $product = RetailerCloneProduct::select('name', 'slug', 'new_price')->find($item->retailer_product_id);
                } else {
                    $product = null;
                }

                if ($product) {
                    $cartItems[] = $product;
                }
            }
        }

        // Generate token
        $token = $customer->createToken('customer-token')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'Login successful.',
            'token' => $token,
            'user_token' => $request->user_token,
            'customer' => $filteredCustomer,
            'wishlist_items' => $wishlistItems,
            'cart_items' => $cartItems
        ]);

    }


    public function loginOtp(Request $request)
    {

        // $request->validate([
        //     'phone_number' => 'required|digits:10',
        // ]);

        // $otpService = new OtpService();

        // // Step 1: Send OTP if not present
        // if (!$request->has('otp')) {
        //     $otp = $otpService->send($request->phone_number);

        //     if (!$otp) {
        //         return response()->json([
        //             'error' => true,
        //             'message' => 'Failed to send OTP. Please try again.'
        //         ], 500);
        //     }

        //     return response()->json([
        //         'success' => true,
        //         'message' => 'OTP sent successfully',
        //         'otp_required' => true,
        //         'otp' => $otp // ✅ returning OTP in response
        //     ], 200);
        // }


        // // Step 2: Verify OTP
        // $request->validate([
        //     'otp' => 'required|digits:6',
        // ]);

        // if (!$otpService->verify($request->phone_number, $request->otp)) {
        //     return response()->json([
        //         'error' => true,
        //         'message' => 'Invalid or expired OTP.'
        //     ], 401);
        // }

        $request->validate([
            'phone_number' => 'required|digits:10',
        ]);

        // Check if this number has been verified
        if (!Cache::get('otp_verified_' . $request->phone_number)) {
            return response()->json([
                'error' => true,
                'message' => 'Phone number not verified. Please verify via OTP first.'
            ], 401);
        }

        // Optionally clear verification after use
        Cache::forget('otp_verified_' . $request->phone_number);

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
        $customer = StoreCustomersDetails::where('user_id', $retailer->retailer_id)
            ->where('phone_number', $request->phone_number)
            ->where('is_active', true)
            ->first();


        if (!$customer) {
            return response()->json([
                'status' => false,
                'message' => 'Please Verify your email.'
            ], 404);
        }

        // Optional: Fetch full customer details if needed
        $customerDetails = CustomerDetails::where('id', $customer->customer_id)->first();

        $filteredCustomer = $customerDetails ? collect($customerDetails)->except([
            'id',
            'user_id',
            'created_at',
            'updated_at'
        ]) : collect($customer)->except([
                        'id',
                        'user_id',
                        'created_at',
                        'updated_at'
                    ]);


        $customerCart = CustomerCart::where('customer_id', $customerDetails->id)->get();

        $cartItems = [];
        $wishlistItems = [];

        foreach ($customerCart as $item) {
            if ($item->type === 'wishlist') {
                if (!is_null($item->product_id) && is_null($item->retailer_product_id)) {
                    $product = Product::select('name', 'slug', 'new_price')->find($item->product_id);
                } elseif (!is_null($item->retailer_product_id) && is_null($item->product_id)) {
                    $product = RetailerCloneProduct::select('name', 'slug', 'new_price')->find($item->retailer_product_id);
                } else {
                    $product = null;
                }

                if ($product) {
                    $wishlistItems[] = $product;
                }
            } elseif ($item->type === 'cart') {
                if (!is_null($item->product_id) && is_null($item->retailer_product_id)) {
                    $product = Product::select('name', 'slug', 'new_price')->find($item->product_id);
                } elseif (!is_null($item->retailer_product_id) && is_null($item->product_id)) {
                    $product = RetailerCloneProduct::select('name', 'slug', 'new_price')->find($item->retailer_product_id);
                } else {
                    $product = null;
                }

                if ($product) {
                    $cartItems[] = $product;
                }
            }
        }

        // Generate token
        $token = $customer->createToken('customer-token')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'Login successful.',
            'token' => $token,
            'user_token' => $request->user_token,
            'customer' => $filteredCustomer,
            'wishlist_items' => $wishlistItems,
            'cart_items' => $cartItems
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


    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $customer = StoreCustomersDetails::where('email', $request->email)->first();

        if (!$customer) {
            return response()->json([
                'status' => false,
                'message' => 'No account found with this email.'
            ], 404);
        }

        // Prepare secure payload
        $payload = [
            'email' => $customer->email,
            'expires_at' => Carbon::now()->addMinutes(30)->timestamp, // expires in 30 mins
            'password_updated_at' => $customer->updated_at->timestamp, // for token invalidation
        ];

        $token = Crypt::encrypt($payload);

        // Send reset email
        Mail::to($customer->email)->send(new \App\Mail\CustomerPasswordResetMail($token));

        return response()->json([
            'status' => true,
            'token' => $token, // 🔐 Token included in API response
            'reset_link' => url("/reset-password?token={$token}"), // Optional
            'message' => 'Password reset link sent to your email.'
        ]);
    }


    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        try {
            $payload = Crypt::decrypt($request->token);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid or tampered token.'
            ], 400);
        }

        // Check token expiration
        if (Carbon::now()->timestamp > $payload['expires_at']) {
            return response()->json([
                'status' => false,
                'message' => 'Token has expired.'
            ], 400);
        }

        // Find the user
        $customer = StoreCustomersDetails::where('email', $payload['email'])->first();

        if (!$customer) {
            return response()->json([
                'status' => false,
                'message' => 'Customer not found.'
            ], 404);
        }

        // Check if password was already updated after token was issued
        if ($customer->updated_at->timestamp != $payload['password_updated_at']) {
            return response()->json([
                'status' => false,
                'message' => 'This reset link is no longer valid. Please request a new one.'
            ], 400);
        }

        // Update password
        $customer->update([
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Password has been reset successfully.'
        ]);
    }


    public function getCustomerDetails(Request $request)
    {
        $customer = auth()->user();

        return response()->json([
            'status' => true,
            'data' => [
                'id' => $customer->id,
                'name' => $customer->name,
                'email' => $customer->email,
            ]
        ]);
    }
}
