<?php

use App\Exceptions\ApiException;
use App\Http\Middleware\AssignApiRequestId;
use App\Http\Middleware\EnsureApiUserIsActive;
use App\Http\Middleware\EnsureIdempotencyKey;
use App\Http\Middleware\EnsurePasswordChanged;
use App\Http\Middleware\RoleMiddleware;
use App\Support\Api\ApiErrorCode;
use App\Support\Api\ApiResponse;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\Request;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Validation\ValidationException;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Laravel\Sanctum\Exceptions\MissingAbilityException;
use Laravel\Sanctum\Http\Middleware\CheckAbilities;
use Laravel\Sanctum\Http\Middleware\CheckForAnyAbility;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => RoleMiddleware::class,
            'active.api' => EnsureApiUserIsActive::class,
            'idempotency' => EnsureIdempotencyKey::class,
            'api.request-id' => AssignApiRequestId::class,
            'abilities' => CheckAbilities::class,
            'ability' => CheckForAnyAbility::class,
        ]);

        $middleware->group('web', [
            EncryptCookies::class,
            AddQueuedCookiesToResponse::class,
            StartSession::class,
            ShareErrorsFromSession::class,
            PreventRequestForgery::class,
            SubstituteBindings::class,
            EnsurePasswordChanged::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );

        $exceptions->render(function (Throwable $exception, Request $request) {
            if (! $request->is('api/*')) {
                return null;
            }

            if ($exception instanceof ApiException) {
                return ApiResponse::error(
                    $exception->getMessage(),
                    $exception->errorCode,
                    $exception->status,
                    $exception->errors,
                );
            }

            if ($exception instanceof ValidationException) {
                return ApiResponse::error(
                    'Data yang dikirim tidak valid.',
                    ApiErrorCode::VALIDATION_ERROR,
                    422,
                    $exception->errors(),
                );
            }

            if ($exception instanceof MissingAbilityException) {
                return ApiResponse::error(
                    'Token tidak memiliki scope yang dibutuhkan untuk aksi ini.',
                    ApiErrorCode::SCOPE_MISSING,
                    403,
                );
            }

            if ($exception instanceof AuthenticationException) {
                return ApiResponse::error(
                    'Autentikasi diperlukan.',
                    ApiErrorCode::UNAUTHENTICATED,
                    401,
                );
            }

            if ($exception instanceof AuthorizationException) {
                return ApiResponse::error(
                    'Anda tidak memiliki akses ke sumber daya ini.',
                    ApiErrorCode::FORBIDDEN,
                    403,
                );
            }

            if ($exception instanceof AccessDeniedHttpException) {
                $isScope = $exception->getMessage() === 'Invalid ability provided.';

                return ApiResponse::error(
                    $isScope ? 'Token tidak memiliki scope yang dibutuhkan untuk aksi ini.' : 'Anda tidak memiliki akses ke sumber daya ini.',
                    $isScope ? ApiErrorCode::SCOPE_MISSING : ApiErrorCode::FORBIDDEN,
                    403,
                );
            }

            if ($exception instanceof ModelNotFoundException || $exception instanceof NotFoundHttpException) {
                return ApiResponse::error(
                    'Data tidak ditemukan.',
                    ApiErrorCode::NOT_FOUND,
                    404,
                );
            }

            if ($exception instanceof MethodNotAllowedHttpException) {
                return ApiResponse::error(
                    'Metode HTTP tidak diizinkan untuk endpoint ini.',
                    ApiErrorCode::METHOD_NOT_ALLOWED,
                    405,
                );
            }

            if ($exception instanceof ThrottleRequestsException) {
                return ApiResponse::error(
                    'Terlalu banyak permintaan. Silakan coba lagi nanti.',
                    ApiErrorCode::RATE_LIMITED,
                    429,
                )->withHeaders($exception->getHeaders());
            }

            report($exception);

            return ApiResponse::error(
                'Terjadi kesalahan pada server.',
                ApiErrorCode::SERVER_ERROR,
                500,
            );
        });
    })->create();
