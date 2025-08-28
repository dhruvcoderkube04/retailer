<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RetailerOrderMail extends Mailable
{
    use Queueable, SerializesModels;

    public $orderItemsForMail;
    public $retailer;

    /**
     * Create a new message instance.
     */
    public function __construct($orderItemsForMail, $retailer)
    {
        $this->orderItemsForMail = $orderItemsForMail;
        $this->retailer = $retailer;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $product_count = count($this->orderItemsForMail);
        return new Envelope(
            subject: "New Order Alert! ({$product_count} Product" . ($product_count > 1 ? 's' : '') . ")"
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.retailer_order_mail',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
