<?php

namespace App\Support\Api;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

final class ApiResponse
{
    /**
     * @param  array<string, mixed>  $meta
     * @param  array<int, string>  $warnings
     */
    public static function success(
        mixed $data,
        array $meta = [],
        array $warnings = [],
        int $status = 200,
    ): JsonResponse {
        return response()
            ->json([
                'data' => $data,
                'warnings' => array_values($warnings),
                'meta' => self::meta($meta),
            ], $status)
            ->header('X-Request-Id', self::requestId());
    }

    /**
     * @param  array<int, mixed>  $data
     * @param  array<string, mixed>  $meta
     * @param  array<int, string>  $warnings
     */
    public static function paginated(
        LengthAwarePaginator $paginator,
        array $data,
        array $meta = [],
        array $warnings = [],
    ): JsonResponse {
        return self::success($data, [
            ...$meta,
            'pagination' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
                'from' => $paginator->firstItem(),
                'to' => $paginator->lastItem(),
            ],
        ], $warnings);
    }

    /**
     * @param  array<string, array<int, string>>  $errors
     */
    public static function error(
        string $message,
        string $code,
        int $status,
        array $errors = [],
    ): JsonResponse {
        return response()
            ->json([
                'message' => $message,
                'errors' => (object) $errors,
                'code' => $code,
            ], $status)
            ->header('X-Request-Id', self::requestId());
    }

    /**
     * @param  array<string, mixed>  $meta
     * @return array<string, mixed>
     */
    private static function meta(array $meta): array
    {
        return [
            'request_id' => self::requestId(),
            'api_version' => 'v1',
            'generated_at' => now()->toIso8601String(),
            ...$meta,
        ];
    }

    public static function requestId(): string
    {
        $requestId = request()->attributes->get('api_request_id');

        if (is_string($requestId) && $requestId !== '') {
            return $requestId;
        }

        $requestId = (string) Str::uuid();
        request()->attributes->set('api_request_id', $requestId);

        return $requestId;
    }
}
