<?php

namespace App\Http\Controllers;

use App\Models\Coupon as CouponModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
class CouponController extends Controller
{
    public function index()
    {
        // Log the request method to debug
        Log::info('Index method called with method: ' . request()->method());

        if (request()->isMethod('post')) {
            Log::error('Unexpected POST request to coupon-page');
            return response()->json(['error' => 'Method not allowed'], 405);
        }

        $user_id = Auth::user()->id;
        $coupons = CouponModel::where('retailer_id', $user_id)->get();
        return view('coupon.index', compact('coupons'));
    }

    public function fetchCouponsRecord(Request $request)
    {
        $user_id = Auth::id();
        $search = $request->input('search');
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        $draw = $request->input('draw');

        $query = CouponModel::where('retailer_id', $user_id);

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('coupon_name', 'like', "%{$search}%")
                  ->orWhere('coupon_code', 'like', "%{$search}%");
            });
        }

        $total = CouponModel::where('retailer_id', $user_id)->count();
        $filtered = $query->count();

        $coupons = $query->offset($start)
            ->limit($length)
            ->orderBy('created_at', 'desc')
            ->get();

        $data = [];
        foreach ($coupons as $coupon) {
            $data[] = [
                'coupon_name' => '<a href="#" class="text-gray-800 text-hover-primary mb-1">' . e($coupon->coupon_name) . '</a>',
                'status' => '<div class="badge ' . ($coupon->status == 1 ? 'badge-light-success' : 'badge-light-danger') . '">' . ($coupon->status == 1 ? 'Active' : 'Inactive') . '</div>',
                'coupon_code' => '<div class="badge badge-light">' . e($coupon->coupon_code) . '</div>',
                'discount' => $coupon->discount,
                'quantity' => $coupon->usage_limit,
                'created_at' => $coupon->created_at->format('Y-m-d'),
                'actions' => '<button class="btn btn-icon btn-danger btn-light-danger w-30px h-30px me-3 delete-coupon"
                                data-id="' . $coupon->id . '" title="Remove">
                                <i class="fas fa-trash"></i>
                              </button>
                              <button class="btn btn-icon btn-success btn-light-success w-30px h-30px me-3 edit-coupon"
                                data-id="' . $coupon->id . '" data-bs-toggle="modal" data-bs-target="#kt_modal_edit_coupon"
                                title="Edit">
                                <i class="fas fa-pencil-alt"></i>
                              </button>'
            ];
        }

        return response()->json([
            'draw' => intval($draw),
            'recordsTotal' => $total,
            'recordsFiltered' => $filtered,
            'data' => $data,
        ]);
    }

    public function addCoupon(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'coupon_name' => 'required|string|max:255',
            'coupon_code' => 'required|string|unique:coupons,coupon_code|max:50',
            'discount_price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:1',
            'status' => 'required|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user_id = Auth::user()->id;

        $coupon = new CouponModel();
        $coupon->coupon_name = $request->coupon_name;
        $coupon->coupon_code = $request->coupon_code;
        $coupon->retailer_id = $user_id;
        $coupon->discount = $request->discount_price;
        $coupon->usage_limit = $request->quantity;
        $coupon->used_count = 0;
        $coupon->status = $request->status;
        $coupon->save();

        return response()->json([
            'success' => true,
            'message' => 'Coupon added successfully!'
        ]);
    }

    public function editCoupon($id)
    {
        $coupon = CouponModel::findOrFail($id);
        return response()->json($coupon);
    }

    public function updateCoupon(Request $request, $id)
    {
        Log::info('Update Coupon Request:', $request->all());

        $validator = Validator::make($request->all(), [
            'coupon_name' => 'required|string|max:255',
            'coupon_code' => 'required|string|max:50|unique:coupons,coupon_code,' . $id,
            'discount' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:1',
            'status' => 'required|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $coupon = CouponModel::findOrFail($id);
        $coupon->update([
            'coupon_name' => $request->coupon_name,
            'coupon_code' => $request->coupon_code,
            'discount' => $request->discount,
            'usage_limit' => $request->quantity,
            'status' => $request->status
        ]);

        return response()->json(['success' => true, 'message' => 'Coupon updated successfully!']);
    }

    public function deleteCoupon(Request $request)
    {
        $user_id = Auth::user()->id;
        $coupon = CouponModel::where('id', $request->coupon_id)->where('retailer_id', $user_id)->first();
        if ($coupon) {
            $coupon->delete();
            return response()->json(['success' => true, 'message' => 'Coupon deleted successfully.']);
        }
        return response()->json(['success' => false, 'message' => 'Coupon not found.']);
    }

}
