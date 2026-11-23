<?php

namespace Tests\Feature\Api;

use App\Models\Agenda;
use App\Models\Article;
use App\Models\Attendance;
use App\Models\AttendanceReview;
use App\Models\Employee;
use App\Models\MediaAlbum;
use App\Models\OrganizationalUnit;
use App\Models\Person;
use App\Models\Position;
use App\Models\ScheduleCell;
use App\Models\ScheduleModel;
use App\Models\ScheduleSlot;
use App\Models\Score;
use App\Models\TuitionSetting;

class ReadTest extends ApiTestCase
{
    public function test_student_directory_and_detail_protect_sensitive_fields(): void
    {
        $enrollment = $this->makeStudent('251001', 'Aisyah');
        $nik = $enrollment->student->person->nik;
        $admin = $this->apiUser('super_admin');
        $readToken = $this->bearer($admin, ['website:read']);

        $list = $this->withHeaders($this->authHeaders($readToken))
            ->getJson(route('api.v1.siswa.index'));

        $list->assertOk()
            ->assertJsonPath('data.0.nis', '251001')
            ->assertJsonPath('data.0.name', 'Aisyah');
        $this->assertStringNotContainsString($nik, $list->getContent());
        $this->assertStringNotContainsString('Jalan Rahasia', $list->getContent());
        $this->assertStringNotContainsString('081200000000', $list->getContent());

        $limited = $this->withHeaders($this->authHeaders($readToken))
            ->getJson(route('api.v1.siswa.show', $enrollment->student_id));

        $limited->assertOk();
        $this->assertStringNotContainsString($nik, $limited->getContent());

        $fullToken = $this->bearer($admin, ['website:read', 'akademik:write']);
        $full = $this->withHeaders($this->authHeaders($fullToken))
            ->getJson(route('api.v1.siswa.show', $enrollment->student_id));

        $full->assertOk()
            ->assertJsonPath('data.person.nik', $nik)
            ->assertJsonPath('data.person.address', 'Jalan Rahasia');
    }

    public function test_content_lists_only_show_public_records_by_default(): void
    {
        $author = $this->apiUser('editor_berita');

        Article::create([
            'title' => 'Berita Publik',
            'slug' => 'berita-publik',
            'body' => 'Isi publik.',
            'status' => Article::PUBLISH,
            'published_at' => now()->subDay(),
            'author_id' => $author->id,
        ]);
        Article::create([
            'title' => 'Berita Draft',
            'slug' => 'berita-draft',
            'body' => 'Isi draft.',
            'status' => Article::DRAFT,
            'author_id' => $author->id,
        ]);

        Agenda::create([
            'title' => 'Agenda Publik',
            'jenis' => 'agenda',
            'target' => 'publik',
            'tampil_mulai' => now()->subDay()->toDateString(),
            'status' => 'aktif',
            'created_by' => $author->id,
        ]);
        Agenda::create([
            'title' => 'Agenda Arsip',
            'jenis' => 'pengumuman',
            'target' => 'internal',
            'tampil_mulai' => now()->subDay()->toDateString(),
            'status' => 'arsip',
            'created_by' => $author->id,
        ]);

        MediaAlbum::create(['title' => 'Album Publik', 'slug' => 'album-publik', 'status' => 'publik']);
        MediaAlbum::create(['title' => 'Album Privat', 'slug' => 'album-privat', 'status' => 'privat']);

        $readToken = $this->bearer($author, ['website:read']);

        $this->withHeaders($this->authHeaders($readToken))
            ->getJson(route('api.v1.berita.index'))
            ->assertOk()
            ->assertJsonPath('data.0.title', 'Berita Publik')
            ->assertJsonMissing(['title' => 'Berita Draft']);

        $this->withHeaders($this->authHeaders($readToken))
            ->getJson(route('api.v1.berita.index', ['status' => 'draft']))
            ->assertForbidden()
            ->assertJsonPath('code', 'SCOPE_MISSING');

        $this->withHeaders($this->authHeaders($readToken))
            ->getJson(route('api.v1.agenda.index'))
            ->assertOk()
            ->assertJsonPath('data.0.title', 'Agenda Publik')
            ->assertJsonMissing(['title' => 'Agenda Arsip']);

        $this->withHeaders($this->authHeaders($readToken))
            ->getJson(route('api.v1.galeri.index'))
            ->assertOk()
            ->assertJsonPath('data.0.title', 'Album Publik')
            ->assertJsonMissing(['title' => 'Album Privat']);

        $cmsToken = $this->bearer($author, ['website:read', 'cms:write']);
        $this->withHeaders($this->authHeaders($cmsToken))
            ->getJson(route('api.v1.berita.index', ['status' => 'draft']))
            ->assertOk()
            ->assertJsonPath('data.0.title', 'Berita Draft');
    }

    public function test_dashboard_employee_and_tuition_ledger(): void
    {
        $admin = $this->apiUser('super_admin');
        $enrollment = $this->makeStudent('251002', 'Bilal');
        TuitionSetting::create(['academic_year_id' => $this->year->id, 'nominal' => 150000]);

        $person = Person::create([
            'nik' => '3500000000000099',
            'name' => 'Ustadz Test',
            'gender' => 'L',
            'religion' => 'Islam',
            'phone' => '081299999999',
        ]);
        $unit = OrganizationalUnit::create(['code' => 'GURU', 'name' => 'Guru']);
        $position = Position::create(['code' => 'GURU_MAPEL', 'name' => 'Guru Mata Pelajaran']);
        Employee::create([
            'person_id' => $person->id,
            'organizational_unit_id' => $unit->id,
            'position_id' => $position->id,
            'nip' => '199001012019031001',
            'employee_status' => 'pns',
            'status' => 'aktif',
        ]);

        $token = $this->bearer($admin, ['website:read']);

        $this->withHeaders($this->authHeaders($token))
            ->getJson(route('api.v1.dashboard.ringkasan'))
            ->assertOk()
            ->assertJsonPath('data.kpis.siswa_aktif', 1);

        $employees = $this->withHeaders($this->authHeaders($token))
            ->getJson(route('api.v1.pegawai.index'));

        $employees->assertOk()->assertJsonPath('data.0.name', 'Ustadz Test');
        $this->assertStringNotContainsString('3500000000000099', $employees->getContent());
        $this->assertStringNotContainsString('081299999999', $employees->getContent());

        $this->withHeaders($this->authHeaders($token))
            ->getJson(route('api.v1.spp.tagihan', ['status' => 'belum_bayar']))
            ->assertOk()
            ->assertJsonPath('data.0.nominal', 150000)
            ->assertJsonPath('data.0.status', 'belum_bayar')
            ->assertJsonPath('data.0.enrollment_id', $enrollment->id);
    }

    public function test_schedule_uses_active_model_and_slots(): void
    {
        $admin = $this->apiUser('super_admin');
        $subject = $this->makeSubject('MTK', 'Matematika');
        $model = ScheduleModel::create([
            'academic_year_id' => $this->year->id,
            'name' => 'Jadwal Utama',
            'start_time' => '07:00',
            'max_hours_per_day' => 8,
            'is_active' => true,
            'created_by' => $admin->id,
        ]);
        ScheduleSlot::create([
            'schedule_model_id' => $model->id,
            'period_no' => 1,
            'start_time' => '07:00',
            'end_time' => '07:40',
        ]);
        ScheduleCell::create([
            'schedule_model_id' => $model->id,
            'academic_year_id' => $this->year->id,
            'class_group_id' => $this->classGroup->id,
            'day' => 'senin',
            'period_no' => 1,
            'teacher_id' => $admin->id,
            'subject_id' => $subject->id,
        ]);

        $token = $this->bearer($admin, ['website:read']);

        $this->withHeaders($this->authHeaders($token))
            ->getJson(route('api.v1.jadwal.index', ['kelas' => $this->classGroup->id, 'hari' => 'senin']))
            ->assertOk()
            ->assertJsonPath('data.0.subject.name', 'Matematika')
            ->assertJsonPath('data.0.time.start', '07:00');
    }

    public function test_attendance_recap_and_scores_are_readable(): void
    {
        $admin = $this->apiUser('super_admin');
        $enrollment = $this->makeStudent('251003', 'Citra');
        $subject = $this->makeSubject('IPA', 'Ilmu Pengetahuan Alam');

        Attendance::create([
            'academic_year_id' => $this->year->id,
            'class_group_id' => $this->classGroup->id,
            'student_enrollment_id' => $enrollment->id,
            'attendance_date' => '2026-07-01',
            'status' => 'hadir',
        ]);
        AttendanceReview::create([
            'class_group_id' => $this->classGroup->id,
            'academic_year_id' => $this->year->id,
            'attendance_date' => '2026-07-01',
            'reviewed_by' => $admin->id,
            'reviewed_at' => now(),
        ]);
        Score::create([
            'student_enrollment_id' => $enrollment->id,
            'subject_id' => $subject->id,
            'academic_year_id' => $this->year->id,
            'semester' => $this->year->semester,
            'score' => 88,
        ]);

        $token = $this->bearer($admin, ['website:read']);

        $this->withHeaders($this->authHeaders($token))
            ->getJson(route('api.v1.kehadiran.index', ['kelas' => $this->classGroup->id, 'bulan' => '2026-07']))
            ->assertOk()
            ->assertJsonPath('data.rows.0.tally.H', 1);

        $this->withHeaders($this->authHeaders($token))
            ->getJson(route('api.v1.nilai.index', ['kelas' => $this->classGroup->id, 'mapel' => 'IPA']))
            ->assertOk()
            ->assertJsonPath('data.0.score', 88)
            ->assertJsonPath('data.0.subject.code', 'IPA');
    }
}
