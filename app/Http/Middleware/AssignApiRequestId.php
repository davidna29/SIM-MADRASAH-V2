<?php

namespace App\Http\Middleware;

use App\Support\Api\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class AssignApiRequestId
{
    public function handle(Request $request, Closure $next): Response
    {
        $incoming = $request->header('X-Request-Id');
        $requestId = is_string($incoming) && preg_match('/^[A-Za-z0-9._-]{8,100}$/', $incoming)
            ? $incoming
            : (string) Str::uuid();

        $request->attributes->set('api_request_id', $requestId);

        $response = $next($request);
        $response->headers->set('X-Request-Id', ApiResponse::requestId());

        return $response;
    }
}
