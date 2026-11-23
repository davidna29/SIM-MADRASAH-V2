<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SimpleImport implements ToArray, WithHeadingRow
{
    public function array(array $array): void
    {
        // Data dibaca melalui Excel::toArray(); kelas ini hanya menandai
        // bahwa baris pertama adalah heading.
    }
}
