<?php

namespace App\Http\Controllers\Akademik;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\ClassGroup;
use App\Models\Report;
use App\Models\Score;
use App\Models\StudentEnrollment;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RekapRaporController extends Controller
{
    public function index(Request $request): View
    {
        $tahun = AcademicYear::active();
        $semester = $request->get('semester', $tahun->semester);
        $classFilter = $request->integer('class_group_id');

        // Daftar kelas tahun aktif dengan jumlah enrollment (satu query + withCount).
        $classes = ClassGroup::query()
            ->whereHas('enrollments', fn ($q) => $q->where('academic_year_id', $tahun->id)->where('status', 'aktif'))
            ->withCount(['enrollments' => fn ($q) => $q->where('academic_year_id', $tahun->id)->where('status', 'aktif')])
            ->when($classFilter, fn ($q) => $q->whereKey($classFilter))
            ->orderBy('grade_level')
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        // Penghitung rapor terbit per kelas: satu query agregat group by class_group_id.
        $publishedByClass = Report::query()
            ->where('reports.academic_year_id', $tahun->id)
            ->where('reports.semester', $semester)
            ->where('reports.status', 'terbit')
            ->join('students', 'students.id', '=', 'reports.student_id')
            ->join('student_enrollments', function ($join) use ($tahun) {
                $join->on('student_enrollments.student_id', '=', 'students.id')
                    ->where('student_enrollments.academic_year_id', '=', $tahun->id)
                    ->where('student_enrollments.status', '=', 'aktif');
            })
            ->selectRaw('student_enrollments.class_group_id, count(distinct reports.student_id) as total')
            ->groupBy('student_enrollments.class_group_id')
            ->pluck('total', 'student_enrollments.class_group_id');

        // Penghitung siswa yang sudah punya nilai (>=1 score) per kelas: satu query agregat.
        $scoredByClass = Score::query()
            ->where('scores.academic_year_id', $tahun->id)
            ->where('scores.semester', $semester)
            ->join('student_enrollments', 'student_enrollments.id', '=', 'scores.student_enrollment_id')
            ->where('student_enrollments.academic_year_id', $tahun->id)
            ->where('student_enrollments.status', 'aktif')
            ->whereNotNull('scores.score')
            ->selectRaw('student_enrollments.class_group_id, count(distinct scores.student_enrollment_id) as total')
            ->groupBy('student_enrollments.class_group_id')
            ->pluck('total', 'student_enrollments.class_group_id');

        $perClass = $classes->map(function (ClassGroup $class) use ($publishedByClass, $scoredByClass) {
            $totalSiswa = (int) $class->enrollments_count;
            $raporTerbit = (int) ($publishedByClass[$class->id] ?? 0);
            $nilaiTerisi = (int) ($scoredByClass[$class->id] ?? 0);

            return [
                'class' => $class,
                'total_siswa' => $totalSiswa,
                'rapor_terbit' => $raporTerbit,
                'nilai_terisi' => $nilaiTerisi,
                'persentase_rapor' => $totalSiswa > 0 ? round(($raporTerbit / $totalSiswa) * 100, 1) : 0,
                'persentase_nilai' => $totalSiswa > 0 ? round(($nilaiTerisi / $totalSiswa) * 100, 1) : 0,
            ];
        });

        // Total seluruh madrasah dihitung dari SEMUA kelas aktif (bukan hanya halaman pagination),
        // agar KPI tidak bergeser saat berada di halaman 2+ atau filter.
        $allClasses = ClassGroup::query()
            ->whereHas('enrollments', fn ($q) => $q->where('academic_year_id', $tahun->id)->where('status', 'aktif'))
            ->withCount(['enrollments' => fn ($q) => $q->where('academic_year_id', $tahun->id)->where('status', 'aktif')])
            ->orderBy('grade_level')
            ->orderBy('name')
            ->get();

        $totals = [
            'total_siswa' => $allClasses->sum('enrollments_count'),
            'rapor_terbit' => $allClasses->sum(fn ($c) => (int) ($publishedByClass[$c->id] ?? 0)),
            'nilai_terisi' => $allClasses->sum(fn ($c) => (int) ($scoredByClass[$c->id] ?? 0)),
        ];
        $totals['persentase_rapor'] = $totals['total_siswa'] > 0 ? round(($totals['rapor_terbit'] / $totals['total_siswa']) * 100, 1) : 0;
        $totals['persentase_nilai'] = $totals['total_siswa'] > 0 ? round(($totals['nilai_terisi'] / $totals['total_siswa']) * 100, 1) : 0;

        return view('pages.akademik.rapor.index', [
            'roleLabel' => 'Rekap Rapor & Nilai',
            'breadcrumb' => [
                ['label' => 'Akademik', 'href' => route('dashboard')],
                ['label' => 'Rekap Rapor & Nilai'],
            ],
            'tahun' => $tahun,
            'semester' => $semester,
            'classes' => $classes,
            'perClass' => $perClass,
            'totals' => $totals,
            'classFilter' => $classFilter,
            'allClasses' => $allClasses,
        ]);
    }

    public function kelas(ClassGroup $classGroup, Request $request): View
    {
        $tahun = AcademicYear::active();
        $semester = $request->get('semester', $tahun->semester);

        $enrollments = StudentEnrollment::with('student')
            ->where('class_group_id', $classGroup->id)
            ->where('academic_year_id', $tahun->id)
            ->where('status', 'aktif')
            ->orderBy('student_id')
            ->paginate(15)
            ->withQueryString();

        // Mapel pada tahun ini (semua mapel yang pernah dinilai di kelas tsb).
        $subjects = Subject::whereHas('scores', function ($q) use ($classGroup, $tahun, $semester) {
            $q->where('academic_year_id', $tahun->id)
                ->where('semester', $semester)
                ->whereHas('enrollment', fn ($e) => $e->where('class_group_id', $classGroup->id));
        })->orderBy('sort_order')->orderBy('code')->get();

        // Nilai per enrollment per mapel: satu query.
        $scores = Score::with('componentValues')
            ->where('academic_year_id', $tahun->id)
            ->where('semester', $semester)
            ->whereHas('enrollment', fn ($e) => $e->where('class_group_id', $classGroup->id))
            ->get();

        $matrix = $enrollments->map(function ($enrollment) use ($subjects, $scores) {
            $row = [];
            foreach ($subjects as $subject) {
                $score = $scores->first(fn ($s) => $s->student_enrollment_id === $enrollment->id && $s->subject_id === $subject->id);
                $row[$subject->id] = $score;
            }

            return ['enrollment' => $enrollment, 'scores' => $row];
        });

        return view('pages.akademik.rapor.kelas', [
            'roleLabel' => 'Rekap Rapor & Nilai',
            'breadcrumb' => [
                ['label' => 'Akademik', 'href' => route('dashboard')],
                ['label' => 'Rekap Rapor & Nilai', 'href' => route('akademik.rapor.index')],
                ['label' => $classGroup->grade_level.' '.$classGroup->name],
            ],
            'tahun' => $tahun,
            'semester' => $semester,
            'classGroup' => $classGroup,
            'enrollments' => $enrollments,
            'subjects' => $subjects,
            'matrix' => $matrix,
        ]);
    }

    public function siswa(StudentEnrollment $studentEnrollment, Request $request): View
    {
        $tahun = AcademicYear::active();
        $semester = $request->get('semester', $tahun->semester);

        $enrollment = $studentEnrollment->load('student', 'classGroup');

        $scores = Score::with(['subject', 'componentValues.scoreComponent'])
            ->where('student_enrollment_id', $enrollment->id)
            ->where('academic_year_id', $tahun->id)
            ->where('semester', $semester)
            ->get();

        return view('pages.akademik.rapor.siswa', [
            'roleLabel' => 'Rekap Rapor & Nilai',
            'breadcrumb' => [
                ['label' => 'Akademik', 'href' => route('dashboard')],
                ['label' => 'Rekap Rapor & Nilai', 'href' => route('akademik.rapor.index')],
                ['label' => $studentEnrollment->student->name],
            ],
            'tahun' => $tahun,
            'semester' => $semester,
            'enrollment' => $enrollment,
            'scores' => $scores,
        ]);
    }

    protected function semesterOptions(): array
    {
        return ['ganjil' => 'Ganjil', 'genap' => 'Genap'];
    }
}
