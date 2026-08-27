<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeAttendance extends Model
{
    use HasFactory;

    /** Status kehadiran pegawai: value => label */
    public const STATUSES = [
        'hadir' => 'Hadir',
        'izin' => 'Izin',
        'sakit' => 'Sakit',
        'dinas_luar' => 'Dinas Luar',
        'cuti' => 'Cuti',
        'terlambat' => 'Terlambat',
        'pulang_awal' => 'Pulang Awal',
        'alpha' => 'Alpha',
    ];

    /** Mark pendek untuk rekap bulanan (mirror rekap siswa: hadir = titik) */
    public const STATUS_MARKS = [
        'hadir' => '•',
        'izin' => 'I',
        'sakit' => 'S',
        'dinas_luar' => 'DL',
        'cuti' => 'C',
        'terlambat' => 'T',
        'pulang_awal' => 'PA',
        'alpha' => 'A',
    ];

    protected $fillable = [
        'employee_id', 'attendance_date', 'status',
        'clock_in', 'clock_out', 'note', 'recorded_by',
    ];

    protected function casts(): array
    {
        return ['attendance_date' => 'date'];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public static function markFor(string $status): ?string
    {
        return self::STATUS_MARKS[$status] ?? null;
    }
}
