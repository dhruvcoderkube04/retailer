<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\RetailerWebManagement as RetailerWeb;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class RetilerWebManagement extends Controller
{
    // retailer web setting
    public function webSetting()
    {
        $id = Auth::user()->id;
        $reatiler_data = RetailerWeb::where('retailer_id',$id)->first();
        return view('web-setting-page',['reatiler' => $reatiler_data ]);
    }

    public function webSettingSetup(Request $request)
    {
        $id = Auth::user()->id;
        $reatiler_details = User::where('id',$id)->first();

        $company_name = !empty(@$reatiler_details->userDetail->company_name) ? @$reatiler_details->userDetail->company_name : '';
        $clean_name = strtolower(trim(str_replace(' ', '', $company_name))); // make lowercase and remove spaces
        $retailer_subdomain = 'https://' . $clean_name . '.trendmart.com';


        $product_list_key = Str::uuid();

        if (!empty($company_name))
        {
            RetailerWeb::create([
                'retailer_id'=> $id,
                'store_name'=>$company_name,
                'theme'=>'',
                'subdomain'=> $retailer_subdomain,
                'product_listing_key'=>$product_list_key,
                // 'is_active'=> $request->status == null ? 0:1,
                'is_active'=> 1,
                'settings'=>'',
            ]);

            return back()->with('success', 'Congratulation Your Website On Internet Shortly !');
        }
        else
        {
            return back()->with('error', 'Update Your Company Name !');
        }

    }

    public function chnageStatus(Request $request)
    {
        dd($request->all());
    }
}
