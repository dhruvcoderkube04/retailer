<?php

namespace App\Http\Controllers;

use App\Models\c;
use Illuminate\Http\Request;

class ShippingController extends Controller
{
    public function index(){
        return view('shipping.shipping-list');
    }
    public function directShipping(){
        return view('shipping.direct-shipping');
    }
    public function CreateOwnOrder(){
        return view('shipping.create-own-order');
    }
    public function NDR(){
        return view('shipping.ndr');
    }
    public function labelSetting(){
        return view('shipping.label-setting');
    }
    public function pickAddressList(){
        return view('shipping.pick-up-address-list');
    }
    public function rtoAddress(){
        return view('shipping.rto-address');
    }
    public function reportPage(){
        return view('shipping.report-page');
    }
    public function shippingCharges(){
        return view('shipping.shipping-charges');
    }
}
