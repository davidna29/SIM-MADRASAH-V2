<?php

namespace App\Support\Misnu;

use PhpOffice\PhpSpreadsheet\IOFactory;
use RuntimeException;

/**
 * Membaca sheet "DATABASE" pada workbook EMIS MIS NU.
 *
 * Baris pertama adalah header; nilai tanggal dikembalikan apa adanya (serial
 * Excel atau string) agar normalisasi tanggal ditangani terpisah dan tercatat
 * bila tidak valid.
 */
final class StudentWorkbookReader
{
    /** @var array<string, string> */
    private const HEADER_ALIASES = [
        'TEMPAL LAHIR' => 'birth_place',
        'TANGGAL LAHIR' => 'birth_date',
        'JENIS KELAMIN' => 'gender',
        'KELAS' => 'grade',
        'ROMBEL' => 'rombel',
        'ASAL ROMBEL' => 'origin_rombel',
        'NO.ABSEN' => 'attendance_no',
        'NIK SISWA' => 'nik',
        'NISN' => 'nisn',
        'NIS' => 'nis_emis',
        'NIS 6 DIGIT' => 'nis_local',
        'AGAMA' => 'religion',
        'PERNAH TK' => 'ever_tk',
        'PERNAH PAUD' => 'ever_paud',
        'HOBI' => 'hobby',
        'CITA-CITA' => 'ambition',
        'ANAK KE' => 'child_order',
        'JML. SAUDARA' => 'sibling_count',
        'TGL MASUK' => 'entry_date',
        'HEPATITIS B' => 'imm_hepb',
        'POLIO' => 'imm_polio',
        'BCG' => 'imm_bcg',
        'CAMPAK' => 'imm_campak',
        'DPT-HB-HiB' => 'imm_dpt',
        'COVID' => 'imm_covid',
        'TUNA RUNGU' => 'dis_deaf',
        'TUNA NETRA' => 'dis_blind',
        'TUNA DAKSA' => 'dis_disabled',
        'TUNA GRAHITA' => 'dis_intellectual',
        'TUNA LARAS' => 'dis_behavioral',
        'LAMBAN BELAJAR' => 'dis_slow_learner',
        'GANGGUAN KOMUNIKASI' => 'dis_communication',
        'BAKAT LUAR BIASA' => 'dis_gifted',
        'JENIS TMP TINGGAL' => 'residence_type',
        'ALAMAT' => 'address',
        'PROVINSI' => 'province',
        'KOTA' => 'city',
        'KECAMATAN' => 'district',
        'KELURAHAN' => 'village',
        'RT' => 'rt',
        'RW' => 'rw',
        'KODE POS' => 'postal_code',
        'JARAK MADRASAH' => 'distance',
        'TRANSPORTASI' => 'transport',
        'WAKTU TEMPUH' => 'commute_time',
        'NO. TELPON RUMAH' => 'home_phone',
        'NO. HANDPHONE' => 'phone',
        'EMAIL' => 'email',
        'NOMOR KK' => 'kk_number',
        'NAMA KEPALA KELUARGA' => 'kk_head_name',
        'NAMA AYAH' => 'father_name',
        'STATUS AYAH' => 'father_status',
        'NIK AYAH' => 'father_nik',
        'TEMPAT LAHIR AYAH' => 'father_birth_place',
        'TANGGAL LAHIR AYAH' => 'father_birth_date',
        'PENDIDIKAN AYAH' => 'father_education',
        'PEKERJAAN AYAH' => 'father_job',
        'PENGHASILAN AYAH' => 'father_income',
        'NOMOR TELP AYAH' => 'father_phone',
        'NAMA IBU' => 'mother_name',
        'STATUS IBU' => 'mother_status',
        'TEMPAT LAHIR IBU' => 'mother_birth_place',
        'TANGGAL LAHIR IBU' => 'mother_birth_date',
        'NIK IBU' => 'mother_nik',
        'PENDIDIKAN IBU' => 'mother_education',
        'PEKERJAAN IBU' => 'mother_job',
        'PENGHASILAN IBU' => 'mother_income',
        'NOMOR TELP IBU' => 'mother_phone',
        'NAMA WALI' => 'guardian_name',
        'TEMPAT LAHIR WALI' => 'guardian_birth_place',
        'TAHUN LAHIR WALI' => 'guardian_birth_year',
        'NIK WALI' => 'guardian_nik',
        'PENDIDIKAN WALI' => 'guardian_education',
        'PEKERJAAN WALI' => 'guardian_job',
        'PENGHASILAN WALI' => 'guardian_income',
        'NOMOR TELP WALI' => 'guardian_phone',
        'NO. KKS' => 'social_kks',
        'NO. PKH' => 'social_pkh',
        'NO. KIP' => 'social_kip',
        'STATUS KEPEMILIKAN' => 'parent_ownership',
        'NAMA SEKOLAH ASAL' => 'origin_school',
        'NSM SEKOLAH ASAL' => 'origin_nsm',
        'NPSN SEKOLAH ASAL' => 'origin_npsn',
        'ALAMAT SEKOLAH ASAL' => 'origin_address',
    ];

    /** @var array<string, string> */
    private const SCAN_ALIASES = [
        'SCAN KK' => 'scan_kk',
        'SCAN KK WALI' => 'scan_kk_wali',
        'SCAN AKTA' => 'scan_akta',
        'SCAN IJAZAH' => 'scan_ijazah',
        'SCAN FOTO' => 'scan_foto',
    ];

    /**
     * @return list<array<string, mixed>>
     */
    public function rows(string $path): array
    {
        if (! is_file($path)) {
            throw new RuntimeException("Workbook tidak ditemukan: {$path}");
        }

        if (ini_get('memory_limit') !== '-1' && $this->bytes(ini_get('memory_limit')) < 512 * 1024 * 1024) {
            ini_set('memory_limit', '512M');
        }

        $reader = IOFactory::createReaderForFile($path);
        $reader->setReadDataOnly(true);
        $reader->setLoadSheetsOnly(['DATABASE']);
        $spreadsheet = $reader->load($path);
        $sheet = $spreadsheet->getSheetByName('DATABASE');

        if (! $sheet) {
            throw new RuntimeException('Sheet DATABASE tidak ditemukan pada workbook.');
        }

        $highestRow = $sheet->getHighestDataRow();
        $highestColumn = $sheet->getHighestDataColumn();
        $headerRow = $sheet->rangeToArray("A1:{$highestColumn}1", null, false, false)[0] ?? [];

        $map = [];
        $seen = [];
        foreach ($headerRow as $index => $header) {
            $key = $this->normalizeHeader($header);

            if ($key === '') {
                continue;
            }

            $occurrence = $seen[$key] = ($seen[$key] ?? 0) + 1;

            // Kolom ganda pada workbook: alamat/RT/RW/kode pos siswa lalu orang tua.
            $duplicateAliases = [
                'ALAMAT' => ['address', 'parent_address'],
                'RT' => ['rt', 'parent_rt'],
                'RW' => ['rw', 'parent_rw'],
                'KODE POS' => ['postal_code', 'parent_postal_code'],
            ];

            if (isset($duplicateAliases[$key])) {
                $map[$index] = $duplicateAliases[$key][$occurrence - 1] ?? $duplicateAliases[$key][0];
            } elseif (isset(self::HEADER_ALIASES[$key])) {
                $map[$index] = self::HEADER_ALIASES[$key];
            } elseif (isset(self::SCAN_ALIASES[$key])) {
                $map[$index] = self::SCAN_ALIASES[$key];
            } elseif ($key === 'NAMA') {
                $map[$index] = 'name';
            }
        }

        $rows = [];
        for ($row = 2; $row <= $highestRow; $row++) {
            $values = $sheet->rangeToArray("A{$row}:{$highestColumn}{$row}", null, false, false)[0] ?? [];
            $record = ['_row' => $row];

            foreach ($map as $index => $field) {
                $record[$field] = $values[$index] ?? null;
            }

            if ($this->blank($record['name'] ?? null)) {
                continue;
            }

            $rows[] = $record;
        }

        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);

        return $rows;
    }

    private function normalizeHeader(mixed $header): string
    {
        if (! is_scalar($header)) {
            return '';
        }

        $text = strtoupper(trim((string) $header));
        $text = preg_replace('/\s+/', ' ', $text) ?? $text;

        // Alamat muncul dua kali (siswa & orang tua); yang pertama milik siswa.
        if ($text === 'ALAMAT') {
            return 'ALAMAT';
        }

        return $text;
    }

    private function blank(mixed $value): bool
    {
        return $value === null || (is_string($value) && trim($value) === '');
    }

    private function bytes(string $value): int
    {
        $value = trim($value);

        if ($value === '-1') {
            return PHP_INT_MAX;
        }

        $unit = strtolower(substr($value, -1));
        $number = (int) $value;

        return match ($unit) {
            'g' => $number * 1024 ** 3,
            'm' => $number * 1024 ** 2,
            'k' => $number * 1024,
            default => $number,
        };
    }
}
