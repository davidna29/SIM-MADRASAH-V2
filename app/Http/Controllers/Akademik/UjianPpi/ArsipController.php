<?php

namespace App\Http\Controllers\Akademik\UjianPpi;

use App\Exports\PpiExamArchiveTemplateExport;
use App\Http\Controllers\Controller;
use App\Imports\PpiExamArchiveImport;
use App\Models\AcademicYear;
use App\Models\PpiExamArchive;
use App\Models\PpiExamPeriod;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class ArsipController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', PpiExamPeriod::class);

        $archived = PpiExamPeriod::with('academicYear', 'archives')
            ->where('status', PpiExamPeriod::DIARSIPKAN)
            ->orderByDesc('id')
            ->get();

        return view('pages.ujianppi.arsip.index', [
            'archived' => $archived,
            'years' => AcademicYear::orderByDesc('id')->get(),
        ]);
    }

    public function template()
    {
        $this->authorize('archive', new PpiExamPeriod);

        return Excel::download(new PpiExamArchiveTemplateExport, 'template-arsip-ujian-ppi.xlsx');
    }

    public function preview(Request $request): RedirectResponse
    {
        $this->authorize('archive', new PpiExamPeriod);

        $validated = $request->validate([
            'judul' => ['required', 'string', 'max:150'],
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'rombel' => ['nullable', 'string', 'max:20'],
            'file' => ['required', 'file', 'mimes:xlsx,xls'],
        ]);

        $sheets = Excel::toArray(new PpiExamArchiveImport, $request->file('file'));
        $rows = $sheets[0] ?? [];

        $preview = collect($rows)->map(fn ($row) => $this->validateRow($row))->values()->all();

        session([
            'ppi_arsip_import' => [
                'judul' => $validated['judul'],
                'academic_year_id' => $validated['academic_year_id'],
                'rombel' => $validated['rombel'] ?? null,
                'rows' => $preview,
            ],
        ]);

        return redirect()->route('ujianppi.arsip.previewShow');
    }

    public function previewShow(): View
    {
        $this->authorize('archive', new PpiExamPeriod);

        $meta = session('ppi_arsip_import');

        $rows = collect($meta['rows'] ?? []);
        $ok = $rows->whereNull('error')->count();
        $err = $rows->count() - $ok;

        return view('pages.ujianppi.arsip.preview', [
            'meta' => $meta,
            'rows' => $rows,
            'ok' => $ok,
            'err' => $err,
        ]);
    }

    public function simpan(): RedirectResponse
    {
        $this->authorize('archive', new PpiExamPeriod);

        $meta = session('ppi_arsip_import');

        if (! $meta) {
            return redirect()->route('ujianppi.arsip.index')->withErrors(['arsip' => 'Belum ada data import untuk disimpan.']);
        }

        $rows = collect($meta['rows']);

        $period = null;
        $disimpan = 0;
        $gagal = 0;

        DB::transaction(function () use ($meta, $rows, &$period, &$disimpan, &$gagal) {
            $period = PpiExamPeriod::create([
                'academic_year_id' => $meta['academic_year_id'],
                'judul' => $meta['judul'],
                'status' => PpiExamPeriod::DIARSIPKAN,
                'config_locked_at' => now(),
                'bobot_p1' => 0,
                'bobot_p2' => 0,
                'bobot_p3' => 0,
                'bobot_hafalan' => 0,
            ]);

            foreach ($rows as $row) {
                if (! empty($row['error'])) {
                    $gagal++;

                    continue;
                }
                PpiExamArchive::create($row['data'] + ['exam_period_id' => $period->id]);
                $disimpan++;
            }
        });

        session()->forget('ppi_arsip_import');

        activity('akademik')
            ->performedOn($period)
            ->withProperties(['jumlah' => $disimpan])
            ->log('ujian_ppi_arsip_import');

        return redirect()->route('ujianppi.arsip.index')
            ->with('status', "Periode arsip '{$meta['judul']}' dibuat — {$disimpan} siswa diarsipkan, {$gagal} baris gagal.");
    }

    public function batal(): RedirectResponse
    {
        $this->authorize('archive', new PpiExamPeriod);

        session()->forget('ppi_arsip_import');

        return redirect()->route('ujianppi.arsip.index');
    }

    protected function validateRow(array $row): array
    {
        $cell = fn ($key) => trim((string) ($row[$key] ?? ''));

        $nisn = $cell('nisn');
        $nama = $cell('nama');
        $rataP1 = $cell('rata_p1');
        $rataP2 = $cell('rata_p2');
        $rataP3 = $cell('rata_p3');
        $rataHafalan = $cell('nilai_hafalan');
        $nilaiAkhir = $cell('nilai_akhir');
        $predikat = $cell('predikat');
        $statusLulus = $cell('status_lulus');
        $rank = $cell('rank');
        $rombel = $cell('rombel');

        $result = [
            'nisn' => $nisn,
            'nama' => $nama,
            'error' => null,
            'data' => null,
        ];

        $invalid = function (string $pesan) use ($result) {
            $result['error'] = $pesan;

            return $result;
        };

        if ($nama === '') {
            return $invalid('Nama siswa wajib diisi.');
        }

        $num = function (?string $value) {
            if ($value === null || $value === '' || $value === '—' || $value === '-') {
                return null;
            }

            return (float) str_replace(',', '.', $value);
        };

        foreach (['rata_p1' => $rataP1, 'rata_p2' => $rataP2, 'rata_p3' => $rataP3, 'nilai_hafalan' => $rataHafalan, 'nilai_akhir' => $nilaiAkhir] as $key => $raw) {
            $v = $num($raw);
            if ($v !== null && ($v < 0 || $v > 100)) {
                return $invalid(ucfirst(str_replace('_', ' ', $key)).' harus antara 0–100.');
            }
        }

        $lulus = null;
        if ($statusLulus !== '') {
            $lulus = in_array(strtolower($statusLulus), ['lulus', 'l'], true) ? 'Lulus'
                : (in_array(strtolower($statusLulus), ['tidak lulus', 'tidak', 'tl'], true) ? 'Tidak Lulus' : null);
            if ($lulus === null) {
                return $invalid('Status Lulus harus "Lulus" atau "Tidak Lulus".');
            }
        }

        $result['data'] = [
            'nisn' => $nisn ?: null,
            'nama_siswa' => $nama,
            'rata_p1' => $num($rataP1),
            'rata_p2' => $num($rataP2),
            'rata_p3' => $num($rataP3),
            'rata_hafalan' => $num($rataHafalan),
            'nilai_akhir' => $num($nilaiAkhir),
            'predikat' => $predikat ?: null,
            'status_lulus' => $lulus,
            'rank' => $rank ?: null,
            'rombel' => $rombel ?: null,
        ];

        return $result;
    }
}
