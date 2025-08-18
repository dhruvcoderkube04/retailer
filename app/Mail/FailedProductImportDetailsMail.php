<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class FailedProductImportDetailsMail extends Mailable
{
    use Queueable, SerializesModels;

    public $invalidRows;
    public $validRows;
    public $importStartDateTime;

    /**
     * Create a new message instance.
     */
    public function __construct($invalidRows, $validRows, $importStartDateTime)
    {
        $this->invalidRows = $invalidRows;
        $this->validRows = $validRows;
        $this->importStartDateTime = $importStartDateTime;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Failed Product Import Details Mail',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.failed_product_import_details',
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
