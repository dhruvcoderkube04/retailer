<?php

namespace App\Http\Controllers\API\Retailer;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\CustomerCart;
use App\Models\CustomerDetails;
use App\Models\CustomerOrders;
use App\Models\OrderProductDetails;
use App\Models\Product;
use App\Models\RetailerCloneProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;


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

    public function verifyOtpLogin(Request $request)
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
            Cache::put('otp_verified_' . $validated['mobile'], true, now()->addMinutes(5));

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
                $productType = null;

                if (!is_null($item->product_id)) {
                    $product = Product::find($item->product_id);
                    $productType = 'wholesaler';
                } elseif (!is_null($item->retailer_product_id)) {
                    $product = RetailerCloneProduct::find($item->retailer_product_id);
                    $productType = 'retailer';
                }

                if ($product) {
                    $baseData = [
                        'wishlist_id' => $item->id,
                        'product_id' => $productType === 'wholesaler' ? $product->id : null,
                        'retailer_product_id' => $productType === 'retailer' ? $product->id : null,
                        'product_name' => $product->name ?? "",
                        'product_image' => explode(',', $product->images),
                        'price' => $product->new_price ?? "",
                        'product_link' => url('/api/singal-product-details/' . $product->slug),
                    ];

                    if ($item->type === 'wishlist') {
                        $wishlistItems[] = array_merge($baseData, [
                            'added_on' => \Carbon\Carbon::parse($item->created_at)->diffForHumans(),
                        ]);
                    } elseif ($item->type === 'cart') {
                        $cartItems[] = array_merge($baseData, [
                            'quantity' => $item->quantity,
                        ]);
                    }
                }
            }

            $orders = CustomerOrders::where('customer_id', $customer->id)->get();
            $orderList = [];

            foreach ($orders as $order) {
                $product = OrderProductDetails::find($order->order_product_id);

                if (!$product) {
                    Log::warning("OrderProductDetails not found for order_id: {$order->id}");
                    continue;
                }

                $productLink = Product::find($product->product_id ?? $product->id);

                if (!$productLink) {
                    Log::warning("Product not found for order_product_id: {$product->id}");
                    continue;
                }

                $productUrl = url('/api/singal-product-details/' . $productLink->slug);
                $imageArray = explode(',', $product->images ?? '');

                $orderList[] = [
                    'order_id' => $order->id,
                    'product_id' => $product->product_id ?? $product->id,
                    'product_name' => $productLink->name ?? null,
                    'image' => $imageArray,
                    'price' => $productLink->new_price ?? null,
                    'order_date' => $order->created_at->format('d F Y'),
                    'product_link' => $productUrl,
                    'checkout_type' => $order->checkout_type,
                    'status' => $order->status,
                ];
            }

            return ApiResponse::success([
                'token' => $token,
                'customer' => $customerData,
                'wishlist_items' => $wishlistItems,
                'cart_items' => $cartItems,
                'orders' => $orderList,
            ], 'OTP verified successfully.');
        }

        return ApiResponse::error('Invalid or expired OTP. Please try again.');
    }

    public function verifyOtpCheckout(Request $request)
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

            Cache::put('otp_verified_' . $validated['mobile'], true, now()->addMinutes(15));

            $customer = CustomerDetails::where('phone_number', $validated['mobile'])->first();

            // If customer doesn't exist, return success with nulls
            if (!$customer) {
                return ApiResponse::success([
                    'token' => null,
                    'customer' => null,
                    'wishlist_items' => [],
                    'cart_items' => [],
                    'orders' => [],
                ], 'OTP verified successfully.');
            }

            // Token for existing customer
            $token = $customer->createToken('customer-token')->plainTextToken;
            $customerData = collect($customer)->except(['id', 'user_id', 'created_at', 'updated_at']);

            $customerCartItems = CustomerCart::where('customer_id', $customer->id)->get();
            $wishlistItems = [];
            $cartItems = [];

            foreach ($customerCartItems as $item) {
                $product = null;
                $product_type = null;

                if (!is_null($item->product_id)) {
                    $product = Product::find($item->product_id);
                    $product_type = 'wholesaler';
                } elseif (!is_null($item->retailer_product_id)) {
                    $product = RetailerCloneProduct::find($item->retailer_product_id);
                    $product_type = 'retailer';
                }

                if ($product) {
                    $productData = [
                        'wishlist_id' => $item->id,
                        'product_id' => $product_type === 'wholesaler' ? $product->id : null,
                        'retailer_product_id' => $product_type === 'retailer' ? $product->id : null,
                        'product_name' => $product->name ?? "",
                        'product_image' => explode(',', $product->images),
                        'price' => $product->new_price ?? "",
                        'product_link' => url('/api/singal-product-details/' . $product->slug),
                    ];

                    if ($item->type === 'wishlist') {
                        $wishlistItems[] = array_merge($productData, [
                            'added_on' => \Carbon\Carbon::parse($item->created_at)->diffForHumans(),
                        ]);
                    } elseif ($item->type === 'cart') {
                        $cartItems[] = array_merge($productData, [
                            'quantity' => $item->quantity,
                        ]);
                    }
                }
            }

            $orders = CustomerOrders::where('customer_id', $customer->id)->get();
            $orderList = [];

            foreach ($orders as $order) {
                $product = OrderProductDetails::find($order->order_product_id);

                if (!$product) {
                    Log::warning("OrderProductDetails not found for order_id: {$order->id}");
                    continue;
                }

                $product_link = Product::find($product->product_id ?? $product->id);

                if (!$product_link) {
                    Log::warning("Product not found for order_product_id: {$product->id}");
                    continue;
                }

                $productUrl = url('/api/singal-product-details/' . $product_link->slug);
                $imageArray = explode(',', $product->images ?? '');

                $orderList[] = [
                    'order_id' => $order->id,
                    'product_id' => $product->product_id ?? $product->id,
                    'product_name' => $product_link->name ?? null,
                    'image' => $imageArray,
                    'price' => $product_link->new_price ?? null,
                    'order_date' => $order->created_at->format('d F Y'),
                    'product_link' => $productUrl,
                    'checkout_type' => $order->checkout_type,
                    'status' => $order->status
                ];
            }

            return ApiResponse::success([
                'token' => $token,
                'customer' => $customerData,
                'wishlist_items' => $wishlistItems,
                'cart_items' => $cartItems,
                'orders' => $orderList,
            ], 'OTP verified successfully.');
        }

        return ApiResponse::error('Invalid or expired OTP. Please try again.');
    }
}
