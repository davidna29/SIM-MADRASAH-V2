<?php

namespace Tests\Feature\Api;

use App\Models\Report;
use App\Models\Score;
use App\Models\ScoreComponent;
use Spatie\Activitylog\Models\Activity;

class ScoreApiTest extends ApiTestCase
{
    public function test_score_preview_does_not_persist_and_write_does(): void
    {
        $guru = $this->apiUser('guru');
        $enrollment = $this->makeStudent('251001', 'Aisyah');
        $subject = $this->makeSubject('MTK', 'Matematika');
        $assignment = $this->makeAssignment($guru, $subject);
        $componentA = ScoreComponent::create([
            'academic_year_id' => $this->year->id,
            'name' => 'Tugas',
            'weight' => 40,
            'sort_order' => 1,
        ]);
        $componentB = ScoreComponent::create([
            'academic_year_id' => $this->year->id,
            'name' => 'Ujian',
            'weight' => 60,
            'sort_order' => 2,
        ]);

        $token = $this->bearer($guru, ['website:read', 'akademik:write']);
        $headers = $this->idempotentHeaders('nilai-key-0001', $this->authHeaders($token));
        $payload = [
            'teacher_assignment_id' => $assignment->id,
            'values' => [
                $enrollment->id => [
                    $componentA->id => 80,
                    $componentB->id => 90,
                ],
            ],
            'dry_run' => true,
        ];

        $preview = $this->withHeaders($headers)->postJson(route('api.v1.nilai.store'), $payload);

        $preview->assertOk()
            ->assertJsonPath('data.dry_run', true)
            ->assertJsonPath('data.items.0.before', null)
            ->assertJsonPath('data.items.0.after', 86)
            ->assertJsonPath('data.items.0.status', 'changed');

        $this->assertDatabaseCount('scores', 0);
        $this->assertSame(0, Activity::where('log_name', 'api')->count());

        $writeHeaders = $this->idempotentHeaders('nilai-key-0001-write', $this->authHeaders($token));
        $write = $this->withHeaders($writeHeaders)->postJson(route('api.v1.nilai.store'), [...$payload, 'dry_run' => false]);

        $write->assertCreated()
            ->assertJsonPath('data.created', 1);

        $this->assertDatabaseHas('scores', [
            'student_enrollment_id' => $enrollment->id,
            'subject_id' => $subject->id,
            'score' => 86,
        ]);
        $this->assertDatabaseHas('activity_log', ['log_name' => 'api', 'description' => 'api_nilai_disimpan']);
    }

    public function test_published_report_blocks_score_changes(): void
    {
        $guru = $this->apiUser('guru');
        $enrollment = $this->makeStudent('251002', 'Bilal');
        $subject = $this->makeSubject('IPA', 'Ilmu Pengetahuan Alam');
        $assignment = $this->makeAssignment($guru, $subject);
        ScoreComponent::create([
            'academic_year_id' => $this->year->id,
            'name' => 'Tugas',
            'weight' => 100,
            'sort_order' => 1,
        ]);
        $component = ScoreComponent::first();
        Report::create([
            'student_id' => $enrollment->student_id,
            'academic_year_id' => $this->year->id,
            'semester' => $this->year->semester,
            'status' => 'terbit',
            'version' => 1,
            'snapshot' => [],
        ]);

        $token = $this->bearer($guru, ['website:read', 'akademik:write']);

        $this->withHeaders($this->idempotentHeaders('nilai-key-0002', $this->authHeaders($token)))
            ->postJson(route('api.v1.nilai.store'), [
                'teacher_assignment_id' => $assignment->id,
                'values' => [$enrollment->id => [$component->id => 90]],
            ])
            ->assertStatus(409)
            ->assertJsonPath('code', 'RECORD_ALREADY_PUBLISHED');

        $this->assertSame(0, Score::count());
    }

    public function test_score_write_requires_owned_assignment_and_valid_range(): void
    {
        $guru = $this->apiUser('guru');
        $other = $this->apiUser('guru');
        $enrollment = $this->makeStudent('251003', 'Citra');
        $subject = $this->makeSubject('BIN', 'Bahasa Indonesia');
        $assignment = $this->makeAssignment($other, $subject);

        $token = $this->bearer($guru, ['website:read', 'akademik:write']);

        $this->withHeaders($this->idempotentHeaders('nilai-key-0003', $this->authHeaders($token)))
            ->postJson(route('api.v1.nilai.store'), [
                'teacher_assignment_id' => $assignment->id,
                'scores' => [$enrollment->id => 80],
            ])
            ->assertForbidden();

        $subject2 = $this->makeSubject('BIN2', 'Bahasa Indonesia Lanjutan');
        $own = $this->makeAssignment($guru, $subject2);
        $this->withHeaders($this->idempotentHeaders('nilai-key-0004', $this->authHeaders($token)))
            ->postJson(route('api.v1.nilai.store'), [
                'teacher_assignment_id' => $own->id,
                'scores' => [$enrollment->id => 101],
            ])
            ->assertStatus(422)
            ->assertJsonPath('code', 'VALIDATION_ERROR');
    }
}
