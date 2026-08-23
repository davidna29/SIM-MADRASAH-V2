<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Person extends Model
{
    use HasFactory;

    protected $fillable = ['nik', 'name', 'gender', 'religion', 'birth_place', 'birth_date', 'phone', 'email'];

    protected function casts(): array
    {
        return ['birth_date' => 'date'];
    }

    public function employee(): HasOne
    {
        return $this->hasOne(Employee::class);
    }
}
