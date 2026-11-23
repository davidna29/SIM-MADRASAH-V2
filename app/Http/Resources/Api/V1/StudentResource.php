<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $enrollment = $this->enrollments->first();

        return [
            'id' => $this->id,
            'nis' => $this->nis,
            'name' => $this->displayName(),
            'gender' => $this->gender ?? $this->person?->gender,
            'class_group' => $enrollment?->classGroup ? [
                'id' => $enrollment->classGroup->id,
                'name' => $enrollment->classGroup->name,
            ] : null,
            'enrollment' => $enrollment ? [
                'id' => $enrollment->id,
                'status' => $enrollment->status,
                'academic_year_id' => $enrollment->academic_year_id,
            ] : null,
        ];
    }
}
