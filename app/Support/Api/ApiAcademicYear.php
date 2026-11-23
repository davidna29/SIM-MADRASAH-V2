<?php

namespace App\Support\Api;

use App\Exceptions\ApiException;
use App\Models\AcademicYear;
use Illuminate\Database\Eloquent\ModelNotFoundException;

final class ApiAcademicYear
{
    public static function active(): AcademicYear
    {
        try {
            return AcademicYear::active();
        } catch (ModelNotFoundException) {
            throw new ApiException(
                ApiErrorCode::NO_ACTIVE_ACADEMIC_YEAR,
                'Tidak ada tahun ajaran aktif. Aktifkan tahun ajaran terlebih dahulu.',
                409,
            );
        }
    }
}
