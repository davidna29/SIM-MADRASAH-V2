<?php

namespace App\Http\Controllers\Publik;

use App\Http\Controllers\Controller;
use App\Models\AcademicCalendarEvent;
use App\Models\AcademicCalendarPeriod;
use App\Models\AcademicYear;
use App\Services\Akademik\AcademicCalendarService;
use Carbon\CarbonImmutable;
use Illuminate\View\View;

class KalenderController extends Controller
{
    public function index(AcademicCalendarService $service): View
    {
        $tahun = AcademicYear::where('is_active', true)->firstOrFail();
        $periods = AcademicCalendarPeriod::where('academic_year_id', $tahun->id)->get()->keyBy('semester');

        $months = $periods
            ->flatMap(function (AcademicCalendarPeriod $period) {
                $cursor = CarbonImmutable::parse($period->start_date)->startOfMonth();
                $end = CarbonImmutable::parse($period->end_date)->endOfMonth();
                $months = [];

                while ($cursor->lessThanOrEqualTo($end)) {
                    $months[] = ['year' => (int) $cursor->year, 'month' => (int) $cursor->month, 'semester' => $period->semester];
                    $cursor = $cursor->addMonth();
                }

                return $months;
            })
            ->unique(fn ($month) => $month['year'].'-'.$month['month'])
            ->sortBy(fn ($month) => $month['year'] * 100 + $month['month'])
            ->values()
            ->map(fn ($month) => [
                ...$month,
                'summary' => $service->monthSummary($tahun, $month['year'], $month['month'], $month['semester']),
                'events' => $service->events(
                    $tahun,
                    CarbonImmutable::create($month['year'], $month['month'], 1)->startOfMonth(),
                    CarbonImmutable::create($month['year'], $month['month'], 1)->endOfMonth()
                ),
            ]);

        return view('pages.publik.kalender', [
            'tahun' => $tahun,
            'months' => $months,
            'periods' => $periods,
            'categories' => AcademicCalendarEvent::CATEGORIES,
        ]);
    }
}
