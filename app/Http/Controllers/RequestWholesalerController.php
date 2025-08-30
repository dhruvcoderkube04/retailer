<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserDetail;
use Illuminate\Support\Facades\Auth;
use App\Models\WholesalerCategory;
use App\Models\RetailerWholesalerCategoryRequest;

class RequestWholesalerController extends Controller
{
    public function fetchWholesalerCategory(string $id)
    {
        $wholesalerId = decryptId($id);

        if (!is_numeric($wholesalerId)) {
            abort(404, 'Invalid ID format after decryption.');
        }

        $retailer = Auth::user();

        $wholesaler = UserDetail::select('user_id', 'company_name')
            ->where('user_id', $wholesalerId)
            ->first();

       $subCategories = WholesalerCategory::where('wholesaler_id', $wholesalerId)
        ->join('sub_categories', 'wholesaler_categories.sub_category_id', '=', 'sub_categories.id')
        ->select(
            'sub_categories.id as sub_category_id',
            'sub_categories.sub_category_name',
            'sub_categories.sub_category_image',
            'sub_categories.sub_category_variation',
            'sub_categories.status'
        )
        ->get();

        return view('wholesaler.request-for-category', compact('wholesaler', 'subCategories', 'retailer'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'wholesaler_id' => 'required|exists:users,id',
            'sub_category_ids' => 'required|array|min:1',
            'sub_category_ids.*' => 'exists:sub_categories,id',
        ]);

        RetailerWholesalerCategoryRequest::create([
            'retailer_id'      => auth()->id(),
            'wholesaler_id'    => $request->wholesaler_id,
            'sub_category_ids' => $request->sub_category_ids,
            'status'           => 0,
        ]);

        return response()->json([
            'message' => 'Your request has been sent successfully!',
        ]);
    }

}
