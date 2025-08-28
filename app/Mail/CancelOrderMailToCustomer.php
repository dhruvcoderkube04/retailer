<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CancelOrderMailToCustomer extends Mailable
{
    use Queueable, SerializesModels;

    public $customerOrder;
    public $customer;
    public $cancelled_reason;

    /**
     * Create a new message instance.
     */
    public function __construct($customerOrder, $customer, $cancelled_reason)
    {
        $this->customerOrder = $customerOrder;
        $this->customer = $customer;
        $this->cancelled_reason = $cancelled_reason;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $order_id = $this->customerOrder->order_id;
        $product_name = $this->customerOrder->order_product_detail->name;
        return new Envelope(
            subject: 'Your Order ' . $order_id . ' has been cancelled | ' . $product_name,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.cancel_order_mail_to_customer',
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
