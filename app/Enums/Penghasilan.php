<?php

namespace App\Enums;

enum Penghasilan: string
{
    case KURANG_500RB = '<Rp500rb';
    case RP500RB_1JT = 'Rp500rb-1jt';
    case RP1JT_2JT = 'Rp1jt-2jt';
    case RP2JT_3JT = 'Rp2jt-3jt';
    case RP3JT_5JT = 'Rp3jt-5jt';
    case LEBIH_5JT = '>';
    case TIDAK_ADA = 'Tidak ada';

    public static function options(): array
    {
        return [
            '<Rp500rb' => '< Rp500rb',
            'Rp500rb-1jt' => 'Rp500rb – 1jt',
            'Rp1jt-2jt' => 'Rp1jt – 2jt',
            'Rp2jt-3jt' => 'Rp2jt – 3jt',
            'Rp3jt-5jt' => 'Rp3jt – 5jt',
            '>' => '> Rp5jt',
            'Tidak ada' => 'Tidak ada',
        ];
    }
}
