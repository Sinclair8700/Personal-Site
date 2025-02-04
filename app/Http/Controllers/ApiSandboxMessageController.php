<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\SandboxApiMessageRequest;
use App\Http\Traits\ApiResponses;
use Illuminate\Support\Facades\Cache;

class ApiSandboxMessageController extends Controller
{
    use ApiResponses;

    public function store(SandboxApiMessageRequest $request)
    {
        $request->validated();

        $token = $request->input('token');
        $message = $request->input('message');

        $messages = Cache::get('sandbox_messages_' . $token, []);
        $messages[] = $message;

        Cache::put('sandbox_messages_' . $token, $messages, now()->addMinutes(30));

        return $this->success('Message stored successfully');
    }

    public function index(SandboxApiMessageRequest $request){
        $request->validated();
        $token = $request->input('token');

        $messages = Cache::get('sandbox_messages_' . $token, []);

        return $this->success($messages, 'Messages retrieved successfully');
    }
}
