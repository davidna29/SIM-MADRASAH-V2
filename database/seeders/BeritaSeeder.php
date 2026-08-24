<?php

namespace Database\Seeders;

use App\Models\Agenda;
use App\Models\Article;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BeritaSeeder extends Seeder
{
    public function run(): void
    {
        $editor = User::firstOrCreate(
            ['username' => 'editor.humas'],
            [
                'name' => 'Humas Madrasah',
                'email' => 'humas@madrasah.sch.id',
                'password' => 'password',
                'role' => 'editor_berita',
            ]
        );

        $guru = User::where('username', 'guru.umar')->first();

        $berita = [
            [
                'title' => 'Kegiatan MPLS Tahun Ajaran 2026/2027 Sukses Dilaksanakan',
                'summary' => 'Rangkaian Masa Pengenalan Lingkungan Sekolah berjalan lancar dan penuh antusiasme.',
                'body' => "Masa Pengenalan Lingkungan Sekolah (MPLS) Tahun Ajaran 2026/2027 telah sukses dilaksanakan.\n\nKegiatan diisi dengan pengenalan lingkungan madrasah, tata tertib, serta perkenalan dengan dewan guru dan staf. Orang tua siswa juga diberikan sesi informasi mengenai program madrasah selama satu tahun ke depan.",
                'category' => 'Kegiatan',
                'tags' => 'mpls, kegiatan',
                'status' => Article::PUBLISH,
            ],
            [
                'title' => 'Penerimaan PPDB Gelombang Dua Dibuka',
                'summary' => 'PPDB jalur reguler gelombang kedua dibuka mulai 10 September 2026.',
                'body' => "Madrasah membuka Penerimaan Peserta Didik Baru (PPDB) gelombang kedua.\n\nPendaftaran dapat dilakukan secara daring melalui laman resmi madrasah. Persyaratan dan tata cara pendaftaran dapat dilihat pada menu PPDB.",
                'category' => 'PPDB',
                'tags' => 'ppdb, pendaftaran',
                'status' => Article::PUBLISH,
            ],
        ];

        foreach ($berita as $i => $b) {
            Article::firstOrCreate(
                ['slug' => Str::slug($b['title'])],
                [
                    'title' => $b['title'],
                    'summary' => $b['summary'],
                    'body' => $b['body'],
                    'category' => $b['category'],
                    'tags' => $b['tags'],
                    'status' => $b['status'],
                    'author_id' => $i === 0 ? $editor->id : ($guru?->id ?? $editor->id),
                    'published_at' => now()->subDays($i),
                ]
            );
        }

        // Contoh berita draf oleh guru
        if ($guru) {
            Article::firstOrCreate(
                ['slug' => Str::slug('Draft: Inovasi Pembelajaran Berbasis Proyek')],
                [
                    'title' => 'Inovasi Pembelajaran Berbasis Proyek',
                    'summary' => null,
                    'body' => 'Draft artikel ini menunggu dilengkapi oleh penulis.',
                    'category' => 'Akademik',
                    'status' => Article::DRAFT,
                    'author_id' => $guru->id,
                ]
            );
        }

        Agenda::firstOrCreate(
            ['title' => 'Rapat Dewan Guru — Pembagian Rapor'],
            [
                'jenis' => 'agenda',
                'tanggal' => now()->addDays(3)->toDateString(),
                'waktu' => '14:00',
                'lokasi' => 'Aula Madrasah',
                'penanggung_jawab' => 'Wakamad Kurikulum',
                'isi' => 'Pembahasan kelulusan dan persiapan pembagian rapor.',
                'target' => 'internal',
                'tampil_mulai' => now()->toDateString(),
                'status' => 'aktif',
                'created_by' => $editor->id,
            ]
        );

        Agenda::firstOrCreate(
            ['title' => 'Asesmen Sumatif Semester Ganjil'],
            [
                'jenis' => 'pengumuman',
                'tanggal' => now()->addWeeks(2)->toDateString(),
                'lokasi' => null,
                'penanggung_jawab' => 'Wakamad Kurikulum',
                'isi' => 'Asesmen sumatif semester ganjil akan berlangsung dua pekan lagi. Siswa diharapkan mempersiapkan diri.',
                'target' => 'publik',
                'tampil_mulai' => now()->toDateString(),
                'status' => 'aktif',
                'created_by' => $editor->id,
            ]
        );
    }
}
