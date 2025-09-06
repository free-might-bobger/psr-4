<?php

namespace App\Exceptions;

use Exception;

class AccessDeniedException extends Exception
{
    public function report()
    {
        // Optional: Log the exception or send notifications
        logger()->error($this->getMessage());
    }

    public function render($request)
    {
        return response()->json([
            'message' => $this->getMessage(),
        ], 403);
    }
}
