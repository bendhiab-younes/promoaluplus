<?php

namespace App\Mail;

use App\Models\Quote;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class QuoteRequestNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public Quote $quote;

    public function __construct(Quote $quote)
    {
        $this->quote = $quote;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[Nouveau Devis] ' . $this->quote->name . ' - ' . $this->quote->project_type,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.quote-notification',
            with: ['quote' => $this->quote],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
