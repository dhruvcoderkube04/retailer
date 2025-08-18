<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WelcomeCustomerMail extends Mailable
{
    use Queueable, SerializesModels;

    public $storeCutsomerDetails;
    public $verificationUrl;
    public $randomPassword;

    public function __construct($storeCutsomerDetails, $randomPassword)
    {
        $this->storeCutsomerDetails = $storeCutsomerDetails;
        $this->randomPassword = $randomPassword;
        // $this->verificationUrl = url("/api/customer/verify-email/" . $storeCutsomerDetails->email_verification_token);
    }

    public function build()
    {
        return $this->subject('Welcome to ' . config('app.name') . '! Your login details')
            ->markdown('emails.customer.welcome')
            ->with([
                'name' => trim($this->storeCutsomerDetails->firstname . ' ' . $this->storeCutsomerDetails->lastname),
                'email' => $this->storeCutsomerDetails->email,
                'randomPassword' => $this->randomPassword,
                // 'verificationUrl' => $this->verificationUrl, // optional
            ]);

    }
}
