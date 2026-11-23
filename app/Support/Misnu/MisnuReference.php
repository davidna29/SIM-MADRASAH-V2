<?php

namespace App\Support\Misnu;

/**
 * Data referensi MIS Nahdlatul Ulama Palangka Raya.
 *
 * Sumber: dokumen "DATA PENDIDIK & TENAGA KEPENDIDIKAN" (gambar), "Struktur
 * Organisasi" (gambar), "Struktur Tugas Tambahan" (gambar), serta
 * "KM MI NU TP. 2026-2027 REVISI2" untuk struktur mata pelajaran.
 */
final class MisnuReference
{
    public const NAME = "Madrasah Ibtidaiyah Nahdlatul 'Ulama Palangka Raya";

    public const SHORT_NAME = "MI Nahdlatul 'Ulama";

    public const NSM = '111262710006';

    public const NPSN = '60722774';

    public const TAGLINE = 'Ilmu Amaliah, Amal Ilmiah, Akhlakul Karimah';

    /**
     * @return array<string, string>
     */
    public static function settings(): array
    {
        return [
            'madrasah_name' => self::NAME,
            'madrasah_nsm' => self::NSM,
            'madrasah_npsn' => self::NPSN,
            'madrasah_jenjang' => 'MI',
            'madrasah_status' => 'swasta',
            'madrasah_tahun_berdiri' => '1975',
            'madrasah_tagline' => self::TAGLINE,
            'madrasah_jalan' => 'Jl. Dr. Murjani No. 81',
            'madrasah_desa' => 'Kel. Pahandut',
            'madrasah_kecamatan' => 'Kec. Pahandut',
            'madrasah_kabupaten' => 'Kota Palangka Raya',
            'madrasah_provinsi' => 'Kalimantan Tengah',
            'madrasah_kode_pos' => '73111',
            'madrasah_latitude' => '-2.208400',
            'madrasah_longitude' => '113.916700',
            'madrasah_phone' => '(0536) 3222395',
            'madrasah_email' => 'misnupalangkaraya@gmail.com',
            'madrasah_website' => 'https://misnupalangkaraya.sch.id',
            'madrasah_whatsapp' => '',
            'madrasah_jam_layanan' => 'Senin–Sabtu, 07.00–13.00 WIB',
            'madrasah_sk_pendirian' => 'No. 6/36/1970',
            'madrasah_tgl_sk_pendirian' => '1975-12-12',
            'madrasah_sk_operasional' => 'MP-6/5/PP.001/136/1994',
            'madrasah_akreditasi' => 'terakreditasi',
            'madrasah_nilai_akreditasi' => 'A',
            'madrasah_naungan' => "Kementerian Agama & Yayasan Al-Ma'arif NU Kalimantan Tengah",
            'ppdb_kuota' => '77',
            'ppdb_kontak_telepon' => '(0536) 3222395',
            'mutasi_kontak_telepon' => '(0536) 3222395',
        ];
    }

    /**
     * Pegawai mengikuti gambar "Data Pendidik & Tenaga Kependidikan".
     *
     * @return list<array{name: string, nip: ?string, nuptk: ?string, gender: string, birth_place: string, birth_date: string, task: string, status: string, education_level: string, major: ?string, tmt: ?string, position: string, unit: string}>
     */
    public static function employees(): array
    {
        return [
            ['name' => 'ERNA, S.Ag.', 'nip' => '197512122007012044', 'nuptk' => null, 'gender' => 'P', 'birth_place' => 'Babai', 'birth_date' => '1975-12-12', 'task' => 'Kepala Madrasah', 'status' => 'pns', 'education_level' => 'S1', 'major' => 'Tarbiyah', 'tmt' => '2007-01-01', 'position' => 'KEPALA_MADRASAH', 'unit' => 'PIMPINAN'],
            ['name' => 'ESTI MUNIARTINI, A.Ma, S.Pd.', 'nip' => '197406071999032001', 'nuptk' => null, 'gender' => 'P', 'birth_place' => 'Sruweng', 'birth_date' => '1974-07-07', 'task' => 'Guru Kelas', 'status' => 'pns', 'education_level' => 'S1', 'major' => 'Tarbiyah', 'tmt' => '2016-08-01', 'position' => 'GURU_MAPEL', 'unit' => 'GURU'],
            ['name' => 'SRI HARYATI, S.Pd.', 'nip' => '196804081999032004', 'nuptk' => null, 'gender' => 'P', 'birth_place' => 'Pekalongan', 'birth_date' => '1968-04-08', 'task' => 'Guru Kelas', 'status' => 'pns', 'education_level' => 'S1', 'major' => 'PGSD', 'tmt' => '2020-08-03', 'position' => 'GURU_MAPEL', 'unit' => 'GURU'],
            ['name' => 'ANWARI ANAS, A.Ma, S.Pd.I.', 'nip' => '197706212007011017', 'nuptk' => null, 'gender' => 'L', 'birth_place' => 'Negara', 'birth_date' => '1977-06-21', 'task' => 'Guru Bidang Studi', 'status' => 'pns', 'education_level' => 'S1', 'major' => 'Tarbiyah/PAI', 'tmt' => '2024-01-01', 'position' => 'GURU_MAPEL', 'unit' => 'GURU'],
            ['name' => 'IBRAHIM, S.Pd.I, M.Pd.', 'nip' => '197810051999031003', 'nuptk' => null, 'gender' => 'L', 'birth_place' => 'Kuala Kapuas', 'birth_date' => '1978-05-10', 'task' => 'Guru Bidang Studi', 'status' => 'pns', 'education_level' => 'S2', 'major' => 'Tarbiyah/PAI', 'tmt' => '2023-01-16', 'position' => 'GURU_MAPEL', 'unit' => 'GURU'],
            ['name' => 'MELY ASTUTI, S.Pd.', 'nip' => '197905062007102008', 'nuptk' => null, 'gender' => 'P', 'birth_place' => 'Pontianak', 'birth_date' => '1979-05-06', 'task' => 'Guru Kelas', 'status' => 'pns', 'education_level' => 'S1', 'major' => 'FKIP/PGSD', 'tmt' => '2022-02-01', 'position' => 'GURU_MAPEL', 'unit' => 'GURU'],
            ['name' => 'SAIDAH, S.Ag.', 'nip' => '196801121997032002', 'nuptk' => null, 'gender' => 'P', 'birth_place' => 'Kab Banjar', 'birth_date' => '1968-01-12', 'task' => 'Guru Kelas', 'status' => 'pns', 'education_level' => 'S1', 'major' => 'Tarbiyah', 'tmt' => '1997-03-01', 'position' => 'GURU_MAPEL', 'unit' => 'GURU'],
            ['name' => 'MAHMUDAH, M.Pd.', 'nip' => '198507122005012001', 'nuptk' => null, 'gender' => 'P', 'birth_place' => 'Anjir Serapat', 'birth_date' => '1985-07-12', 'task' => 'Guru Kelas', 'status' => 'pns', 'education_level' => 'S2', 'major' => 'Tarbiyah/PAI', 'tmt' => '2022-02-01', 'position' => 'GURU_MAPEL', 'unit' => 'GURU'],
            ['name' => 'SUWARNI, S.Pd', 'nip' => '197106122007012034', 'nuptk' => null, 'gender' => 'P', 'birth_place' => 'Barabai', 'birth_date' => '1971-06-12', 'task' => 'Guru Kelas', 'status' => 'pns', 'education_level' => 'S1', 'major' => 'FKIP', 'tmt' => '2020-09-24', 'position' => 'GURU_MAPEL', 'unit' => 'GURU'],
            ['name' => 'NIDA RAHMAWATI, S.Pd.', 'nip' => '198304252007102001', 'nuptk' => null, 'gender' => 'P', 'birth_place' => 'Palangka Raya', 'birth_date' => '1983-04-25', 'task' => 'Guru Kelas', 'status' => 'pns', 'education_level' => 'S1', 'major' => 'PGSD', 'tmt' => '2007-10-01', 'position' => 'GURU_MAPEL', 'unit' => 'GURU'],
            ['name' => 'SITI ISTIKHAROH', 'nip' => null, 'nuptk' => '4154747650300013', 'gender' => 'P', 'birth_place' => 'Madiun', 'birth_date' => '1969-08-22', 'task' => 'Guru Kelas', 'status' => 'honor', 'education_level' => 'S1', 'major' => 'FKIP/PBI', 'tmt' => '2005-07-16', 'position' => 'BENDAHARA', 'unit' => 'TU'],
            ['name' => 'ABDUL SANI, S.Ag.', 'nip' => null, 'nuptk' => '5538748652200002', 'gender' => 'L', 'birth_place' => 'Pandamaan', 'birth_date' => '1970-02-06', 'task' => 'Guru Kelas', 'status' => 'honor', 'education_level' => 'S1', 'major' => 'Tarbiyah', 'tmt' => '1997-07-17', 'position' => 'WAKAMAD_KURIKULUM', 'unit' => 'KURIKULUM'],
            ['name' => 'H. MUHAMMAD MAHLAN', 'nip' => null, 'nuptk' => '3936743643200002', 'gender' => 'L', 'birth_place' => 'Lok Gabang', 'birth_date' => '1965-05-25', 'task' => 'Guru Bidang Studi', 'status' => 'honor', 'education_level' => 'SMA', 'major' => null, 'tmt' => '1986-02-01', 'position' => 'WAKAMAD_SARPRAS', 'unit' => 'SARPRAS'],
            ['name' => 'RAHMAN, S.Pd.I, M.Pd.', 'nip' => null, 'nuptk' => '1559763663200003', 'gender' => 'L', 'birth_place' => 'Baru', 'birth_date' => '1985-12-27', 'task' => 'Guru Kelas', 'status' => 'honor', 'education_level' => 'S2', 'major' => 'Tarbiyah', 'tmt' => '2022-01-01', 'position' => 'GURU_MAPEL', 'unit' => 'GURU'],
            ['name' => 'RUSHANA SULISTIANI, S.Pd.', 'nip' => null, 'nuptk' => '6746760661300162', 'gender' => 'P', 'birth_place' => 'Kuala Kapuas', 'birth_date' => '1982-04-14', 'task' => 'Guru Kelas', 'status' => 'honor', 'education_level' => 'S1', 'major' => 'Tarbiyah', 'tmt' => '2013-12-30', 'position' => 'GURU_MAPEL', 'unit' => 'GURU'],
            ['name' => 'AHMAD BAIHAKI, S.Pd.I.', 'nip' => null, 'nuptk' => '6135764665200013', 'gender' => 'L', 'birth_place' => 'Palangka Raya', 'birth_date' => '1986-08-03', 'task' => 'Guru Bidang Studi', 'status' => 'honor', 'education_level' => 'S1', 'major' => 'Tarbiyah', 'tmt' => '2007-11-12', 'position' => 'WAKAMAD_HUMAS', 'unit' => 'HUMAS'],
            ['name' => 'MELIA AYU LINDASARI, S.Pd.I.', 'nip' => null, 'nuptk' => null, 'gender' => 'P', 'birth_place' => 'Palangka Raya', 'birth_date' => '1988-07-13', 'task' => 'Guru Bidang Studi', 'status' => 'honor', 'education_level' => 'S1', 'major' => 'Tarbiyah/TBI', 'tmt' => '2010-01-02', 'position' => 'GURU_MAPEL', 'unit' => 'HUMAS'],
            ['name' => 'WIWIN ELPIRA, S.Pd.', 'nip' => null, 'nuptk' => null, 'gender' => 'P', 'birth_place' => 'Baru', 'birth_date' => '1989-11-04', 'task' => 'Guru Kelas', 'status' => 'honor', 'education_level' => 'S1', 'major' => 'FKIP/PBSI', 'tmt' => '2019-01-01', 'position' => 'WAKAMAD_KESISWAAN', 'unit' => 'KESISWAAN'],
            ['name' => 'SALAMAT, S.Pd.I.', 'nip' => null, 'nuptk' => '5938767668200012', 'gender' => 'L', 'birth_place' => 'Sei Lunuk', 'birth_date' => '1989-06-06', 'task' => 'Guru Bidang Studi', 'status' => 'honor', 'education_level' => 'S1', 'major' => 'Tarbiyah', 'tmt' => '2020-01-01', 'position' => 'GURU_MAPEL', 'unit' => 'GURU'],
            ['name' => 'FELIA DESINTIAWATI, S.Pd.', 'nip' => null, 'nuptk' => null, 'gender' => 'P', 'birth_place' => 'Banjarmasin', 'birth_date' => '1999-12-15', 'task' => 'Guru Bidang Studi', 'status' => 'honor', 'education_level' => 'S1', 'major' => 'Tarbiyah/PAI', 'tmt' => '2022-01-01', 'position' => 'GURU_MAPEL', 'unit' => 'GURU'],
            ['name' => 'RASIDAH, S.Pd.', 'nip' => null, 'nuptk' => null, 'gender' => 'P', 'birth_place' => 'Tamban Raya', 'birth_date' => '1998-12-19', 'task' => 'Guru Kelas', 'status' => 'honor', 'education_level' => 'S1', 'major' => 'Tarbiyah/TBG', 'tmt' => '2022-01-01', 'position' => 'GURU_MAPEL', 'unit' => 'GURU'],
            ['name' => 'NURUL AZIZAH, S.Pd.', 'nip' => null, 'nuptk' => null, 'gender' => 'P', 'birth_place' => 'Palangka Raya', 'birth_date' => '1999-06-02', 'task' => 'Guru Kelas', 'status' => 'honor', 'education_level' => 'S1', 'major' => 'PGSD', 'tmt' => '2022-01-01', 'position' => 'GURU_MAPEL', 'unit' => 'GURU'],
            ['name' => 'AHMADI MAULANA, S.Pd.', 'nip' => null, 'nuptk' => null, 'gender' => 'L', 'birth_place' => 'Palangka Raya', 'birth_date' => '1998-05-06', 'task' => 'Guru Kelas', 'status' => 'honor', 'education_level' => 'S1', 'major' => 'PGMI', 'tmt' => '2021-07-31', 'position' => 'GURU_MAPEL', 'unit' => 'GURU'],
            ['name' => 'ALWAFA AMRULLAH, S.Pd.', 'nip' => null, 'nuptk' => null, 'gender' => 'L', 'birth_place' => 'Palangka Raya', 'birth_date' => '2000-05-14', 'task' => 'Guru Bidang Studi', 'status' => 'honor', 'education_level' => 'S1', 'major' => 'Tarbiyah', 'tmt' => '2024-07-01', 'position' => 'GURU_MAPEL', 'unit' => 'GURU'],
            ['name' => 'MUHAMMAD NOOR RAHMAN, S.Pd.', 'nip' => null, 'nuptk' => null, 'gender' => 'L', 'birth_place' => 'Barabai', 'birth_date' => '1971-06-12', 'task' => 'Guru Bidang Studi', 'status' => 'honor', 'education_level' => 'S1', 'major' => 'PGMI/PBA', 'tmt' => '2024-07-01', 'position' => 'GURU_MAPEL', 'unit' => 'GURU'],
            ['name' => 'FITRIANI, S.Pd', 'nip' => null, 'nuptk' => null, 'gender' => 'P', 'birth_place' => 'Palangka Raya', 'birth_date' => '2002-12-01', 'task' => 'Guru Kelas', 'status' => 'honor', 'education_level' => 'S1', 'major' => 'Tarbiyah', 'tmt' => '2024-07-01', 'position' => 'GURU_MAPEL', 'unit' => 'GURU'],
            ['name' => 'AKHMAD HULAIFI, S.Pd', 'nip' => null, 'nuptk' => null, 'gender' => 'L', 'birth_place' => 'Samba Katung', 'birth_date' => '2000-06-23', 'task' => 'Guru Kelas', 'status' => 'honor', 'education_level' => 'S1', 'major' => 'FTIK/PGMI', 'tmt' => '2023-07-01', 'position' => 'GURU_MAPEL', 'unit' => 'GURU'],
            ['name' => 'MUHAMMAD ARSYAD, A.Ma', 'nip' => null, 'nuptk' => null, 'gender' => 'L', 'birth_place' => 'Palangka Raya', 'birth_date' => '1989-11-04', 'task' => 'Satpam/Jaga Malam', 'status' => 'honor', 'education_level' => 'D3', 'major' => 'PGSD', 'tmt' => '2008-07-16', 'position' => 'SATPAM', 'unit' => 'SARPRAS'],
            ['name' => 'M. DEDE MAULANA, S.Pd', 'nip' => null, 'nuptk' => null, 'gender' => 'L', 'birth_place' => 'Palangka Raya', 'birth_date' => '1996-07-16', 'task' => 'Operator & Kepala Tata Usaha', 'status' => 'honor', 'education_level' => 'S1', 'major' => 'BDP/AGRO', 'tmt' => '2023-07-01', 'position' => 'TATA_USAHA', 'unit' => 'TU'],
            ['name' => 'YULIA AMELIA', 'nip' => null, 'nuptk' => null, 'gender' => 'P', 'birth_place' => 'Palangka Raya', 'birth_date' => '2004-06-19', 'task' => 'Guru Kelas', 'status' => 'honor', 'education_level' => 'SMA', 'major' => null, 'tmt' => '2026-07-13', 'position' => 'GURU_MAPEL', 'unit' => 'GURU'],
            ['name' => 'ZAHRATUNNISA, S.Pd', 'nip' => null, 'nuptk' => null, 'gender' => 'P', 'birth_place' => 'Palangka Raya', 'birth_date' => '1999-07-16', 'task' => 'Tata Usaha', 'status' => 'honor', 'education_level' => 'S1', 'major' => 'Tarbiyah/PAI', 'tmt' => '2026-07-13', 'position' => 'TATA_USAHA', 'unit' => 'TU'],
        ];
    }

    /**
     * Wali kelas mengikuti gambar "Struktur Organisasi".
     *
     * @return array<string, string> rombel => nama pegawai
     */
    public static function homerooms(): array
    {
        return [
            'I-A' => 'SUWARNI, S.Pd',
            'I-B' => 'NURUL AZIZAH, S.Pd.',
            'I-C' => 'YULIA AMELIA',
            'II-A' => 'MELY ASTUTI, S.Pd.',
            'II-B' => 'SAIDAH, S.Ag.',
            'II-C' => 'SRI HARYATI, S.Pd.',
            'III-A' => 'RAHMAN, S.Pd.I, M.Pd.',
            'III-B' => 'AHMADI MAULANA, S.Pd.',
            'III-C' => 'AKHMAD HULAIFI, S.Pd',
            'III-D' => 'WIWIN ELPIRA, S.Pd.',
            'IV-A' => 'RASIDAH, S.Pd.',
            'IV-B' => 'MAHMUDAH, M.Pd.',
            'IV-C' => 'FITRIANI, S.Pd',
            'V-A' => 'NIDA RAHMAWATI, S.Pd.',
            'V-B' => 'ESTI MUNIARTINI, A.Ma, S.Pd.',
            'V-C' => 'RUSHANA SULISTIANI, S.Pd.',
            'VI-A' => 'ABDUL SANI, S.Ag.',
            'VI-B' => 'SITI ISTIKHAROH',
        ];
    }

    /**
     * Struktur mata pelajaran + JP/minggu per tingkat (KMA 1503/2025).
     *
     * @return list<array{code: string, name: string, category: string, jp: array<string, int>}>
     */
    public static function subjects(): array
    {
        return [
            ['code' => 'QUR', 'name' => "Al-Qur'an Hadis", 'category' => 'keagamaan', 'jp' => ['I' => 2, 'II' => 2, 'III' => 2, 'IV' => 2, 'V' => 2, 'VI' => 2]],
            ['code' => 'AQA', 'name' => 'Akidah Akhlak', 'category' => 'keagamaan', 'jp' => ['I' => 2, 'II' => 2, 'III' => 2, 'IV' => 2, 'V' => 2, 'VI' => 2]],
            ['code' => 'FQH', 'name' => 'Fikih', 'category' => 'keagamaan', 'jp' => ['I' => 2, 'II' => 2, 'III' => 2, 'IV' => 2, 'V' => 2, 'VI' => 2]],
            ['code' => 'SKI', 'name' => 'Sejarah Kebudayaan Islam', 'category' => 'keagamaan', 'jp' => ['III' => 2, 'IV' => 2, 'V' => 2, 'VI' => 2]],
            ['code' => 'BAR', 'name' => 'Bahasa Arab', 'category' => 'keagamaan', 'jp' => ['I' => 2, 'II' => 2, 'III' => 2, 'IV' => 2, 'V' => 2, 'VI' => 2]],
            ['code' => 'PPK', 'name' => 'Pendidikan Pancasila', 'category' => 'umum', 'jp' => ['I' => 4, 'II' => 4, 'III' => 4, 'IV' => 4, 'V' => 4, 'VI' => 4]],
            ['code' => 'BIN', 'name' => 'Bahasa Indonesia', 'category' => 'umum', 'jp' => ['I' => 7, 'II' => 8, 'III' => 6, 'IV' => 6, 'V' => 6, 'VI' => 6]],
            ['code' => 'MAT', 'name' => 'Matematika', 'category' => 'umum', 'jp' => ['I' => 4, 'II' => 4, 'III' => 5, 'IV' => 5, 'V' => 5, 'VI' => 5]],
            ['code' => 'IPAS', 'name' => 'Ilmu Pengetahuan Alam dan Sosial (IPAS)', 'category' => 'umum', 'jp' => ['III' => 5, 'IV' => 5, 'V' => 5, 'VI' => 5]],
            ['code' => 'PJOK', 'name' => 'Pendidikan Jasmani, Olahraga, dan Kesehatan', 'category' => 'umum', 'jp' => ['I' => 3, 'II' => 3, 'III' => 3, 'IV' => 3, 'V' => 3, 'VI' => 3]],
            ['code' => 'SBDP', 'name' => 'Seni Budaya dan Prakarya', 'category' => 'umum', 'jp' => ['I' => 3, 'II' => 3, 'III' => 3, 'IV' => 3, 'V' => 3, 'VI' => 3]],
            ['code' => 'BIG', 'name' => 'Bahasa Inggris', 'category' => 'mulok', 'jp' => ['I' => 2, 'II' => 2, 'III' => 2, 'IV' => 2, 'V' => 2, 'VI' => 2]],
            ['code' => 'ASW', 'name' => 'Aswaja / Ke-NU-an', 'category' => 'mulok', 'jp' => ['IV' => 2, 'V' => 2, 'VI' => 2]],
        ];
    }

    /**
     * @return list<array{name: string, grade_level: string}>
     */
    public static function classGroups(): array
    {
        $groups = [];
        $distribution = ['I' => ['A', 'B', 'C'], 'II' => ['A', 'B', 'C'], 'III' => ['A', 'B', 'C', 'D'], 'IV' => ['A', 'B', 'C'], 'V' => ['A', 'B', 'C'], 'VI' => ['A', 'B']];

        foreach ($distribution as $grade => $suffixes) {
            foreach ($suffixes as $suffix) {
                $groups[] = ['name' => "{$grade}-{$suffix}", 'grade_level' => $grade];
            }
        }

        return $groups;
    }

    /**
     * Ruang & laboratorium mengikuti PROFIL 2026-2027 (luas & jumlah unit).
     *
     * @return list<array{code: string, name: string, type: string, building: string, floor: string, capacity: int, condition: string, employee: ?string, description: ?string}>
     */
    public static function rooms(): array
    {
        return [
            ['code' => 'R-001', 'name' => 'Kantor Kepala Madrasah', 'type' => 'ruangan', 'building' => 'Gedung Utama', 'floor' => 'Lantai 1', 'capacity' => 5, 'condition' => 'baik', 'employee' => 'ERNA', 'description' => 'Luas 12 m².'],
            ['code' => 'R-002', 'name' => 'Kantor Tata Usaha', 'type' => 'ruangan', 'building' => 'Gedung Utama', 'floor' => 'Lantai 1', 'capacity' => 8, 'condition' => 'baik', 'employee' => 'M. DEDE', 'description' => 'Luas 12 m².'],
            ['code' => 'R-003', 'name' => 'Ruang Guru', 'type' => 'ruangan', 'building' => 'Gedung Utama', 'floor' => 'Lantai 1', 'capacity' => 35, 'condition' => 'baik', 'employee' => 'ABDUL SANI', 'description' => 'Luas 84 m².'],
            ['code' => 'R-004', 'name' => 'Ruang Kelas (18 ruang)', 'type' => 'ruangan', 'building' => 'Gedung A & B', 'floor' => 'Lantai 1–2', 'capacity' => 600, 'condition' => 'baik', 'employee' => null, 'description' => 'Total luas 1.080 m² untuk 18 rombongan belajar.'],
            ['code' => 'R-005', 'name' => 'Ruang Perpustakaan', 'type' => 'ruangan', 'building' => 'Gedung Utama', 'floor' => 'Lantai 1', 'capacity' => 30, 'condition' => 'baik', 'employee' => 'MELY ASTUTI', 'description' => 'Luas 64 m².'],
            ['code' => 'R-006', 'name' => 'Musholla', 'type' => 'ruangan', 'building' => 'Gedung Utama', 'floor' => 'Lantai 1', 'capacity' => 80, 'condition' => 'baik', 'employee' => 'SALAMAT', 'description' => 'Luas 64 m²; 3 tempat ibadah.'],
            ['code' => 'R-007', 'name' => 'Ruang UKS', 'type' => 'ruangan', 'building' => 'Gedung Utama', 'floor' => 'Lantai 1', 'capacity' => 5, 'condition' => 'baik', 'employee' => 'SUWARNI', 'description' => 'Luas 16 m².'],
            ['code' => 'R-008', 'name' => 'Ruang Kesenian', 'type' => 'ruangan', 'building' => 'Gedung B', 'floor' => 'Lantai 1', 'capacity' => 30, 'condition' => 'baik', 'employee' => 'FELIA DESINTIAWATI', 'description' => 'Luas 64 m².'],
            ['code' => 'R-009', 'name' => 'Kantin Madrasah', 'type' => 'ruangan', 'building' => 'Gedung Utama', 'floor' => 'Lantai 1', 'capacity' => 20, 'condition' => 'baik', 'employee' => null, 'description' => 'Luas 12 m².'],
            ['code' => 'R-010', 'name' => 'Area Parkir', 'type' => 'ruangan', 'building' => 'Halaman', 'floor' => '-', 'capacity' => 50, 'condition' => 'baik', 'employee' => 'MUHAMMAD ARSYAD', 'description' => 'Luas 35 m².'],
            ['code' => 'R-011', 'name' => 'Lapangan Olahraga', 'type' => 'ruangan', 'building' => 'Halaman', 'floor' => '-', 'capacity' => 200, 'condition' => 'baik', 'employee' => 'AKHMAD HULAIFI', 'description' => 'Luas 82 m².'],
            ['code' => 'R-012', 'name' => 'Toilet Siswa & Guru', 'type' => 'ruangan', 'building' => 'Gedung Utama', 'floor' => 'Lantai 1', 'capacity' => 20, 'condition' => 'baik', 'employee' => 'MUHAMMAD ARSYAD', 'description' => 'Luas 41 m²; 7 unit (4 siswa + 2 guru + 1 cadangan).'],
            ['code' => 'R-013', 'name' => 'Gudang & Ruang Kebersihan', 'type' => 'ruangan', 'building' => 'Gedung B', 'floor' => 'Lantai 1', 'capacity' => 4, 'condition' => 'baik', 'employee' => 'MUHAMMAD ARSYAD', 'description' => 'Penyimpanan sarpras dan alat kebersihan.'],
            ['code' => 'LAB-001', 'name' => 'Laboratorium Komputer', 'type' => 'laboratorium', 'building' => 'Gedung B', 'floor' => 'Lantai 2', 'capacity' => 30, 'condition' => 'baik', 'employee' => 'M. DEDE', 'description' => 'Luas 64 m²; 20 komputer.'],
            ['code' => 'LAB-002', 'name' => 'Laboratorium Perpustakaan Digital', 'type' => 'laboratorium', 'building' => 'Gedung Utama', 'floor' => 'Lantai 1', 'capacity' => 20, 'condition' => 'baik', 'employee' => 'MELY ASTUTI', 'description' => 'Pojok baca digital dan katalog daring.'],
            ['code' => 'LAB-003', 'name' => 'Laboratorium Kesenian & Hadrah', 'type' => 'laboratorium', 'building' => 'Gedung B', 'floor' => 'Lantai 1', 'capacity' => 30, 'condition' => 'baik', 'employee' => 'SAIDAH', 'description' => 'Latihan hadrah, tilawah, dan seni tari.'],
        ];
    }

    /**
     * Ekstrakurikuler mengikuti KM BAB III (jenis, waktu, pembina).
     *
     * @return list<array{name: string, description: string, pembina: ?string, hari: string, waktu: string, lokasi: string}>
     */
    public static function extracurriculars(): array
    {
        return [
            ['name' => "Tahfidz Al-Qur'an", 'description' => 'Hafalan juz 30 — pembinaan harian bersama wali kelas.', 'pembina' => 'ABDUL SANI', 'hari' => 'senin', 'waktu' => '06:30', 'lokasi' => 'Ruang kelas masing-masing'],
            ['name' => 'Tilawah Al-Qur’an', 'description' => 'Makhraj, tartil, dan tajwid.', 'pembina' => 'AHMAD BAIHAKI', 'hari' => 'jumat', 'waktu' => '15:00', 'lokasi' => 'Musholla'],
            ['name' => 'Pramuka', 'description' => 'PBB, semaphore, dan simpul — kepramukaan wajib.', 'pembina' => 'AKHMAD HULAIFI', 'hari' => 'sabtu', 'waktu' => '14:00', 'lokasi' => 'Lapangan utama'],
            ['name' => 'Tata Upacara (LKBB)', 'description' => 'Petugas upacara dan pengibaran bendera.', 'pembina' => 'AHMADI MAULANA', 'hari' => 'selasa', 'waktu' => '14:00', 'lokasi' => 'Lapangan utama'],
            ['name' => 'Seni Habsy', 'description' => 'Rebana, vokal, dan kekompakan.', 'pembina' => 'SAIDAH', 'hari' => 'selasa', 'waktu' => '14:30', 'lokasi' => 'Ruang kesenian'],
            ['name' => 'Seni Tari', 'description' => 'Gerak dasar dan tari daerah Dayak/Banjar.', 'pembina' => 'FELIA DESINTIAWATI', 'hari' => 'rabu', 'waktu' => '15:00', 'lokasi' => 'Ruang kesenian'],
            ['name' => 'Sains IPAS', 'description' => 'Persiapan olimpiade dan eksperimen.', 'pembina' => 'RASIDAH', 'hari' => 'jumat', 'waktu' => '13:00', 'lokasi' => 'Ruang kelas'],
            ['name' => 'Sains Matematika', 'description' => 'Olimpiade dan pemecahan masalah.', 'pembina' => 'RUSHANA SULISTIANI', 'hari' => 'jumat', 'waktu' => '13:00', 'lokasi' => 'Ruang kelas'],
            ['name' => 'Pencak Silat', 'description' => 'Dasar bela diri dan kebugaran.', 'pembina' => 'AKHMAD HULAIFI', 'hari' => 'kamis', 'waktu' => '14:30', 'lokasi' => 'Lapangan utama'],
        ];
    }
}
