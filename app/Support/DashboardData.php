<?php

namespace App\Support;

use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\AttendanceReview;
use App\Models\ClassGroup;
use App\Models\Employee;
use App\Models\Report;
use App\Models\StudentEnrollment;
use App\Models\TuitionPayment;
use Carbon\Carbon;
use Spatie\Activitylog\Models\Activity;

class DashboardData
{
    public static function kpis(AcademicYear $tahun): array
    {
        $siswaAktif = StudentEnrollment::where('academic_year_id', $tahun->id)
            ->where('status', 'aktif')
            ->count();

        $guruPegawai = Employee::where('status', 'aktif')->count();

        $sppTerkumpul = TuitionPayment::where('academic_year_id', $tahun->id)
            ->where('semester', $tahun->semester)
            ->where('status', 'lunas')
            ->sum('nominal');

        $attToday = Attendance::where('attendance_date', now()->toDateString())->get();
        $hadir = $attToday->where('status', 'hadir')->count();
        $total = $attToday->count();
        $persenHadir = $total > 0 ? round($hadir / $total * 100, 1) : null;

        return [
            'siswa_aktif' => $siswaAktif,
            'guru_pegawai' => $guruPegawai,
            'spp_terkumpul' => $sppTerkumpul,
            'kehadiran_persen' => $persenHadir,
            'kehadiran_hadir' => $hadir,
        ];
    }

    public static function perluTindakan(AcademicYear $tahun): array
    {
        $items = [];
        $today = now()->toDateString();
        $classes = self::classes();

        // 1. Rombel yang belum mereview kehadiran hari ini
        foreach ($classes as $class) {
            $hasStudents = StudentEnrollment::where('academic_year_id', $tahun->id)
                ->where('class_group_id', $class->id)
                ->where('status', 'aktif')
                ->exists();

            if (! $hasStudents) {
                continue;
            }

            $reviewed = AttendanceReview::where('class_group_id', $class->id)
                ->where('attendance_date', $today)
                ->exists();

            if (! $reviewed) {
                $items[] = [
                    'id' => 'R-'.$class->name,
                    'jenis' => 'Kehadiran',
                    'urgensi' => 'tinggi',
                    'label' => 'Kelas '.$class->name.' belum mereview kehadiran hari ini',
                    'waktu' => 'Hari ini',
                ];
            }
        }

        // 2. SPP belum lunas bulan berjalan (bila bulan termasuk semester aktif)
        $currentMonth = (int) now()->format('n');
        $months = $tahun->semester === 'ganjil' ? range(7, 12) : range(1, 6);

        if (in_array($currentMonth, $months, true)) {
            $paidIds = TuitionPayment::where('academic_year_id', $tahun->id)
                ->where('bulan', $currentMonth)
                ->where('status', 'lunas')
                ->pluck('student_enrollment_id');

            $unpaid = StudentEnrollment::with('student')
                ->where('academic_year_id', $tahun->id)
                ->where('status', 'aktif')
                ->whereNotIn('id', $paidIds)
                ->take(5)
                ->get();

            foreach ($unpaid as $e) {
                $items[] = [
                    'id' => 'S-'.$e->student->nis,
                    'jenis' => 'SPP',
                    'urgensi' => 'sedang',
                    'label' => $e->student->name.' — SPP '.self::bulanLabel($currentMonth).' belum lunas',
                    'waktu' => ucfirst(self::bulanLabel($currentMonth)),
                ];
            }
        }

        // 3. Rombel yang rapor semesternya belum terbit
        $belumRapor = [];
        foreach ($classes as $class) {
            $studentIds = StudentEnrollment::where('academic_year_id', $tahun->id)
                ->where('class_group_id', $class->id)
                ->where('status', 'aktif')
                ->pluck('student_id');

            if ($studentIds->isEmpty()) {
                continue;
            }

            $terbit = Report::whereIn('student_id', $studentIds)
                ->where('academic_year_id', $tahun->id)
                ->where('semester', $tahun->semester)
                ->where('status', 'terbit')
                ->exists();

            if (! $terbit) {
                $belumRapor[] = $class;
            }
        }

        foreach (array_slice($belumRapor, 0, 5) as $class) {
            $items[] = [
                'id' => 'N-'.$class->name,
                'jenis' => 'Nilai',
                'urgensi' => 'sedang',
                'label' => 'Kelas '.$class->name.' — rapor semester belum terbit',
                'waktu' => 'Semester '.ucfirst($tahun->semester),
            ];
        }

        return $items;
    }

    public static function kehadiranRombel(AcademicYear $tahun): array
    {
        $today = now()->toDateString();
        $result = [];

        foreach (self::classes() as $class) {
            $enrollmentIds = StudentEnrollment::where('academic_year_id', $tahun->id)
                ->where('class_group_id', $class->id)
                ->where('status', 'aktif')
                ->pluck('id');

            if ($enrollmentIds->isEmpty()) {
                continue;
            }

            $attToday = Attendance::whereIn('student_enrollment_id', $enrollmentIds)
                ->where('attendance_date', $today)
                ->get();

            $result[] = [
                'class' => $class,
                'hadir' => $attToday->where('status', 'hadir')->count(),
                'total' => $attToday->count(),
                'reviewed' => AttendanceReview::where('class_group_id', $class->id)
                    ->where('attendance_date', $today)
                    ->exists(),
            ];
        }

        return $result;
    }

    public static function tagihanTerbaru(AcademicYear $tahun, int $limit = 6)
    {
        return TuitionPayment::with('enrollment.student')
            ->where('academic_year_id', $tahun->id)
            ->where('status', 'lunas')
            ->orderByDesc('tanggal_bayar')
            ->orderByDesc('id')
            ->take($limit)
            ->get()
            ->map(fn ($p) => [
                'nama' => $p->enrollment?->student?->name ?? '—',
                'nis' => $p->enrollment?->student?->nis ?? '—',
                'jenis' => 'SPP '.self::bulanLabel($p->bulan),
                'tanggal' => ($p->tanggal_bayar ?? $p->created_at)->toDateString(),
                'nominal' => $p->nominal,
                'status' => 'lunas',
            ]);
    }

    public static function aktivitas(int $limit = 8)
    {
        return Activity::query()
            ->with('causer')
            ->orderByDesc('id')
            ->take($limit)
            ->get()
            ->map(fn ($a) => [
                'nama' => $a->causer?->name ?? 'Sistem',
                'aksi' => self::aktivitasText($a->description),
                'waktu' => $a->created_at->diffForHumans(),
            ]);
    }

    protected static function classes()
    {
        return ClassGroup::orderByRaw("FIELD(grade_level,'I','II','III','IV','V','VI')")->orderBy('name')->get();
    }

    protected static function bulanLabel(int $bulan): string
    {
        return Carbon::create(null, $bulan, 1)->locale('id')->translatedFormat('F');
    }

    protected static function aktivitasText(string $description): string
    {
        return match ($description) {
            'kehadiran_diinput' => 'mencatat kehadiran siswa',
            'jurnal_diisi' => 'mengisi jurnal mengajar',
            'jurnal_diubah' => 'mengubah jurnal mengajar',
            'jurnal_dihapus' => 'menghapus jurnal mengajar',
            'jadwal_disusun' => 'menyusun jadwal pelajaran',
            'jadwal_generate_blank' => 'membuat kerangka kosong jadwal',
            'jadwal_generate_copy' => 'menyalin jadwal tahun sebelumnya',
            'rapor_diterbitkan' => 'menerbitkan rapor',
            'spp_dibayar' => 'mencatat pembayaran SPP',
            'spp_diubah' => 'mengubah pembayaran SPP',
            'spp_nominal_diatur' => 'mengatur nominal SPP',
            'spp_override_diatur' => 'mengatur keringanan SPP',
            default => ucfirst(str_replace('_', ' ', $description)),
        };
    }
}
