<?php

namespace App\Helpers\Response;

use Illuminate\Http\Request;

class ResponseFactory
{
    /**
     * Return the appropriate Response strategy based on the request.
     */
    public static function make(Request $request): ResponseInterface
    {
        return match (true) {
            $request->expectsJson(), $request->is('api/*') => new ApiResponse(),
            default => new WebResponse(),
        };
    }
}
