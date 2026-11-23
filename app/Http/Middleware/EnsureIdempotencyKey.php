<?php

namespace App\Http\Middleware;

use App\Exceptions\ApiException;
use App\Models\ApiIdempotencyKey;
use App\Support\Api\ApiErrorCode;
use App\Support\Api\ApiResponse;
use Closure;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class EnsureIdempotencyKey
{
    private const TTL_HOURS = 24;

    public function handle(Request $request, Closure $next, string $mode = 'required'): Response
    {
        $key = trim((string) $request->header('Idempotency-Key'));

        if ($key === '' && $mode === 'optional') {
            return $next($request);
        }

        if ($key === '') {
            throw new ApiException(
                ApiErrorCode::IDEMPOTENCY_KEY_REQUIRED,
                'Header Idempotency-Key wajib diisi untuk permintaan tulis ini.',
                422,
            );
        }

        if (strlen($key) < 8 || strlen($key) > 100) {
            throw ApiException::validation('Idempotency-Key harus 8–100 karakter.');
        }

        $user = $request->user();

        if (! $user) {
            throw new ApiException(ApiErrorCode::UNAUTHENTICATED, 'Autentikasi diperlukan.', 401);
        }

        $hash = $this->requestHash($request, $user->id);
        $route = $request->method().' '.$request->path();

        $existing = ApiIdempotencyKey::query()
            ->where('user_id', $user->id)
            ->where('key', $key)
            ->first();

        if ($existing?->expires_at && $existing->expires_at->isPast()) {
            $existing->delete();
            $existing = null;
        }

        if ($existing) {
            return $this->replayOrReject($existing, $hash);
        }

        $lock = Cache::lock('api-idempotency:'.$user->id.':'.$key, 15);

        try {
            return $lock->block(10, function () use ($request, $next, $user, $key, $hash, $route) {
                $existing = ApiIdempotencyKey::query()
                    ->where('user_id', $user->id)
                    ->where('key', $key)
                    ->first();

                if ($existing) {
                    return $this->replayOrReject($existing, $hash);
                }

                $response = $next($request);

                if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
                    try {
                        ApiIdempotencyKey::create([
                            'user_id' => $user->id,
                            'key' => $key,
                            'route' => substr($route, 0, 150),
                            'request_hash' => $hash,
                            'response_status' => $response->getStatusCode(),
                            'response_body' => json_decode($response->getContent(), true) ?: [],
                            'expires_at' => now()->addHours(self::TTL_HOURS),
                        ]);
                    } catch (UniqueConstraintViolationException) {
                        // Request paralel dengan key yang sama sudah menyimpan respons.
                    }
                }

                return $response;
            });
        } catch (LockTimeoutException) {
            throw ApiException::conflict(
                'Permintaan dengan Idempotency-Key ini sedang diproses.',
                ApiErrorCode::IDEMPOTENCY_IN_PROGRESS,
            );
        }
    }

    private function replayOrReject(ApiIdempotencyKey $record, string $hash): Response
    {
        if (! hash_equals($record->request_hash, $hash)) {
            throw ApiException::conflict(
                'Idempotency-Key sudah dipakai untuk payload yang berbeda.',
                ApiErrorCode::IDEMPOTENCY_KEY_REUSED,
            );
        }

        return response()
            ->json($record->response_body, 200)
            ->header('X-Request-Id', ApiResponse::requestId())
            ->header('Idempotent-Replayed', 'true');
    }

    private function requestHash(Request $request, int $userId): string
    {
        $payload = $this->normalizePayload($request->except(['_token', '_method']));
        $this->ksortRecursive($payload);

        $files = [];
        foreach ($request->allFiles() as $field => $file) {
            $files[$field] = is_array($file)
                ? array_map(fn ($item) => $this->fileHash($item), $file)
                : $this->fileHash($file);
        }

        return hash('sha256', json_encode([
            'user_id' => $userId,
            'method' => $request->method(),
            'path' => $request->path(),
            'payload' => $payload,
            'files' => $files,
        ], JSON_THROW_ON_ERROR));
    }

    private function fileHash(mixed $file): ?string
    {
        if (! $file || ! method_exists($file, 'getRealPath')) {
            return null;
        }

        $path = $file->getRealPath();

        return $path && is_file($path) ? hash_file('sha256', $path) : null;
    }

    private function normalizePayload(mixed $value): mixed
    {
        if (is_array($value)) {
            return array_map(fn ($item) => $this->normalizePayload($item), $value);
        }

        return is_scalar($value) || $value === null ? $value : null;
    }

    private function ksortRecursive(array &$array): void
    {
        ksort($array);

        foreach ($array as &$value) {
            if (is_array($value)) {
                $this->ksortRecursive($value);
            }
        }
    }
}
