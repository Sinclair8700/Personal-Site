<?php

namespace App\Mail;

use App\Models\Contact;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

/** Sent to config('mail.contact_to') for each message that gets past the contact form's spam checks. */
class NewContactMessage extends Mailable
{
    public function __construct(public Contact $contact)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New message from your website',
            // Hitting Reply goes straight to the person who wrote in.
            replyTo: [new Address($this->contact->email_address)],
        );
    }

    public function content(): Content
    {
        return new Content(text: 'mail.contact-message');
    }
}
