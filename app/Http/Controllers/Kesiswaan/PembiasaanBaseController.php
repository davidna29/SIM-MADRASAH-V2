<?php

namespace App\Http\Controllers\Kesiswaan;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\ClassGroup;
use App\Models\PembiasaanMateri;
use App\Models\PembiasaanMateriPeriode;
use App\Models\PembiasaanNilai;
use App\Models\Student;
use App\Services\PembiasaanService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

abstract class PembiasaanBaseController extends Controller
{
    protected string $modul = 'ppi';

    public function __construct(protected PembiasaanService $service) {}

    protected function label(): string
    {
        return $this->modul === 'ppi' ? 'PPI' : 'Tahfidz';
    }

    protected function title(): string
    {
        return $this->modul === 'ppi' ? 'PPI (Praktek Pengamalan Ibadah)' : 'Tahfidz';
    }

    public function index(Request $request): View
    {
        $this->authorize('viewAny', PembiasaanMateri::class);

        $tahun = AcademicYear::active();
        if (! $tahun) {
            return redirect()->route('dashboard')->with('error', 'Belum ada tahun ajaran aktif.');
        }

        $query = Student::query()
            ->whereHas('enrollments', fn ($e) => $e->where('academic_year_id', $tahun->id)->where('status', 'aktif'));

        if ($request->class_group_id) {
            $query->whereHas('enrollments', fn ($e) => $e
                ->where('academic_year_id', $tahun->id)
                ->where('class_group_id', $request->class_group_id)
                ->where('status', 'aktif'));
        }

        if ($request->q) {
            $q = $request->q;
            $query->where(fn ($s) => $s->where('name', 'like', "%{$q}%")->orWhere('nis', 'like', "%{$q}%"));
        }

        $students = $query
            ->with(['enrollments' => function ($q) use ($tahun) {
                $q->where('academic_year_id', $tahun->id)
                    ->where('status', 'aktif')
                    ->with('classGroup');
            }])
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        $students->getCollection()->transform(function ($s) {
            $s->activeClass = $s->enrollments->first()?->classGroup?->name;

            return $s;
        });

        return view('pages.kesiswaan.pembiasaan.index', [
            'modul' => $this->modul,
            'title' => $this->title(),
            'students' => $students,
            'classes' => ClassGroup::orderByRaw("FIELD(grade_level,'I','II','III','IV','V','VI')")->orderBy('name')->get(),
            'roleLabel' => 'Kesiswaan',
        ]);
    }

    public function input(Student $siswa): View
    {
        $this->authorize('input', PembiasaanMateri::class);

        $data = $this->service->buildMatrix($siswa, $this->modul);

        return view('pages.kesiswaan.pembiasaan.input', array_merge($data, [
            'siswa' => $siswa,
            'title' => 'Input Nilai '.$this->label(),
            'roleLabel' => 'Kesiswaan',
        ]));
    }

    public function store(Request $request, Student $siswa): RedirectResponse
    {
        $this->authorize('input', PembiasaanMateri::class);

        $current = $this->service->currentGradeSemester($siswa);
        if (! $current) {
            return back()->with('error', 'Siswa tidak memiliki enrollmen aktif.');
        }

        $kelas = $current['kelas'];
        $semester = $current['semester'];

        $allowedIds = $this->service->materials($this->modul)->pluck('id')->all();

        $validated = $request->validate([
            'nilai' => ['array'],
            'nilai.*' => ['nullable', 'integer', 'between:0,100'],
        ]);

        foreach (($validated['nilai'] ?? []) as $materiId => $value) {
            $materiId = (int) $materiId;
            if (! in_array($materiId, $allowedIds, true)) {
                continue;
            }

            $periode = PembiasaanMateriPeriode::where('materi_id', $materiId)
                ->where('kelas', $kelas)
                ->where('semester', $semester)
                ->first();

            if (! $periode || ! $periode->aktif) {
                continue;
            }

            PembiasaanNilai::updateOrCreate(
                ['siswa_id' => $siswa->id, 'materi_id' => $materiId, 'kelas' => $kelas, 'semester' => $semester],
                [
                    'nilai' => ($value === null || $value === '') ? null : (int) $value,
                    'tahun_pelajaran' => $current['tahun'],
                ]
            );
        }

        activity('kesiswaan')->performedOn($siswa)->log($this->modul.'_nilai_disimpan');

        return redirect()->route($this->modul.'.input', $siswa)->with('status', 'Nilai '.$this->label().' disimpan.');
    }

    public function konfigurasi(): View
    {
        $this->authorize('configure', PembiasaanMateri::class);

        $materials = PembiasaanMateri::forModul($this->modul)->with('periodes')->get();

        return view('pages.kesiswaan.pembiasaan.konfigurasi', [
            'modul' => $this->modul,
            'title' => 'Konfigurasi Materi '.$this->label(),
            'label' => $this->label(),
            'materials' => $materials,
            'pairs' => PembiasaanService::PAIRS,
            'roleLabel' => 'Kesiswaan',
        ]);
    }

    public function konfigurasiUpdate(Request $request): RedirectResponse
    {
        $this->authorize('configure', PembiasaanMateri::class);

        $ids = $this->service->materials($this->modul)->pluck('id')->all();

        PembiasaanMateriPeriode::whereIn('materi_id', $ids)->update(['aktif' => false]);

        foreach ($request->input('periode', []) as $key => $val) {
            [$materiId, $kelas, $semester] = explode('-', (string) $key);
            $materiId = (int) $materiId;
            if (! in_array($materiId, $ids, true)) {
                continue;
            }
            PembiasaanMateriPeriode::updateOrCreate(
                ['materi_id' => $materiId, 'kelas' => (int) $kelas, 'semester' => (int) $semester],
                ['aktif' => true]
            );
        }

        activity('kesiswaan')->log($this->modul.'_konfigurasi');

        return redirect()->route($this->modul.'.konfigurasi')->with('status', 'Konfigurasi materi '.$this->label().' disimpan.');
    }

    public function cetak(Student $siswa): Response
    {
        $this->authorize('viewAny', PembiasaanMateri::class);

        $data = $this->service->buildMatrix($siswa, $this->modul);
        $kop = $this->service->kop();

        $filename = $this->modul.'-'.($siswa->nis ?? $siswa->id).'-'
            .str_replace('/', '-', (string) ($data['current']['tahun'] ?? 'TA')).'.pdf';

        $pdf = Pdf::loadView('pages.kesiswaan.pembiasaan.cetak', array_merge($data, [
            'siswa' => $siswa,
            'kop' => $kop,
            'title' => $this->modul === 'ppi' ? 'PPI (BIMBINGAN KELAS)' : 'TAHFIDZ',
            'label' => $this->label(),
        ]));

        $pdf->setPaper('A4', 'landscape');

        return $pdf->download($filename);
    }
}
