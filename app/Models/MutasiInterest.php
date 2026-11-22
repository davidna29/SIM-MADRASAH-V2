<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MutasiInterest extends Model
{
    protected $fillable = ['name', 'phone', 'privacy_consent_at', 'privacy_consent_version'];

    protected function casts(): array
    {
        return ['privacy_consent_at' => 'datetime'];
    }
}
