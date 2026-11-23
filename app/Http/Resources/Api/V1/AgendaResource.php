<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AgendaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'jenis' => $this->jenis,
            'tanggal' => $this->tanggal?->toDateString(),
            'waktu' => $this->waktu?->format('H:i'),
            'lokasi' => $this->lokasi,
            'penanggung_jawab' => $this->penanggung_jawab,
            'isi' => $this->isi,
            'target' => $this->target,
            'tampil_mulai' => $this->tampil_mulai?->toDateString(),
            'tampil_selesai' => $this->tampil_selesai?->toDateString(),
            'status' => $this->status,
            'creator' => $this->creator?->name,
        ];
    }
}
