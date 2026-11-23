<?php

namespace Database\Seeders;

use App\Models\PublicPage;
use Illuminate\Database\Seeder;

class MisnuPublicPageSeeder extends Seeder
{
    public function run(): void
    {
        $pages = [
            'beranda' => [
                'title' => 'MI Nahdlatul Ulama Palangka Raya',
                'subtitle' => 'Beriman, Berilmu, Berakhlakul Karimah, Cinta Agama dan Tanah Air',
                'summary' => 'Madrasah Ibtidaiyah di jantung Kota Palangka Raya yang menumbuhkan generasi muslim berilmu, berakhlak, dan berwawasan global sejak 1975.',
                'body' => "MI Nahdlatul 'Ulama Palangka Raya adalah madrasah ibtidaiyah swasta di bawah Yayasan Al-Ma'arif NU Kalimantan Tengah.\n\nKami menyelenggarakan pendidikan dasar Islam dengan Kurikulum Merdeka yang diperkaya Kurikulum Berbasis Cinta (KBC), penguatan Aswaja, Tahfidz Al-Qur'an, Praktek Pengamalan Ibadah (PPI), literasi, dan kecakapan digital.\n\nVisi kami: terwujudnya generasi muslim yang beriman, berilmu, berakhlakul karimah, berprestasi, cinta agama, cinta tanah air, peduli sesama dan lingkungan, serta berwawasan global.",
                'meta' => 'MI Nahdlatul Ulama Palangka Raya — madrasah ibtidaiyah Islam di Pahandut, Kota Palangka Raya, Kalimantan Tengah.',
            ],
            'profil' => [
                'title' => 'Profil Madrasah',
                'subtitle' => "Madrasah Ibtidaiyah Nahdlatul 'Ulama Palangka Raya",
                'summary' => 'Identitas, akreditasi, peserta didik, pendidik, dan program unggulan MI NU Palangka Raya.',
                'body' => "Nama: Madrasah Ibtidaiyah Nahdlatul 'Ulama Palangka Raya\nNSM: 111262710006\nNPSN: 60722774\nJenjang: MI (kelas I–VI)\nStatus: Swasta\nNaungan: Kementerian Agama RI dan Yayasan Al-Ma'arif NU Kalimantan Tengah\nAlamat: Jl. Dr. Murjani No. 81, Kel. Pahandut, Kec. Pahandut, Kota Palangka Raya, Kalimantan Tengah 73111\nTelepon: (0536) 3222395\nAkreditasi: A\nTahun berdiri: 1975\n\nPeserta didik tahun ajaran 2026/2027: 521 siswa dalam 18 rombongan belajar (kelas I–VI).\nPendidik dan tenaga kependidikan: 31 orang, dengan kualifikasi S1/S2 dan komitmen pengembangan profesional berkelanjutan.\n\nProgram unggulan:\n1. Tahfidz Al-Qur'an juz 30\n2. Praktek Pengamalan Ibadah (PPI) dan munaqasah kelas VI\n3. Pembiasaan keagamaan: Salat Dhuha dan Zuhur berjamaah, istighasah, salawat, Aqidatul Awam\n4. English Morning dan literasi–numerasi\n5. Penguatan Aswaja (tawassuth, tawazun, tasamuh, i'tidal)\n6. Ekstrakurikuler: Pramuka, tahfidz, tilawah, habsy, tari, LKBB, sains, dan pencak silat\n\nPrestasi: medali emas, perak, dan perunggu Olimpiade Sains Nasional (KTON) 2024 bidang IPA dan Matematika; berbagai juara MTQ, kaligrafi, nasyid, hadrah, pramuka, dan tari tingkat kota hingga nasional.",
                'meta' => 'Profil MI Nahdlatul Ulama Palangka Raya: identitas, akreditasi A, 521 siswa, 18 rombel, dan program unggulan.',
            ],
            'sambutan' => [
                'title' => 'Sambutan Kepala Madrasah',
                'subtitle' => 'Erna, S.Ag — Kepala MI Nahdlatul Ulama Palangka Raya',
                'summary' => 'Sambutan Kepala Madrasah atas dedikasi keluarga besar MI NU Palangka Raya.',
                'body' => "Assalamu'alaikum warahmatullahi wabarakatuh.\n\nSegala puji bagi Allah SWT, salawat dan salam semoga selalu tercurah kepada Rasulullah SAW.\n\nDokumen dan layanan digital madrasah ini kami hadirkan sebagai wujud dedikasi keluarga besar MI Nahdlatul 'Ulama Palangka Raya. Di dalamnya tergambar visi, misi, program, nilai, kurikulum, ekstrakurikuler, dan prestasi yang kami rawat bersama.\n\nKami menyadari bahwa madrasah yang kuat lahir dari kolaborasi: guru yang teladan, orang tua yang mendampingi, komite dan yayasan yang menguatkan, serta masyarakat yang memberi ruang tumbuh. Karena itu kami membuka pintu masukan dan saran untuk perbaikan layanan.\n\nSemoga ikhtiar ini menjadi amal ilmiah dan ilmu yang amaliah, serta mengantar peserta didik menjadi generasi yang beriman, berilmu, dan berakhlakul karimah.\n\nWassalamu'alaikum warahmatullahi wabarakatuh.\n\nErna, S.Ag\nKepala Madrasah",
                'meta' => 'Sambutan Kepala Madrasah MI Nahdlatul Ulama Palangka Raya, Erna, S.Ag.',
            ],
            'sejarah' => [
                'title' => 'Sejarah Madrasah',
                'subtitle' => 'Berdiri 12 Desember 1975 di bawah Yayasan Pendidikan Ma’arif NU',
                'summary' => 'Perjalanan MI NU Palangka Raya dari madrasah terdaftar hingga terakreditasi A.',
                'body' => "Jam'iyah Nahdlatul Ulama Kalimantan Tengah berdiri pada 1958, disusul kepengurusan Wilayah Ma'arif NU tingkat I Kalimantan Tengah setahun kemudian.\n\nMI Nahdlatul 'Ulama Palangka Raya didirikan pada 12 Desember 1975 oleh Yayasan Pendidikan Ma'arif. Para pendiri: K.H. Muhammad Majedi (alm.), H. Jantarmin H.M. (alm.), H. Sufyan Sayuti, dan H. Umariyah (alm.).\n\nStatus madrasah berkembang dari terdaftar (surat No. 6/36/1970), diakui (sertifikat MP-6/5/PP.001/136/1994 tanggal 4 Oktober 1994), hingga terakreditasi pada 2007 dengan nilai 89,07 (A) dan akreditasi kedua pada 2013 dengan nilai 93 (A).\n\nKepala madrasah periode 1970–1999 diangkat oleh yayasan; 1999–2017 oleh Kementerian Agama Kota Palangka Raya; dan sejak 1 April 2018 di-SK-kan oleh Yayasan Al-Ma'arif NU Kalimantan Tengah.\n\nYayasan yang sama juga menaungi SLTP NU dan SMU NU di Palangka Raya. Hingga kini MI NU terus tumbuh sebagai madrasah rujukan di Kelurahan Pahandut, tak jauh dari Pasar Besar Palangka Raya.",
                'meta' => 'Sejarah MI Nahdlatul Ulama Palangka Raya: berdiri 12 Desember 1975, terakreditasi A.',
            ],
            'visi-misi' => [
                'title' => 'Visi dan Misi',
                'subtitle' => 'Visi 2026/2027 dan lima belas misi madrasah',
                'summary' => 'Visi dan misi MI NU Palangka Raya dalam bingkai Kurikulum Berbasis Cinta dan nilai Aswaja.',
                'body' => "VISI\n\n“Terwujudnya Generasi Muslim yang Beriman, Berilmu, Berakhlakul Karimah, Berprestasi, Cinta Agama, Cinta Tanah Air, Peduli Sesama dan Lingkungan, serta Berwawasan Global.”\n\nMISI\n\n1. Menanamkan iman dan takwa melalui pembelajaran dan pembiasaan ibadah.\n2. Menumbuhkan cinta kepada Rasulullah melalui salawat, sirah, dan keteladanan.\n3. Menyelenggarakan pembelajaran berkualitas yang aktif, kreatif, inovatif, kontekstual, dan berpusat pada murid.\n4. Membangun budaya literasi dan numerasi serta berpikir kritis.\n5. Mengembangkan potensi akademik dan nonakademik melalui intrakurikuler, kokurikuler, dan ekstrakurikuler.\n6. Membentuk akhlakul karimah melalui keteladanan dan budaya positif.\n7. Menumbuhkan cinta sesama: empati, toleransi, dan gotong royong.\n8. Menumbuhkan cinta lingkungan melalui pembiasaan dan keteladanan.\n9. Menanamkan cinta tanah air, Pancasila, dan budaya lokal Kalimantan Tengah.\n10. Mengembangkan kecakapan digital yang bijak dan bertanggung jawab.\n11. Menguatkan nilai Aswaja: tawassuth, tawazun, tasamuh, dan i'tidal.\n12. Mewujudkan lingkungan madrasah yang aman, inklusif, ramah anak, dan bebas kekerasan.\n13. Meningkatkan kompetensi dan profesionalisme pendidik melalui pengembangan keprofesian berkelanjutan, supervisi, dan refleksi.\n14. Membangun kemitraan harmonis dengan orang tua, komite, yayasan, dan masyarakat.\n15. Mewujudkan tata kelola madrasah yang efektif, transparan, dan akuntabel.",
                'meta' => 'Visi dan misi MI Nahdlatul Ulama Palangka Raya tahun ajaran 2026/2027.',
            ],
            'kontak' => [
                'title' => 'Kontak & Lokasi',
                'subtitle' => 'Jl. Dr. Murjani No. 81, Pahandut, Kota Palangka Raya',
                'summary' => 'Alamat, telepon, dan jam layanan MI NU Palangka Raya.',
                'body' => "Alamat: Jl. Dr. Murjani No. 81, Kel. Pahandut, Kec. Pahandut, Kota Palangka Raya, Kalimantan Tengah 73111\nTelepon: (0536) 3222395\nSitus: https://misnupalangkaraya.sch.id\n\nJam layanan tata usaha: Senin–Sabtu, 07.00–13.00 WIB.\n\nLokasi madrasah berada sekitar 300 meter dari Pasar Besar Palangka Raya, mudah dijangkau dengan kendaraan pribadi maupun angkutan kota.",
                'meta' => 'Kontak dan lokasi MI Nahdlatul Ulama Palangka Raya, Jl. Dr. Murjani No. 81 Pahandut.',
            ],
            'kebijakan-privasi' => [
                'title' => 'Kebijakan Privasi',
                'subtitle' => 'Perlindungan data peserta didik, orang tua, dan pegawai',
                'summary' => 'Kebijakan privasi layanan digital MI NU Palangka Raya.',
                'body' => "MI Nahdlatul 'Ulama Palangka Raya menghormati privasi seluruh peserta didik, orang tua/wali, dan pegawai.\n\nData yang dikumpulkan melalui layanan digital madrasah digunakan hanya untuk keperluan pendidikan, administrasi, dan pelaporan resmi. Data tidak dijual atau diserahkan kepada pihak ketiga tanpa dasar hukum atau persetujuan.\n\nAkses data dibatasi berdasarkan peran pengguna. Dokumen sensitif (berkas konseling, dokumen keluarga, dan arsip kepegawaian) disimpan secara terbatas dan hanya dapat diakses pihak berwenang.\n\nPermintaan koreksi, pembaruan, atau penghapusan data dapat diajukan kepada tata usaha madrasah. Kami akan menindaklanjuti sesuai ketentuan peraturan perundang-undangan yang berlaku.",
                'meta' => 'Kebijakan privasi MI Nahdlatul Ulama Palangka Raya.',
            ],
        ];

        foreach ($pages as $slug => $page) {
            PublicPage::updateOrCreate(
                ['slug' => $slug],
                [
                    'title' => $page['title'],
                    'subtitle' => $page['subtitle'] ?? null,
                    'summary' => $page['summary'] ?? null,
                    'body' => $page['body'],
                    'meta_description' => $page['meta'] ?? null,
                    'status' => PublicPage::PUBLISHED,
                ]
            );
        }
    }
}
