<?php

namespace App\Http\Controllers\Akademik;

use App\Exports\SimpleTemplateExport;
use App\Http\Controllers\Controller;
use App\Imports\SimpleImport;
use App\Models\AcademicYear;
use App\Models\ClassGroup;
use App\Models\Employee;
use App\Models\EmployeePositionHistory;
use App\Models\Guardian;
use App\Models\OrganizationalUnit;
use App\Models\Person;
use App\Models\Position;
use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\Subject;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExcelImportController extends Controller
{
    /**
     * @return array<string, array<string, mixed>>
     */
    private function modules(): array
    {
        return [
            'siswa' => [
                'title' => 'Import Data Siswa',
                'description' => 'Unggah berkas Excel berisi data siswa. Baris divalidasi satu per satu sebelum disimpan.',
                'headings' => ['nis', 'nisn', 'nama', 'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir', 'nik', 'kelas', 'nama_ayah', 'nama_ibu', 'nama_wali', 'no_hp'],
                'sample' => ['216001', '0123456789', 'NAMA SISWA', 'L', 'Palangka Raya', '2014-01-01', '6271010101010001', 'VI-A', 'NAMA AYAH', 'NAMA IBU', '', '081234567890'],
                'index' => 'siswa.index',
                'label' => 'Data Siswa',
            ],
            'guru' => [
                'title' => 'Import Data Guru & Pegawai',
                'description' => 'Unggah berkas Excel data pendidik dan tenaga kependidikan.',
                'headings' => ['nip', 'nuptk', 'nama', 'nik', 'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir', 'status_pegawai', 'jabatan', 'unit_kerja', 'tmt', 'jenjang_pendidikan', 'jurusan'],
                'sample' => ['197512122007012044', '', 'NAMA GURU', '6271010101010002', 'P', 'Palangka Raya', '1980-01-01', 'pns', 'GURU_MAPEL', 'GURU', '2007-01-01', 'S1', 'Tarbiyah'],
                'index' => 'pegawai.index',
                'label' => 'Data Guru & Pegawai',
            ],
            'mapel' => [
                'title' => 'Import Mata Pelajaran',
                'description' => 'Unggah berkas Excel daftar mata pelajaran beserta alokasi JP per tingkat.',
                'headings' => ['kode', 'nama', 'kelompok', 'jp_I', 'jp_II', 'jp_III', 'jp_IV', 'jp_V', 'jp_VI'],
                'sample' => ['QUR', "Al-Qur'an Hadis", 'keagamaan', 2, 2, 2, 2, 2, 2],
                'index' => 'mapel.index',
                'label' => 'Mata Pelajaran',
            ],
        ];
    }

    public function template(string $module): BinaryFileResponse
    {
        $config = $this->config($module);

        return Excel::download(
            new SimpleTemplateExport($config['headings'], [$config['sample']]),
            "template-import-{$module}.xlsx"
        );
    }

    public function upload(string $module): View
    {
        $config = $this->config($module);

        return view('pages.import.upload', [
            'module' => $module,
            'config' => $config,
            'roleLabel' => $config['label'],
            'breadcrumb' => [
                ['label' => 'Beranda', 'href' => route('dashboard')],
                ['label' => $config['label'], 'href' => route($config['index'])],
                ['label' => 'Import Excel'],
            ],
        ]);
    }

    public function process(Request $request, string $module): RedirectResponse
    {
        $config = $this->config($module);

        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:5120'],
        ], [], ['file' => 'berkas Excel']);

        $rows = Excel::toArray(new SimpleImport, $request->file('file'))[0] ?? [];

        if ($rows === []) {
            return back()->withErrors(['file' => 'Berkas tidak berisi data. Pastikan baris pertama adalah heading sesuai template.']);
        }

        $prepared = [];

        foreach ($rows as $index => $row) {
            $prepared[] = $this->validateRow($module, $row, $index + 2);
        }

        session(["excel_import_{$module}" => $prepared]);

        return redirect()->route("{$module}.import.preview")
            ->with('status', 'Berkas dibaca — tinjau hasil validasi sebelum menyimpan.');
    }

    public function preview(string $module): View|RedirectResponse
    {
        $rows = session("excel_import_{$module}");

        if (! is_array($rows)) {
            return redirect()->route("{$module}.import")->withErrors(['file' => 'Tidak ada berkas yang menunggu konfirmasi.']);
        }

        $config = $this->config($module);

        return view('pages.import.preview', [
            'module' => $module,
            'config' => $config,
            'rows' => $rows,
            'roleLabel' => $config['label'],
            'breadcrumb' => [
                ['label' => 'Beranda', 'href' => route('dashboard')],
                ['label' => $config['label'], 'href' => route($config['index'])],
                ['label' => 'Import Excel', 'href' => route("{$module}.import")],
                ['label' => 'Pratinjau'],
            ],
        ]);
    }

    public function commit(string $module): RedirectResponse
    {
        $rows = session("excel_import_{$module}");

        if (! is_array($rows)) {
            return redirect()->route("{$module}.import")->withErrors(['file' => 'Tidak ada berkas yang menunggu konfirmasi.']);
        }

        $saved = 0;
        $failed = 0;

        DB::transaction(function () use ($module, $rows, &$saved, &$failed) {
            foreach ($rows as $row) {
                if (! empty($row['error']) || empty($row['data'])) {
                    $failed++;

                    continue;
                }

                match ($module) {
                    'siswa' => $this->saveStudent($row['data']),
                    'guru' => $this->saveEmployee($row['data']),
                    'mapel' => $this->saveSubject($row['data']),
                };

                $saved++;
            }
        });

        session()->forget("excel_import_{$module}");
        activity('akademik')->log("import_{$module}", ['jumlah' => $saved, 'gagal' => $failed]);

        return redirect()->route($this->config($module)['index'])
            ->with('status', "Import selesai — {$saved} data disimpan, {$failed} baris gagal/dilewati.");
    }

    public function cancel(string $module): RedirectResponse
    {
        session()->forget("excel_import_{$module}");

        return redirect()->route($this->config($module)['index'])->with('status', 'Import dibatalkan.');
    }

    /**
     * @param  array<string, mixed>  $row
     * @return array{baris: int, label: string, error: ?string, data: ?array<string, mixed>}
     */
    private function validateRow(string $module, array $row, int $line): array
    {
        $cell = fn (string $key) => trim((string) ($row[$key] ?? ''));

        return match ($module) {
            'siswa' => $this->validateStudentRow($row, $line, $cell),
            'guru' => $this->validateEmployeeRow($row, $line, $cell),
            'mapel' => $this->validateSubjectRow($row, $line, $cell),
        };
    }

    private function validateStudentRow(array $row, int $line, callable $cell): array
    {
        $nis = $cell('nis');
        $nisn = $cell('nisn');
        $nama = $cell('nama');
        $gender = strtoupper($cell('jenis_kelamin'));
        $kelasName = $this->classCode($cell('kelas'));
        $nik = $cell('nik');
        $result = ['baris' => $line, 'label' => $nama ?: '—', 'error' => null, 'data' => null];

        $invalid = function (string $message) use ($result): array {
            $result['error'] = $message;

            return $result;
        };

        if ($nama === '') {
            return $invalid('Nama wajib diisi.');
        }

        if ($nis === '') {
            return $invalid('NIS wajib diisi.');
        }

        if (! in_array($gender, ['L', 'P'], true)) {
            return $invalid('Jenis kelamin harus L/P.');
        }

        $class = $kelasName ? ClassGroup::where('name', $kelasName)->first() : null;

        if (! $class) {
            return $invalid('Kelas tidak ditemukan (contoh format: VI-A).');
        }

        if ($nisn !== '' && ! preg_match('/^\d{10}$/', $nisn)) {
            return $invalid('NISN harus 10 digit.');
        }

        if ($nik !== '' && ! preg_match('/^\d{16}$/', $nik)) {
            return $invalid('NIK harus 16 digit.');
        }

        if ($nik !== '') {
            $person = Person::where('nik', $nik)->first();

            if ($person && ($person->employee()->exists() || $person->students()->exists())) {
                return $invalid('NIK sudah dipakai pegawai/siswa lain.');
            }
        }

        if ($cell('tanggal_lahir') !== '' && $this->parseDate($cell('tanggal_lahir')) === null) {
            return $invalid('Tanggal lahir tidak valid.');
        }

        $result['label'] = $nama.' · '.$class->name;
        $result['data'] = [
            'nis' => $nis,
            'nisn' => $nisn ?: null,
            'nama' => $nama,
            'gender' => $gender,
            'tempat_lahir' => $cell('tempat_lahir') ?: null,
            'tanggal_lahir' => $this->parseDate($cell('tanggal_lahir')),
            'nik' => $nik ?: null,
            'class_group_id' => $class->id,
            'nama_ayah' => $cell('nama_ayah') ?: null,
            'nama_ibu' => $cell('nama_ibu') ?: null,
            'nama_wali' => $cell('nama_wali') ?: null,
            'no_hp' => $cell('no_hp') ?: null,
        ];

        return $result;
    }

    private function validateEmployeeRow(array $row, int $line, callable $cell): array
    {
        $nama = $cell('nama');
        $result = ['baris' => $line, 'label' => $nama ?: '—', 'error' => null, 'data' => null];

        $invalid = function (string $message) use ($result): array {
            $result['error'] = $message;

            return $result;
        };

        if ($nama === '') {
            return $invalid('Nama wajib diisi.');
        }

        $nik = $cell('nik');
        $nip = $cell('nip');

        if ($nik !== '' && ! preg_match('/^\d{16}$/', $nik)) {
            return $invalid('NIK harus 16 digit.');
        }

        if ($nip !== '' && ! preg_match('/^\d{16,20}$/', $nip)) {
            return $invalid('NIP harus 16–20 digit.');
        }

        if ($cell('tanggal_lahir') !== '' && $this->parseDate($cell('tanggal_lahir')) === null) {
            return $invalid('Tanggal lahir tidak valid.');
        }

        $status = strtolower($cell('status_pegawai') ?: 'honor');

        if (! in_array($status, ['pns', 'pppk', 'honor'], true)) {
            return $invalid('Status pegawai harus pns/pppk/honor.');
        }

        $positionCode = $cell('jabatan') ?: 'GURU_MAPEL';
        $position = Position::where('code', $positionCode)->orWhere('name', 'like', $positionCode)->first();

        if (! $position) {
            return $invalid('Jabatan tidak ditemukan.');
        }

        $unitCode = $cell('unit_kerja') ?: 'GURU';
        $unit = OrganizationalUnit::where('code', $unitCode)->orWhere('name', 'like', $unitCode)->first();

        if (! $unit) {
            return $invalid('Unit kerja tidak ditemukan.');
        }

        $result['label'] = $nama.' · '.$position->name;
        $result['data'] = [
            'nama' => $nama,
            'nik' => $nik ?: null,
            'nip' => $nip ?: null,
            'nuptk' => $cell('nuptk') ?: null,
            'gender' => strtoupper($cell('jenis_kelamin')) === 'P' ? 'P' : 'L',
            'tempat_lahir' => $cell('tempat_lahir') ?: null,
            'tanggal_lahir' => $this->parseDate($cell('tanggal_lahir')),
            'status' => $status,
            'position_id' => $position->id,
            'unit_id' => $unit->id,
            'tmt' => $this->parseDate($cell('tmt')),
            'jenjang' => $cell('jenjang_pendidikan') ?: null,
            'jurusan' => $cell('jurusan') ?: null,
        ];

        return $result;
    }

    private function validateSubjectRow(array $row, int $line, callable $cell): array
    {
        $kode = strtoupper($cell('kode'));
        $nama = $cell('nama');
        $result = ['baris' => $line, 'label' => $nama ?: '—', 'error' => null, 'data' => null];

        $invalid = function (string $message) use ($result): array {
            $result['error'] = $message;

            return $result;
        };

        if ($kode === '') {
            return $invalid('Kode mapel wajib diisi.');
        }

        if (strlen($kode) > 10) {
            return $invalid('Kode mapel maksimal 10 karakter.');
        }

        if ($nama === '') {
            return $invalid('Nama mapel wajib diisi.');
        }

        $category = strtolower($cell('kelompok') ?: 'umum');

        if (! in_array($category, ['keagamaan', 'umum', 'mulok'], true)) {
            return $invalid('Kelompok harus keagamaan/umum/mulok.');
        }

        $jp = [];

        foreach (['I', 'II', 'III', 'IV', 'V', 'VI'] as $grade) {
            $value = $cell('jp_'.strtolower($grade));

            if ($value === '') {
                continue;
            }

            if (! is_numeric($value) || (int) $value < 1 || (int) $value > 12) {
                return $invalid("JP kelas {$grade} harus angka 1–12.");
            }

            $jp[$grade] = (int) $value;
        }

        if (Subject::where('code', $kode)->exists()) {
            return $invalid('Kode mapel sudah ada — gunakan menu Ubah untuk memperbarui.');
        }

        $result['label'] = $kode.' · '.$nama;
        $result['data'] = [
            'code' => $kode,
            'name' => $nama,
            'category' => $category,
            'jp_per_grade' => $jp ?: null,
        ];

        return $result;
    }

    private function saveStudent(array $data): void
    {
        $tahun = AcademicYear::active();
        $student = Student::where('nis', $data['nis'])->first();
        $nik = $data['nik'] ?: $this->syntheticNik();

        $person = $student?->person;

        if ($person) {
            $nikTaken = Person::where('nik', $nik)->whereKeyNot($person->id)->exists();

            if (! $nikTaken) {
                $person->update(['nik' => $nik]);
            }
        } else {
            $person = Person::firstOrCreate(
                ['nik' => $nik],
                ['name' => $data['nama'], 'gender' => $data['gender'], 'religion' => 'Islam']
            );
        }

        $person->fill([
            'name' => $data['nama'],
            'gender' => $data['gender'],
            'religion' => 'Islam',
            'birth_place' => $data['tempat_lahir'],
            'birth_date' => $data['tanggal_lahir'],
            'phone' => $data['no_hp'],
        ])->save();

        $student = Student::updateOrCreate(
            ['nis' => $data['nis']],
            [
                'person_id' => $person->id,
                'name' => $data['nama'],
                'gender' => $data['gender'],
                'nisn' => $data['nisn'],
            ]
        );

        $enrollment = StudentEnrollment::where('academic_year_id', $tahun->id)
            ->where('student_id', $student->id)
            ->first();

        if ($enrollment) {
            $enrollment->update(['class_group_id' => $data['class_group_id'], 'status' => 'aktif']);
        } else {
            StudentEnrollment::create([
                'academic_year_id' => $tahun->id,
                'class_group_id' => $data['class_group_id'],
                'student_id' => $student->id,
                'status' => 'aktif',
            ]);
        }

        foreach (['ayah' => $data['nama_ayah'], 'ibu' => $data['nama_ibu'], 'wali' => $data['nama_wali']] as $relation => $name) {
            if (! $name) {
                continue;
            }

            $guardian = Guardian::firstOrCreate(['name' => $name, 'nik' => null]);

            if (! $student->guardians()->where('guardians.id', $guardian->id)->exists()) {
                $student->guardians()->attach($guardian->id, ['relation' => $relation]);
            }
        }
    }

    private function saveEmployee(array $data): void
    {
        $nik = $data['nik'] ?: $this->syntheticNik();
        $person = Person::firstOrCreate(
            ['nik' => $nik],
            ['name' => $data['nama'], 'gender' => $data['gender'], 'religion' => 'Islam']
        );

        $person->fill([
            'name' => $data['nama'],
            'gender' => $data['gender'],
            'religion' => 'Islam',
            'birth_place' => $data['tempat_lahir'],
            'birth_date' => $data['tanggal_lahir'],
        ])->save();

        $nip = $data['nip'];

        if ($nip && Employee::where('nip', $nip)->where('person_id', '!=', $person->id)->exists()) {
            $nip = null;
        }

        $employee = Employee::updateOrCreate(
            ['person_id' => $person->id],
            [
                'nip' => $nip,
                'nuptk' => $data['nuptk'],
                'education_level' => $data['jenjang'],
                'major' => $data['jurusan'],
                'employee_status' => $data['status'],
                'status' => 'aktif',
                'tmt' => $data['tmt'],
                'position_id' => $data['position_id'],
                'organizational_unit_id' => $data['unit_id'],
            ]
        );

        EmployeePositionHistory::updateOrCreate(
            [
                'employee_id' => $employee->id,
                'position_id' => $employee->position_id,
                'started_at' => $data['tmt'] ?? now()->toDateString(),
            ],
            ['organizational_unit_id' => $employee->organizational_unit_id, 'ended_at' => null, 'reason' => 'pengangkatan']
        );
    }

    private function saveSubject(array $data): void
    {
        Subject::create([
            ...$data,
            'sort_order' => (Subject::max('sort_order') ?? 0) + 1,
        ]);
    }

    private function syntheticNik(): string
    {
        do {
            $nik = '6201'.str_pad((string) random_int(100000000000, 999999999999), 12, '0', STR_PAD_LEFT);
        } while (Person::where('nik', $nik)->exists());

        return $nik;
    }

    private function parseDate(string $value): ?string
    {
        if ($value === '') {
            return null;
        }

        if (is_numeric($value)) {
            try {
                return ExcelDate::excelToDateTimeObject((float) $value)->format('Y-m-d');
            } catch (\Throwable) {
                return null;
            }
        }

        try {
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Throwable) {
            return null;
        }
    }

    private function classCode(string $value): ?string
    {
        $value = strtoupper(trim($value));

        if ($value === '') {
            return null;
        }

        if (preg_match('/^(I|II|III|IV|V|VI)[- ]?([A-Z])$/', $value, $matches) === 1) {
            return $matches[1].'-'.$matches[2];
        }

        if (preg_match('/^(\d)[- ]?([A-Z])$/', $value, $matches) === 1) {
            $map = [1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI'];

            return ($map[(int) $matches[1]] ?? $matches[1]).'-'.$matches[2];
        }

        return null;
    }

    /**
     * @return array<string, mixed>
     */
    private function config(string $module): array
    {
        $modules = $this->modules();

        abort_unless(isset($modules[$module]), 404);

        return $modules[$module];
    }
}
