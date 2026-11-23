<?php

namespace App\Services\Akademik;

use App\Models\AcademicCalendarEvent;
use App\Models\AcademicCalendarPeriod;
use App\Models\AcademicYear;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;

class AcademicCalendarService
{
    /**
     * @return Collection<int, AcademicCalendarEvent>
     */
    public function events(AcademicYear $year, CarbonImmutable $from, CarbonImmutable $to): Collection
    {
        return AcademicCalendarEvent::query()
            ->where('academic_year_id', $year->id)
            ->where(function ($query) use ($from, $to) {
                $query->whereBetween('start_date', [$from->toDateString(), $to->toDateString()])
                    ->orWhere(function ($inner) use ($from, $to) {
                        $inner->whereNotNull('end_date')
                            ->whereDate('start_date', '<=', $to->toDateString())
                            ->whereDate('end_date', '>=', $from->toDateString());
                    });
            })
            ->orderBy('start_date')
            ->get();
    }

    public function period(AcademicYear $year, string $semester): ?AcademicCalendarPeriod
    {
        return AcademicCalendarPeriod::query()
            ->where('academic_year_id', $year->id)
            ->where('semester', $semester)
            ->first();
    }

    /**
     * @return array{
     *     month: int, label: string, start: CarbonImmutable, end: CarbonImmutable,
     *     hari_tersedia: int, hari_efektif: int, hari_libur: int, pekan_efektif: int,
     *     minggu_tersedia: int, events: Collection<int, AcademicCalendarEvent>
     * }
     */
    public function monthSummary(AcademicYear $year, int $yearNumber, int $month, ?string $semester = null): array
    {
        $start = CarbonImmutable::create($yearNumber, $month, 1)->startOfMonth();
        $end = $start->endOfMonth();

        $semester ??= $this->semesterForDate($start);
        $period = $this->period($year, $semester);

        if ($period) {
            $start = $start->lessThan($period->start_date) ? CarbonImmutable::parse($period->start_date) : $start;
            $end = $end->greaterThan($period->end_date) ? CarbonImmutable::parse($period->end_date) : $end;
        }

        $events = $this->events($year, $start->startOfMonth(), $end->endOfMonth());
        $liburDays = $this->holidayDays($events, true);
        $nonEffectiveDays = $this->holidayDays($events);

        $availableDays = 0;
        $effectiveDays = 0;
        $availableWeeks = [];
        $effectiveWeeks = [];
        $holidaySchoolDays = 0;

        for ($cursor = $start; $cursor->lessThanOrEqualTo($end); $cursor = $cursor->addDay()) {
            if ($cursor->dayOfWeek === CarbonImmutable::SUNDAY) {
                continue;
            }

            $availableDays++;
            $availableWeeks[$cursor->format('o-W')] = true;

            if ($liburDays->has($cursor->toDateString())) {
                $holidaySchoolDays++;
            }

            if ($nonEffectiveDays->has($cursor->toDateString())) {
                continue;
            }

            $effectiveDays++;
            $effectiveWeeks[$cursor->format('o-W')] = true;
        }

        return [
            'month' => $month,
            'label' => $start->locale('id')->translatedFormat('F Y'),
            'start' => $start,
            'end' => $end,
            'hari_tersedia' => $availableDays,
            'hari_efektif' => $effectiveDays,
            'hari_libur' => $holidaySchoolDays,
            'pekan_efektif' => count($effectiveWeeks),
            'minggu_tersedia' => count($availableWeeks),
            'events' => $events,
        ];
    }

    /**
     * @return array{hari_tersedia: int, hari_efektif: int, hari_libur: int, pekan_efektif: int, minggu_tersedia: int, snapshot_pekan: ?int, snapshot_hari: ?int}
     */
    public function semesterSummary(AcademicYear $year, string $semester): array
    {
        $period = $this->period($year, $semester);

        if (! $period) {
            return [
                'hari_tersedia' => 0, 'hari_efektif' => 0, 'hari_libur' => 0,
                'pekan_efektif' => 0, 'minggu_tersedia' => 0,
                'snapshot_pekan' => null, 'snapshot_hari' => null,
            ];
        }

        $start = CarbonImmutable::parse($period->start_date);
        $end = CarbonImmutable::parse($period->end_date);
        $nonEffectiveDays = $this->holidayDays($this->events($year, $start, $end));

        $available = 0;
        $effective = 0;
        $availableWeeks = [];
        $effectiveWeeks = [];

        for ($cursor = $start; $cursor->lessThanOrEqualTo($end); $cursor = $cursor->addDay()) {
            if ($cursor->dayOfWeek === CarbonImmutable::SUNDAY) {
                continue;
            }

            $available++;
            $availableWeeks[$cursor->format('o-W')] = true;

            if ($nonEffectiveDays->has($cursor->toDateString())) {
                continue;
            }

            $effective++;
            $effectiveWeeks[$cursor->format('o-W')] = true;
        }

        return [
            'hari_tersedia' => $available,
            'hari_efektif' => $effective,
            'hari_libur' => $available - $effective,
            'pekan_efektif' => count($effectiveWeeks),
            'minggu_tersedia' => count($availableWeeks),
            'snapshot_pekan' => $period->effective_weeks,
            'snapshot_hari' => $period->effective_days,
        ];
    }

    /**
     * @param  Collection<int, AcademicCalendarEvent>  $events
     * @return Collection<string, true>
     */
    private function holidayDays(Collection $events, bool $onlyLibur = false): Collection
    {
        $days = collect();

        foreach ($events as $event) {
            if ($onlyLibur && ! $event->is_libur) {
                continue;
            }

            if (! $onlyLibur && ! $event->is_libur && $event->is_efektif !== false) {
                continue;
            }

            for ($cursor = $event->start_date->toImmutable(); $cursor->lessThanOrEqualTo($event->endDate()); $cursor = $cursor->addDay()) {
                $days->put($cursor->toDateString(), true);
            }
        }

        return $days;
    }

    public function semesterForDate(CarbonImmutable $date): string
    {
        return (int) $date->format('n') >= 7 ? 'ganjil' : 'genap';
    }
}
