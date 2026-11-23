<?php

namespace App\Support\Api;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

final class ApiAudit
{
    /**
     * @param  array<string, mixed>  $properties
     */
    public static function log(Request $request, string $action, ?Model $subject = null, array $properties = [], ?User $causer = null): ?Activity
    {
        $user = $causer ?? $request->user();
        $token = $user?->currentAccessToken();

        $logger = activity('api')
            ->causedBy($user instanceof User ? $user : null)
            ->withProperties([
                'request_id' => ApiResponse::requestId(),
                'token_id' => $token?->id,
                'token_name' => $token?->name,
                'scopes' => $token?->abilities ?? null,
                'ip' => $request->ip(),
                'user_agent' => substr((string) $request->userAgent(), 0, 255),
                ...$properties,
            ]);

        if ($subject) {
            $logger->performedOn($subject);
        }

        return $logger->log($action);
    }
}
