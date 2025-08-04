<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class OtpService
{
    /**
     * Send OTP to mobile number
     * 
     * @param string $mobile The mobile number to send OTP to
     * @return bool Returns true if OTP was successfully generated and stored
     */
    public function send(string $mobile): bool
    {
        // Generate a 6-digit OTP
        $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

        // Store OTP in cache with 5 minute expiration
        $otpKey = 'otp_' . $mobile;
        Cache::put($otpKey, $otp, now()->addMinutes(15));

        // Here you would typically integrate with an SMS gateway to send the OTP
        // For example: 
        // $smsService->send($mobile, "Your OTP is: $otp");

        // For demo purposes, we'll just log the OTP
        \Log::info("OTP for $mobile: $otp");

        return true;
    }

    /**
     * Verify OTP for mobile number
     * 
     * @param string $mobile The mobile number to verify
     * @param string $otp The OTP to verify
     * @return bool Returns true if OTP is valid
     */
    public function verify(string $mobile, string $otp): bool
    {
        $otpKey = 'otp_' . $mobile;
        $storedOtp = Cache::get($otpKey);

        if ($storedOtp && $storedOtp == $otp) {
            Cache::forget($otpKey); // OTP used, remove from cache
            return true;
        }

        return false;
    }
}