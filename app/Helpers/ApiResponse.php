<?php

namespace App\Helpers;

class ApiResponse
{
    public static function success($data = [], $message = 'Success')
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data'    => $data,
        ], 200);
    }

    public static function error($message = 'Something went wrong', $errors = [], $code = 200)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors'  => $errors,
        ], $code); // Always HTTP 200
    }
}
