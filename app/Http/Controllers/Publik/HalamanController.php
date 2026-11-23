<?php

namespace App\Http\Controllers\Publik;

use App\Http\Controllers\Controller;
use App\Models\PublicPage;
use App\Models\Setting;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class HalamanController extends Controller
{
    private const PAGE_TITLES = [
        'profil' => 'Profil Madrasah',
        'sambutan' => 'Sambutan Kepala Madrasah',
        'sejarah' => 'Sejarah Madrasah',
        'visi-misi' => 'Visi dan Misi',
        'kebijakan-privasi' => 'Kebijakan Privasi',
    ];

    public function profil(): View
    {
        return $this->render('profil');
    }

    public function sambutan(): View
    {
        return $this->render('sambutan');
    }

    public function sejarah(): View
    {
        return $this->render('sejarah');
    }

    public function visiMisi(): View
    {
        return $this->render('visi-misi');
    }

    public function privasi(): View
    {
        return $this->render('kebijakan-privasi');
    }

    public function kontak(): View
    {
        return view('pages.publik.kontak', [
            'page' => PublicPage::published()->where('slug', 'kontak')->first(),
        ]);
    }

    public function show(PublicPage $publicPage): View
    {
        abort_unless($publicPage->status === PublicPage::PUBLISHED, 404);

        return view('pages.publik.halaman', [
            'page' => $publicPage,
            'pageTitle' => $publicPage->title,
            'pageDescription' => $publicPage->meta_description ?: $publicPage->summary,
            'slug' => $publicPage->slug,
        ]);
    }

    private function render(string $slug): View
    {
        $page = PublicPage::published()->where('slug', $slug)->first();

        return view('pages.publik.halaman', [
            'page' => $page,
            'pageTitle' => $page?->title ?? self::PAGE_TITLES[$slug],
            'pageDescription' => $page?->meta_description ?: $page?->summary,
            'slug' => $slug,
            'legalDocuments' => $slug === 'profil' ? $this->legalDocuments() : [],
        ]);
    }

    /**
     * @return list<array{label: string, description: string, url: ?string}>
     */
    private function legalDocuments(): array
    {
        $documents = [
            ['key' => 'madrasah_file_akreditasi', 'label' => 'Sertifikat Akreditasi', 'description' => 'Sertifikat akreditasi BAN-S/M yang berlaku.'],
            ['key' => 'madrasah_file_sk_pendirian', 'label' => 'SK Pendirian', 'description' => 'Surat keputusan pendirian madrasah.'],
            ['key' => 'madrasah_file_ijop', 'label' => 'Izin Operasional (IJOP)', 'description' => 'Izin operasional madrasah.'],
        ];

        return collect($documents)
            ->map(function (array $document): array {
                $path = Setting::get($document['key']);

                return [
                    'label' => $document['label'],
                    'description' => $document['description'],
                    'url' => $path ? Storage::disk('public')->url($path) : null,
                ];
            })
            ->values()
            ->all();
    }
}
