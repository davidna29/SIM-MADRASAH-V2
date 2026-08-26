<?php

namespace App\Http\Controllers\Ppdb;

use App\Http\Controllers\Controller;
use App\Models\PpdbInterest;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PpdbSettingController extends Controller
{
    /**
     * Kunci setting konten landing PPDB (prefix `ppdb_`).
     */
    protected array $contentKeys = [
        'ppdb_tanggal_buka',
        'ppdb_tanggal_tutup',
        'ppdb_tanggal_pengumuman',
        'ppdb_tanggal_daftar_ulang',
        'ppdb_usia_min',
        'ppdb_usia_ket',
        'ppdb_dokumen',
        'ppdb_kuota',
        'ppdb_jalur',
        'ppdb_biaya',
        'ppdb_kontak_wa',
        'ppdb_kontak_telepon',
        'ppdb_jam_layanan',
        'ppdb_faq',
    ];

    public function index(): View
    {
        $settings = Setting::getAll();
        $faq = json_decode((string) $settings->get('ppdb_faq', '[]'), true);

        return view('pages.ppdb.settings', [
            'roleLabel' => 'PPDB',
            'breadcrumb' => [
                ['label' => 'PPDB', 'href' => route('ppdb.index')],
                ['label' => 'Pengaturan PPDB'],
            ],
            'settings' => $settings,
            'faq' => is_array($faq) ? $faq : [],
            'interests' => PpdbInterest::orderByDesc('created_at')->paginate(25),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ppdb_status' => 'required|in:open,closed',
            'ppdb_tanggal_buka' => 'nullable|date',
            'ppdb_tanggal_tutup' => 'nullable|date',
            'ppdb_tanggal_pengumuman' => 'nullable|date',
            'ppdb_tanggal_daftar_ulang' => 'nullable|date',
            'ppdb_usia_min' => 'nullable|integer|min:0|max:20',
            'ppdb_usia_ket' => 'nullable|string|max:200',
            'ppdb_dokumen' => 'nullable|string',
            'ppdb_kuota' => 'nullable|integer|min:0',
            'ppdb_jalur' => 'nullable|string',
            'ppdb_biaya' => 'nullable|string',
            'ppdb_kontak_wa' => 'nullable|string|max:30',
            'ppdb_kontak_telepon' => 'nullable|string|max:30',
            'ppdb_jam_layanan' => 'nullable|string|max:100',
            'faq_q' => 'nullable|array',
            'faq_a' => 'nullable|array',
        ]);

        // Simpan saklar status.
        Setting::set('ppdb_status', $validated['ppdb_status']);

        // Simpan konten landing.
        foreach ($this->contentKeys as $key) {
            $value = $validated[$key] ?? '';
            Setting::set($key, is_array($value) ? json_encode($value) : $value);
        }

        // FAQ: gabungkan pasangan q/a dari input array, buang baris kosong.
        $faq = [];
        $questions = $validated['faq_q'] ?? [];
        $answers = $validated['faq_a'] ?? [];
        foreach ($questions as $i => $q) {
            $q = trim((string) $q);
            $a = trim((string) ($answers[$i] ?? ''));
            if ($q !== '' && $a !== '') {
                $faq[] = ['q' => $q, 'a' => $a];
            }
        }
        Setting::set('ppdb_faq', json_encode($faq));

        activity('ppdb')
            ->event('updated')
            ->log('Pengaturan PPDB diperbarui (status: '.$validated['ppdb_status'].').');

        return back()->with('status', 'Pengaturan PPDB berhasil disimpan.');
    }

    public function interestDestroy(PpdbInterest $interest): RedirectResponse
    {
        $interest->delete();

        activity('ppdb')
            ->performedOn($interest)
            ->event('deleted')
            ->log('Minat PPDB dihapus: '.$interest->name);

        return back()->with('status', 'Minat pendaftaran dihapus.');
    }
}
