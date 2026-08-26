<?php

namespace App\Support;

use App\Models\AcademicYear;
use App\Models\Achievement;
use App\Models\Attendance;
use App\Models\ClassGroup;
use App\Models\Employee;
use App\Models\ExtracurricularMember;
use App\Models\LibraryBook;
use App\Models\LibraryLoan;
use App\Models\LibraryMember;
use App\Models\Offense;
use App\Models\Report;
use App\Models\ReportItem;
use App\Models\StudentEnrollment;
use App\Models\Subject;
use App\Models\TuitionPayment;
use App\Models\User;
use Carbon\Carbon;

class LaporanService
{
    /**
     * Rekap akademik: rata-rata rapor per kelas, distribusi predikat.
     */
    public static function rekapAkademik(AcademicYear $tahun): array
    {
        $classes = ClassGroup::orderByRaw("FIELD(grade_level,'I','II','III','IV','V','VI')")->orderBy('name')->get();

        $rows = $classes->map(function ($class) use ($tahun) {
            $enrollments = StudentEnrollment::where('academic_year_id', $tahun->id)
                ->where('class_group_id', $class->id)
                ->where('status', 'aktif')
                ->count();

            $reports = Report::where('academic_year_id', $tahun->id)
                ->where('semester', $tahun->semester)
                ->where('status', 'terbit')
                ->whereIn('student_id', StudentEnrollment::where('academic_year_id', $tahun->id)
                    ->where('class_group_id', $class->id)
                    ->where('status', 'aktif')
                    ->pluck('student_id'))
                ->get();

            $items = ReportItem::whereIn('report_id', $reports->pluck('id'))->get();
            $scores = $items->whereNotNull('score')->pluck('score');

            $rataRata = $scores->isNotEmpty() ? round($scores->avg(), 1) : null;

            return [
                'kelas' => $class->name,
                'grade_level' => $class->grade_level,
                'jumlah_siswa' => $enrollments,
                'jumlah_rapor' => $reports->count(),
                'rata_rata' => $rataRata,
                'predikat_a' => $scores->filter(fn ($s) => $s >= 85)->count(),
                'predikat_b' => $scores->filter(fn ($s) => $s >= 70 && $s < 85)->count(),
                'predikat_c' => $scores->filter(fn ($s) => $s >= 55 && $s < 70)->count(),
                'predikat_d' => $scores->filter(fn ($s) => $s < 55)->count(),
            ];
        })->filter(fn ($r) => $r['jumlah_siswa'] > 0)->values();

        $allScores = ReportItem::whereIn('report_id', Report::where('academic_year_id', $tahun->id)
            ->where('semester', $tahun->semester)
            ->where('status', 'terbit')
            ->pluck('id'))
            ->whereNotNull('score')
            ->pluck('score');

        return [
            'rows' => $rows,
            'rata_rata_umum' => $allScores->isNotEmpty() ? round($allScores->avg(), 1) : null,
            'kelas_terbaik' => $rows->sortByDesc('rata_rata')->first(),
            'kelas_terendah' => $rows->sortBy('rata_rata')->first(),
            'total_siswa' => $rows->sum('jumlah_siswa'),
            'total_rapor' => $rows->sum('jumlah_rapor'),
        ];
    }

    /**
     * Rekap kehadiran: H/S/I/A per kelas per bulan.
     */
    public static function rekapKehadiran(AcademicYear $tahun): array
    {
        $months = $tahun->semester === 'ganjil' ? range(7, 12) : range(1, 6);
        $classes = ClassGroup::orderByRaw("FIELD(grade_level,'I','II','III','IV','V','VI')")->orderBy('name')->get();

        $rows = $classes->map(function ($class) use ($tahun, $months) {
            $enrollmentIds = StudentEnrollment::where('academic_year_id', $tahun->id)
                ->where('class_group_id', $class->id)
                ->where('status', 'aktif')
                ->pluck('id');

            $attendances = Attendance::whereIn('student_enrollment_id', $enrollmentIds)
                ->get()
                ->groupBy(fn ($a) => (int) $a->attendance_date->format('n'));

            $totalH = 0;
            $totalS = 0;
            $totalI = 0;
            $totalA = 0;

            foreach ($months as $bulan) {
                $rows = $attendances->get($bulan, collect());
                $totalH += $rows->where('status', 'hadir')->count();
                $totalS += $rows->where('status', 'sakit')->count();
                $totalI += $rows->where('status', 'izin')->count();
                $totalA += $rows->where('status', 'alpha')->count();
            }

            $total = $totalH + $totalS + $totalI + $totalA;

            return [
                'kelas' => $class->name,
                'grade_level' => $class->grade_level,
                'jumlah_siswa' => $enrollmentIds->count(),
                'hadir' => $totalH,
                'sakit' => $totalS,
                'izin' => $totalI,
                'alpha' => $totalA,
                'total' => $total,
                'persen_hadir' => $total > 0 ? round($totalH / $total * 100, 1) : 0,
            ];
        })->filter(fn ($r) => $r['jumlah_siswa'] > 0)->values();

        $totalH = $rows->sum('hadir');
        $totalAll = $rows->sum('total');

        return [
            'rows' => $rows,
            'persen_hadir_umum' => $totalAll > 0 ? round($totalH / $totalAll * 100, 1) : 0,
            'kelas_terbaik' => $rows->sortByDesc('persen_hadir')->first(),
            'kelas_terendah' => $rows->sortBy('persen_hadir')->first(),
            'total_siswa' => $rows->sum('jumlah_siswa'),
        ];
    }

    /**
     * Rekap keuangan: SPP per kelas.
     */
    public static function rekapKeuangan(AcademicYear $tahun): array
    {
        $months = $tahun->semester === 'ganjil' ? range(7, 12) : range(1, 6);
        $classes = ClassGroup::orderByRaw("FIELD(grade_level,'I','II','III','IV','V','VI')")->orderBy('name')->get();

        $rows = $classes->map(function ($class) use ($tahun, $months) {
            $enrollmentIds = StudentEnrollment::where('academic_year_id', $tahun->id)
                ->where('class_group_id', $class->id)
                ->where('status', 'aktif')
                ->pluck('id');

            $payments = TuitionPayment::where('academic_year_id', $tahun->id)
                ->whereIn('student_enrollment_id', $enrollmentIds)
                ->whereIn('bulan', $months)
                ->get();

            $lunas = $payments->where('status', 'lunas');
            $totalNominal = $lunas->sum('nominal');
            $totalBulan = $enrollmentIds->count() * count($months);
            $lunasBulan = $lunas->count();

            return [
                'kelas' => $class->name,
                'grade_level' => $class->grade_level,
                'jumlah_siswa' => $enrollmentIds->count(),
                'lunas_bulan' => $lunasBulan,
                'total_bulan' => $totalBulan,
                'total_nominal' => $totalNominal,
                'persen_lunas' => $totalBulan > 0 ? round($lunasBulan / $totalBulan * 100, 1) : 0,
            ];
        })->filter(fn ($r) => $r['jumlah_siswa'] > 0)->values();

        return [
            'rows' => $rows,
            'total_terkumpul' => $rows->sum('total_nominal'),
            'persen_lunas_umum' => $rows->sum('total_bulan') > 0
                ? round($rows->sum('lunas_bulan') / $rows->sum('total_bulan') * 100, 1)
                : 0,
            'total_siswa' => $rows->sum('jumlah_siswa'),
        ];
    }

    /**
     * Rekap kesiswaan: prestasi & pelanggaran per kelas.
     */
    public static function rekapKesiswaan(AcademicYear $tahun): array
    {
        $classes = ClassGroup::orderByRaw("FIELD(grade_level,'I','II','III','IV','V','VI')")->orderBy('name')->get();

        $rows = $classes->map(function ($class) use ($tahun) {
            $enrollmentIds = StudentEnrollment::where('academic_year_id', $tahun->id)
                ->where('class_group_id', $class->id)
                ->where('status', 'aktif')
                ->pluck('id');

            $studentIds = StudentEnrollment::whereIn('id', $enrollmentIds)->pluck('student_id');

            $prestasi = Achievement::whereIn('student_id', $studentIds)
                ->where('status_verifikasi', 'terverifikasi')
                ->count();

            $pelanggaran = Offense::whereIn('student_id', $studentIds)
                ->where('status_penyelesaian', 'selesai')
                ->count();

            $poinPelanggaran = Offense::whereIn('student_id', $studentIds)
                ->where('status_penyelesaian', 'selesai')
                ->sum('poin');

            return [
                'kelas' => $class->name,
                'grade_level' => $class->grade_level,
                'jumlah_siswa' => $enrollmentIds->count(),
                'prestasi' => $prestasi,
                'pelanggaran' => $pelanggaran,
                'poin_pelanggaran' => $poinPelanggaran,
            ];
        })->filter(fn ($r) => $r['jumlah_siswa'] > 0)->values();

        return [
            'rows' => $rows,
            'total_prestasi' => $rows->sum('prestasi'),
            'total_pelanggaran' => $rows->sum('pelanggaran'),
            'total_poin' => $rows->sum('poin_pelanggaran'),
            'total_siswa' => $rows->sum('jumlah_siswa'),
        ];
    }

    /**
     * Rekap tenaga: jumlah guru & pegawai per status.
     */
    public static function rekapTenaga(): array
    {
        $employees = Employee::with('person')->get();

        $byRole = $employees->groupBy(fn ($e) => $e->user?->role ?? 'lainnya');
        $aktif = $employees->where('status', 'aktif');
        $nonaktif = $employees->where('status', 'nonaktif');

        $rows = $byRole->map(function ($group, $role) {
            return [
                'role' => $role,
                'label' => match ($role) {
                    'guru' => 'Guru Mata Pelajaran',
                    'guru_bk' => 'Guru BK',
                    'wali_kelas' => 'Wali Kelas',
                    default => ucfirst(str_replace('_', ' ', $role)),
                },
                'total' => $group->count(),
                'aktif' => $group->where('status', 'aktif')->count(),
                'nonaktif' => $group->where('status', 'nonaktif')->count(),
            ];
        })->values();

        $siswaAktif = StudentEnrollment::where('academic_year_id', AcademicYear::active()?->id)
            ->where('status', 'aktif')
            ->count();

        return [
            'rows' => $rows,
            'total_pegawai' => $employees->count(),
            'total_aktif' => $aktif->count(),
            'total_guru' => $byRole->get('guru', collect())->count(),
            'siswa_aktif' => $siswaAktif,
            'rasio_guru_siswa' => $aktif->where('user.role', 'guru')->count() > 0
                ? round($siswaAktif / $aktif->where('user.role', 'guru')->count(), 1)
                : 0,
        ];
    }

    /**
     * Rekap perpustakaan: buku, peminjaman, anggota.
     */
    public static function rekapPerpustakaan(): array
    {
        $totalBuku = LibraryBook::sum('total_qty');
        $bukuFisik = LibraryBook::where('is_ebook', false)->sum('total_qty');
        $bukuEbook = LibraryBook::where('is_ebook', true)->count();
        $pinjamanAktif = LibraryLoan::where('status', 'dipinjam')->count();
        $anggotaAktif = LibraryMember::where('status', 'aktif')->count();
        $terlambat = LibraryLoan::where('status', 'terlambat')->count();

        $bukuPopuler = LibraryLoan::select('book_id')
            ->selectRaw('COUNT(*) as jumlah')
            ->groupBy('book_id')
            ->orderByDesc('jumlah')
            ->limit(5)
            ->with('book')
            ->get();

        return [
            'total_buku' => $totalBuku,
            'buku_fisik' => $bukuFisik,
            'buku_ebook' => $bukuEbook,
            'pinjaman_aktif' => $pinjamanAktif,
            'anggota_aktif' => $anggotaAktif,
            'terlambat' => $terlambat,
            'buku_populer' => $bukuPopuler,
        ];
    }
}
