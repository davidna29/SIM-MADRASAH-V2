<?php

namespace App\Exports;

use App\Models\AcademicYear;
use App\Models\Setting;
use App\Models\Student;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SiswaEmisExport implements FromCollection, WithHeadings, WithMapping
{
    /** @var array<string, int> */
    private const GRADE_NUMBER = ['I' => 1, 'II' => 2, 'III' => 3, 'IV' => 4, 'V' => 5, 'VI' => 6];

    public function __construct(
        private readonly ?int $academicYearId = null,
        private readonly ?int $classGroupId = null,
        private readonly ?string $search = null,
    ) {}

    public function collection(): Collection
    {
        $yearId = $this->academicYearId ?? AcademicYear::where('is_active', true)->value('id');

        return Student::query()
            ->with([
                'person',
                'guardians',
                'enrollments' => fn ($query) => $query->where('academic_year_id', $yearId)->with('classGroup'),
            ])
            ->when($this->classGroupId, fn ($query, $id) => $query->whereHas('enrollments', fn ($enrollment) => $enrollment
                ->where('academic_year_id', $yearId)
                ->where('class_group_id', $id)))
            ->when($this->search, fn ($query, $search) => $query->where(function ($inner) use ($search) {
                $inner->where('name', 'like', "%{$search}%")
                    ->orWhere('nis', 'like', "%{$search}%")
                    ->orWhereHas('person', fn ($person) => $person->where('nik', 'like', "%{$search}%"));
            }))
            ->orderByRaw("FIELD(SUBSTRING_INDEX((SELECT cg.name FROM student_enrollments se JOIN class_groups cg ON cg.id = se.class_group_id WHERE se.student_id = students.id AND se.academic_year_id = ? LIMIT 1), '-', 1), 'I','II','III','IV','V','VI')", [$yearId])
            ->orderBy('name')
            ->get();
    }

    /**
     * @return list<string>
     */
    public function headings(): array
    {
        return [
            'NAMA', 'Scan KK', 'Scan KK Wali', 'Scan Akta', 'Scan Ijazah', 'Scan foto',
            'TEMPAL LAHIR', 'TANGGAL LAHIR', 'JENIS KELAMIN', 'KELAS', 'ROMBEL', 'ASAL ROMBEL',
            'NO.ABSEN', 'NIK SISWA', 'NISN', 'NIS', 'NIS 6 DIGIT', 'AGAMA',
            'PERNAH TK', 'PERNAH PAUD', 'HOBI', 'CITA-CITA', 'ANAK KE', 'JML. SAUDARA', 'TGL MASUK',
            'HEPATITIS B', 'POLIO', 'BCG', 'CAMPAK', 'DPT-HB-HiB', 'COVID',
            'TUNA RUNGU', 'TUNA NETRA', 'TUNA DAKSA', 'TUNA GRAHITA', 'TUNA LARAS',
            'LAMBAN BELAJAR', 'GANGGUAN KOMUNIKASI', 'BAKAT LUAR BIASA',
            'JENIS TMP TINGGAL', 'ALAMAT', 'PROVINSI', 'KOTA', 'KECAMATAN', 'KELURAHAN',
            'RT', 'RW', 'KODE POS', 'JARAK MADRASAH', 'TRANSPORTASI', 'WAKTU TEMPUH',
            'NO. TELPON RUMAH', 'NO. HANDPHONE', 'EMAIL', 'NOMOR KK', 'NAMA KEPALA KELUARGA',
            'NAMA AYAH', 'STATUS AYAH', 'NIK AYAH', 'TEMPAT LAHIR AYAH', 'TANGGAL LAHIR AYAH',
            'PENDIDIKAN AYAH', 'PEKERJAAN AYAH', 'PENGHASILAN AYAH', 'NOMOR TELP AYAH',
            'NAMA IBU', 'STATUS IBU', 'TEMPAT LAHIR IBU', 'TANGGAL LAHIR IBU', 'NIK IBU',
            'PENDIDIKAN IBU', 'PEKERJAAN IBU', 'PENGHASILAN IBU', 'NOMOR TELP IBU',
            'NAMA WALI', 'TEMPAT LAHIR WALI', 'TAHUN LAHIR WALI', 'NIK WALI',
            'PENDIDIKAN WALI', 'PEKERJAAN WALI', 'PENGHASILAN WALI', 'NOMOR TELP WALI',
            'NO. KKS', 'NO. PKH', 'NO. KIP', 'STATUS KEPEMILIKAN',
            'ALAMAT', 'KODE PROVINSI', 'KODE KOTA', 'KODE KECAMATAN', 'KODE KELURAHAN',
            'RT', 'RW', 'KODE POS',
            'NAMA SEKOLAH ASAL', 'NSM SEKOLAH ASAL', 'NPSN SEKOLAH ASAL', 'ALAMAT SEKOLAH ASAL',
        ];
    }

    /**
     * @param  Student  $student
     * @return list<mixed>
     */
    public function map($student): array
    {
        $documents = $student->documents ?? [];
        $enrollment = $student->enrollments->first();
        $class = $enrollment?->classGroup;
        $gradeNumber = self::GRADE_NUMBER[$class?->grade_level] ?? null;
        $suffix = $class ? substr($class->name, strrpos($class->name, '-') + 1) : null;
        $father = $this->guardian($student, 'ayah');
        $mother = $this->guardian($student, 'ibu');
        $wali = $this->guardian($student, 'wali');
        $nisLocal = $student->nis;
        $nisEmis = $documents['emis_nis'] ?? (Setting::get('madrasah_nsm') ? Setting::get('madrasah_nsm').str_pad((string) $nisLocal, 6, '0', STR_PAD_LEFT) : $nisLocal);

        return [
            $student->displayName(),
            $documents['scan_kk'] ?? '',
            $documents['scan_kk_wali'] ?? '',
            $documents['scan_akta'] ?? '',
            $documents['scan_ijazah'] ?? '',
            $documents['scan_foto'] ?? '',
            $student->person?->birth_place,
            $this->date($student->person?->birth_date),
            $student->gender,
            $gradeNumber,
            $class ? ($gradeNumber.$suffix) : null,
            $documents['asal_rombel'] ?? null,
            $documents['no_absen'] ?? null,
            $student->person?->nik,
            $student->nisn,
            $nisEmis,
            $nisLocal,
            $student->person?->religion ?? 'Islam',
            $student->ever_tk,
            $student->ever_paud,
            $student->hobby,
            $student->ambition,
            $student->child_order,
            $student->sibling_count,
            $this->date($student->entry_date),
            $this->flag($student->imm_hepb),
            $this->flag($student->imm_polio),
            $this->flag($student->imm_bcg),
            $this->flag($student->imm_campak),
            $this->flag($student->imm_dpt),
            $this->flag($student->imm_covid),
            $this->flag($student->dis_deaf),
            $this->flag($student->dis_blind),
            $this->flag($student->dis_disabled),
            $this->flag($student->dis_intellectual),
            $this->flag($student->dis_behavioral),
            $this->flag($student->dis_slow_learner),
            $this->flag($student->dis_communication),
            $this->flag($student->dis_gifted),
            $student->residence_type,
            $student->person?->address,
            $student->person?->province,
            $student->person?->city,
            $student->person?->district,
            $student->person?->village,
            $student->person?->rt,
            $student->person?->rw,
            $student->person?->postal_code,
            $student->distance,
            $student->transport,
            $student->commute_time,
            $student->person?->home_phone,
            $student->person?->phone,
            $student->person?->email,
            $student->kk_number,
            $student->kk_head_name,
            $father?->name,
            $father?->status,
            $father?->nik,
            $father?->birth_place,
            $this->date($father?->birth_date),
            $father?->education,
            $father?->job,
            $father?->income,
            $father?->phone,
            $mother?->name,
            $mother?->status,
            $mother?->birth_place,
            $this->date($mother?->birth_date),
            $mother?->nik,
            $mother?->education,
            $mother?->job,
            $mother?->income,
            $mother?->phone,
            $wali?->name,
            $wali?->birth_place,
            $wali?->birth_date?->format('Y'),
            $wali?->nik,
            $wali?->education,
            $wali?->job,
            $wali?->income,
            $wali?->phone,
            $student->social_kks,
            $student->social_pkh,
            $student->social_kip,
            $student->parent_ownership,
            $student->parent_address,
            null,
            null,
            null,
            null,
            $student->parent_rt,
            $student->parent_rw,
            $student->parent_postal_code,
            $student->origin_school,
            $student->origin_nsm,
            $student->origin_npsn,
            $student->origin_address,
        ];
    }

    private function guardian(Student $student, string $relation): mixed
    {
        return $student->guardians->firstWhere('pivot.relation', $relation);
    }

    private function date(mixed $date): ?string
    {
        if (! $date) {
            return null;
        }

        return $date instanceof \DateTimeInterface ? $date->format('d/m/Y') : (string) $date;
    }

    private function flag(?bool $value): string
    {
        return $value ? 'Pernah' : 'Belum';
    }
}
