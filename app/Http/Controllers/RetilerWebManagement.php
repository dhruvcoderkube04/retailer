<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\RetailerWebManagement as RetailerWeb;
use App\Models\Theme;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
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
        $retailerUser = User::with('userDetail')->find($id);

        $company_name = $retailerUser->userDetail->company_name ?? '';
        $clean_name = strtolower(trim(str_replace(' ', '', $company_name)));
        $product_list_key = Str::uuid();
        $theme = Theme::whereIn('theme_type', ['retailer', 'both'])->where('status', 1)->first();

        if (empty($company_name)) {
            return back()->with('error', 'Please update your Company Name first.');
        }

        DB::beginTransaction();
        try {
            $store = RetailerWeb::create([
                'retailer_id'         => $id,
                'store_name'          => $company_name,
                'theme'               => $theme->id ?? '',
                'subdomain'           => $clean_name,
                'product_listing_key' => $product_list_key,
                'is_active'           => 0,
                'settings'            => '',
            ]);

            // Send email to admin
            Mail::send('emails.store-request', [
                'user' => $retailerUser,
                'subdomain' => $clean_name
            ], function ($message) {
                $message->to('info@techtrendmart.in')
                        ->subject('Retailer Store Creation Request');
            });

            DB::commit();

            return back()->with('success', 'Congratulations! Your website request has been submitted.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating store: ' . $e->getMessage());

            return back()->with('error', 'Something went wrong. Please try again later.');
        }
    }

    public function chnageStatus(Request $request)
    {
        dd($request->all());
    }
}
