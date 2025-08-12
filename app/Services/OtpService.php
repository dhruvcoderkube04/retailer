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
    public function send(string $mobile): ?string
    {
        // Generate a 4-digit OTP
        $otp = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);

        // Store OTP in cache with 15 minute expiration
        $otpKey = 'otp_' . $mobile;
        Cache::put($otpKey, $otp, now()->addMinutes(15));

        // For testing: log it
        \Log::info("OTP for $mobile: $otp");

        // Return the OTP for response
        return $otp;
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