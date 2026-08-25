<?php

namespace App\Http\Controllers\Kesiswaan;

use App\Exports\PrestasiTemplateExport;
use App\Http\Controllers\Controller;
use App\Imports\PrestasiImport;
use App\Models\AcademicYear;
use App\Models\Achievement;
use App\Models\ClassGroup;
use App\Models\StudentEnrollment;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class AchievementController extends Controller
{
    protected array $tingkatList = ['sekolah', 'kecamatan', 'kabupaten', 'provinsi', 'nasional', 'internasional'];

    public function index(): View
    {
        $this->authorize('viewAny', Achievement::class);

        $tahun = AcademicYear::active();

        $achievements = Achievement::with('student')
            ->when(request('class_group_id'), fn ($q, $id) => $q->whereHas('student.enrollments', fn ($e) => $e
                ->where('academic_year_id', $tahun->id)
                ->where('class_group_id', $id)
                ->where('status', 'aktif')))
            ->when(request('jenis'), fn ($q, $v) => $q->where('jenis', $v))
            ->when(request('tingkat'), fn ($q, $v) => $q->where('tingkat', $v))
            ->when(request('status_verifikasi'), fn ($q, $v) => $q->where('status_verifikasi', $v))
            ->when(request('q'), fn ($q, $s) => $q->whereHas('student', fn ($s2) => $s2
                ->where('name', 'like', "%{$s}%")
                ->orWhere('nis', 'like', "%{$s}%")))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('pages.kesiswaan.prestasi.index', [
            'roleLabel' => 'Kesiswaan',
            'breadcrumb' => [
                ['label' => 'Kesiswaan', 'href' => route('dashboard')],
                ['label' => 'Prestasi & Pelanggaran'],
                ['label' => 'Prestasi'],
            ],
            'achievements' => $achievements,
            'classes' => $this->classes(),
            'tingkatList' => $this->tingkatList,
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Achievement::class);

        $selectedClassId = request('class_group_id') ? (int) request('class_group_id') : null;

        return view('pages.kesiswaan.prestasi.form', [
            'roleLabel' => 'Kesiswaan',
            'breadcrumb' => [
                ['label' => 'Kesiswaan', 'href' => route('dashboard')],
                ['label' => 'Prestasi & Pelanggaran', 'href' => route('prestasi.index')],
                ['label' => 'Catat Prestasi'],
            ],
            'editing' => false,
            'classes' => $this->classes(),
            'selectedClassId' => $selectedClassId,
            'students' => $this->formStudents($selectedClassId),
            'tingkatList' => $this->tingkatList,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Achievement::class);

        $validated = $request->validate($this->rules());

        $achievement = Achievement::create([
            ...$validated,
            'sertifikat' => $request->hasFile('sertifikat') ? $request->file('sertifikat')->store('kesiswaan', 'public') : null,
            'foto' => $request->hasFile('foto') ? $request->file('foto')->store('kesiswaan', 'public') : null,
            'status_verifikasi' => 'menunggu',
            'created_by' => auth()->id(),
        ]);

        activity('kesiswaan')->performedOn($achievement)->log('prestasi_dicatat');

        return redirect()->route('prestasi.index')->with('status', 'Prestasi dicatat dan menunggu verifikasi.');
    }

    public function edit(Achievement $achievement): View
    {
        $this->authorize('update', $achievement);

        $tahun = AcademicYear::active();
        $selectedClassId = StudentEnrollment::where('academic_year_id', $tahun->id)
            ->where('student_id', $achievement->student_id)
            ->where('status', 'aktif')
            ->value('class_group_id');

        return view('pages.kesiswaan.prestasi.form', [
            'roleLabel' => 'Kesiswaan',
            'breadcrumb' => [
                ['label' => 'Kesiswaan', 'href' => route('dashboard')],
                ['label' => 'Prestasi & Pelanggaran', 'href' => route('prestasi.index')],
                ['label' => 'Ubah Prestasi'],
            ],
            'editing' => true,
            'achievement' => $achievement,
            'classes' => $this->classes(),
            'selectedClassId' => $selectedClassId,
            'students' => $this->formStudents($selectedClassId),
            'tingkatList' => $this->tingkatList,
        ]);
    }

    public function update(Request $request, Achievement $achievement): RedirectResponse
    {
        $this->authorize('update', $achievement);

        $validated = $request->validate($this->rules());

        $sertifikat = $this->replaceFile($request, 'sertifikat', $achievement->sertifikat);
        $foto = $this->replaceFile($request, 'foto', $achievement->foto);

        $achievement->update([...$validated, 'sertifikat' => $sertifikat, 'foto' => $foto]);

        activity('kesiswaan')->performedOn($achievement)->log('prestasi_diubah');

        return redirect()->route('prestasi.index')->with('status', 'Prestasi diperbarui.');
    }

    public function destroy(Achievement $achievement): RedirectResponse
    {
        $this->authorize('delete', $achievement);

        foreach (['sertifikat', 'foto'] as $field) {
            if ($achievement->{$field}) {
                Storage::disk('public')->delete($achievement->{$field});
            }
        }

        $achievement->delete();

        activity('kesiswaan')->performedOn($achievement)->log('prestasi_dihapus');

        return redirect()->route('prestasi.index')->with('status', 'Prestasi dihapus.');
    }

    public function verifikasi(Request $request, Achievement $achievement): RedirectResponse
    {
        $this->authorize('update', $achievement);

        $validated = $request->validate(['status_verifikasi' => ['required', 'in:menunggu,terverifikasi,ditolak']]);
        $achievement->update(['status_verifikasi' => $validated['status_verifikasi']]);

        activity('kesiswaan')->performedOn($achievement)->log('prestasi_verifikasi_'.$validated['status_verifikasi']);

        return back()->with('status', 'Status verifikasi prestasi diperbarui.');
    }

    public function publikasi(Request $request, Achievement $achievement): RedirectResponse
    {
        $this->authorize('update', $achievement);

        $validated = $request->validate(['status_publikasi' => ['required', 'in:publik,internal']]);
        $achievement->update(['status_publikasi' => $validated['status_publikasi']]);

        activity('kesiswaan')->performedOn($achievement)->log('prestasi_publikasi_'.$validated['status_publikasi']);

        return back()->with('status', 'Status publikasi prestasi diperbarui.');
    }

    public function template()
    {
        $this->authorize('create', Achievement::class);

        return Excel::download(new PrestasiTemplateExport, 'template-prestasi.xlsx');
    }

    public function import(): View
    {
        $this->authorize('create', Achievement::class);

        return view('pages.kesiswaan.prestasi.import', [
            'roleLabel' => 'Kesiswaan',
            'breadcrumb' => [
                ['label' => 'Kesiswaan', 'href' => route('dashboard')],
                ['label' => 'Prestasi & Pelanggaran', 'href' => route('prestasi.index')],
                ['label' => 'Import Prestasi'],
            ],
        ]);
    }

    public function processImport(Request $request): RedirectResponse
    {
        $this->authorize('create', Achievement::class);

        $request->validate(['file' => ['required', 'file', 'mimes:xlsx,xls']]);

        $sheets = Excel::toArray(new PrestasiImport, $request->file('file'));
        $rows = $sheets[0] ?? [];

        $preview = collect($rows)->map(fn ($row) => $this->validateImportRow($row))->values()->all();

        session(['prestasi_import' => $preview]);

        return redirect()->route('prestasi.import.preview');
    }

    public function previewImport(): View
    {
        $this->authorize('create', Achievement::class);

        $rows = collect(session('prestasi_import', []));
        $ok = $rows->whereNull('error')->count();
        $err = $rows->count() - $ok;

        return view('pages.kesiswaan.prestasi.import-preview', [
            'roleLabel' => 'Kesiswaan',
            'breadcrumb' => [
                ['label' => 'Kesiswaan', 'href' => route('dashboard')],
                ['label' => 'Prestasi & Pelanggaran', 'href' => route('prestasi.index')],
                ['label' => 'Preview Import'],
            ],
            'rows' => $rows,
            'ok' => $ok,
            'err' => $err,
        ]);
    }

    public function simpanImport(): RedirectResponse
    {
        $this->authorize('create', Achievement::class);

        $rows = collect(session('prestasi_import', []));
        $disimpan = 0;
        $gagal = 0;

        DB::transaction(function () use ($rows, &$disimpan, &$gagal) {
            foreach ($rows as $row) {
                if (! empty($row['error']) || empty($row['data'])) {
                    $gagal++;

                    continue;
                }

                // Cegah duplikat saat commit
                if (Achievement::where('student_id', $row['data']['student_id'])
                    ->where('nama_kegiatan', $row['data']['nama_kegiatan'])
                    ->exists()) {
                    $gagal++;

                    continue;
                }

                Achievement::create([
                    ...$row['data'],
                    'status_verifikasi' => 'menunggu',
                    'created_by' => auth()->id(),
                ]);

                $disimpan++;
            }
        });

        session()->forget('prestasi_import');

        activity('kesiswaan')->log('prestasi_import');

        return redirect()->route('prestasi.index')->with('status', "Import selesai — {$disimpan} prestasi disimpan, {$gagal} gagal.");
    }

    public function batalImport(): RedirectResponse
    {
        $this->authorize('create', Achievement::class);

        session()->forget('prestasi_import');

        return redirect()->route('prestasi.index');
    }

    protected function validateImportRow(array $row): array
    {
        $cell = fn ($key) => trim((string) ($row[$key] ?? ''));

        $nis = $cell('nis');
        $jenis = $cell('jenis');
        $namaKegiatan = $cell('nama_kegiatan');
        $tingkat = $cell('tingkat');
        $penyelenggara = $cell('penyelenggara');
        $tanggal = $cell('tanggal');
        $peringkat = $cell('peringkat');
        $pembimbing = $cell('pembimbing');
        $statusPublikasi = $cell('status_publikasi') ?: 'publik';

        $result = [
            'nis' => $nis,
            'nama' => '',
            'nama_kegiatan' => $namaKegiatan,
            'tingkat' => $tingkat,
            'tanggal' => $tanggal ?: null,
            'error' => null,
            'data' => null,
        ];

        $invalid = function (string $pesan) use ($result) {
            $result['error'] = $pesan;

            return $result;
        };

        if ($nis === '') {
            return $invalid('NIS kosong');
        }

        if (! in_array($jenis, ['akademik', 'nonakademik'], true)) {
            return $invalid('Jenis tidak valid');
        }

        if ($namaKegiatan === '') {
            return $invalid('Nama kegiatan wajib');
        }

        if (! in_array($tingkat, $this->tingkatList, true)) {
            return $invalid('Tingkat tidak valid');
        }

        if (! in_array($statusPublikasi, ['publik', 'internal'], true)) {
            return $invalid('Status publikasi tidak valid');
        }

        $tahun = AcademicYear::active();
        $enrollment = StudentEnrollment::with('student')
            ->where('academic_year_id', $tahun->id)
            ->where('status', 'aktif')
            ->whereHas('student', fn ($q) => $q->where('nis', $nis))
            ->first();

        if (! $enrollment) {
            return $invalid('NIS tidak ditemukan di kelas aktif');
        }

        $tanggalNormal = null;
        if ($tanggal !== '') {
            try {
                $tanggalNormal = is_numeric($tanggal)
                    ? ExcelDate::excelToDateTimeObject((float) $tanggal)->format('Y-m-d')
                    : Carbon::parse($tanggal)->format('Y-m-d');
            } catch (\Throwable) {
                return $invalid('Tanggal tidak valid');
            }
        }

        if (Achievement::where('student_id', $enrollment->student_id)
            ->where('nama_kegiatan', $namaKegiatan)
            ->exists()) {
            return $invalid('Duplikat — prestasi sudah tercatat');
        }

        $result['nama'] = $enrollment->student->displayName();
        $result['tanggal'] = $tanggalNormal;
        $result['data'] = [
            'student_id' => $enrollment->student_id,
            'jenis' => $jenis,
            'nama_kegiatan' => $namaKegiatan,
            'tingkat' => $tingkat,
            'penyelenggara' => $penyelenggara ?: null,
            'tanggal' => $tanggalNormal,
            'peringkat' => $peringkat ?: null,
            'pembimbing' => $pembimbing ?: null,
            'status_publikasi' => $statusPublikasi,
        ];

        return $result;
    }

    protected function rules(): array
    {
        return [
            'student_id' => ['required', 'exists:students,id'],
            'jenis' => ['required', 'in:akademik,nonakademik'],
            'nama_kegiatan' => ['required', 'string', 'max:150'],
            'tingkat' => ['required', 'in:'.implode(',', $this->tingkatList)],
            'penyelenggara' => ['nullable', 'string', 'max:100'],
            'tanggal' => ['nullable', 'date'],
            'peringkat' => ['nullable', 'string', 'max:50'],
            'pembimbing' => ['nullable', 'string', 'max:100'],
            'sertifikat' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
            'foto' => ['nullable', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'status_publikasi' => ['required', 'in:publik,internal'],
        ];
    }

    protected function replaceFile(Request $request, string $field, ?string $current): ?string
    {
        if (! $request->hasFile($field)) {
            return $current;
        }

        if ($current) {
            Storage::disk('public')->delete($current);
        }

        return $request->file($field)->store('kesiswaan', 'public');
    }

    protected function classes()
    {
        return ClassGroup::orderByRaw("FIELD(grade_level,'I','II','III','IV','V','VI')")->orderBy('name')->get();
    }

    protected function formStudents(?int $classId = null): array
    {
        $tahun = AcademicYear::active();

        if (! $classId) {
            return [];
        }

        return StudentEnrollment::with('student')
            ->where('academic_year_id', $tahun->id)
            ->where('class_group_id', $classId)
            ->where('status', 'aktif')
            ->orderBy('student_id')
            ->get()
            ->mapWithKeys(fn ($e) => [$e->student_id => $e->student->displayName()])
            ->all();
    }
}
