<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PpiExamArchiveImport implements ToArray, WithHeadingRow
{
    public function array(array $array): void
    {
        // Data dibaca langsung oleh Excel::toArray(); concern ini menandai
        // bahwa baris pertama adalah heading (WithHeadingRow).
    }
}
