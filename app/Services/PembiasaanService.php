<?php

namespace App\Services;

use App\Models\AcademicYear;
use App\Models\PembiasaanMateri;
use App\Models\PembiasaanMateriPeriode;
use App\Models\PembiasaanNilai;
use App\Models\Setting;
use App\Models\Student;
use App\Models\StudentEnrollment;

class PembiasaanService
{
    public const GRADE_MAP = ['I' => 1, 'II' => 2, 'III' => 3, 'IV' => 4, 'V' => 5, 'VI' => 6];

    public const GRADE_INT_MAP = [1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI'];

    public const SEMESTER_MAP = ['ganjil' => 1, 'genap' => 2];

    public const PAIRS = [
        [1, 1], [1, 2], [2, 1], [2, 2], [3, 1], [3, 2],
        [4, 1], [4, 2], [5, 1], [5, 2], [6, 1], [6, 2],
    ];

    public static function gradeToInt(string $g): int
    {
        return self::GRADE_MAP[$g] ?? 0;
    }

    public static function intToGrade(int $g): string
    {
        return self::GRADE_INT_MAP[$g] ?? '';
    }

    public function currentGradeSemester(Student $siswa): ?array
    {
        $year = AcademicYear::active();

        if (! $year) {
            return null;
        }

        $enrollment = StudentEnrollment::where('student_id', $siswa->id)
            ->where('academic_year_id', $year->id)
            ->where('status', 'aktif')
            ->with('classGroup')
            ->first();

        if (! $enrollment || ! $enrollment->classGroup) {
            return null;
        }

        return [
            'kelas' => self::gradeToInt($enrollment->classGroup->grade_level),
            'semester' => self::SEMESTER_MAP[$year->semester] ?? 1,
            'tahun' => $year->name,
        ];
    }

    public function materials(string $modul)
    {
        return PembiasaanMateri::forModul($modul)->with('periodes')->get();
    }

    public function buildMatrix(Student $siswa, string $modul): array
    {
        $materials = $this->materials($modul);
        $current = $this->currentGradeSemester($siswa);
        $editable = $current ? "{$current['kelas']}-{$current['semester']}" : null;

        $ids = $materials->pluck('id')->all();
        $nilai = PembiasaanNilai::where('siswa_id', $siswa->id)
            ->whereIn('materi_id', $ids)
            ->get()
            ->keyBy(fn ($n) => "{$n->materi_id}-{$n->kelas}-{$n->semester}");

        $rows = $materials->map(function ($m) use ($nilai) {
            $periodes = $m->periodes->keyBy(fn ($p) => "{$p->kelas}-{$p->semester}");
            $cells = collect(self::PAIRS)->map(function ($pair) use ($m, $periodes, $nilai) {
                [$k, $s] = $pair;
                $key = "{$k}-{$s}";
                $periode = $periodes[$key] ?? null;
                $n = $nilai["{$m->id}-{$k}-{$s}"] ?? null;

                return [
                    'kelas' => $k,
                    'semester' => $s,
                    'aktif' => $periode?->aktif ?? false,
                    'nilai' => $n?->nilai,
                ];
            });

            return ['materi' => $m, 'cells' => $cells];
        });

        return [
            'modul' => $modul,
            'materials' => $materials,
            'rows' => $rows,
            'pairs' => self::PAIRS,
            'editablePair' => $editable,
            'current' => $current,
            'footers' => $this->allFooters($siswa, $modul),
        ];
    }

    public function allFooters(Student $siswa, string $modul): array
    {
        $materials = $this->materials($modul);
        $ids = $materials->pluck('id')->all();

        $periodes = PembiasaanMateriPeriode::whereIn('materi_id', $ids)
            ->where('aktif', true)
            ->get()
            ->keyBy(fn ($p) => "{$p->materi_id}-{$p->kelas}-{$p->semester}");

        $nilai = PembiasaanNilai::where('siswa_id', $siswa->id)
            ->whereIn('materi_id', $ids)
            ->get()
            ->keyBy(fn ($n) => "{$n->materi_id}-{$n->kelas}-{$n->semester}");

        $out = [];
        foreach (self::PAIRS as [$k, $s]) {
            $jumlah = 0;
            $count = 0;
            foreach ($materials as $m) {
                $pk = "{$m->id}-{$k}-{$s}";
                if (! ($periodes[$pk] ?? null)) {
                    continue;
                }
                $count++;
                $jumlah += ($nilai[$pk]?->nilai ?? 0);
            }
            $rata = $count > 0 ? round($jumlah / $count) : null;
            $out["{$k}-{$s}"] = [
                'jumlah' => $jumlah,
                'count' => $count,
                'rata_rata' => $rata,
                'kategori' => $rata !== null ? self::kategori($rata) : '–',
            ];
        }

        return $out;
    }

    public static function kategori(int $rata): string
    {
        if ($rata >= 90) {
            return 'A+';
        }
        if ($rata >= 80) {
            return 'A';
        }
        if ($rata >= 70) {
            return 'B';
        }
        if ($rata >= 60) {
            return 'C';
        }
        if ($rata >= 50) {
            return 'D';
        }

        return '–';
    }

    public function kop(): array
    {
        $naungan = Setting::get('madrasah_naungan', "YAYASAN AL-MA'ARIF NU KALIMANTAN TENGAH");
        $name = Setting::get('madrasah_name', 'MADRASAH IBTIDAIYAH NAHDLATUL ULAMA');
        $akreditasi = strtoupper((string) Setting::get('madrasah_nilai_akreditasi', 'A'));
        $address = collect([
            Setting::get('madrasah_jalan'),
            Setting::get('madrasah_desa'),
            Setting::get('madrasah_kecamatan'),
            Setting::get('madrasah_kabupaten'),
            Setting::get('madrasah_provinsi'),
            Setting::get('madrasah_kode_pos') ? 'Kode Pos '.Setting::get('madrasah_kode_pos') : null,
            Setting::get('madrasah_phone') ? 'Telp. '.Setting::get('madrasah_phone') : null,
        ])->filter()->implode(' ');

        $logo = Setting::get('madrasah_logo');
        $logoPath = null;
        if ($logo && file_exists(storage_path('app/public/'.$logo))) {
            $logoPath = storage_path('app/public/'.$logo);
        }

        return [
            'naungan' => $naungan,
            'name' => $name,
            'akreditasi' => $akreditasi,
            'address' => $address,
            'logoPath' => $logoPath,
        ];
    }
}
