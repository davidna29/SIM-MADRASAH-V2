<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class LibraryMember extends Model
{
    use HasFactory, LogsActivity;

    public const STATUSES = ['aktif', 'nonaktif'];

    public const TYPES = ['siswa', 'pegawai'];

    protected $fillable = [
        'member_type', 'student_id', 'employee_id', 'member_no',
        'name', 'status', 'joined_at',
    ];

    protected function casts(): array
    {
        return [
            'joined_at' => 'date',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'status'])
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function loans(): HasMany
    {
        return $this->hasMany(LibraryLoan::class, 'member_id');
    }

    public function activeLoans(): HasMany
    {
        return $this->loans()->where('status', 'dipinjam');
    }
}
