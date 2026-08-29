<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePasswordChanged
{
    /**
     * Paksa user yang wajib ganti password (must_change_password) ke halaman
     * ganti password sebelum bisa mengakses halaman lain. Dikecualikan: halaman
     * ganti password itu sendiri, proses update password, dan logout.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->must_change_password
            && ! $request->routeIs('password.change', 'password.update', 'logout')
            && ! $request->expectsJson()) {
            return redirect()->route('password.change');
        }

        return $next($request);
    }
}
