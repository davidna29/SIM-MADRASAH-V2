<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Report;
use App\Models\Score;
use App\Models\ScoreComponent;
use App\Models\ScoreComponentValue;
use App\Models\StudentEnrollment;
use App\Models\TeacherAssignment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NilaiController extends Controller
{
    public function penugasan(): View
    {
        $tahun = AcademicYear::active();
        $assignments = auth()->user()->assignments()->with(['classGroup', 'subject'])->where('academic_year_id', $tahun->id)->get();
        $mapelNames = $assignments->pluck('subject.name');

        $reports = Report::with('student')
            ->where('academic_year_id', $tahun->id)
            ->where('semester', $tahun->semester)
            ->where('status', 'terbit')
            ->when($mapelNames->isNotEmpty(), fn ($q) => $q->whereHas('items', fn ($items) => $items->whereIn('subject_name', $mapelNames)))
            ->orderByDesc('created_at')
            ->get();

        return view('pages.guru.penugasan', [
            'roleLabel' => 'Guru Mata Pelajaran',
            'breadcrumb' => [['label' => 'Akademik', 'href' => route('dashboard')], ['label' => 'Penugasan Mengajar']],
            'tahun' => $tahun,
            'assignments' => $assignments,
            'reports' => $reports,
        ]);
    }

    public function edit(TeacherAssignment $assignment): View
    {
        abort_unless($this->owns($assignment), 403);

        $tahun = AcademicYear::active();
        $enrollments = StudentEnrollment::with('student')
            ->where('academic_year_id', $assignment->academic_year_id)
            ->where('class_group_id', $assignment->class_group_id)
            ->orderBy('student_id')
            ->get();

        $scores = Score::with('componentValues')
            ->where('academic_year_id', $assignment->academic_year_id)
            ->where('subject_id', $assignment->subject_id)
            ->where('semester', $tahun->semester)
            ->get()
            ->keyBy('student_enrollment_id');

        $components = ScoreComponent::where('academic_year_id', $assignment->academic_year_id)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return view('pages.guru.nilai', [
            'roleLabel' => 'Guru Mata Pelajaran',
            'breadcrumb' => [
                ['label' => 'Akademik', 'href' => route('dashboard')],
                ['label' => 'Penugasan Mengajar', 'href' => route('guru.penugasan')],
                ['label' => 'Nilai '.$assignment->subject->name],
            ],
            'tahun' => $tahun,
            'assignment' => $assignment,
            'enrollments' => $enrollments,
            'scores' => $scores,
            'components' => $components,
        ]);
    }

    public function update(Request $request, TeacherAssignment $assignment): RedirectResponse
    {
        abort_unless($this->owns($assignment), 403);

        $tahun = AcademicYear::active();
        $enrollments = StudentEnrollment::where('academic_year_id', $assignment->academic_year_id)
            ->where('class_group_id', $assignment->class_group_id)
            ->get();

        $components = ScoreComponent::where('academic_year_id', $assignment->academic_year_id)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        // Mode komponen: input per komponen lalu nilai akhir dihitung dari bobot.
        if ($components->isNotEmpty()) {
            $validated = $request->validate([
                'values' => ['array', 'required'],
                'values.*' => ['array'],
                'values.*.*' => ['nullable', 'integer', 'between:0,100'],
            ]);

            foreach ($enrollments as $enrollment) {
                $values = $validated['values'][$enrollment->id] ?? [];
                $hasAny = collect($values)->filter(fn ($v) => $v !== null)->isNotEmpty();

                if (! $hasAny) {
                    Score::where('student_enrollment_id', $enrollment->id)
                        ->where('subject_id', $assignment->subject_id)
                        ->where('academic_year_id', $assignment->academic_year_id)
                        ->where('semester', $tahun->semester)
                        ->delete();

                    continue;
                }

                $score = Score::updateOrCreate(
                    [
                        'student_enrollment_id' => $enrollment->id,
                        'subject_id' => $assignment->subject_id,
                        'academic_year_id' => $assignment->academic_year_id,
                        'semester' => $tahun->semester,
                    ],
                    ['score' => null]
                );

                foreach ($components as $component) {
                    $value = $values[$component->id] ?? null;

                    if ($value === null) {
                        ScoreComponentValue::where('score_id', $score->id)
                            ->where('score_component_id', $component->id)
                            ->delete();

                        continue;
                    }

                    $score->componentValues()->updateOrCreate(
                        ['score_component_id' => $component->id],
                        ['value' => $value]
                    );
                }

                $score->update(['score' => $score->computeFinalScore()]);
            }

            activity('akademik')->performedOn($assignment)->log('nilai_diinput');

            return back()->with('status', 'Nilai berhasil disimpan ke papan.');
        }

        // Mode legacy: satu kolom nilai 0-100 langsung ke nilai akhir.
        $validated = $request->validate([
            'scores' => ['array', 'required'],
            'scores.*' => ['nullable', 'integer', 'between:0,100'],
        ]);

        foreach ($enrollments as $enrollment) {
            $nilai = $validated['scores'][$enrollment->id] ?? null;

            if ($nilai === null) {
                Score::where('student_enrollment_id', $enrollment->id)
                    ->where('subject_id', $assignment->subject_id)
                    ->where('academic_year_id', $assignment->academic_year_id)
                    ->where('semester', $tahun->semester)
                    ->delete();

                continue;
            }

            Score::updateOrCreate(
                [
                    'student_enrollment_id' => $enrollment->id,
                    'subject_id' => $assignment->subject_id,
                    'academic_year_id' => $assignment->academic_year_id,
                    'semester' => $tahun->semester,
                ],
                ['score' => $nilai]
            );
        }

        activity('akademik')->performedOn($assignment)->log('nilai_diinput');

        return back()->with('status', 'Nilai berhasil disimpan ke papan.');
    }

    public function terbitkan(TeacherAssignment $assignment): RedirectResponse
    {
        abort_unless($this->owns($assignment), 403);

        $tahun = AcademicYear::active();
        $enrollments = StudentEnrollment::with('student')
            ->where('academic_year_id', $assignment->academic_year_id)
            ->where('class_group_id', $assignment->class_group_id)
            ->get();

        $published = 0;

        foreach ($enrollments as $enrollment) {
            $score = Score::where('student_enrollment_id', $enrollment->id)
                ->where('subject_id', $assignment->subject_id)
                ->where('academic_year_id', $assignment->academic_year_id)
                ->where('semester', $tahun->semester)
                ->first();

            if (! $score || $score->score === null) {
                continue;
            }

            // Satu rapor per siswa+tahun+semester (invariant basis data). Penerbitan ulang
            // bersifat idempotent: parent di-update, item mapel di-upsert - bukan baris baru.
            $report = Report::firstOrCreate(
                [
                    'student_id' => $enrollment->student_id,
                    'academic_year_id' => $assignment->academic_year_id,
                    'semester' => $tahun->semester,
                ],
                [
                    'version' => 1,
                    'status' => 'terbit',
                    'snapshot' => $this->buildSnapshot($enrollment, $assignment, $tahun),
                ]
            );

            $report->update([
                'status' => 'terbit',
                'snapshot' => $this->buildSnapshot($enrollment, $assignment, $tahun),
            ]);

            $report->items()->updateOrCreate(
                ['subject_code' => $assignment->subject->code],
                [
                    'subject_name' => $assignment->subject->name,
                    'class_group_id' => $assignment->class_group_id,
                    'class_name' => $assignment->classGroup->name,
                    'teacher_name' => auth()->user()->name,
                    'score' => $score->score,
                    'sort_order' => $assignment->subject->sort_order ?? 0,
                ]
            );

            $published++;
        }

        activity('akademik')->performedOn($assignment)->log('rapor_diterbitkan');

        return redirect()->route('guru.penugasan')
            ->with('status', 'Rapor '.$assignment->subject->name.' kelas '.$assignment->classGroup->name.' diterbitkan/diperbarui ('.$published.' siswa).');
    }

    protected function buildSnapshot(StudentEnrollment $enrollment, TeacherAssignment $assignment, AcademicYear $tahun): array
    {
        return [
            'tahun' => $tahun->name,
            'semester' => $tahun->semester,
            'nis' => $enrollment->student->nis,
            'siswa' => $enrollment->student->displayName(),
            'kelas' => $assignment->classGroup->name,
            'terbit_pada' => now()->toDateTimeString(),
        ];
    }

    public function rapor(Report $report): View
    {
        abort_unless($this->isClassReport($report), 403);

        return view('pages.guru.rapor', [
            'roleLabel' => 'Guru Mata Pelajaran',
            'breadcrumb' => [
                ['label' => 'Akademik', 'href' => route('dashboard')],
                ['label' => 'Penugasan Mengajar', 'href' => route('guru.penugasan')],
                ['label' => 'Rapor '.data_get($report->snapshot, 'siswa')],
            ],
            'report' => $report,
        ]);
    }

    public function unduhRapor(Report $report)
    {
        abort_unless($this->isClassReport($report), 403);

        $pdf = Pdf::loadView('pdf.rapor', ['report' => $report]);
        $tahun = str_replace('/', '-', data_get($report->snapshot, 'tahun'));
        $nis = data_get($report->snapshot, 'nis') ?? $report->student->nis;

        return $pdf->download('rapor-'.$nis.'-'.$tahun.'.pdf');
    }

    protected function owns(TeacherAssignment $assignment): bool
    {
        return $assignment->user_id === auth()->id();
    }

    protected function isClassReport(Report $report): bool
    {
        $mapel = auth()->user()->assignments()->with('subject')->get()->pluck('subject.name');

        return $report->items()->whereIn('subject_name', $mapel)->exists();
    }
}
