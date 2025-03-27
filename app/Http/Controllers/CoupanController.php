<?php

namespace App\Http\Controllers;

use App\Models\Coupan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Calculation\Financial\Coupons;

class CoupanController extends Controller
{
    public function index()
    {
        $coupans = Coupan::where('status',1)->get();
        return view('coupan.index',compact('coupans'));
    }

    public function AddCoupan(Request $request)
    {
        dd($request->all());
       // Validate request
        $validator = Validator::make($request->all(), [
            'coupan_name' => 'required|string',
            'coupan_code' => 'required|string|unique:coupons,coupan_code',
            'discount_price' => 'required|numeric',
            'quantity' => 'required|integer',
            'date_range' => 'required|string',
            'status' => 'required|boolean'
        ]);

        // If validation fails, return errors
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Save Coupon
        $coupon = new Coupons();
        $coupon->coupan_code_name = $request->coupan_name;
        $coupon->code = $request->coupan_code;
        $coupon->discount = $request->discount_price;
        $coupon->usage_limit = $request->quantity;
        $coupon->date_range = $request->date_range;
        $coupon->status = $request->status;
        $coupon->save();

        return response()->json([
            'success' => true,
            'message' => 'Coupon added successfully!'
        ]);
    }
}
