<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use App\Http\Requests\ApiLoginRequest;
use App\Http\Traits\ApiResponses;

class ApiSandboxController extends Controller
{
    use ApiResponses;
    public function index(Request $request)
    {
        $temporary_token = Str::random(16);
        if($request->session()->has('api_sandbox_token')){
            $temporary_token = $request->session()->get('api_sandbox_token');
        }else{
            $request->session()->put('api_sandbox_token', $temporary_token);
        }

        Cache::put('sandbox_token_' . $temporary_token, true, now()->addMinutes(30));

        $messages = Cache::get('sandbox_messages_' . $temporary_token, []);

        return view('projects.api-sandbox.index', ['title' => 'API Sandbox', 'temporary_token' => $temporary_token, 'messages' => $messages]);
    }




}

