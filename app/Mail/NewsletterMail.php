<?php

namespace App\Mail;

use App\Models\Newsletter;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewsletterMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Newsletter $newsletter,
        public bool $isPreview = false,
        public ?string $unsubscribeToken = null
    ) {}

    public function envelope(): Envelope
    {
        $subject = $this->isPreview
            ? '[VORSCHAU] '.$this->newsletter->subject
            : $this->newsletter->subject;

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.newsletter',
        );
    }
}
