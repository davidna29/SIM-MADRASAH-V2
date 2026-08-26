<?php

namespace App\Enums;

enum Pendidikan: string
{
    case TIDAK_SEKOLAH = '0';
    case SD = '1';
    case SMP = '2';
    case SMA = '3';
    case D1 = '4';
    case D2 = '5';
    case D3 = '6';
    case D4_S1 = '7';
    case S2 = '8';
    case S3 = '9';

    public static function options(): array
    {
        return [
            '0' => 'Tidak Sekolah',
            '1' => 'SD/Sederajat',
            '2' => 'SMP/Sederajat',
            '3' => 'SMA/Sederajat',
            '4' => 'D1',
            '5' => 'D2',
            '6' => 'D3',
            '7' => 'D4-S1',
            '8' => 'S2',
            '9' => 'S3',
        ];
    }
}
