<?php

namespace App\Enums;

enum Pekerjaan: string
{
    case TIDAK_BEKERJA = '01';
    case PENSIUNAN = '02';
    case PNS = '03';
    case TNI_POLISI = '04';
    case GURU_DOSEN = '05';
    case PEGAWAI_SWASTA = '06';
    case WIRASWASTA = '07';
    case PENGACARA = '08';
    case SENIMAN = '09';
    case DOKTER = '10';
    case PILOT = '11';
    case PEDAGANG = '12';
    case PETANI = '13';
    case NELAYAN = '14';
    case BURUH = '15';
    case SOPIR = '16';
    case POLITIKUS = '17';
    case LAINNYA = '18';

    public static function options(): array
    {
        return [
            '01' => 'Tidak Bekerja',
            '02' => 'Pensiunan',
            '03' => 'PNS',
            '04' => 'TNI/Polisi',
            '05' => 'Guru/Dosen',
            '06' => 'Pegawai Swasta',
            '07' => 'Wiraswasta',
            '08' => 'Pengacara/Hakim/Jaksa/Notaris',
            '09' => 'Seniman/Pelukis/Artis',
            '10' => 'Dokter/Bidan/Perawat',
            '11' => 'Pilot/Pramugara',
            '12' => 'Pedagang',
            '13' => 'Petani/Peternak',
            '14' => 'Nelayan',
            '15' => 'Buruh',
            '16' => 'Sopir/Masinis/Kondektur',
            '17' => 'Politikus',
            '18' => 'Lainnya',
        ];
    }
}
