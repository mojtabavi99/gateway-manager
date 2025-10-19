<?php

namespace App\Services\Response;

use Illuminate\Http\Request;

class ResponseFactory
{
    /**
     * Return the appropriate Response strategy based on the request.
     */
    public static function make(Request $request): ResponseInterface
    {
        return ($request->expectsJson() || $request->is('api/*'))
            ? new ApiResponse()
            : new WebResponse();
    }
}
