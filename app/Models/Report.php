<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class Report extends Model
{
    use HasFactory;

    protected $fillable = ['student_id', 'academic_year_id', 'semester', 'snapshot', 'pdf_path', 'status', 'version'];

    protected function casts(): array
    {
        return ['snapshot' => 'array'];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(ReportItem::class);
    }

    public function subjectItems(): Collection
    {
        return $this->items()->orderBy('sort_order')->orderBy('id')->get();
    }
}
