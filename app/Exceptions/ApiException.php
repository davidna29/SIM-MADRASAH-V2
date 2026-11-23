<?php

namespace App\Exceptions;

use App\Support\Api\ApiErrorCode;
use Exception;
use Throwable;

class ApiException extends Exception
{
    /**
     * @param  array<string, array<int, string>>  $errors
     */
    public function __construct(
        public readonly string $errorCode,
        string $message,
        public readonly int $status = 422,
        public readonly array $errors = [],
        ?Throwable $previous = null,
    ) {
        parent::__construct($message, 0, $previous);
    }

    public static function validation(string $message, array $errors = []): self
    {
        return new self(ApiErrorCode::VALIDATION_ERROR, $message, 422, $errors);
    }

    public static function business(string $message, string $code = ApiErrorCode::BUSINESS_RULE_VIOLATION): self
    {
        return new self($code, $message, 422);
    }

    public static function conflict(string $message, string $code): self
    {
        return new self($code, $message, 409);
    }

    public static function forbidden(string $message, string $code = ApiErrorCode::FORBIDDEN): self
    {
        return new self($code, $message, 403);
    }
}
