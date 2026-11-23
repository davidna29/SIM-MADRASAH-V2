<?php

namespace Tests\Feature\Api;

use App\Models\TuitionOverride;
use App\Models\TuitionPayment;
use App\Models\TuitionSetting;
use Spatie\Activitylog\Models\Activity;

class TuitionApiTest extends ApiTestCase
{
    public function test_spp_payment_is_computed_server_side_and_idempotent(): void
    {
        $bendahara = $this->apiUser('bendahara');
        $enrollment = $this->makeStudent('251001', 'Aisyah');
        TuitionSetting::create(['academic_year_id' => $this->year->id, 'nominal' => 150000]);
        TuitionOverride::create([
            'student_enrollment_id' => $enrollment->id,
            'academic_year_id' => $this->year->id,
            'nominal' => 75000,
            'keterangan' => 'Keringanan',
        ]);

        $token = $this->bearer($bendahara, ['website:read', 'keuangan:write']);
        $headers = $this->idempotentHeaders('spp-key-0001', $this->authHeaders($token));
        $payload = [
            'student_enrollment_id' => $enrollment->id,
            'bulan' => 7,
            'tanggal_bayar' => now()->toDateString(),
            'metode' => 'transfer',
            'external_reference' => 'TRX-0001',
        ];

        $first = $this->withHeaders($headers)->postJson(route('api.v1.spp.bayar'), $payload);

        $first->assertCreated()
            ->assertJsonPath('data.nominal', 75000)
            ->assertJsonPath('data.status', 'lunas')
            ->assertJsonPath('data.external_reference', 'TRX-0001');

        $this->assertDatabaseCount('tuition_payments', 1);
        $this->assertDatabaseHas('activity_log', ['log_name' => 'api', 'description' => 'api_spp_dibayar']);

        $replay = $this->withHeaders($headers)->postJson(route('api.v1.spp.bayar'), $payload);

        $replay->assertOk()
            ->assertHeader('Idempotent-Replayed', 'true')
            ->assertJsonPath('data.payment_id', $first->json('data.payment_id'));

        $this->assertDatabaseCount('tuition_payments', 1);
        $this->assertDatabaseCount('api_idempotency_keys', 1);
        $this->assertSame(1, Activity::where('description', 'api_spp_dibayar')->count());
    }

    public function test_spp_rejects_missing_key_nominal_and_conflicting_replays(): void
    {
        $bendahara = $this->apiUser('bendahara');
        $enrollment = $this->makeStudent('251002', 'Bilal');
        TuitionSetting::create(['academic_year_id' => $this->year->id, 'nominal' => 150000]);

        $token = $this->bearer($bendahara, ['website:read', 'keuangan:write']);
        $base = [
            'student_enrollment_id' => $enrollment->id,
            'bulan' => 7,
            'tanggal_bayar' => now()->toDateString(),
            'external_reference' => 'TRX-0002',
        ];

        $this->withHeaders($this->authHeaders($token))
            ->postJson(route('api.v1.spp.bayar'), $base)
            ->assertStatus(422)
            ->assertJsonPath('code', 'IDEMPOTENCY_KEY_REQUIRED');

        $this->withHeaders($this->idempotentHeaders('spp-key-0002', $this->authHeaders($token)))
            ->postJson(route('api.v1.spp.bayar'), [...$base, 'nominal' => 1])
            ->assertStatus(422)
            ->assertJsonPath('code', 'VALIDATION_ERROR');

        $key = 'spp-key-0003';
        $this->withHeaders($this->idempotentHeaders($key, $this->authHeaders($token)))
            ->postJson(route('api.v1.spp.bayar'), $base)
            ->assertCreated();

        $this->withHeaders($this->idempotentHeaders($key, $this->authHeaders($token)))
            ->postJson(route('api.v1.spp.bayar'), [...$base, 'bulan' => 8])
            ->assertStatus(409)
            ->assertJsonPath('code', 'IDEMPOTENCY_KEY_REUSED');

        $this->withHeaders($this->idempotentHeaders('spp-key-0004', $this->authHeaders($token)))
            ->postJson(route('api.v1.spp.bayar'), [...$base, 'external_reference' => 'TRX-OTHER'])
            ->assertStatus(409)
            ->assertJsonPath('code', 'ALREADY_PAID');
    }

    public function test_spp_requires_configured_nominal_and_unique_external_reference(): void
    {
        $bendahara = $this->apiUser('bendahara');
        $first = $this->makeStudent('251003', 'Citra');
        $second = $this->makeStudent('251004', 'Daud');

        $token = $this->bearer($bendahara, ['website:read', 'keuangan:write']);

        $this->withHeaders($this->idempotentHeaders('spp-key-0005', $this->authHeaders($token)))
            ->postJson(route('api.v1.spp.bayar'), [
                'student_enrollment_id' => $first->id,
                'bulan' => 7,
                'tanggal_bayar' => now()->toDateString(),
                'external_reference' => 'TRX-NOSET',
            ])
            ->assertStatus(422)
            ->assertJsonPath('code', 'SPP_NOMINAL_NOT_SET');

        TuitionSetting::create(['academic_year_id' => $this->year->id, 'nominal' => 100000]);

        $this->withHeaders($this->idempotentHeaders('spp-key-0006', $this->authHeaders($token)))
            ->postJson(route('api.v1.spp.bayar'), [
                'student_enrollment_id' => $first->id,
                'bulan' => 7,
                'tanggal_bayar' => now()->toDateString(),
                'external_reference' => 'TRX-SAME',
            ])
            ->assertCreated();

        $this->withHeaders($this->idempotentHeaders('spp-key-0007', $this->authHeaders($token)))
            ->postJson(route('api.v1.spp.bayar'), [
                'student_enrollment_id' => $second->id,
                'bulan' => 7,
                'tanggal_bayar' => now()->toDateString(),
                'external_reference' => 'TRX-SAME',
            ])
            ->assertStatus(409)
            ->assertJsonPath('code', 'EXTERNAL_REFERENCE_DUPLICATE');
    }

    public function test_spp_requires_keuangan_scope(): void
    {
        $bendahara = $this->apiUser('bendahara');
        $enrollment = $this->makeStudent('251005', 'Eka');
        $token = $this->bearer($bendahara, ['website:read']);

        $this->withHeaders($this->idempotentHeaders('spp-key-0008', $this->authHeaders($token)))
            ->postJson(route('api.v1.spp.bayar'), [
                'student_enrollment_id' => $enrollment->id,
                'bulan' => 7,
                'external_reference' => 'TRX-SCOPE',
            ])
            ->assertForbidden()
            ->assertJsonPath('code', 'SCOPE_MISSING');

        $this->assertSame(0, TuitionPayment::count());
    }
}
