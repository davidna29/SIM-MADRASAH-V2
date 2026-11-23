<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ScheduleCellResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'day' => $this->day,
            'period_no' => $this->period_no,
            'class_group' => $this->classGroup ? [
                'id' => $this->classGroup->id,
                'name' => $this->classGroup->name,
            ] : null,
            'subject' => $this->subject ? [
                'id' => $this->subject->id,
                'code' => $this->subject->code,
                'name' => $this->subject->name,
            ] : null,
            'teacher' => $this->teacher ? [
                'id' => $this->teacher->id,
                'name' => $this->teacher->name,
            ] : null,
            'time' => $this->time ?? null,
        ];
    }
}
