<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WelcomeVerifyCustomerMail extends Mailable
{
    use Queueable, SerializesModels;

    public $storeCutsomerDetails;
    public $verificationUrl;

    public function __construct($storeCutsomerDetails)
    {
        $this->storeCutsomerDetails = $storeCutsomerDetails;
        $this->verificationUrl = url("/api/customer/verify-email/" . $storeCutsomerDetails->email_verification_token);
    }

    public function build()
    {
        return $this->subject('Welcome! Verify your email')
                    ->markdown('emails.customer.welcomeverify')
                    ->with([
                        'name' => $this->storeCutsomerDetails->firstname,
                        'verificationUrl' => $this->verificationUrl,
                    ]);
    }
}
