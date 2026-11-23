<?php

namespace App\Services\Publikasi;

use App\Models\Agenda;

class AgendaService
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data, int $userId): Agenda
    {
        return Agenda::create([
            ...$data,
            'created_by' => $userId,
        ]);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Agenda $agenda, array $data): Agenda
    {
        $agenda->update($data);

        return $agenda;
    }
}
