<?php

namespace App\Http\Controllers;

use App\Models\PageVisit;
use Illuminate\Http\Client\Request;

abstract class Controller
{
    public function __construct(Request $request)
    {
        PageVisit::create([
            'ip_address' => $request->ip(),
            'session' => $request->session()->id,
        ]);
    }
}
