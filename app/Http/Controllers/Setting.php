<?php

namespace App\Http\Controllers;

use App\Models\RetailerWebManagement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;


class Setting extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $store = RetailerWebManagement::where('retailer_id', $user->id)->where('is_active', 1)->first();

        if (!$store) {
            return redirect()->route('retailer.web.setting')->with('error', 'You need to create a store first.');
        }

        return view('setting.index', compact('store'));
    }


    public function webSettingUpdate(Request $request)
    {
        $user = Auth::user();
        $validatedData = $request->validate([
            'store_name' => 'nullable|string|max:255',
            'mobile_no' => 'nullable|string|max:20',
            'logo' => 'nullable|mimes:jpeg,jpg,png|max:2048',
            'address' => 'nullable|string|max:255',
            'store_time' => 'nullable|string|max:255',
            'facebook_url' => 'nullable|string',
            'google_analytics_id' => 'nullable|string|max:255',
            'google_plus_url' => 'nullable|string|max:255',
            'twitter_url' => 'nullable|string',
            'instagram_id' => 'nullable|string|max:255',
            'facebook_pixel_id' => 'nullable|string|max:255',
            'apple_store_id' => 'nullable|string|max:255',
            'linkedin_url' => 'nullable|string',
            'play_store_url' => 'nullable|string',
            'instagram_url' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_keywords' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'cod_charge' => 'nullable|numeric|min:0',
            'shipping_charge' => 'nullable|numeric|min:0',
            'cart_limit' => 'nullable|integer|min:0',
            'favicon' => 'nullable|mimes:jpeg,jpg,png|max:1048',
            'banner' => 'nullable|mimes:jpeg,jpg,png|max:2048',
            'offer_text'  => 'nullable|string',
            'banner_title'   => 'nullable|string',
            'banner_sub_title'   => 'nullable|string',
            'banner_button_title' => 'nullable|string',
        ]);

        $retailer = RetailerWebManagement::where('retailer_id', $user->id)->first();

        if (!$retailer) {
            return redirect()->back()->with('error', 'Create Your Store Account');
        }

        // Handle logo upload
        if ($request->hasFile('logo')) {
            try {
                $file = $request->file('logo');
                $validatedData['logo'] = uploadOrUpdateImageToSpaces($file, 'company_logos', $retailer->logo);
            } catch (\Illuminate\Validation\ValidationException $validationException) {
                Log::error('Logo Validation Failed: ' . $validationException->getMessage());
                return back()->withErrors($validationException->errors())->withInput();
            } catch (\Exception $e) {
                Log::error('Logo Upload Failed: ' . $e->getMessage());
                return back()->with('error', 'Logo upload failed.');
            }
        }

        // Handle favicon upload
        if ($request->hasFile('favicon')) {
            try {
                $file = $request->file('favicon');
                $validatedData['favicon'] = uploadOrUpdateImageToSpaces($file, 'favicons', $retailer->favicon);
            } catch (\Illuminate\Validation\ValidationException $validationException) {
                Log::error('Favicon Validation Failed: ' . $validationException->getMessage());
                return back()->withErrors($validationException->errors())->withInput();
            } catch (\Exception $e) {
                Log::error('Favicon Upload Failed: ' . $e->getMessage());
                return back()->with('error', 'Favicon upload failed.');
            }
        }

        // Handle banner upload
        if ($request->hasFile('banner')) {
            try {
                $file = $request->file('banner');
                $validatedData['banner'] = uploadOrUpdateImageToSpaces($file, 'banners', $retailer->favicon);
            } catch (\Illuminate\Validation\ValidationException $validationException) {
                Log::error('Banner Validation Failed: ' . $validationException->getMessage());
                return back()->withErrors($validationException->errors())->withInput();
            } catch (\Exception $e) {
                Log::error('Banner Upload Failed: ' . $e->getMessage());
                return back()->with('error', 'Banner upload failed.');
            }
        }

        // Boolean toggles
        $validatedData['sms_service'] = $request->boolean('sms_service') ? 1 : 0;
        $validatedData['enquiry_whatsapp'] = $request->boolean('enquiry_whatsapp') ? 1 : 0;
        $validatedData['hide_pickup_address'] = $request->boolean('hide_pickup_address') ? 1 : 0;
        $validatedData['request_offer'] = $request->boolean('request_offer') ? 1 : 0;

        // Remove nulls
        $filteredData = array_filter($validatedData, fn($value) => !is_null($value));

        if (!empty($filteredData)) {
            $retailer->update($filteredData);
            return redirect()->back()->with('success', 'Store Update Successful');
        }

        return redirect()->back()->with('info', 'No changes were made.');
    }
}
