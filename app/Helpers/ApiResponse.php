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

    public static function error($message = 'Something went wrong', $errors = [])
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors'  => $errors,
        ], 200); // Always HTTP 200
    }
}
