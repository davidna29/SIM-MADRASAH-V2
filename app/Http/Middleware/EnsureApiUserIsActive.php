<?php

namespace App\Http\Middleware;

use App\Exceptions\ApiException;
use App\Support\Api\ApiErrorCode;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureApiUserIsActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! $user->is_active) {
            $token = $user?->currentAccessToken();

            if ($token && method_exists($token, 'delete')) {
                $token->delete();
            }

            throw new ApiException(
                ApiErrorCode::ACCOUNT_INACTIVE,
                'Akun Anda tidak aktif. Hubungi tata usaha madrasah.',
                403,
            );
        }

        return $next($request);
    }
}
