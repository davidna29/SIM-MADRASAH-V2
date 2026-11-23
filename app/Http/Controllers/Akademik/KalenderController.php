<?php

namespace App\Http\Controllers\Akademik;

use App\Http\Controllers\Controller;
use App\Http\Requests\AcademicCalendarEventRequest;
use App\Models\AcademicCalendarEvent;
use App\Models\AcademicCalendarPeriod;
use App\Models\AcademicYear;
use App\Services\Akademik\AcademicCalendarService;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KalenderController extends Controller
{
    public function index(Request $request, AcademicCalendarService $service): View
    {
        $tahun = AcademicYear::where('is_active', true)->firstOrFail();
        $months = $this->monthsFor($tahun);
        $validated = $request->validate([
            'tahun' => ['nullable', 'integer', 'min:2000', 'max:2100'],
            'bulan' => ['nullable', 'integer', 'min:1', 'max:12'],
        ]);
        $selectedYear = isset($validated['tahun']) ? (int) $validated['tahun'] : null;
        $selectedMonth = (int) ($request->input('bulan') ?: now()->month);

        $selectedYear = $selectedYear ?: ($months[0]['year'] ?? (int) now()->year);

        if (! collect($months)->contains(fn ($month) => $month['year'] === $selectedYear && $month['month'] === $selectedMonth)) {
            $selectedMonth = $months[0]['month'] ?? (int) now()->month;
            $selectedYear = $months[0]['year'] ?? (int) now()->year;
        }

        $summaries = collect($months)->map(fn ($month) => $service->monthSummary($tahun, $month['year'], $month['month']));
        $selected = $service->monthSummary($tahun, $selectedYear, $selectedMonth);
        $periods = AcademicCalendarPeriod::where('academic_year_id', $tahun->id)->get()->keyBy('semester');

        $monthStart = CarbonImmutable::create($selectedYear, $selectedMonth, 1);
        $events = $service->events($tahun, $monthStart->startOfMonth(), $monthStart->endOfMonth());

        return view('pages.akademik.kalender.index', [
            'roleLabel' => 'Kalender Pendidikan',
            'breadcrumb' => [
                ['label' => 'Akademik', 'href' => route('dashboard')],
                ['label' => 'Kalender Pendidikan'],
            ],
            'tahun' => $tahun,
            'months' => $summaries,
            'selected' => $selected,
            'selectedYear' => $selectedYear,
            'selectedMonth' => $selectedMonth,
            'events' => $events,
            'periods' => $periods,
            'categories' => AcademicCalendarEvent::CATEGORIES,
            'canManage' => in_array(auth()->user()?->role, ['super_admin', 'wakamad_kurikulum', 'tata_usaha'], true),
            'semesterSummary' => [
                'ganjil' => $service->semesterSummary($tahun, 'ganjil'),
                'genap' => $service->semesterSummary($tahun, 'genap'),
            ],
        ]);
    }

    public function store(AcademicCalendarEventRequest $request): RedirectResponse
    {
        $this->ensureManager();

        $tahun = AcademicYear::where('is_active', true)->firstOrFail();
        $validated = $request->validated();

        $event = AcademicCalendarEvent::create([
            ...$validated,
            'academic_year_id' => $tahun->id,
            'is_libur' => $request->boolean('is_libur'),
            'is_efektif' => $request->boolean('is_efektif', true),
            'created_by' => auth()->id(),
        ]);

        activity('akademik')->performedOn($event)->log('kalender_kegiatan_baru');

        return redirect()
            ->route('kalender.index', ['tahun' => $event->start_date->year, 'bulan' => $event->start_date->month])
            ->with('status', 'Kegiatan kalender berhasil disimpan.');
    }

    public function update(AcademicCalendarEventRequest $request, AcademicCalendarEvent $event): RedirectResponse
    {
        $this->ensureManager();

        $validated = $request->validated();
        $event->update([
            ...$validated,
            'is_libur' => $request->boolean('is_libur'),
            'is_efektif' => $request->boolean('is_efektif', true),
        ]);

        activity('akademik')->performedOn($event)->log('kalender_kegiatan_diubah');

        return redirect()
            ->route('kalender.index', ['tahun' => $event->start_date->year, 'bulan' => $event->start_date->month])
            ->with('status', 'Kegiatan kalender berhasil diperbarui.');
    }

    public function destroy(AcademicCalendarEvent $event): RedirectResponse
    {
        $this->ensureManager();

        $event->delete();

        activity('akademik')->performedOn($event)->log('kalender_kegiatan_dihapus');

        return back()->with('status', 'Kegiatan kalender dihapus.');
    }

    public function updatePeriods(Request $request): RedirectResponse
    {
        $this->ensureManager();

        $validated = $request->validate([
            'semester' => ['required', 'in:ganjil,genap'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'effective_weeks' => ['nullable', 'integer', 'min:1', 'max:52'],
            'effective_days' => ['nullable', 'integer', 'min:1', 'max:366'],
            'note' => ['nullable', 'string', 'max:255'],
        ]);

        $tahun = AcademicYear::where('is_active', true)->firstOrFail();

        AcademicCalendarPeriod::updateOrCreate(
            ['academic_year_id' => $tahun->id, 'semester' => $validated['semester']],
            [
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'effective_weeks' => $validated['effective_weeks'] ?? null,
                'effective_days' => $validated['effective_days'] ?? null,
                'note' => $validated['note'] ?? null,
            ]
        );

        return back()->with('status', 'Periode semester berhasil diperbarui.');
    }

    public function cetak(Request $request, AcademicCalendarService $service): View
    {
        $tahun = AcademicYear::where('is_active', true)->firstOrFail();
        $months = collect($this->monthsFor($tahun))
            ->map(fn ($month) => $service->monthSummary($tahun, $month['year'], $month['month']));

        return view('pages.akademik.kalender.cetak', [
            'tahun' => $tahun,
            'months' => $months,
            'periods' => AcademicCalendarPeriod::where('academic_year_id', $tahun->id)->get()->keyBy('semester'),
        ]);
    }

    /**
     * @return list<array{year: int, month: int}>
     */
    private function monthsFor(AcademicYear $tahun): array
    {
        $periods = AcademicCalendarPeriod::where('academic_year_id', $tahun->id)->get();

        if ($periods->isNotEmpty()) {
            return $periods
                ->flatMap(function (AcademicCalendarPeriod $period) {
                    $cursor = CarbonImmutable::parse($period->start_date)->startOfMonth();
                    $end = CarbonImmutable::parse($period->end_date)->endOfMonth();
                    $months = [];

                    while ($cursor->lessThanOrEqualTo($end)) {
                        $months[] = ['year' => (int) $cursor->year, 'month' => (int) $cursor->month];
                        $cursor = $cursor->addMonth();
                    }

                    return $months;
                })
                ->unique(fn ($month) => $month['year'].'-'.$month['month'])
                ->sortBy(fn ($month) => $month['year'] * 100 + $month['month'])
                ->values()
                ->all();
        }

        preg_match('/(\d{4})\s*\/\s*(\d{4})/', $tahun->name, $matches);
        $first = (int) ($matches[1] ?? now()->year);
        $second = (int) ($matches[2] ?? now()->addYear()->year);
        $months = [];

        foreach (range(7, 12) as $month) {
            $months[] = ['year' => $first, 'month' => $month];
        }

        foreach (range(1, 6) as $month) {
            $months[] = ['year' => $second, 'month' => $month];
        }

        return $months;
    }

    private function ensureManager(): void
    {
        abort_unless(in_array(auth()->user()?->role, ['super_admin', 'wakamad_kurikulum', 'tata_usaha'], true), 403);
    }
}
