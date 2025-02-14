<?php

namespace App\Http\Controllers\Wholesale;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WholesalerController extends Controller
{
    public function wholesalerDashboard()
    {
        $auth_id = Auth::user()->id;
        $data['wholesaer_total_product'] = Product::where('status','active')->where('wholesaler_id',$auth_id)->count();
        return view('wholesale.dashboard',['data'=>$data]);
    }

    public function productList()
    {
        $product = Product::all();
        return view('wholesale.product-list',['products'=>$product]);
    }

    public function addProductview()
    {
        return view('wholesale.add-product');
    }

    public function postNewproduct(Request $request)
    {

        $request->validate([
            'sku' => 'required|string|unique:products,sku',
            'tags' => 'required',
            'slug'=>'required|string|unique:products,slug',
            'status' => 'required|string|in:active,inactive',
            'product_name' => 'required|string|max:255',
            'product_description' => 'required|string|min:10',
            'price' => 'required|numeric|min:1',
            'discount_option' => 'required|numeric|min:0|max:100',
            'quantity' => 'required|integer|min:1',
            'meta_title' => 'required|string|max:255',
            'meta_description'=>'required|string|max:255',
            'product_meta_keywords' => 'nullable|string',
            // 'avatar' => 'required|image|mimes:jpg,png,jpeg|max:2048',
            // 'categories'=>'nullable|string',
        ]);
        // 'dicsounted_price' => 'required|numeric|lt:price',

        // Handle Image Upload
        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('products', $fileName, 'public'); // No extra quotes

            // Store only the relative path (without double quotes)
            $imagePath = "products/" . $fileName;
        }


        // Create Product
        Product::create([
            'wholesaler_id' => Auth::id(),
            'name' => $request->product_name,
            'description' => $request->product_description,
            'price' => $request->price,
            'slug'=>$request->slug,
            'discount_option' => $request->discount_option,
            'discount_price' => $request->discounted_price,
            'sku' => $request->sku,
            'videos'=>'',
            'url'=>'',
            'status'=>$request->status,
            'color'=>'',
            'size'=>'',
            'specifications'=>'',
            'quantity' => $request->quantity,
            'tags' => json_decode($request->tags, true),
            'meta_title' => $request->meta_title,
            'meta_description'=>'',
            'meta_keywords' => $request->product_meta_keywords,
            'images' => $imagePath ?? '',
        ]);

        return redirect()->back()->with('success', 'Product added successfully!');
    }

    public function editProduct(string $id)
    {
        $product_detail = Product::where('wholesaler_id',Auth::id())->where('id',$id)->first();
        return view('wholesale.product-edit',['product_detail' => $product_detail]);
    }

    public function updateProduct(Request $request, $id)
    {
        // Find the product
        $product = Product::where('wholesaler_id',Auth::id())->where('id',$id)->first();
        $request->validate([
            // 'sku' => 'nullable|string|unique:products,sku',
            'tags' => 'required',
            'slug'=>'nullable|string|unique:products,slug',
            'status' => 'nullable|string|in:active,inactive',
            'product_name' => 'nullable|string|max:255',
            'product_description' => 'nullable|string|min:10',
            'price' => 'nullable|numeric|min:1',
            'discount_option' => 'nullable|numeric|min:0|max:100',
            'quantity' => 'nullable|integer|min:1',
            'meta_title' => 'nullable|string|max:255',
            'meta_description'=>'nullable|string|max:255',
            'product_meta_keywords' => 'nullable|string',
            // 'avatar' => 'required|image|mimes:jpg,png,jpeg|max:2048',
            // 'categories'=>'nullable|string',
        ]);
        // 'dicsounted_price' => 'required|numeric|lt:price',

        // Handle Image Upload
        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('products', $fileName, 'public'); // No extra quotes

            // Store only the relative path (without double quotes)
            $imagePath = "products/" . $fileName;
        }

            // Collect fields for update
        $updateData = [];

        if ($request->filled('product_name')) {
            $updateData['name'] = $request->product_name;
        }
        if ($request->filled('product_description')) {
            $updateData['description'] = $request->product_description;
        }
        if ($request->filled('price')) {
            $updateData['price'] = $request->price;
        }
        if ($request->filled('slug')) {
            $updateData['slug'] = $request->slug;
        }
        if ($request->filled('discount_option')) {
            $updateData['discount_option'] = $request->discount_option;
        }
        if ($request->filled('discounted_price')) {
            $updateData['discount_price'] = $request->discounted_price;
        }
        if ($request->filled('sku')) {
            $updateData['sku'] = $request->sku;
        }
        if ($request->filled('quantity')) {
            $updateData['quantity'] = $request->quantity;
        }
        if ($request->filled('status')) {
            $updateData['status'] = $request->status;
        }
        if ($request->filled('meta_title')) {
            $updateData['meta_title'] = $request->meta_title;
        }
        if ($request->filled('meta_description')) {
            $updateData['meta_description'] = $request->meta_description;
        }
        if ($request->filled('product_meta_keywords')) {
            $updateData['meta_keywords'] = $request->product_meta_keywords;
        }

        // Convert tags JSON to array if provided
        if ($request->has('tags')) {
            $updateData['tags'] = json_decode($request->tags, true);
        }

        // Handle image upload
        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('products', $fileName, 'public');

            $updateData['images'] = "products/" . $fileName; // Store new image path
        }

        // Update product
        $product->update($updateData);

        return redirect()->back()->with('success', 'Product updated successfully!');
    }

    public function orderList()
    {
        return view('wholesale.order-list');
    }


    public function orderItem()
    {
        return view('wholesale.order-item');
    }


    public function paymentHistory()
    {
        return view('wholesale.payment-history');
    }
}
