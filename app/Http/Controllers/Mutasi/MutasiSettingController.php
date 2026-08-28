<?php

namespace App\Http\Controllers\Mutasi;

use App\Http\Controllers\Controller;
use App\Models\MutasiInterest;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MutasiSettingController extends Controller
{
    protected array $contentKeys = [
        'mutasi_tanggal_buka',
        'mutasi_tanggal_tutup',
        'mutasi_tanggal_pengumuman',
        'mutasi_tanggal_daftar_ulang',
        'mutasi_syarat',
        'mutasi_kuota',
        'mutasi_kelas_tersedia',
        'mutasi_biaya',
        'mutasi_kontak_wa',
        'mutasi_kontak_telepon',
        'mutasi_jam_layanan',
        'mutasi_faq',
    ];

    public function index(): View
    {
        $settings = Setting::getAll();
        $faq = json_decode((string) $settings->get('mutasi_faq', '[]'), true);

        return view('pages.mutasi.settings', [
            'roleLabel' => 'Mutasi',
            'breadcrumb' => [
                ['label' => 'Mutasi Masuk', 'href' => route('mutasi.index')],
                ['label' => 'Pengaturan Mutasi Masuk'],
            ],
            'settings' => $settings,
            'faq' => is_array($faq) ? $faq : [],
            'interests' => MutasiInterest::orderByDesc('created_at')->paginate(25),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'mutasi_status' => 'required|in:open,closed',
            'mutasi_tanggal_buka' => 'nullable|date',
            'mutasi_tanggal_tutup' => 'nullable|date',
            'mutasi_tanggal_pengumuman' => 'nullable|date',
            'mutasi_tanggal_daftar_ulang' => 'nullable|date',
            'mutasi_syarat' => 'nullable|string',
            'mutasi_kuota' => 'nullable|integer|min:0',
            'mutasi_kelas_tersedia' => 'nullable|string',
            'mutasi_biaya' => 'nullable|string',
            'mutasi_kontak_wa' => 'nullable|string|max:30',
            'mutasi_kontak_telepon' => 'nullable|string|max:30',
            'mutasi_jam_layanan' => 'nullable|string|max:100',
            'faq_q' => 'nullable|array',
            'faq_a' => 'nullable|array',
        ]);

        Setting::set('mutasi_status', $validated['mutasi_status']);

        foreach ($this->contentKeys as $key) {
            $value = $validated[$key] ?? '';
            Setting::set($key, is_array($value) ? json_encode($value) : $value);
        }

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
        Setting::set('mutasi_faq', json_encode($faq));

        activity('mutasi')
            ->event('updated')
            ->log('Pengaturan Mutasi Masuk diperbarui (status: '.$validated['mutasi_status'].').');

        return back()->with('status', 'Pengaturan Mutasi Masuk berhasil disimpan.');
    }

    public function interestDestroy(MutasiInterest $interest): RedirectResponse
    {
        $interest->delete();

        activity('mutasi')
            ->performedOn($interest)
            ->event('deleted')
            ->log('Minat mutasi dihapus: '.$interest->name);

        return back()->with('status', 'Minat pendaftaran pindah dihapus.');
    }
}
