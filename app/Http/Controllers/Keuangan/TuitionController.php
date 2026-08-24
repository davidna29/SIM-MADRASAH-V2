<?php

namespace App\Http\Controllers\Keuangan;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\ClassGroup;
use App\Models\StudentEnrollment;
use App\Models\TuitionOverride;
use App\Models\TuitionPayment;
use App\Models\TuitionSetting;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TuitionController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', TuitionPayment::class);

        $tahun = AcademicYear::active();
        $classes = ClassGroup::orderByRaw("FIELD(grade_level,'I','II','III','IV','V','VI')")->orderBy('name')->get();

        $classGroup = request('class_group_id')
            ? $classes->firstWhere('id', (int) request('class_group_id'))
            : $classes->first();

        $months = $this->semesterMonths($tahun);
        $defaultNominal = TuitionSetting::where('academic_year_id', $tahun->id)->value('nominal');

        $rows = collect();

        if ($classGroup) {
            $enrollments = StudentEnrollment::with('student')
                ->where('academic_year_id', $tahun->id)
                ->where('class_group_id', $classGroup->id)
                ->where('status', 'aktif')
                ->orderBy('student_id')
                ->get();

            $overrides = TuitionOverride::where('academic_year_id', $tahun->id)
                ->whereIn('student_enrollment_id', $enrollments->pluck('id'))
                ->get()
                ->keyBy('student_enrollment_id');

            $payments = TuitionPayment::where('academic_year_id', $tahun->id)
                ->whereIn('student_enrollment_id', $enrollments->pluck('id'))
                ->whereIn('bulan', $months)
                ->get()
                ->keyBy(fn ($p) => $p->student_enrollment_id.'|'.$p->bulan);

            $rows = $enrollments->map(function ($enrollment) use ($overrides, $payments, $months, $defaultNominal) {
                $nominal = $overrides->get($enrollment->id)?->nominal ?? $defaultNominal;

                return [
                    'enrollment' => $enrollment,
                    'student' => $enrollment->student,
                    'nominal' => $nominal,
                    'override' => $overrides->get($enrollment->id),
                    'cells' => collect($months)->mapWithKeys(fn ($bulan) => [
                        $bulan => $payments->get($enrollment->id.'|'.$bulan),
                    ]),
                ];
            });
        }

        return view('pages.keuangan.spp.index', [
            'roleLabel' => 'Bendahara / TU',
            'breadcrumb' => [
                ['label' => 'Keuangan & TU', 'href' => route('dashboard')],
                ['label' => 'SPP Bulanan'],
            ],
            'tahun' => $tahun,
            'classes' => $classes,
            'classGroup' => $classGroup,
            'months' => $months,
            'monthsLabel' => collect($months)->mapWithKeys(fn ($bulan) => [$bulan => $this->bulanLabel($bulan)]),
            'defaultNominal' => $defaultNominal,
            'rows' => $rows,
        ]);
    }

    public function pay(Request $request): RedirectResponse
    {
        $this->authorize('manage', TuitionPayment::class);

        $validated = $request->validate([
            'student_enrollment_id' => ['required', 'exists:student_enrollments,id'],
            'bulan' => ['required', 'integer', 'between:1,12'],
            'nominal' => ['required', 'integer', 'min:0'],
            'tanggal_bayar' => ['nullable', 'date', 'before_or_equal:today'],
            'metode' => ['nullable', 'in:tunai,transfer'],
            'catatan' => ['nullable', 'string', 'max:255'],
        ]);

        $tahun = AcademicYear::active();
        $enrollment = StudentEnrollment::find($validated['student_enrollment_id']);

        abort_unless($enrollment && $enrollment->academic_year_id === $tahun->id, 422);

        $tanggalBayar = $validated['tanggal_bayar'] ?? null;

        $payment = TuitionPayment::updateOrCreate(
            [
                'student_enrollment_id' => $enrollment->id,
                'academic_year_id' => $tahun->id,
                'bulan' => $validated['bulan'],
            ],
            [
                'semester' => $tahun->semester,
                'nominal' => $validated['nominal'],
                'status' => $tanggalBayar ? 'lunas' : 'belum_bayar',
                'tanggal_bayar' => $tanggalBayar,
                'metode' => $validated['metode'] ?? null,
                'catatan' => $validated['catatan'] ?? null,
                'recorded_by' => auth()->id(),
            ]
        );

        activity('keuangan')->performedOn($payment)->log($payment->wasRecentlyCreated ? 'spp_dibayar' : 'spp_diubah');

        $message = $payment->wasRecentlyCreated
            ? 'Pembayaran SPP berhasil dicatat dan disematkan ke papan.'
            : 'Pembayaran SPP diperbarui.';

        return back()->with('status', $message);
    }

    public function settings(): View
    {
        $this->authorize('manage', TuitionPayment::class);

        $years = AcademicYear::orderByDesc('name')->get();
        $settings = TuitionSetting::whereIn('academic_year_id', $years->pluck('id'))->get()->keyBy('academic_year_id');

        return view('pages.keuangan.spp.settings', [
            'roleLabel' => 'Bendahara / TU',
            'breadcrumb' => [
                ['label' => 'Keuangan & TU', 'href' => route('dashboard')],
                ['label' => 'SPP Bulanan', 'href' => route('spp.index')],
                ['label' => 'Nominal SPP'],
            ],
            'years' => $years,
            'settings' => $settings,
        ]);
    }

    public function settingsStore(Request $request): RedirectResponse
    {
        $this->authorize('manage', TuitionPayment::class);

        $validated = $request->validate([
            'nominal' => ['required', 'array'],
            'nominal.*' => ['nullable', 'integer', 'min:0'],
        ]);

        foreach ($validated['nominal'] as $yearId => $nominal) {
            if ($nominal === null || $nominal === '') {
                continue;
            }

            TuitionSetting::updateOrCreate(
                ['academic_year_id' => $yearId],
                ['nominal' => $nominal]
            );
        }

        activity('keuangan')->log('spp_nominal_diatur');

        return redirect()->route('spp.settings')->with('status', 'Nominal SPP per tahun ajaran berhasil disimpan.');
    }

    public function overrides(): View
    {
        $this->authorize('manage', TuitionPayment::class);

        $tahun = AcademicYear::active();
        $classes = ClassGroup::orderByRaw("FIELD(grade_level,'I','II','III','IV','V','VI')")->orderBy('name')->get();

        $classGroup = request('class_group_id')
            ? $classes->firstWhere('id', (int) request('class_group_id'))
            : $classes->first();

        $rows = collect();

        if ($classGroup) {
            $enrollments = StudentEnrollment::with('student')
                ->where('academic_year_id', $tahun->id)
                ->where('class_group_id', $classGroup->id)
                ->where('status', 'aktif')
                ->orderBy('student_id')
                ->get();

            $overrides = TuitionOverride::where('academic_year_id', $tahun->id)
                ->whereIn('student_enrollment_id', $enrollments->pluck('id'))
                ->get()
                ->keyBy('student_enrollment_id');

            $rows = $enrollments->map(fn ($enrollment) => [
                'enrollment' => $enrollment,
                'student' => $enrollment->student,
                'override' => $overrides->get($enrollment->id),
            ]);
        }

        return view('pages.keuangan.spp.overrides', [
            'roleLabel' => 'Bendahara / TU',
            'breadcrumb' => [
                ['label' => 'Keuangan & TU', 'href' => route('dashboard')],
                ['label' => 'SPP Bulanan', 'href' => route('spp.index')],
                ['label' => 'Keringanan SPP'],
            ],
            'tahun' => $tahun,
            'classes' => $classes,
            'classGroup' => $classGroup,
            'rows' => $rows,
        ]);
    }

    public function overridesStore(Request $request): RedirectResponse
    {
        $this->authorize('manage', TuitionPayment::class);

        $validated = $request->validate([
            'student_enrollment_id' => ['required', 'exists:student_enrollments,id'],
            'nominal' => ['required', 'integer', 'min:0'],
            'keterangan' => ['nullable', 'string', 'max:255'],
        ]);

        $tahun = AcademicYear::active();
        $enrollment = StudentEnrollment::find($validated['student_enrollment_id']);

        abort_unless($enrollment && $enrollment->academic_year_id === $tahun->id, 422);

        $override = TuitionOverride::updateOrCreate(
            [
                'student_enrollment_id' => $enrollment->id,
                'academic_year_id' => $tahun->id,
            ],
            [
                'nominal' => $validated['nominal'],
                'keterangan' => $validated['keterangan'] ?? null,
            ]
        );

        activity('keuangan')->performedOn($override)->log('spp_override_diatur');

        return back()->with('status', 'Keringanan SPP siswa disimpan.');
    }

    protected function semesterMonths(AcademicYear $tahun): array
    {
        return $tahun->semester === 'ganjil' ? range(7, 12) : range(1, 6);
    }

    protected function bulanLabel(int $bulan): string
    {
        return Carbon::create(null, $bulan, 1)->locale('id')->translatedFormat('M');
    }
}
