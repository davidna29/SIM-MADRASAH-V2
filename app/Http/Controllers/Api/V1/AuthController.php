<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\ApiException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\LoginRequest;
use App\Http\Resources\Api\V1\UserResource;
use App\Models\User;
use App\Support\Api\ApiAudit;
use App\Support\Api\ApiErrorCode;
use App\Support\Api\ApiResponse;
use App\Support\Api\ApiScope;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(LoginRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $login = $validated['login'];
        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        $user = User::query()->where($field, $login)->first();

        if (! $user || ! Hash::check($validated['password'], $user->password)) {
            throw new ApiException(
                ApiErrorCode::INVALID_CREDENTIALS,
                'Username/email atau kata sandi salah.',
                401,
            );
        }

        if (! $user->is_active) {
            throw new ApiException(
                ApiErrorCode::ACCOUNT_INACTIVE,
                'Akun Anda telah dinonaktifkan. Silakan hubungi tata usaha.',
                403,
            );
        }

        if ($user->must_change_password) {
            throw new ApiException(
                ApiErrorCode::PASSWORD_CHANGE_REQUIRED,
                'Anda harus mengganti kata sandi melalui website sebelum menggunakan API.',
                403,
            );
        }

        if (! ApiScope::isStaff($user->role)) {
            throw new ApiException(
                ApiErrorCode::ROLE_NOT_ALLOWED,
                'Role akun ini tidak diizinkan menggunakan API asisten.',
                403,
            );
        }

        $allowed = ApiScope::forRole($user->role);
        $requested = array_values(array_unique($validated['scopes'] ?? $allowed));

        if (array_diff($requested, $allowed) !== []) {
            throw new ApiException(
                ApiErrorCode::SCOPE_NOT_ALLOWED,
                'Ada scope yang tidak diizinkan untuk role ini.',
                403,
                ['scopes' => ['Scope tidak sesuai dengan role pengguna.']],
            );
        }

        $expiration = config('sanctum.expiration');
        $token = $user->createToken(
            $validated['device_name'],
            $requested,
            $expiration ? now()->addMinutes((int) $expiration) : null,
        );

        ApiAudit::log($request, 'api_login', $user, [
            'token_id' => $token->accessToken->id,
            'device_name' => $validated['device_name'],
            'scopes' => $requested,
        ], $user);

        return ApiResponse::success([
            'token' => $token->plainTextToken,
            'token_type' => 'Bearer',
            'token_id' => $token->accessToken->id,
            'expires_at' => $token->accessToken->expires_at?->toIso8601String(),
            'scopes' => $requested,
            'user' => UserResource::make($user)->resolve($request),
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        $user = $request->user();
        $token = $user->currentAccessToken();

        return ApiResponse::success([
            'user' => UserResource::make($user)->resolve($request),
            'token' => [
                'id' => $token?->id,
                'name' => $token?->name,
                'scopes' => $token?->abilities ?? [],
                'expires_at' => $token?->expires_at?->toIso8601String(),
            ],
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $validated = $request->validate(['all_devices' => ['nullable', 'boolean']]);
        $user = $request->user();
        $token = $user->currentAccessToken();

        if (! empty($validated['all_devices'])) {
            $user->tokens()->delete();
        } elseif ($token && method_exists($token, 'delete')) {
            $token->delete();
        }

        ApiAudit::log($request, ($validated['all_devices'] ?? false) ? 'api_logout_all' : 'api_logout', $user);

        return ApiResponse::success([
            'revoked' => true,
            'all_devices' => (bool) ($validated['all_devices'] ?? false),
        ]);
    }
}
