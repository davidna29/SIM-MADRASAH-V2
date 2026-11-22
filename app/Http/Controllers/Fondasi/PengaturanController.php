<?php

namespace App\Http\Controllers\Fondasi;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PengaturanController extends Controller
{
    public function index(): View
    {
        $settings = Setting::getAll();

        return view('pages.fondasi.pengaturan', [
            'roleLabel' => 'Super Admin',
            'breadcrumb' => [
                ['label' => 'Fondasi', 'href' => route('dashboard')],
                ['label' => 'Pengaturan Sistem'],
            ],
            'settings' => $settings,
            'logoUrl' => Setting::logoUrl($settings),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            // Data Utama
            'madrasah_name' => 'required|string|max:200',
            'madrasah_nsm' => 'nullable|string|max:20',
            'madrasah_npsn' => 'nullable|string|max:20',
            'madrasah_jenjang' => 'required|in:RA,MI,MTs,MA',
            'madrasah_status' => 'required|in:negeri,swasta',
            'madrasah_tahun_berdiri' => 'nullable|string|max:4',
            'madrasah_tagline' => 'nullable|string|max:180',

            // Alamat & Lokasi
            'madrasah_jalan' => 'nullable|string|max:255',
            'madrasah_desa' => 'nullable|string|max:100',
            'madrasah_kecamatan' => 'nullable|string|max:100',
            'madrasah_kabupaten' => 'nullable|string|max:100',
            'madrasah_provinsi' => 'nullable|string|max:100',
            'madrasah_kode_pos' => 'nullable|string|max:10',
            'madrasah_latitude' => 'nullable|string|max:20',
            'madrasah_longitude' => 'nullable|string|max:20',

            // Kontak
            'madrasah_phone' => 'nullable|string|max:30',
            'madrasah_email' => 'nullable|email|max:100',
            'madrasah_website' => 'nullable|url|max:200',
            'madrasah_whatsapp' => 'nullable|string|max:20',
            'madrasah_jam_layanan' => 'nullable|string|max:100',
            'madrasah_facebook' => 'nullable|url|max:200',
            'madrasah_instagram' => 'nullable|url|max:200',
            'madrasah_youtube' => 'nullable|url|max:200',
            'madrasah_tiktok' => 'nullable|url|max:200',

            // Legalitas
            'madrasah_sk_pendirian' => 'nullable|string|max:100',
            'madrasah_tgl_sk_pendirian' => 'nullable|date',
            'madrasah_sk_operasional' => 'nullable|string|max:100',

            // Akreditasi & Naungan
            'madrasah_akreditasi' => 'required|in:terakreditasi,belum',
            'madrasah_nilai_akreditasi' => 'nullable|in:A,B,C',
            'madrasah_naungan' => 'nullable|string|max:200',

            // Logo
            'madrasah_logo' => 'nullable|image|max:2048',
        ]);

        // Handle logo upload
        if ($request->hasFile('madrasah_logo')) {
            // Delete old logo
            $oldLogo = Setting::get('madrasah_logo');
            if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
                Storage::disk('public')->delete($oldLogo);
            }
            if ($oldLogo && str_starts_with($oldLogo, 'settings/') && Storage::disk('public')->exists('settings/'.$oldLogo)) {
                Storage::disk('public')->delete('settings/'.$oldLogo);
            }

            $file = $request->file('madrasah_logo');
            $filename = 'logo.'.$file->getClientOriginalExtension();
            $validated['madrasah_logo'] = $file->storeAs('settings', $filename, 'public');
        }

        // Remove the file object from validated data (it's not a string)
        unset($validated['madrasah_logo_file']);

        // Save all settings (exclude logo if not uploaded)
        foreach ($validated as $key => $value) {
            if ($key !== 'madrasah_logo' || $request->hasFile('madrasah_logo')) {
                Setting::set($key, $value);
            }
        }

        // If logo was uploaded, save it separately
        if ($request->hasFile('madrasah_logo')) {
            Setting::set('madrasah_logo', $validated['madrasah_logo']);
        }

        return back()->with('status', 'Pengaturan sistem berhasil disimpan.');
    }
}
