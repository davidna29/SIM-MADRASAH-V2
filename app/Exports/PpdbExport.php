<?php

namespace App\Exports;

use App\Models\PpdbRegistration;
use App\Support\PpdbService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PpdbExport implements FromQuery, WithHeadings, WithMapping, WithStyles
{
    use Exportable;

    protected ?string $status;

    protected ?int $academicYearId;

    protected array $columnMap;

    public function __construct(?string $status = null, ?int $academicYearId = null)
    {
        $this->status = $status;
        $this->academicYearId = $academicYearId;
        $this->columnMap = PpdbService::exportMapping();
    }

    public function query(): Builder
    {
        $query = PpdbRegistration::query()
            ->where('status', '!=', 'draft');

        if ($this->status) {
            $query->where('status', $this->status);
        }

        if ($this->academicYearId) {
            $query->where('academic_year_id', $this->academicYearId);
        }

        return $query->orderByRaw('UPPER(name)');
    }

    public function headings(): array
    {
        return array_values($this->columnMap);
    }

    public function map($registration): array
    {
        $data = [];
        foreach (array_keys($this->columnMap) as $field) {
            $value = $registration->{$field} ?? '';

            // Format dates
            if ($value instanceof Carbon) {
                $value = $value->format('d/m/Y');
            }

            // Format booleans
            if (is_bool($value)) {
                $value = $value ? 'Ya' : 'Tidak';
            }

            $data[] = $value;
        }

        return $data;
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
