<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->person?->name,
            'nip' => $this->nip,
            'employee_status' => $this->employee_status,
            'status' => $this->status,
            'show_on_website' => (bool) $this->show_on_website,
            'unit' => $this->organizationalUnit ? [
                'id' => $this->organizationalUnit->id,
                'name' => $this->organizationalUnit->name,
            ] : null,
            'position' => $this->position ? [
                'id' => $this->position->id,
                'name' => $this->position->name,
            ] : null,
        ];
    }
}
