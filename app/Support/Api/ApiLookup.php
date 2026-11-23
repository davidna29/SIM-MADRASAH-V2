<?php

namespace App\Support\Api;

use App\Exceptions\ApiException;
use App\Models\ClassGroup;
use App\Models\Subject;

final class ApiLookup
{
    public static function classGroup(string|int $value): ClassGroup
    {
        $class = is_numeric($value)
            ? ClassGroup::find((int) $value)
            : ClassGroup::where('name', $value)->first();

        if (! $class) {
            throw new ApiException(ApiErrorCode::NOT_FOUND, 'Kelas tidak ditemukan.', 404);
        }

        return $class;
    }

    public static function subject(string|int $value): Subject
    {
        $subject = is_numeric($value)
            ? Subject::find((int) $value)
            : Subject::where('code', $value)->orWhere('name', $value)->first();

        if (! $subject) {
            throw new ApiException(ApiErrorCode::NOT_FOUND, 'Mata pelajaran tidak ditemukan.', 404);
        }

        return $subject;
    }
}
