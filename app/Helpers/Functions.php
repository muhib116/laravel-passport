<?php
    function send_error($error = [], $code = 404)
    {
        return response()->json([
            'status' => false,
            'Message' => 'Validation Error',
            'Errors' => $error
        ], $code);
    }