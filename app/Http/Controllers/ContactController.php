<?php

namespace App\Http\Controllers;

use App\Mail\NewContactMessage;
use App\Models\Contact;
use App\Support\ContactChallenge;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Throwable;

class ContactController extends Controller
{
    private const THANKS = 'Thanks for your message! I\'ll get back to you soon.';

    /** More links than this in one message is almost always spam. */
    private const MAX_LINKS = 2;

    public function index()
    {
        return view('contact.index', [
            'title' => 'Contact',
            'description' => 'Get in touch with Alex Davies, a full-stack software developer in Stoke-on-Trent, about work, a project or just to say hello.',
            'challenge' => ContactChallenge::issue(),
        ]);
    }

    /**
     * Spam and bot protection, in order: a honeypot field, a proof-of-work
     * challenge (App\Support\ContactChallenge), a link limit and duplicate
     * suppression. The route is also rate limited (see AppServiceProvider).
     * Bots get the normal thank-you so they don't learn they were caught.
     */
    public function store(Request $request)
    {
        $thanks = fn () => redirect()->back()->with('success', self::THANKS);

        // Honeypot: real users never see or fill the "website" field.
        if ($request->filled('website')) {
            return $thanks();
        }

        $check = ContactChallenge::verify(
            $request->input('challenge_salt'),
            $request->input('challenge'),
            $request->input('challenge_signature'),
            $request->input('challenge_number'),
        );

        // Missing, forged or replayed: nobody filling the form in a browser sends that.
        if ($check === ContactChallenge::INVALID) {
            return $thanks();
        }

        if ($check !== ContactChallenge::OK) {
            return redirect()->back()->withInput()->withErrors(['challenge' => $check === ContactChallenge::EXPIRED
                ? 'This page was open too long, so the form expired. Please press Send again.'
                : 'That was quick! Please wait a moment and press Send again.']);
        }

        $data = $request->validate([
            'email_address' => 'required|email|max:255',
            'message' => ['required', 'string', 'min:10', 'max:511', function (string $attribute, mixed $value, Closure $fail) {
                if (preg_match_all('~https?://|www\.~i', (string) $value) > self::MAX_LINKS) {
                    $fail('Please include no more than '.self::MAX_LINKS.' links.');
                }
            }],
        ]);

        $alreadySent = Contact::where('email_address', $data['email_address'])
            ->where('message', $data['message'])
            ->where('created_at', '>=', now()->subDay())
            ->exists();

        if (! $alreadySent) {
            $contact = Contact::create($data);

            // The message is already saved, so a mail failure is logged rather than shown to the visitor.
            if ($to = config('mail.contact_to')) {
                try {
                    Mail::to($to)->send(new NewContactMessage($contact));
                } catch (Throwable $e) {
                    report($e);
                }
            }
        }

        return $thanks();
    }
}
