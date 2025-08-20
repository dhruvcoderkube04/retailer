<?php

namespace App\Http\Controllers;

use App\Models\Coupon as CouponModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Calculation\Financial\Coupons;

class CoupanController extends Controller
{
    public function index()
    {
        $user_id = Auth::user()->id;
        $coupons = CouponModel::where('retailer_id',$user_id)->get();
        return view('coupon.index',compact('coupons'));
    }

    public function fetchCouponsRecord(Request $request)
    {
        $user_id = Auth::id();
        $search = $request->input('search');
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        $draw = $request->input('draw');

        $query = CouponModel::where('retailer_id', $user_id);

        // Apply search filter
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

        // Format data for DataTables
        $data = [];
        foreach ($coupons as $coupon) {
            $data[] = [
                'checkbox' => '<div class="form-check form-check-sm form-check-custom form-check-solid">
                                <input class="form-check-input" type="checkbox" value="' . $coupon->id . '" />
                            </div>',
                'coupon_name' => '<a href="#" class="text-gray-800 text-hover-primary mb-1">' . e($coupon->coupon_name) . '</a>',
                'status' => '<div class="badge ' . ($coupon->status == 1 ? 'badge-light-success' : 'badge-light-danger') . '">' . ($coupon->status == 1 ? 'Active' : 'Inactive') . '</div>',
                'coupon_code' => '<div class="badge badge-light">' . e($coupon->coupon_code) . '</div>',
                'discount' => $coupon->discount,
                'created_at' => $coupon->created_at->format('Y-m-d'),
                'actions' => '<button class="btn btn-icon btn-danger btn-light-danger w-30px h-30px me-3 delete-coupan"
                                data-id="' . $coupon->id . '" title="Remove">
                                <i class="fas fa-trash"></i>
                            </button>
                            <button class="btn btn-icon btn-success btn-light-success w-30px h-30px me-3 edit-coupan"
                                data-id="' . $coupon->id . '" data-bs-toggle="modal" data-bs-target="#kt_modal_edit_coupan"
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

    public function AddCoupon(Request $request)
    {
        // Validate request
        $validator = Validator::make($request->all(), [
            'coupon_name' => 'required|string',
            'coupon_code' => 'required|string|unique:coupons,coupon_code',
            'discount_price' => 'required|numeric',
            'quantity' => 'required|integer',
            'status' => 'required|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user_id = Auth::user()->id;

        // Save Coupon
        $coupon = new CouponModel();
        $coupon->coupon_name = $request->coupon_name; // Fixed spelling
        $coupon->coupon_code = $request->coupon_code; // Fixed spelling
        $coupon->retailer_id = $user_id; // Set retailer id
        $coupon->discount = $request->discount_price;
        $coupon->usage_limit = $request->quantity;
        $coupon->used_count = 0; // Set default value
        $coupon->status = $request->status;
        $coupon->save();

        return response()->json([
            'success' => true,
            'message' => 'Coupon added successfully!'
        ]);
    }


    public function editCoupon($id)
    {
        $address = CouponModel::findOrFail($id);

        // dd($address);
        return response()->json($address);
    }

    public function updateCoupon(Request $request, $id)
    {

        // dd($request->all());
        // Debugging: Check received data
        if ($request->all()) {
            \Log::info('Update Coupon Request:', $request->all());
        }

        $request->validate([
            'coupon_name' => 'required|string',
            'coupon_code' => 'required|string|unique:coupons,coupon_code,' . $id,
            'discount' => 'required|numeric',
            'quantity' => 'required|integer',
            'status' => 'required|boolean'
        ]);

        $Coupon = CouponModel::findOrFail($id);
        $Coupon->update([
            'coupon_name' => $request->coupon_name,
            'coupon_code' => $request->coupon_code,
            'discount_price' => $request->discount_price,
            'usage_limit' => $request->quantity,
            'status' => $request->status
        ]);

        return response()->json(['success' => true, 'message' => 'Coupon updated successfully!']);
    }

    public function deleteCoupon(Request $request)
    {
        $user_id = Auth::user()->id;
        $coupon = CouponModel::where('id',$request->coupon_id)->where('retailer_id',$user_id)->first();
        if ($coupon) {
            $coupon->delete();
            return response()->json(['success' => true, 'message' => 'Coupon deleted successfully.']);
        }
        return response()->json(['success' => false, 'message' => 'Coupon not found.']);
    }

}
