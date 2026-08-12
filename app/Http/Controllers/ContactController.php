<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('contact.index', [
            'title' => 'Contact',
            'description' => 'Get in touch with Alex Davies, software developer — about work, collaboration, or just to say hello.',
        ]);
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Honeypot: real users never see or fill the "website" field. Silently
        // accept-and-drop so bots don't learn they've been caught.
        if ($request->filled('website')) {
            return redirect()->back()->with('success', 'Thanks for your message! I\'ll get back to you soon.');
        }

        $request->validate([
            'message' => 'required|max:511|min:10',
            'email_address' => 'required|email|max:255',
        ]);

        Contact::create($request->only(['email_address', 'message']));

        return redirect()->back()->with('success', 'Thanks for your message! I\'ll get back to you soon.');
    }
}
