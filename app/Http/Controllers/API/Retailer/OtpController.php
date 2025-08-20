<?php

namespace App\Http\Controllers\API\Retailer;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\CustomerCart;
use App\Models\CustomerDetails;
use App\Models\Product;
use App\Models\RetailerCloneProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class OtpController extends Controller
{
    public function sendOtp(Request $request)
    {
        $validatedData = $request->validate([
            'mobile' => ['required', 'regex:/^[6-9]\d{9}$/'],
        ], [
            'mobile.required' => 'Mobile number is required.',
            'mobile.regex' => 'Please enter a valid 10-digit mobile number starting with 6, 7, 8, or 9.',
        ]);

        $otp = rand(1000, 9999);

        Cache::put('otp_' . $validatedData['mobile'], $otp, now()->addMinutes(5));

        return ApiResponse::success(['otp' => $otp], 'OTP sent successfully.');
    }


    public function verifyOtp(Request $request)
    {
        $validated = $request->validate([
            'mobile' => ['required', 'regex:/^[6-9]\d{9}$/'],
            'otp' => ['required', 'digits:4'],
        ], [
            'mobile.required' => 'Mobile number is required.',
            'mobile.regex' => 'Please enter a valid 10-digit mobile number starting with 6, 7, 8, or 9.',
            'otp.required' => 'OTP is required.',
            'otp.digits' => 'OTP must be exactly 4 digits.',
        ]);

        $otpKey = 'otp_' . $validated['mobile'];
        $storedOtp = Cache::get($otpKey);


        if ($storedOtp && $storedOtp == $validated['otp']) {

            // Cache::forget($otpKey);

            Cache::put('otp_verified_' . $validated['mobile'], true, now()->addMinutes(15));

            $customer = CustomerDetails::where('phone_number', $validated['mobile'])->first();

            if (!$customer) {
                return ApiResponse::error("Your number is not registered. Please sign up first.");
            }

            $token = $customer->createToken('customer-token')->plainTextToken;

            $customerData = collect($customer)->except(['id', 'user_id', 'created_at', 'updated_at']);

            $customerCartItems = CustomerCart::where('customer_id', $customer->id)->get();
            $wishlistItems = [];
            $cartItems = [];

            foreach ($customerCartItems as $item) {
                $product = null;

                if (!is_null($item->product_id)) {
                    $product = Product::find($item->product_id);
                } elseif (!is_null($item->retailer_product_id)) {
                    $product = RetailerCloneProduct::find($item->retailer_product_id);
                }

                if ($product) {
                    if ($item->type === 'wishlist') {
                        $wishlistItems[] = [
                            'wishlist_id' => $item->id,
                            'product_id' => $product->id,
                            'product_name' => $product->name ?? "",
                            'product_image' => explode(',', $product->images),
                            'price' => $product->new_price ?? "",
                            'product_link' => url('/api/singal-product-details/' . $product->slug),
                            'added_on' => \Carbon\Carbon::parse($item->created_at)->diffForHumans(),
                        ];
                    } elseif ($item->type === 'cart') {
                        $cartItems[] = [
                            'wishlist_id' => $item->id,
                            'product_id' => $product->id,
                            'product_name' => $product->name ?? null,
                            'quantity' => $item->quantity,
                            'price' => $product->new_price ?? null,
                            'product_link' => url('/api/singal-product-details/' . $product->slug),
                        ];
                    }
                }
            }

            return ApiResponse::success([
                'token' => $token,
                'customer' => $customerData,
                'wishlist_items' => $wishlistItems,
                'cart_items' => $cartItems,
            ], 'OTP verified successfully.');

        }

        return ApiResponse::error('Invalid or expired OTP. Please try again.');
    }
}
