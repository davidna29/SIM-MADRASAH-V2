<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportItem extends Model
{
    use HasFactory;

    protected $table = 'report_items';

    protected $fillable = [
        'report_id', 'subject_code', 'subject_name',
        'class_group_id', 'class_name', 'teacher_name',
        'score', 'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'score' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class);
    }

    public function classGroup(): BelongsTo
    {
        return $this->belongsTo(ClassGroup::class);
    }
}
