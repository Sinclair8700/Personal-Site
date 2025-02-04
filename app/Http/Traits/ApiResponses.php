<?php

namespace App\Http\Traits;

trait ApiResponses
{
    protected function success($data, $message = 'Success', $code = 200)
    {
        return response()->json(['message' => $message, 'data' => $data], $code);
    }

    protected function error($message = 'Error', $code = 400)
    {
        return response()->json(['message' => $message], $code);
    }
}
