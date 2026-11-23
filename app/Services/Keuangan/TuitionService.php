<?php

namespace App\Services\Keuangan;

use App\Models\AcademicYear;
use App\Models\StudentEnrollment;
use App\Models\TuitionOverride;
use App\Models\TuitionPayment;
use App\Models\TuitionSetting;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class TuitionService
{
    /**
     * @return array<int, int>
     */
    public function semesterMonths(AcademicYear $year): array
    {
        return $year->semester === 'ganjil' ? range(7, 12) : range(1, 6);
    }

    public function resolveNominal(AcademicYear $year, StudentEnrollment $enrollment): ?int
    {
        $override = TuitionOverride::query()
            ->where('academic_year_id', $year->id)
            ->where('student_enrollment_id', $enrollment->id)
            ->value('nominal');

        if ($override !== null) {
            return (int) $override;
        }

        $setting = TuitionSetting::query()
            ->where('academic_year_id', $year->id)
            ->value('nominal');

        return $setting !== null ? (int) $setting : null;
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    public function ledger(
        AcademicYear $year,
        ?int $classGroupId = null,
        ?int $bulan = null,
        ?string $status = null,
    ): Collection {
        $months = $this->semesterMonths($year);

        if ($bulan !== null) {
            $months = in_array($bulan, $months, true) ? [$bulan] : [];
        }

        if ($months === []) {
            return collect();
        }

        $enrollments = StudentEnrollment::with(['student', 'classGroup'])
            ->where('academic_year_id', $year->id)
            ->where('status', 'aktif')
            ->when($classGroupId, fn ($query, $id) => $query->where('class_group_id', $id))
            ->orderBy('class_group_id')
            ->orderBy('student_id')
            ->get();

        $overrides = TuitionOverride::query()
            ->where('academic_year_id', $year->id)
            ->whereIn('student_enrollment_id', $enrollments->pluck('id'))
            ->get()
            ->keyBy('student_enrollment_id');

        $payments = TuitionPayment::query()
            ->where('academic_year_id', $year->id)
            ->whereIn('student_enrollment_id', $enrollments->pluck('id'))
            ->whereIn('bulan', $months)
            ->get()
            ->keyBy(fn (TuitionPayment $payment) => $payment->student_enrollment_id.'|'.$payment->bulan);

        $defaultNominal = TuitionSetting::query()
            ->where('academic_year_id', $year->id)
            ->value('nominal');

        $rows = collect();

        foreach ($enrollments as $enrollment) {
            $nominal = $overrides->get($enrollment->id)?->nominal ?? $defaultNominal;

            foreach ($months as $month) {
                $payment = $payments->get($enrollment->id.'|'.$month);
                $paymentStatus = $payment?->isLunas() ? 'lunas' : 'belum_bayar';

                if ($status !== null && $status !== $paymentStatus) {
                    continue;
                }

                $rows->push([
                    'enrollment' => $enrollment,
                    'student' => $enrollment->student,
                    'bulan' => $month,
                    'bulan_label' => $this->bulanLabel($month),
                    'nominal' => $nominal !== null ? (int) $nominal : null,
                    'status' => $paymentStatus,
                    'payment' => $payment,
                    'external_reference' => $payment?->external_reference,
                    'tanggal_bayar' => $payment?->tanggal_bayar?->toDateString(),
                ]);
            }
        }

        return $rows;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function recordPayment(
        AcademicYear $year,
        StudentEnrollment $enrollment,
        array $data,
        int $nominal,
    ): TuitionPayment {
        $tanggalBayar = $data['tanggal_bayar'] ?? null;

        return TuitionPayment::updateOrCreate(
            [
                'student_enrollment_id' => $enrollment->id,
                'academic_year_id' => $year->id,
                'bulan' => $data['bulan'],
            ],
            [
                'semester' => $year->semester,
                'nominal' => $nominal,
                'status' => $tanggalBayar ? 'lunas' : 'belum_bayar',
                'tanggal_bayar' => $tanggalBayar,
                'metode' => $data['metode'] ?? null,
                'catatan' => $data['catatan'] ?? null,
                'external_reference' => $data['external_reference'] ?? null,
                'recorded_by' => auth()->id(),
            ],
        );
    }

    private function bulanLabel(int $bulan): string
    {
        return Carbon::create(null, $bulan, 1)->locale('id')->translatedFormat('F');
    }
}
