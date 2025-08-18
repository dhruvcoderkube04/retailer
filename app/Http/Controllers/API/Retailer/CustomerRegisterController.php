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
use App\Helpers\ApiResponse;



class CustomerRegisterController extends Controller
{

    //Register API
    public function register(Request $request)
    {
        // Step 1: Validate request
        try {
            $request->validate([
                'user_token' => [
                    'required',
                    'string',
                ],
                'email' => [
                    'required',
                    'email',
                    function ($attribute, $value, $fail) use ($request) {
                        $existing = DB::table('store_customers_details')
                            ->where('email', $value)
                            ->where('user_token', $request->user_token)
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

                        if (
                            strpos('1234567890', $value) !== false ||
                            strpos('9876543210', $value) !== false
                        ) {
                            return $fail('The :attribute should not be a sequential or reverse-sequential number.');
                        }
                    },
                ],
                'password' => 'required|string|min:6',
            ]);
        } catch (ValidationException $e) {
            return ApiResponse::error('Validation failed', $e->errors());
        }

        // Step 2: Validate retailer token
        $retailer = RetailerWebManagement::where('product_listing_key', $request->user_token)
            ->where('is_active', 1)
            ->first();

        if (!$retailer) {
            return ApiResponse::error('Invalid user token.');
        }

        // Step 3: Start DB transaction
        DB::beginTransaction();

        try {
            $verificationToken = Str::random(64);

            $customer = StoreCustomersDetails::create([
                'user_id' => $retailer->retailer_id,
                'firstname' => $request->firstname,
                'lastname' => $request->lastname,
                'phone_number' => $request->phone_number,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'user_token' => $request->user_token,
                'is_active' => false,
                'email_verification_token' => $verificationToken,
            ]);

            // Send verification email
            Mail::to($customer->email)->send(new WelcomeVerifyCustomerMail($customer));

            DB::commit();

            return ApiResponse::success([], 'Customer registered. Please verify your email.');

        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();

            if ($e->getCode() === '23000') {
                return ApiResponse::error('This email is already registered.');
            }

            return ApiResponse::error('Database error.');
        } catch (\Exception $e) {
            DB::rollBack();

            return ApiResponse::error(
                config('app.debug') ? $e->getMessage() : 'An unexpected error occurred.'
            );
        }
    }

    //Verify Email
    public function verifyEmail($token)
    {
        DB::beginTransaction();
    
        try {
            $customer = StoreCustomersDetails::where('email_verification_token', $token)->first();
    
            if (!$customer) {
                return ApiResponse::error('Invalid or expired token.');
            }
    
            // Update email verification status
            $customer->email_verified_at = now();
            $customer->email_verification_token = null;
            $customer->is_active = true;
            $customer->save();
    
            // Check or create customer details record
            $existing = CustomerDetails::where('user_id', $customer->user_id)
                ->where('email', $customer->email)
                ->first();
    
            if (!$existing) {
                $newCustomerDetail = CustomerDetails::create([
                    'user_id' => $customer->user_id,
                    'firstname' => $customer->firstname,
                    'lastname' => $customer->lastname,
                    'phone_number' => $customer->phone_number,
                    'email' => $customer->email,
                ]);
    
                $customer->customer_id = $newCustomerDetail->id;
                $customer->save();
            } elseif (!$customer->customer_id) {
                $customer->customer_id = $existing->id;
                $customer->save();
            }
    
            DB::commit();
    
            return ApiResponse::success([], 'Email verified successfully!');
        } catch (\Throwable $e) {
            DB::rollBack();
    
            return ApiResponse::error('Something went wrong during verification.', [
                'error' => $e->getMessage()
            ]);
        }
    }

    //login API
    public function login(Request $request)
    {
        // Step 1: Validation
        try {
            $request->validate([
                'user_token' => 'required|string',
                'email' => 'required|email',
                'password' => 'required|string'
            ]);
        } catch (ValidationException $e) {
            return ApiResponse::error('Validation failed', $e->errors());
        }

        DB::beginTransaction();

        try {
            // Step 2: Check retailer
            $retailer = RetailerWebManagement::where('product_listing_key', $request->user_token)
                ->where('is_active', 1)
                ->first();

            if (!$retailer) {
                return ApiResponse::error('Invalid user token.');
            }

            // Step 3: Check customer
            $customer = StoreCustomersDetails::where('user_id', $retailer->retailer_id)
                ->where('email', $request->email)
                ->first();

            if (!$customer) {
                return ApiResponse::error('No account found with this email under this retailer.');
            }

            if (is_null($customer->email_verified_at)) {
                return ApiResponse::error('Please verify your email before logging in.');
            }

            if (!$customer->is_active) {
                return ApiResponse::error('Your account is not active. Please contact support.');
            }

            if (!Hash::check($request->password, $customer->password)) {
                return ApiResponse::error('Invalid credentials.');
            }

            // Step 4: Get customer details
            $customerDetails = CustomerDetails::where('id', $customer->customer_id)->first();
            $filteredCustomer = $customerDetails
                ? collect($customerDetails)->except(['id', 'user_id', 'created_at', 'updated_at'])
                : collect($customer)->except(['id', 'user_id', 'created_at', 'updated_at']);

            // Step 5: Cart & Wishlist
            $cartItems = [];
            $wishlistItems = [];

            $customerCart = CustomerCart::where('customer_id', $customer->customer_id)->get();

            foreach ($customerCart as $item) {
                $product = null;

                if (!is_null($item->product_id) && is_null($item->retailer_product_id)) {
                    $product = Product::select('name', 'slug', 'new_price')->find($item->product_id);
                } elseif (!is_null($item->retailer_product_id) && is_null($item->product_id)) {
                    $product = RetailerCloneProduct::select('name', 'slug', 'new_price')->find($item->retailer_product_id);
                }

                if ($product) {
                    if ($item->type === 'wishlist') {
                        $wishlistItems[] = $product;
                    } elseif ($item->type === 'cart') {
                        $cartItems[] = $product;
                    }
                }
            }

            // Step 6: Token creation
            $token = $customer->createToken('customer-token')->plainTextToken;

            DB::commit();

            // Step 7: Return consistent success response
            return ApiResponse::success([
                'token' => $token,
                'user_token' => $request->user_token,
                'customer' => $filteredCustomer,
                'wishlist_items' => $wishlistItems,
                'cart_items' => $cartItems
            ], 'Login successful.');

        } catch (\Exception $e) {
            DB::rollBack();

            return ApiResponse::error(
                config('app.debug') ? $e->getMessage() : 'An unexpected error occurred.'
            );
        }
    }

    //log-out API
    public function logout(Request $request)
    {
        $user = auth('sanctum')->user();

        if ($user) {
            $user->currentAccessToken()->delete();

            return ApiResponse::success([], 'Logout successful');
        }

        return ApiResponse::error('User not authenticated');
    }

    //forgot password API via Email
    public function forgotPassword(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
            ]);
        } catch (ValidationException $e) {
            return ApiResponse::error('Validation failed', $e->errors());
        }

        $customer = StoreCustomersDetails::where('email', $request->email)->first();

        if (!$customer) {
            return ApiResponse::error('No account found with this email.');
        }

        $payload = [
            'email' => $customer->email,
            'expires_at' => Carbon::now()->addMinutes(10)->timestamp,
            'password_updated_at' => $customer->updated_at->timestamp,
        ];

        $token = Crypt::encrypt($payload);

        Mail::to($customer->email)->send(new \App\Mail\CustomerPasswordResetMail($token));

        return ApiResponse::success([
            'token' => $token,
            'reset_link' => url("/reset-password?token={$token}"),
        ], 'Password reset link sent to your email.');
    }


    //reset password API via Email
    public function resetPassword(Request $request)
    {
        try {
            $request->validate([
                'token' => 'required|string',
                'password' => 'required|string|min:6|confirmed',
            ]);
        } catch (ValidationException $e) {
            return ApiResponse::error('Validation failed', $e->errors());
        }

        try {
            $payload = Crypt::decrypt($request->token);
        } catch (\Exception $e) {
            return ApiResponse::error('Invalid or tampered token.');
        }

        if (Carbon::now()->timestamp > $payload['expires_at']) {
            return ApiResponse::error('Token has expired.');
        }

        $customer = StoreCustomersDetails::where('email', $payload['email'])->first();

        if (!$customer) {
            return ApiResponse::error('Customer not found.');
        }

        if ($customer->updated_at->timestamp != $payload['password_updated_at']) {
            return ApiResponse::error('This reset link is no longer valid. Please request a new one.');
        }

        $customer->update([
            'password' => Hash::make($request->password),
        ]);

        return ApiResponse::success([], 'Password has been reset successfully.');
    }



    //unused API
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
