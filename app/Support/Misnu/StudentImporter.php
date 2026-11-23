<?php

namespace App\Support\Misnu;

use App\Models\ClassGroup;
use App\Models\Guardian;
use App\Models\Person;
use App\Models\Student;
use App\Models\StudentEnrollment;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

/**
 * Mengubah baris workbook EMIS menjadi Person + Student + Enrollment + Guardian.
 *
 * Aturan utama: idempotent (kunci NIS/NIK), data kotor dicatat di laporan
 * (bukan ditebak diam-diam), dan tidak membuat akun login.
 */
final class StudentImporter
{
    private int $syntheticNikCounter = 900000000001;

    /** @var array<string, int> */
    private array $guardianCache = [];

    /** @var list<array{row: int|string, rombel: string, nama: string, nis: string, level: string, pesan: string}> */
    private array $issues = [];

    private int $created = 0;

    private int $updated = 0;

    private int $skipped = 0;

    private int $guardiansCreated = 0;

    private int $guardiansAttached = 0;

    public function __construct(private readonly int $academicYearId) {}

    /**
     * @param  list<array<string, mixed>>  $rows
     * @param  list<string>  $rombelFilter  kode rombel ternormalisasi (mis. VI-A); kosong = semua
     * @return array{summary: array<string, mixed>, issues: list<array<string, mixed>>, per_class: array<string, int>, report: ?string}
     */
    public function import(array $rows, array $rombelFilter, bool $dryRun = false, ?string $reportPath = null, ?int $limit = null): array
    {
        $this->issues = [];
        $this->guardianCache = [];
        $this->created = $this->updated = $this->skipped = 0;
        $this->guardiansCreated = $this->guardiansAttached = 0;

        $perClass = [];
        $processed = 0;

        $callback = function () use ($rows, $rombelFilter, $dryRun, $limit, &$perClass, &$processed) {
            foreach ($rows as $row) {
                if ($limit !== null && $processed >= $limit) {
                    break;
                }

                // Saring rombel sebelum normalisasi agar laporan hanya memuat
                // baris yang memang diproses.
                $preRombel = $this->classCode($row['grade'] ?? null, $row['rombel'] ?? null);

                if ($preRombel !== null && $rombelFilter !== [] && ! in_array($preRombel, $rombelFilter, true)) {
                    $this->skipped++;

                    continue;
                }

                $normalized = $this->normalizeRow($row);

                if ($normalized === null) {
                    $this->skipped++;

                    continue;
                }

                if ($rombelFilter !== [] && ! in_array($normalized['rombel'], $rombelFilter, true)) {
                    $this->skipped++;

                    continue;
                }

                $processed++;
                $perClass[$normalized['rombel']] = ($perClass[$normalized['rombel']] ?? 0) + 1;

                $this->persist($normalized, $dryRun);
            }
        };

        if ($dryRun) {
            $callback();
        } else {
            DB::transaction($callback);
        }

        $report = $reportPath ? $this->writeReport($reportPath) : null;

        return [
            'summary' => [
                'total_baris' => count($rows),
                'diproses' => $processed,
                'dibuat' => $this->created,
                'diperbarui' => $this->updated,
                'dilewati' => $this->skipped,
                'wali_dibuat' => $this->guardiansCreated,
                'wali_ditautkan' => $this->guardiansAttached,
                'masalah' => count($this->issues),
                'dry_run' => $dryRun,
            ],
            'issues' => $this->issues,
            'per_class' => $perClass,
            'report' => $report,
        ];
    }

    /**
     * @param  array<string, mixed>  $row
     * @return array<string, mixed>|null
     */
    private function normalizeRow(array $row): ?array
    {
        $rowNumber = $row['_row'] ?? '?';
        $name = $this->text($row['name'] ?? null);

        if ($name === '') {
            return null;
        }

        $rombel = $this->classCode($row['grade'] ?? null, $row['rombel'] ?? null);

        if ($rombel === null) {
            $this->issue($rowNumber, (string) ($row['rombel'] ?? '-'), $name, '', 'error', 'Kelas/rombel tidak dapat dibaca dari kolom KELAS + ROMBEL.');

            return null;
        }

        [$birthDate, $birthRaw] = $this->date($row['birth_date'] ?? null, $rowNumber, $name, 'tanggal lahir');
        [$entryDate] = $this->date($row['entry_date'] ?? null, $rowNumber, $name, 'tanggal masuk');

        $nik = $this->digits($row['nik'] ?? null, 16);
        $nikSynthetic = false;

        if ($nik === null) {
            $nik = $this->syntheticNik();
            $nikSynthetic = true;
            $this->issue($rowNumber, $rombel, $name, '', 'warning', 'NIK kosong/tidak valid — memakai NIK sintetis.');
        }

        $nisLocal = $this->digits($row['nis_local'] ?? null, null, 20);
        $nisEmis = $this->digits($row['nis_emis'] ?? null);

        if ($nisLocal === null && $nisEmis !== null) {
            $nisLocal = substr($nisEmis, -6);
        }

        $nisn = $this->digits($row['nisn'] ?? null);

        if ($nisn !== null && strlen($nisn) === 9) {
            // Excel menghapus nol depan pada NISN 10 digit.
            $nisn = '0'.$nisn;
            $this->issue($rowNumber, $rombel, $name, (string) $nisLocal, 'warning', 'NISN 9 digit dipulihkan dengan nol depan.');
        } elseif ($nisn !== null && strlen($nisn) !== 10) {
            $this->issue($rowNumber, $rombel, $name, (string) $nisLocal, 'warning', "NISN '{$row['nisn']}' bukan 10 digit — dikosongkan.");
            $nisn = null;
        }

        if ($nisLocal === null) {
            $this->issue($rowNumber, $rombel, $name, '', 'warning', 'NIS lokal kosong — lengkapi manual di Data Siswa.');
        }

        $gender = strtoupper($this->text($row['gender'] ?? null));

        if (! in_array($gender, ['L', 'P'], true)) {
            $gender = 'L';
            $this->issue($rowNumber, $rombel, $name, (string) $nisLocal, 'warning', 'Jenis kelamin kosong/tidak valid — diisi L.');
        }

        $documents = array_filter([
            'sumber' => 'EMIS MIS NU 2026/2027',
            'baris_workbook' => $rowNumber,
            'emis_nis' => $nisEmis,
            'nis_lokal' => $nisLocal,
            'asal_rombel' => $this->text($row['origin_rombel'] ?? null) ?: null,
            'no_absen' => $this->digits($row['attendance_no'] ?? null),
            'scan_kk' => $this->url($row['scan_kk'] ?? null),
            'scan_kk_wali' => $this->url($row['scan_kk_wali'] ?? null),
            'scan_akta' => $this->url($row['scan_akta'] ?? null),
            'scan_ijazah' => $this->url($row['scan_ijazah'] ?? null),
            'scan_foto' => $this->url($row['scan_foto'] ?? null),
        ], fn ($value) => $value !== null && $value !== '');

        if ($nikSynthetic) {
            $documents['nik_sintetis'] = true;
        }

        if ($birthRaw !== null) {
            $documents['tanggal_lahir_sumber'] = $birthRaw;
        }

        return [
            'row' => $rowNumber,
            'rombel' => $rombel,
            'name' => $this->fit($name, 100),
            'nik' => $nik,
            'nis' => $nisLocal,
            'nis_emis' => $nisEmis,
            'nisn' => $nisn,
            'gender' => $gender,
            'religion' => $this->text($row['religion'] ?? null) ?: 'Islam',
            'birth_place' => $this->fit($this->text($row['birth_place'] ?? null) ?: null, 60),
            'birth_date' => $birthDate,
            'phone' => $this->phone($row['phone'] ?? null),
            'home_phone' => $this->phone($row['home_phone'] ?? null),
            'email' => $this->email($row['email'] ?? null),
            'address' => $this->fit($this->text($row['address'] ?? null) ?: null, 255),
            'province' => $this->fit($this->text($row['province'] ?? null) ?: null, 60),
            'city' => $this->fit($this->text($row['city'] ?? null) ?: null, 60),
            'district' => $this->fit($this->text($row['district'] ?? null) ?: null, 60),
            'village' => $this->fit($this->text($row['village'] ?? null) ?: null, 60),
            'rt' => $this->digits($row['rt'] ?? null, null, 3),
            'rw' => $this->digits($row['rw'] ?? null, null, 3),
            'postal_code' => $this->digits($row['postal_code'] ?? null, 5),
            'student' => [
                'entry_date' => $entryDate,
                'hobby' => $this->fit($this->text($row['hobby'] ?? null) ?: null, 60),
                'ambition' => $this->fit($this->text($row['ambition'] ?? null) ?: null, 60),
                'child_order' => $this->smallInt($row['child_order'] ?? null),
                'sibling_count' => $this->smallInt($row['sibling_count'] ?? null),
                'ever_tk' => $this->fit($this->text($row['ever_tk'] ?? null) ?: null, 30),
                'ever_paud' => $this->fit($this->text($row['ever_paud'] ?? null) ?: null, 30),
                'residence_type' => $this->fit($this->text($row['residence_type'] ?? null) ?: null, 60),
                'distance' => $this->fit($this->text($row['distance'] ?? null) ?: null, 20),
                'transport' => $this->fit($this->text($row['transport'] ?? null) ?: null, 60),
                'commute_time' => $this->fit($this->text($row['commute_time'] ?? null) ?: null, 30),
                'kk_number' => $this->digits($row['kk_number'] ?? null, 16),
                'kk_head_name' => $this->fit($this->text($row['kk_head_name'] ?? null) ?: null, 100),
                'social_kks' => $this->digits($row['social_kks'] ?? null, null, 30),
                'social_pkh' => $this->digits($row['social_pkh'] ?? null, null, 30),
                'social_kip' => $this->digits($row['social_kip'] ?? null, null, 30),
                'parent_ownership' => $this->fit($this->text($row['parent_ownership'] ?? null) ?: null, 40),
                'parent_address' => $this->fit($this->text($row['parent_address'] ?? null) ?: null, 255),
                'parent_rt' => $this->digits($row['parent_rt'] ?? null, null, 3),
                'parent_rw' => $this->digits($row['parent_rw'] ?? null, null, 3),
                'parent_postal_code' => $this->digits($row['parent_postal_code'] ?? null, 5),
                'imm_hepb' => $this->yes($row['imm_hepb'] ?? null),
                'imm_polio' => $this->yes($row['imm_polio'] ?? null),
                'imm_bcg' => $this->yes($row['imm_bcg'] ?? null),
                'imm_campak' => $this->yes($row['imm_campak'] ?? null),
                'imm_dpt' => $this->yes($row['imm_dpt'] ?? null),
                'imm_covid' => $this->yes($row['imm_covid'] ?? null),
                'dis_deaf' => $this->yes($row['dis_deaf'] ?? null),
                'dis_blind' => $this->yes($row['dis_blind'] ?? null),
                'dis_disabled' => $this->yes($row['dis_disabled'] ?? null),
                'dis_intellectual' => $this->yes($row['dis_intellectual'] ?? null),
                'dis_behavioral' => $this->yes($row['dis_behavioral'] ?? null),
                'dis_slow_learner' => $this->yes($row['dis_slow_learner'] ?? null),
                'dis_communication' => $this->yes($row['dis_communication'] ?? null),
                'dis_gifted' => $this->yes($row['dis_gifted'] ?? null),
                'origin_school' => $this->fit($this->text($row['origin_school'] ?? null) ?: null, 100),
                'origin_nsm' => $this->digits($row['origin_nsm'] ?? null, 12),
                'origin_npsn' => $this->digits($row['origin_npsn'] ?? null, 8),
                'origin_address' => $this->fit($this->text($row['origin_address'] ?? null) ?: null, 255),
                'previous_school' => $this->fit($this->text($row['origin_school'] ?? null) ?: null, 100),
                'documents' => $documents,
            ],
            'guardians' => $this->guardiansFromRow($row, $rowNumber, $rombel, $name, (string) $nisLocal),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function persist(array $data, bool $dryRun): void
    {
        $classGroup = ClassGroup::where('name', $data['rombel'])->first();

        if (! $classGroup) {
            $this->issue($data['row'], $data['rombel'], $data['name'], (string) $data['nis'], 'error', 'Rombel belum ada di master kelas — jalankan MisnuClassGroupSeeder.');

            return;
        }

        $student = $this->findExisting($data);

        if ($dryRun) {
            $student ? $this->updated++ : $this->created++;
            $this->guardiansAttached += count($data['guardians']);

            return;
        }

        $person = $student?->person;

        if ($person && $person->nik !== $data['nik']) {
            $nikTaken = Person::where('nik', $data['nik'])->whereKeyNot($person->id)->exists();

            if ($nikTaken) {
                $this->issue($data['row'], $data['rombel'], $data['name'], (string) $data['nis'], 'error', 'NIK sudah dipakai orang lain — NIK lama dipertahankan.');
                $data['nik'] = $person->nik;
            }
        }

        if (! $person) {
            $nikOwner = Person::where('nik', $data['nik'])->first();

            if ($nikOwner && ($nikOwner->students()->exists() || $nikOwner->employee()->exists())) {
                $this->issue($data['row'], $data['rombel'], $data['name'], (string) $data['nis'], 'warning', 'NIK sudah dipakai orang lain — memakai NIK sintetis.');
                $data['nik'] = $this->syntheticNik();
                $data['student']['documents']['nik_sintetis'] = true;
                $nikOwner = null;
            }

            $person = $nikOwner ?? Person::create([
                'nik' => $data['nik'],
                'name' => $data['name'],
                'gender' => $data['gender'],
                'religion' => $data['religion'],
            ]);
        }

        $person->fill([
            'name' => $data['name'],
            'gender' => $data['gender'],
            'religion' => $data['religion'],
            'birth_place' => $data['birth_place'],
            'birth_date' => $data['birth_date'],
            'phone' => $data['phone'],
            'email' => $data['email'],
            'address' => $data['address'],
            'province' => $data['province'],
            'city' => $data['city'],
            'district' => $data['district'],
            'village' => $data['village'],
            'rt' => $data['rt'],
            'rw' => $data['rw'],
            'postal_code' => $data['postal_code'],
            'home_phone' => $data['home_phone'],
        ])->save();

        $attributes = array_merge($data['student'], [
            'person_id' => $person->id,
            'nis' => $this->uniqueNis($data['nis'], $student?->id, $data),
            'name' => $data['name'],
            'gender' => $data['gender'],
            'nisn' => $data['nisn'],
        ]);

        if ($student) {
            $student->update($attributes);
            $this->updated++;
        } else {
            $student = Student::create($attributes);
            $this->created++;
        }

        $enrollment = StudentEnrollment::where('academic_year_id', $this->academicYearId)
            ->where('student_id', $student->id)
            ->first();

        if ($enrollment) {
            if ($enrollment->class_group_id !== $classGroup->id || $enrollment->status !== 'aktif') {
                $enrollment->update(['class_group_id' => $classGroup->id, 'status' => 'aktif']);
            }
        } else {
            StudentEnrollment::create([
                'academic_year_id' => $this->academicYearId,
                'class_group_id' => $classGroup->id,
                'student_id' => $student->id,
                'status' => 'aktif',
            ]);
        }

        foreach ($data['guardians'] as $relation => $guardianData) {
            $this->syncGuardian($student, $relation, $guardianData, $data);
        }
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function findExisting(array $data): ?Student
    {
        if ($data['nis']) {
            $student = Student::with('person')->where('nis', $data['nis'])->first();

            if ($student) {
                return $student;
            }
        }

        // NIS lokal bisa kosong; gunakan NIS EMIS yang tersimpan di documents
        // agar impor ulang tidak membuat duplikat.
        if ($data['nis_emis']) {
            $byEmis = Student::with('person')
                ->where('documents->emis_nis', $data['nis_emis'])
                ->first();

            if ($byEmis) {
                return $byEmis;
            }
        }

        // Fallback untuk baris tanpa NIS/NIS EMIS/tanggal lahir: nama + rombel
        // pada tahun ajaran yang sama. Mencegah duplikat saat impor ulang.
        if (! $data['nis']) {
            $byName = Student::with('person')
                ->whereRaw('LOWER(students.name) = ?', [mb_strtolower($data['name'])])
                ->whereHas('enrollments', fn ($query) => $query
                    ->where('academic_year_id', $this->academicYearId)
                    ->whereHas('classGroup', fn ($class) => $class->where('name', $data['rombel'])))
                ->first();

            if ($byName) {
                return $byName;
            }
        }

        // Jangan menautkan lewat NIK bila NIK itu sudah milik siswa lain:
        // workbook kadang memuat NIK ganda untuk dua siswa berbeda.
        $person = Person::where('nik', $data['nik'])->first();

        if (! $person) {
            return null;
        }

        $candidate = Student::with('person')->where('person_id', $person->id)->first();

        if ($candidate && (! $data['nis'] || $candidate->nis === $data['nis'])) {
            return $candidate;
        }

        return null;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function uniqueNis(?string $nis, ?int $ignoreStudentId, array $data): ?string
    {
        if ($nis === null) {
            return null;
        }

        $query = Student::where('nis', $nis);

        if ($ignoreStudentId) {
            $query->whereKeyNot($ignoreStudentId);
        }

        if (! $query->exists()) {
            return $nis;
        }

        $this->issue($data['row'], $data['rombel'], $data['name'], $nis, 'error', 'NIS sudah dipakai siswa lain — NIS dikosongkan, lengkapi manual.');

        return null;
    }

    /**
     * @param  array<string, mixed>  $guardianData
     * @param  array<string, mixed>  $studentData
     */
    private function syncGuardian(Student $student, string $relation, array $guardianData, array $studentData): void
    {
        $key = $guardianData['nik']
            ? 'nik:'.$guardianData['nik']
            : 'nama:'.mb_strtolower($guardianData['name']).'|'.($guardianData['birth_date'] ?? '');

        if (isset($this->guardianCache[$key])) {
            $guardian = Guardian::find($this->guardianCache[$key]);
        } elseif ($guardianData['nik']) {
            $guardian = Guardian::where('nik', $guardianData['nik'])->first();
        } else {
            $guardian = Guardian::whereNull('nik')
                ->whereRaw('LOWER(name) = ?', [mb_strtolower($guardianData['name'])])
                ->first();
        }

        if (! $guardian) {
            $guardian = Guardian::create([
                'name' => $guardianData['name'],
                'nik' => $guardianData['nik'],
                'status' => $guardianData['status'],
                'birth_place' => $guardianData['birth_place'],
                'birth_date' => $guardianData['birth_date'],
                'education' => $guardianData['education'],
                'job' => $guardianData['job'],
                'income' => $guardianData['income'],
                'phone' => $guardianData['phone'],
            ]);
            $this->guardiansCreated++;
        } else {
            $guardian->fill(array_filter([
                'status' => $guardianData['status'],
                'birth_place' => $guardianData['birth_place'],
                'birth_date' => $guardianData['birth_date'],
                'education' => $guardianData['education'],
                'job' => $guardianData['job'],
                'income' => $guardianData['income'],
                'phone' => $guardianData['phone'],
            ], fn ($value) => $value !== null && $value !== ''))->save();
        }

        $this->guardianCache[$key] = $guardian->id;

        $existing = $student->guardians()->where('guardians.id', $guardian->id)->first();

        if ($existing) {
            if ($existing->pivot->relation !== $relation) {
                $student->guardians()->updateExistingPivot($guardian->id, ['relation' => $relation]);
            }
        } else {
            $student->guardians()->attach($guardian->id, ['relation' => $relation]);
            $this->guardiansAttached++;
        }
    }

    /**
     * @param  array<string, mixed>  $row
     * @return array<string, array<string, mixed>>
     */
    private function guardiansFromRow(array $row, int|string $rowNumber, string $rombel, string $name, string $nis): array
    {
        $guardians = [];
        $definitions = [
            'ayah' => ['father_name', 'father_nik', 'father_status', 'father_birth_place', 'father_birth_date', 'father_education', 'father_job', 'father_income', 'father_phone'],
            'ibu' => ['mother_name', 'mother_nik', 'mother_status', 'mother_birth_place', 'mother_birth_date', 'mother_education', 'mother_job', 'mother_income', 'mother_phone'],
            'wali' => ['guardian_name', 'guardian_nik', null, 'guardian_birth_place', 'guardian_birth_year', 'guardian_education', 'guardian_job', 'guardian_income', 'guardian_phone'],
        ];

        foreach ($definitions as $relation => $fields) {
            [$nameField, $nikField, $statusField, $birthPlaceField, $birthField, $educationField, $jobField, $incomeField, $phoneField] = $fields;

            $guardianName = $this->text($row[$nameField] ?? null);

            if ($guardianName === '') {
                continue;
            }

            $birthDate = null;

            if ($relation === 'wali') {
                $year = $this->digits($row[$birthField] ?? null);

                if ($year !== null && strlen($year) === 4) {
                    $guardianBirthYear = (int) $year;

                    if ($guardianBirthYear >= 1940 && $guardianBirthYear <= (int) date('Y')) {
                        $birthDate = sprintf('%04d-01-01', $guardianBirthYear);
                    }
                }
            } else {
                [$birthDate] = $this->date($row[$birthField] ?? null, $rowNumber, $name, "tanggal lahir {$relation}", false);
            }

            $guardianNik = $this->digits($row[$nikField] ?? null, 16);

            if ($guardianNik === null && $this->text($row[$nikField] ?? null) !== '') {
                $this->issue($rowNumber, $rombel, $name, $nis, 'warning', "NIK {$relation} tidak valid — dikosongkan.");
            }

            $guardians[$relation] = [
                'name' => $this->fit($guardianName, 100),
                'nik' => $guardianNik,
                'status' => $statusField ? $this->fit($this->text($row[$statusField] ?? null) ?: null, 30) : null,
                'birth_place' => $this->fit($this->text($row[$birthPlaceField] ?? null) ?: null, 60),
                'birth_date' => $birthDate,
                'education' => $this->label($row[$educationField] ?? null),
                'job' => $this->label($row[$jobField] ?? null),
                'income' => $this->label($row[$incomeField] ?? null),
                'phone' => $this->phone($row[$phoneField] ?? null),
            ];
        }

        return $guardians;
    }

    /**
     * @return array{0: ?string, 1: ?string}
     */
    private function date(mixed $value, int|string $rowNumber, string $name, string $label, bool $report = true): array
    {
        if ($value === null || $value === '') {
            return [null, null];
        }

        $raw = is_scalar($value) ? trim((string) $value) : null;
        $date = null;

        if (is_numeric($value)) {
            $serial = (float) $value;

            if ($serial > 1000 && $serial < 80000) {
                try {
                    $date = ExcelDate::excelToDateTimeObject($serial);
                } catch (\Throwable) {
                    $date = null;
                }
            }
        } elseif (is_string($value)) {
            $text = preg_replace('/[^0-9\/\-\.]/', '', $value) ?? '';

            if (preg_match('/^(\d{4})[\/\-\.](\d{1,2})[\/\-\.](\d{1,2})$/', $text, $matches) === 1) {
                $year = (int) $matches[1];
                $month = (int) $matches[2];
                $day = (int) $matches[3];

                if (checkdate($month, $day, $year)) {
                    $date = new \DateTimeImmutable(sprintf('%04d-%02d-%02d', $year, $month, $day));
                }
            }

            if (! $date && $text !== '') {
                $text = str_replace(['.', '-'], '/', $text);
                $parts = array_values(array_filter(explode('/', $text), fn ($part) => $part !== ''));

                if (count($parts) === 3 && strlen($parts[2]) === 4) {
                    [$a, $b, $year] = array_map('intval', $parts);
                    // Format Indonesia (DD/MM/YYYY) adalah default; jika angka
                    // pertama > 12 atau kedua > 12, posisi ditukar.
                    $day = $a > 12 ? $a : ($b > 12 ? $b : $a);
                    $month = $a > 12 ? $b : ($b > 12 ? $a : $b);

                    if (checkdate($month, $day, $year)) {
                        $date = new \DateTimeImmutable(sprintf('%04d-%02d-%02d', $year, $month, $day));
                    }
                }
            }
        }

        if (! $date) {
            if ($report) {
                $this->issue($rowNumber, '', $name, '', 'warning', "{$label} tidak valid ('{$raw}') — dikosongkan.");
            }

            return [null, $raw];
        }

        $year = (int) $date->format('Y');

        if ($year < 1930 || $year > (int) date('Y')) {
            if ($report) {
                $this->issue($rowNumber, '', $name, '', 'warning', "{$label} di luar rentang wajar ('{$raw}') — dikosongkan.");
            }

            return [null, $raw];
        }

        return [$date->format('Y-m-d'), $raw];
    }

    private function syntheticNik(): string
    {
        do {
            $nik = '6201'.str_pad((string) $this->syntheticNikCounter++, 12, '0', STR_PAD_LEFT);
        } while (Person::where('nik', $nik)->exists());

        return $nik;
    }

    private function classCode(mixed $grade, mixed $rombel): ?string
    {
        $map = [1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI'];
        $gradeNumber = (int) preg_replace('/\D/', '', (string) $grade);

        if (! isset($map[$gradeNumber])) {
            return null;
        }

        $rombelText = strtoupper(trim((string) $rombel));

        if ($rombelText === '') {
            return $map[$gradeNumber].'-A';
        }

        $suffix = preg_replace('/[^A-Z]/', '', substr($rombelText, strlen((string) $gradeNumber)));

        if ($suffix === '') {
            $suffix = substr($rombelText, -1);
        }

        return $map[$gradeNumber].'-'.($suffix ?: 'A');
    }

    private function fit(?string $value, int $max): ?string
    {
        if ($value === null) {
            return null;
        }

        return mb_substr($value, 0, $max);
    }

    private function label(mixed $value, int $max = 30): ?string
    {
        $text = $this->text($value);

        if ($text === '') {
            return null;
        }

        // Buang penomoran pilihan EMIS, mis. "15.Buruh (Tani/Pabrik/Bangunan)".
        $text = preg_replace('/^\d+\.\s*/u', '', $text) ?? $text;

        return mb_substr(trim($text), 0, $max);
    }

    private function text(mixed $value): string
    {
        if (! is_scalar($value)) {
            return '';
        }

        $text = trim((string) $value);
        $text = preg_replace('/\s+/', ' ', $text) ?? $text;

        return $text;
    }

    private function digits(mixed $value, ?int $length = null, ?int $maxLength = null): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_float($value) || is_int($value)) {
            $value = sprintf('%.0f', $value);
        }

        $digits = preg_replace('/\D/', '', (string) $value) ?? '';

        if ($digits === '' || ($length !== null && strlen($digits) !== $length)) {
            return null;
        }

        if ($maxLength !== null && strlen($digits) > $maxLength) {
            return null;
        }

        return $digits;
    }

    private function smallInt(mixed $value): ?int
    {
        $digits = $this->digits($value);

        return $digits === null ? null : max(0, min(255, (int) $digits));
    }

    private function phone(mixed $value): ?string
    {
        $digits = $this->digits($value);

        if ($digits === null) {
            return null;
        }

        if (str_starts_with($digits, '62')) {
            $digits = '0'.substr($digits, 2);
        } elseif (str_starts_with($digits, '8')) {
            $digits = '0'.$digits;
        }

        return strlen($digits) <= 20 ? $digits : null;
    }

    private function email(mixed $value): ?string
    {
        $email = $this->text($value);

        return filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : null;
    }

    private function url(mixed $value): ?string
    {
        $text = $this->text($value);

        if ($text === '') {
            return null;
        }

        return preg_match('#^https?://#i', $text) ? $text : null;
    }

    private function yes(mixed $value): bool
    {
        $text = mb_strtolower($this->text($value));

        return in_array($text, ['pernah', 'sudah', 'ya', 'yes', '1', 'true', 'v'], true);
    }

    private function issue(int|string $row, string $rombel, string $name, string $nis, string $level, string $message): void
    {
        $this->issues[] = [
            'row' => $row,
            'rombel' => $rombel,
            'nama' => $name,
            'nis' => $nis,
            'level' => $level,
            'pesan' => $message,
        ];
    }

    private function writeReport(string $path): ?string
    {
        $directory = dirname($path);

        if (! is_dir($directory)) {
            mkdir($directory, 0775, true);
        }

        $handle = fopen($path, 'wb');

        if (! $handle) {
            return null;
        }

        fwrite($handle, "\xEF\xBB\xBF");
        fputcsv($handle, ['baris_workbook', 'rombel', 'nama', 'nis', 'level', 'pesan']);

        foreach ($this->issues as $issue) {
            fputcsv($handle, [$issue['row'], $issue['rombel'], $issue['nama'], $issue['nis'], $issue['level'], $issue['pesan']]);
        }

        fclose($handle);

        return $path;
    }
}
