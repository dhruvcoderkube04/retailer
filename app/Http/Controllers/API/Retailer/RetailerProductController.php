<?php

namespace App\Http\Controllers\API\Retailer;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\CustomerDetails;
use App\Models\CustomerOrders;
use App\Models\RetailerProducts;
use App\Models\RetailerCloneProduct;
use App\Models\RetailerWebManagement;
use App\Models\UserDetail;
use Dotenv\Util\Regex;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class RetailerProductController extends Controller
{
    

public function getRetailerProducts(Request $request)
{
    try {
        $apiKey = $request->header('API-KEY');
        if (!$apiKey) {
            return response()->json(['error' => 'API Key is required.'], 401);
        }

        $retailer = RetailerWebManagement::where('product_listing_key', $apiKey)->first();
        if (!$retailer) {
            return response()->json(['error' => 'Unauthorized: Invalid API Key.'], 403);
        }

        // Fetch retailer products with wholesaler relation
        $retailerProducts = RetailerProducts::with(['wholesaler.products'])
            ->where('retailer_id', $retailer->retailer_id)
            ->get();

        // Fetch retailer clone products
        $retailerCloneProducts = RetailerCloneProduct::where('retailer_id', $retailer->retailer_id)
            ->get();

        // Merge both collections
        $allProducts = $retailerProducts->merge($retailerCloneProducts);

        // Process each record differently based on its source.
        $products = $allProducts->flatMap(function ($item) {
            if ($item instanceof RetailerProducts) {
                if (!$item->wholesaler) {
                    return [];
                }
                return $item->wholesaler->products->map(function ($product) use ($item) {
                    return $this->formatProductFromRetailerProduct($product, $item);
                });
            } else {
                return [$this->formatProductFromClone($item)];
            }
        })->values();

        // Extract categories separately
        $categoryIds = $products->pluck('category_id')->filter()->unique()->toArray();
        $categories = Category::whereIn('id', $categoryIds)->pluck('category_name')->toArray();

        // Implement pagination (8 products per page)
        $perPage = 8;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $productsCollection = new Collection($products);
        $currentPageItems = $productsCollection->slice(($currentPage - 1) * $perPage, $perPage)->values();
        $paginatedProducts = new LengthAwarePaginator($currentPageItems, $productsCollection->count(), $perPage);
        $paginatedProducts->setPath(url()->current());

        return response()->json([
            'success'    => true,
            'products'   => $paginatedProducts,
            'categories' => $categories,
        ]);

    } catch (\Exception $e) {
        \Log::error('Error fetching retailer products: ' . $e->getMessage());
        return response()->json(['error' => 'An unexpected error occurred.'], 500);
    }
}


    private function formatProductFromRetailerProduct($product, $retailerProduct)
    {
        $finalPrice = $product->new_price + $retailerProduct->margin;

        return [
            'id'              => $product->id,
            'sku'             => $product->sku,
            'name'            => $product->name,
            'slug'            => $product->slug,
            'description'     => $product->description,
            'category_id'     => $product->category_id,
            'wholesaler_id'   => $product->wholesaler_id,
            'new_price'       => $product->new_price,
            'final_price'     => $finalPrice,
            'quantity'        => $product->quantity,
            'product_images'  => $product->images ?? null,
            'product_video'   => $product->videos ?? null,
            'product_url'     => $product->url,
            'color'           => $product->color ?? null,
            'size'            => $product->size,
            'specifications'  => $product->specifications,
            'retailer_id'     => $product->retailer_id ?? null,
        ];
    }

    private function formatProductFromClone($cloneProduct)
    {
        $finalPrice = $cloneProduct->new_price + $cloneProduct->margin;

        return [
            'id'              => $cloneProduct->id,
            'sku'             => $cloneProduct->sku,
            'name'            => $cloneProduct->name,
            'slug'            => $cloneProduct->slug,
            'description'     => $cloneProduct->description,
            // If RetailerCloneProduct does not store category, this can be null or a default value.
            'category_id'     => $cloneProduct->category_id ?? null,
            'wholesaler_id'   => null, // No wholesaler relation here.
            'new_price'       => $cloneProduct->new_price,
            'final_price'     => $finalPrice,
            'quantity'        => $cloneProduct->quantity,
            'product_images'  => $cloneProduct->images ?? null,
            'product_video'   => $cloneProduct->videos ?? null,
            'product_url'     => $cloneProduct->url,
            'color'           => $cloneProduct->color ?? null,
            'size'            => $cloneProduct->size,
            'specifications'  => $cloneProduct->specifications,
            'retailer_id'     => $cloneProduct->retailer_id ?? null,
        ];
    }


    public function getRetailerWebInfo(Request $request)
    {
        try {
            $apiKey = $request->header('API-KEY');
            if (!$apiKey) {
                return response()->json(['error' => 'API Key is required.'], 401);
            }

            $retailer = RetailerWebManagement::where('product_listing_key', $apiKey)->first();
            if (!$retailer) {
                return response()->json(['error' => 'Unauthorized: Invalid API Key.'], 403);
            }

            // $companyInfo = UserDetail::select('company_logo', 'company_name')
            //     ->where('user_id', $retailer->retailer_id)
            //     ->first();
            $companyInfo = RetailerWebManagement::where('product_listing_key', $apiKey)->first();
            return response()->json([
                'success' => true,
                'companyinfo' => $companyInfo
            ]);

        } catch (\Exception $e) {
            \Log::error('Error fetching retailer company info: ' . $e->getMessage());
            return response()->json(['error' => 'An unexpected error occurred.'], 500);
        }
    }


    public function checkout(Request $request)
    {
        // Validate input data
        $validator = Validator::make($request->all(), [
            'firstname' => 'required|max:30',
            'lastname' => 'required|max:30',
            'phone_number' => 'required|numeric|digits:10',
            'email' => 'required|email',
            'address' => 'required|max:250',
            'payment_method' => 'required|in:cod,upi',
            'final_amount' => 'required|numeric|min:0',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'nullable', 
            'products.*.retailer_clone_product_id' => 'nullable', 
            'products.*.wholesaler_id' => 'nullable',
            'products.*.retailer_id' => 'nullable',
            'products.*.quantity' => 'required|integer|min:1'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Validate API key
        $apiKey = $request->header('API-KEY');
        if (!$apiKey) {
            return response()->json(['error' => 'API Key is required.'], 401);
        }

        // Validate retailer based on API key
        $retailer = RetailerWebManagement::where('product_listing_key', $apiKey)->first();
        if (!$retailer) {
            return response()->json(['error' => 'Unauthorized: Invalid API Key.'], 403);
        }

        DB::beginTransaction();
        try {
            // Store customer details
            $customerDetail = CustomerDetails::create([
                'firstname' => $request->firstname,
                'lastname' => $request->lastname,
                'phone_number' => $request->phone_number,
                'email' => $request->email ?? null,
                'address' => $request->address,
                'state' => $request->state,
                'city' => $request->city,
                'pincode' => $request->pincode
            ]);

            // Generate a unique order ID
            $orderID = 'ORD' . now()->timestamp . rand(10000, 99999);

            // Prepare order items
            $orderItems = collect($request->products)->map(function ($product) use ($orderID, $customerDetail, $retailer) {

                $wholesalerId = $product['wholesaler_id'] ?? null;
                $retailerId = $product['retailer_id'] ?? $retailer->retailer_id;
                $productId = $product['product_id'] ?? null;
                $retailercloneproductId = $product['retailer_clone_product_id'] ?? null;

                return [
                    'order_id' => $orderID,
                    'customer_id' => $customerDetail->id,
                    'product_id' =>  $productId ?? null,
                    'retailer_clone_product_id' =>  !is_null($retailercloneproductId) ? $retailercloneproductId : null,
                    'retailer_id' => !is_null($retailerId) ? $retailerId : null,
                    'wholesaler_id' => !is_null($wholesalerId) ? $wholesalerId : null,
                    'quantity' => $product['quantity'],
                    'final_amount' => request()->final_amount, 
                    'payment_method' => request()->payment_method,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            })->toArray();

            // Bulk insert orders
            CustomerOrders::insert($orderItems);

            DB::commit();

            return response()->json([
                'success' => true,
                'order_id' => $orderID,
                'message' => 'Your order has been placed successfully!'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Checkout Error:', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Something went wrong! Please try again.'], 500);
        }
    }

}
