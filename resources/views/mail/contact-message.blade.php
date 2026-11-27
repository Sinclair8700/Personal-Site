{{-- Plain-text email, so output is raw: escaping would turn ' and & into HTML entities. --}}
New message from the contact form at {!! config('app.url') !!}/contact

From: {!! $contact->email_address !!}
Sent: {{ $contact->created_at->timezone('Europe/London')->format('j M Y, H:i') }}

{!! $contact->message !!}

Reply to this email to answer them.
