<?php

if (!function_exists('apiResponse')) {
    function apiResponse($data = null, $message = '', $status = 200, $success = true)
    {
        return response()->json([
            'status' => $status,
            'success' => $success,
            'message' => $message,
            'data' => $data,
        ], $status);
    }
}
