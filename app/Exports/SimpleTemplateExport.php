<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SimpleTemplateExport implements FromArray, WithHeadings
{
    /**
     * @param  list<string>  $headings
     * @param  list<array<int, mixed>>  $rows
     */
    public function __construct(
        private readonly array $headings,
        private readonly array $rows = [],
    ) {}

    public function array(): array
    {
        return $this->rows;
    }

    public function headings(): array
    {
        return $this->headings;
    }
}
