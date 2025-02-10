<?php

namespace App\Http\Controllers\Wholesale;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WholesalerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function wholesalerDashboard()
    {
        return view('wholesale.dashboard');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function productList()
    {
        return view('wholesale.product-list');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function addProductview()
    {
        return view('wholesale.add-product');
    }

    /**
     * Display the specified resource.
     */
    public function postNewproduct(Request $request)
    {
        // dd("product added ");
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function orderList()
    {
        return view('wholesale.order-list');
    }

    /**
     * Update the specified resource in storage.
     */
    public function orderItem()
    {
        return view('wholesale.order-item');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function orderItemUpdate(Request $request)
    {
        //
    }

    public function paymentHistory()
    {
        return view('wholesale.payment-history');
    }
}
