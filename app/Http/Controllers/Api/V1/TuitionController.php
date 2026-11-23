<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\ApiException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\PayTuitionRequest;
use App\Models\StudentEnrollment;
use App\Models\TuitionPayment;
use App\Services\Keuangan\TuitionService;
use App\Support\Api\ApiAcademicYear;
use App\Support\Api\ApiAudit;
use App\Support\Api\ApiErrorCode;
use App\Support\Api\ApiLookup;
use App\Support\Api\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class TuitionController extends Controller
{
    public function __construct(private readonly TuitionService $tuition) {}

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', TuitionPayment::class);

        $validated = $request->validate([
            'status' => ['nullable', 'in:lunas,belum_bayar'],
            'kelas' => ['nullable', 'string', 'max:50'],
            'bulan' => ['nullable', 'integer', 'between:1,12'],
        ]);

        $year = ApiAcademicYear::active();
        $classGroup = isset($validated['kelas']) ? ApiLookup::classGroup($validated['kelas']) : null;
        $ledger = $this->tuition->ledger(
            $year,
            $classGroup?->id,
            $validated['bulan'] ?? null,
            $validated['status'] ?? null,
        );

        $page = max((int) $request->integer('page', 1), 1);
        $perPage = min(max((int) $request->integer('per_page', 20), 1), 100);
        $paginator = new LengthAwarePaginator(
            $ledger->forPage($page, $perPage)->values(),
            $ledger->count(),
            $perPage,
            $page,
            ['path' => $request->url()],
        );

        return ApiResponse::paginated(
            $paginator,
            $paginator->getCollection()->map(fn ($row) => [
                'enrollment_id' => $row['enrollment']->id,
                'student' => [
                    'id' => $row['student']?->id,
                    'nis' => $row['student']?->nis,
                    'name' => $row['student']?->displayName(),
                ],
                'class_group' => [
                    'id' => $row['enrollment']->classGroup?->id,
                    'name' => $row['enrollment']->classGroup?->name,
                ],
                'bulan' => $row['bulan'],
                'bulan_label' => $row['bulan_label'],
                'nominal' => $row['nominal'],
                'status' => $row['status'],
                'payment_id' => $row['payment']?->id,
                'external_reference' => $row['external_reference'],
                'tanggal_bayar' => $row['tanggal_bayar'],
            ])->all(),
            [
                'filters' => [
                    'status' => $validated['status'] ?? null,
                    'kelas' => $classGroup?->name,
                    'bulan' => $validated['bulan'] ?? null,
                ],
                'academic_year_id' => $year->id,
                'semester' => $year->semester,
            ],
            ['Tagihan dibentuk dari nominal SPP dan keringanan siswa; belum tentu sudah memiliki baris pembayaran.'],
        );
    }

    public function pay(PayTuitionRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $year = ApiAcademicYear::active();
        $enrollment = StudentEnrollment::with(['student', 'classGroup'])->findOrFail($validated['student_enrollment_id']);

        if ($enrollment->academic_year_id !== $year->id) {
            throw ApiException::business('Enrollment bukan untuk tahun ajaran aktif.');
        }

        $existing = TuitionPayment::query()
            ->where('student_enrollment_id', $enrollment->id)
            ->where('academic_year_id', $year->id)
            ->where('bulan', $validated['bulan'])
            ->first();

        if ($existing?->isLunas()) {
            if ($existing->external_reference === $validated['external_reference']) {
                return ApiResponse::success(
                    $this->paymentPayload($existing, $enrollment),
                    warnings: ['Pembayaran sudah tercatat dengan referensi yang sama.'],
                );
            }

            throw ApiException::conflict(
                'SPP bulan ini sudah lunas dengan referensi lain.',
                ApiErrorCode::ALREADY_PAID,
            );
        }

        $duplicateReference = TuitionPayment::query()
            ->where('external_reference', $validated['external_reference'])
            ->where(function ($query) use ($enrollment, $year) {
                $query->where('student_enrollment_id', '!=', $enrollment->id)
                    ->orWhere('academic_year_id', '!=', $year->id);
            })
            ->exists();

        if ($duplicateReference) {
            throw ApiException::conflict(
                'External reference sudah dipakai pembayaran lain.',
                ApiErrorCode::EXTERNAL_REFERENCE_DUPLICATE,
            );
        }

        $nominal = $this->tuition->resolveNominal($year, $enrollment);

        if ($nominal === null) {
            throw ApiException::business(
                'Nominal SPP belum diatur untuk tahun ajaran ini.',
                ApiErrorCode::SPP_NOMINAL_NOT_SET,
            );
        }

        $payment = $this->tuition->recordPayment($year, $enrollment, $validated, $nominal);

        ApiAudit::log($request, 'api_spp_dibayar', $payment, [
            'external_reference' => $validated['external_reference'],
            'nominal' => $nominal,
            'bulan' => $validated['bulan'],
            'created' => $payment->wasRecentlyCreated,
        ]);

        return ApiResponse::success(
            $this->paymentPayload($payment, $enrollment),
            warnings: ['Nominal dihitung server-side dari keringanan atau nominal SPP tahun ajaran.'],
            status: $payment->wasRecentlyCreated ? 201 : 200,
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function paymentPayload(TuitionPayment $payment, StudentEnrollment $enrollment): array
    {
        return [
            'payment_id' => $payment->id,
            'external_reference' => $payment->external_reference,
            'status' => $payment->status,
            'nominal' => $payment->nominal,
            'bulan' => $payment->bulan,
            'tanggal_bayar' => $payment->tanggal_bayar?->toDateString(),
            'student' => [
                'id' => $enrollment->student?->id,
                'nis' => $enrollment->student?->nis,
                'name' => $enrollment->student?->displayName(),
            ],
            'class_group' => [
                'id' => $enrollment->classGroup?->id,
                'name' => $enrollment->classGroup?->name,
            ],
        ];
    }
}
