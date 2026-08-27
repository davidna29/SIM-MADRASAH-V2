<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\OrganizationalUnit;
use App\Models\Person;
use App\Models\Position;
use Illuminate\Database\Seeder;

class GuruMISeeder extends Seeder
{
    public function run(): void
    {
        // Pastikan posisi & unit yang dibutuhkan ada
        Position::updateOrCreate(['code' => 'SATPAM'], ['name' => 'Satpam / Jaga Malam']);
        Position::updateOrCreate(['code' => 'OPERATOR'], ['name' => 'Operator Madrasah']);

        $pos = fn (string $code) => Position::where('code', $code)->first()?->id;
        $unit = fn (string $code) => OrganizationalUnit::where('code', $code)->first()?->id;

        $teachers = [
            // No, Nama, NIP (nullable), NUPTK/PegID (nullable), Gender, BirthPlace, BirthDate, Tugas, Status, Jenjang, Jurusan
            [1, 'ERNA, S.Ag.', '197512122007012044', '0544753655300023', 'P', 'Babai', '1975-12-12', 'Kepala Madrasah', 'pns', 'S1', 'Tarbiyah'],
            [2, 'ESTI MUNIARTINI, A.Ma, S.Pd.', '197406071999032001', '6939752654300012', 'P', 'Sruweng', '1974-07-07', 'Guru Kelas', 'pns', 'S1', 'Tarbiyah'],
            [3, 'SRI HARYATI, S.Pd.', '196804081999032004', '2740746648300032', 'P', 'Pekalongan', '1968-04-08', 'Guru Kelas', 'pns', 'S1', 'PGSD'],
            [4, 'ANWARI ANAS, A.Ma, S.Pd.I.', '197706212007011017', '2953755657200012', 'L', 'Negara', '1977-06-21', 'Guru Bidang Studi', 'pns', 'S1', 'Tarbiyah/PAI'],
            [5, 'IBRAHIM, S.Pd.I, M.Pd.', '197810051999031003', '0337756658200063', 'L', 'Kuala Kapuas', '1978-05-10', 'Guru Bidang Studi', 'pns', 'S2', 'Tarbiyah/PAI'],
            [6, 'MELY ASTUTI, S.Pd.', '197905062007102008', '8838757658300022', 'P', 'Pontianak', '1979-05-06', 'Guru Kelas', 'pns', 'S1', 'FKIP/PGSD'],
            [7, 'SAIDAH, S.Ag.', '196801121997032002', '6444746648300012', 'P', 'Kab Banjar', '1968-01-12', 'Guru Kelas', 'pns', 'S1', 'Tarbiyah'],
            [8, 'MAHMUDAH, M.Pd.', '198507122005012001', '2044763663200003', 'P', 'Anjir Serapat', '1985-07-12', 'Guru Kelas', 'pns', 'S2', 'Tarbiyah/PAI'],
            [9, 'SUWARNI, S.Pd', '197106122007012034', '1944749651300092', 'P', 'Barabai', '1971-06-12', 'Guru Kelas', 'pns', 'S1', 'FKIP'],
            [10, 'NIDA RAHMAWATI, S.Pd.', '198304252007102001', '3757761662300032', 'P', 'Palangka Raya', '1983-04-25', 'Guru Kelas', 'pns', 'S1', 'PGSD'],
            [11, 'SITI ISTIKHAROH', null, '4154747650300013', 'P', 'Madiun', '1969-08-22', 'Guru Kelas', 'honor', 'S1', 'FKIP/PBI'],
            [12, 'ABDUL SANI, S.Ag.', null, '5538748652200002', 'L', 'Pandamaan', '1970-02-06', 'Guru Kelas', 'honor', 'S1', 'Tarbiyah'],
            [13, 'H. MUHAMMAD MAHLAN', null, '3936743643200002', 'L', 'Lok Gabang', '1965-05-25', 'Guru Bidang Studi', 'honor', 'SMA', null],
            [14, 'RAHMAN, S.Pd.I, M.Pd.', null, '1559763663200003', 'L', 'Baru', '1985-12-27', 'Guru Kelas', 'honor', 'S2', 'Tarbiyah'],
            [15, 'RUSHANA SULISTIANI, S.Pd.', null, '6746760661300162', 'P', 'Kuala Kapuas', '1982-04-14', 'Guru Kelas', 'honor', 'S1', 'Tarbiyah'],
            [16, 'AHMAD BAIHAKI, S.Pd.I.', null, '6135764665200013', 'L', 'Palangka Raya', '1986-08-03', 'Guru Bidang Studi', 'honor', 'S1', 'Tarbiyah'],
            [17, 'MELIA AYU LINDASARI, S.Pd.I.', null, null, 'P', 'Palangka Raya', '1988-07-13', 'Guru Bidang Studi', 'honor', 'S1', 'Tarbiyah/TBI'],
            [18, 'WIWIN ELPIRA, S.Pd.', null, null, 'P', 'Baru', '1989-11-04', 'Guru Kelas', 'honor', 'S1', 'FKIP/PBSI'],
            [19, 'SALAMAT, S.Pd.I.', null, '5938767668200012', 'L', 'Sei Lunuk', '1989-06-06', 'Guru Bidang Studi', 'honor', 'S1', 'Tarbiyah'],
            [20, 'FELIA DESINTIAWATI, S.Pd.', null, null, 'P', 'Banjarmasin', '1999-12-15', 'Guru Bidang Studi', 'honor', 'S1', 'Tarbiyah/PAI'],
            [21, 'RASIDAH, S.Pd.', null, null, 'P', 'Tamban Raya', '1998-12-19', 'Guru Kelas', 'honor', 'S1', 'Tarbiyah/TBG'],
            [22, 'NURUL AZIZAH, S.Pd.', null, null, 'P', 'Palangka Raya', '1999-06-02', 'Guru Kelas', 'honor', 'S1', 'PGSD'],
            [23, 'AHMADI MAULANA, S.Pd.', null, null, 'L', 'Palangka Raya', '1998-05-06', 'Guru Kelas', 'honor', 'S1', 'PGMI'],
            [24, 'ALWAFA AMRULLAH, S.Pd.', null, null, 'L', 'Palangka Raya', '2000-05-14', 'Guru Bidang Studi', 'honor', 'S1', 'Tarbiyah'],
            [25, 'MUHAMMAD NOOR RAHMAN, S.Pd.', null, null, 'L', 'Barabai', '1971-06-12', 'Guru Bidang Studi', 'honor', 'S1', 'PGMI/PBA'],
            [26, 'FITRIANI, S.Pd', null, null, 'P', 'Palangka Raya', '2002-12-01', 'Guru Kelas', 'honor', 'S1', 'Tarbiyah'],
            [27, 'AKHMAD HULAIFI, S.Pd', null, null, 'L', 'Samba Katung', '2000-06-23', 'Guru Kelas', 'honor', 'S1', 'FTIK/PGMI'],
            [28, 'MUHAMMAD ARSYAD, A.Ma', null, null, 'L', 'Palangka Raya', '1989-11-04', 'Satpam/Jaga Malam', 'honor', 'D3', 'PGSD'],
            [29, 'M. DEDE MAULANA, S.Pd', null, null, 'L', 'Palangka Raya', '1996-07-16', 'Operator Madrasah', 'honor', 'S1', 'BDP/AGRO'],
            [30, 'YULIA AMELIA', null, null, 'P', 'Palangka Raya', '2004-06-19', 'Guru Kelas', 'honor', 'SMA', null],
            [31, 'ZAHRATUNNISA, S.Pd', null, null, 'P', 'Palangka Raya', '1999-07-16', 'Tata Usaha', 'honor', 'S1', 'Tarbiyah/PAI'],
        ];

        foreach ($teachers as [$no, $name, $nip, $nuptk, $gender, $birthPlace, $birthDate, $tugas, $status, $jenjang, $jurusan]) {
            // NIK: pakai NUPTK/PegID bila ada, bila tidak buat sintetis
            $nik = $nuptk ?? sprintf('6201%012d', $no);

            $person = Person::updateOrCreate(
                ['nik' => $nik],
                [
                    'name' => $name,
                    'gender' => $gender,
                    'religion' => 'Islam',
                    'birth_place' => $birthPlace,
                    'birth_date' => $birthDate,
                ]
            );

            // Posisi
            $positionCode = match (true) {
                str_contains($tugas, 'Kepala') => 'KEPALA_MADRASAH',
                str_contains($tugas, 'Satpam') => 'SATPAM',
                str_contains($tugas, 'Operator') => 'OPERATOR',
                str_contains($tugas, 'Tata Usaha') => 'TATA_USAHA',
                default => 'GURU_MAPEL',
            };

            // Unit organisasi
            $unitCode = match (true) {
                str_contains($tugas, 'Kepala') => 'PIMPINAN',
                str_contains($tugas, 'Satpam') => 'SARPRAS',
                str_contains($tugas, 'Operator') => 'TU',
                str_contains($tugas, 'Tata Usaha') => 'TU',
                default => 'GURU',
            };

            Employee::updateOrCreate(
                ['person_id' => $person->id],
                [
                    'nip' => $nip,
                    'employee_status' => $status,
                    'status' => 'aktif',
                    'position_id' => $pos($positionCode),
                    'organizational_unit_id' => $unit($unitCode),
                ]
            );
        }
    }
}
