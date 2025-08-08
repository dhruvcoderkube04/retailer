<?php

namespace App\Http\Controllers\API\Retailer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class OtpController extends Controller
{
    public function sendOtp(Request $request)
    {
        $request->validate([
            'mobile' => 'required|digits:10',
        ]);

        $otp = rand(1000, 9999); // generate 4-digit OTP

        // Store in cache for 5 minutes
        Cache::put('otp_' . $request->mobile, $otp, now()->addMinutes(5));

        return response()->json([
            'success' => true,
	        'message' => 'OTP sent successfully. Your OTP is ' . $otp,

            //'message' => 'OTP sent successfully.',
            'otp' => $otp // return it for testing
        ]);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'mobile' => 'required|digits:10',
            'otp' => 'required|digits:4',
        ]);

        $otpKey = 'otp_' . $request->mobile;
        $storedOtp = Cache::get($otpKey);

        if ($storedOtp && $storedOtp == $request->otp) {
            Cache::forget($otpKey); // Invalidate OTP after use

            return response()->json([
                'success' => true,
                'message' => 'OTP verified successfully.',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Invalid OTP.',
        ]);
    }
}
