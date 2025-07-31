<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WelcomeCustomerMail extends Mailable
{
    use Queueable, SerializesModels;

    public $customer;
    public $verificationUrl;

    public function __construct($customer)
    {
        $this->customer = $customer;
        $this->verificationUrl = url("/api/customer/verify-email/" . $customer->email_verification_token);
    }

    public function build()
    {
        return $this->subject('Welcome! Verify your email')
                    ->markdown('emails.customer.welcome')
                    ->with([
                        'name' => $this->customer->firstname,
                        'verificationUrl' => $this->verificationUrl,
                    ]);
    }
}
