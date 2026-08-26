<?php

namespace App\Enums;

enum Kecamatan: string
{
    case PAHANDUT = 'Pahandut';
    case BUKIT_BATU = 'Bukit Batu';
    case JEKAN_RAYA = 'Jekan Raya';
    case SEBANGAU = 'Sebangau';
    case RAKUMPIT = 'Rakumpit';

    public static function options(): array
    {
        return [
            'Pahandut' => 'Pahandut',
            'Bukit Batu' => 'Bukit Batu',
            'Jekan Raya' => 'Jekan Raya',
            'Sebangau' => 'Sebangau',
            'Rakumpit' => 'Rakumpit',
        ];
    }
}
