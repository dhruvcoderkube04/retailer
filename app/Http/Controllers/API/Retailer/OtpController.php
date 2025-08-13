<?php

namespace App\Http\Controllers\API\Retailer;

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
        $request->validate([
            'mobile' => 'required|digits:10',
        ]);

        $otp = rand(1000, 9999); // generate 4-digit OTP

        // Store in cache for 5 minutes
        Cache::put('otp_' . $request->mobile, $otp, now()->addMinutes(5));

        return response()->json([
            'success' => true,
            'message' => 'OTP sent successfully. Your OTP is ' . $otp,

            //'message' => 'OTP sent successfully.',
            'otp' => $otp // return it for testing
        ]);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'mobile' => 'required|digits:10',
            'otp' => 'required|digits:4',
        ]);
    
        $otpKey = 'otp_' . $request->mobile;
        $storedOtp = Cache::get($otpKey);
    
        if ($storedOtp && $storedOtp == $request->otp) {
            Cache::forget($otpKey); // Remove OTP
            Cache::put('otp_verified_' . $request->mobile, true, now()->addMinutes(15)); // Set verified flag
    
            // Get customer
            $customer = CustomerDetails::where('phone_number', $request->mobile)->first();
    
            if ($customer) {
                $token = $customer->createToken('customer-token')->plainTextToken;
                $customerData = collect($customer)->except(['id', 'user_id', 'created_at', 'updated_at']);
    
                // Get all cart/wishlist entries
                $customerCartItems = CustomerCart::where('customer_id', $customer->id)->get();
    
                $wishlistItems = [];
                $cartItems = [];
    
                foreach ($customerCartItems as $item) {
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
            } else {
                $token = null;
                $customerData = [];
                $wishlistItems = [];
                $cartItems = [];
            }
    
            return response()->json([
                'success' => true,
                'message' => 'OTP verified successfully.',
                'token' => $token,
                'customer' => $customerData,
                'wishlist_items' => $wishlistItems,
                'cart_items' => $cartItems,
            ]);
        }
    
        return response()->json([
            'success' => false,
            'message' => 'Invalid OTP.',
        ]);
    }
    

}
