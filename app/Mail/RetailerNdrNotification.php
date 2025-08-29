<?php

namespace App\Mail;

use App\Models\CustomerOrders;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RetailerNdrNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $order;

    public function __construct(CustomerOrders $order)
    {
        $this->order = $order;
    }

    public function build()
    {
        return $this->subject('Order Moved to NDR Status')
                    ->markdown('emails.retailer.ndr');
    }

}
