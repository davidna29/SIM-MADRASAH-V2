<?php

namespace Tests\Feature\Api;

use App\Models\Attendance;
use App\Models\ClassGroup;
use Carbon\Carbon;

class AttendanceApiTest extends ApiTestCase
{
    public function test_teacher_can_save_today_attendance_and_replay_idempotently(): void
    {
        $guru = $this->apiUser('guru');
        $subject = $this->makeSubject('MTK', 'Matematika');
        $this->makeAssignment($guru, $subject);
        $enrollment = $this->makeStudent('251001', 'Aisyah');

        $token = $this->bearer($guru, ['website:read', 'akademik:write']);
        $headers = $this->idempotentHeaders('attendance-key-0001', $this->authHeaders($token));
        $payload = [
            'attendance_date' => now()->toDateString(),
            'class_group_id' => $this->classGroup->id,
            'attendances' => [
                $enrollment->id => ['status' => 'sakit', 'note' => 'Demam'],
            ],
        ];

        $this->withHeaders($headers)->postJson(route('api.v1.kehadiran.store'), $payload)
            ->assertCreated()
            ->assertJsonPath('data.created', 1)
            ->assertJsonPath('data.reviewed', true);

        $this->assertDatabaseHas('attendances', [
            'student_enrollment_id' => $enrollment->id,
            'status' => 'sakit',
        ]);
        $this->assertDatabaseHas('attendance_reviews', [
            'class_group_id' => $this->classGroup->id,
            'attendance_date' => now()->toDateString(),
        ]);
        $this->assertDatabaseHas('activity_log', ['log_name' => 'api', 'description' => 'api_kehadiran_disimpan']);

        $this->withHeaders($headers)->postJson(route('api.v1.kehadiran.store'), $payload)
            ->assertOk()
            ->assertHeader('Idempotent-Replayed', 'true');

        $this->assertSame(1, Attendance::count());
    }

    public function test_locked_date_and_foreign_enrollment_are_rejected(): void
    {
        $guru = $this->apiUser('guru');
        $subject = $this->makeSubject('IPA', 'Ilmu Pengetahuan Alam');
        $this->makeAssignment($guru, $subject);
        $enrollment = $this->makeStudent('251002', 'Bilal');

        $otherClass = ClassGroup::create(['name' => 'II-A', 'grade_level' => 'II']);
        $foreign = $this->makeStudent('251003', 'Citra', $otherClass);

        $token = $this->bearer($guru, ['website:read', 'akademik:write']);

        $this->withHeaders($this->idempotentHeaders('attendance-key-0002', $this->authHeaders($token)))
            ->postJson(route('api.v1.kehadiran.store'), [
                'attendance_date' => now()->subDay()->toDateString(),
                'class_group_id' => $this->classGroup->id,
                'attendances' => [$enrollment->id => ['status' => 'hadir']],
            ])
            ->assertStatus(422)
            ->assertJsonPath('code', 'ATTENDANCE_DATE_LOCKED');

        $this->withHeaders($this->idempotentHeaders('attendance-key-0003', $this->authHeaders($token)))
            ->postJson(route('api.v1.kehadiran.store'), [
                'attendance_date' => now()->toDateString(),
                'class_group_id' => $this->classGroup->id,
                'attendances' => [$foreign->id => ['status' => 'hadir']],
            ])
            ->assertStatus(422)
            ->assertJsonPath('code', 'VALIDATION_ERROR');

        $this->assertSame(0, Attendance::count());
    }

    public function test_privileged_role_can_write_past_date_and_non_assigned_teacher_cannot_write(): void
    {
        $admin = $this->apiUser('super_admin');
        $enrollment = $this->makeStudent('251004', 'Daud');
        $date = Carbon::now()->subDays(2)->toDateString();

        $adminToken = $this->bearer($admin, ['website:read', 'akademik:write']);

        $this->withHeaders($this->idempotentHeaders('attendance-key-0004', $this->authHeaders($adminToken)))
            ->postJson(route('api.v1.kehadiran.store'), [
                'attendance_date' => $date,
                'class_group_id' => $this->classGroup->id,
                'attendances' => [$enrollment->id => ['status' => 'hadir']],
            ])
            ->assertCreated();

        $guru = $this->apiUser('guru');
        $guruToken = $this->bearer($guru, ['website:read', 'akademik:write']);

        $this->withHeaders($this->idempotentHeaders('attendance-key-0005', $this->authHeaders($guruToken)))
            ->postJson(route('api.v1.kehadiran.store'), [
                'attendance_date' => now()->toDateString(),
                'class_group_id' => $this->classGroup->id,
                'attendances' => [$enrollment->id => ['status' => 'hadir']],
            ])
            ->assertForbidden();
    }
}
