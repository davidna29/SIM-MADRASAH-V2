<?php

namespace App\Services;

use App\Models\AcademicYear;
use App\Models\Employee;
use App\Models\PpdbRegistration;
use App\Models\PpiExamGroup;
use App\Models\PpiExamParticipant;
use App\Models\PpiExamPeriod;
use App\Models\PpiExamRoom;
use App\Models\Setting;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\HttpException;

class PpiExamService
{
    public const ADMIN_ROLES = ['super_admin', 'wakamad_kurikulum'];

    public const DEFAULT_TEKS_MC = <<<'TXT'
TEKS PEMBAWA ACARA

1. PEMBUKAAN

Assalamu'alaikum Warahmatullahi wabarakatuh.

Alhamdulillah Asholatu wassalamu ala sayidina maulana muhammadin wa 'ala alihi
shohbihi ajma'in, amma ba'du

Sidang Asesmen PPI atas nama {{NAMA_SISWA}} Bin/Binti {{NAMA_AYAH}}

Secara resmi di buka dengan ucapan Basmalah

Kami persilahkan kepada Penguji Pertama untuk mengawali pertanyaan

2. PEMBACAAN BERITA ACARA (terlampir)

3. PENUTUP

Sebelum kita akhiri sidang Asesmen Praktek Pengamalan Ibadah

kami mohon kepada penguji {{NAMA_PENGUJI_PENUTUP}} untuk memberikan pesan/nasehat.

Kepada bapak/ibu {{NAMA_PENGUJI_PENUTUP}} dipersilahkan.

Demikian sidang asesmen PPI pada hari ini

apabila kami segenap penguji ada khilaf dalam ucapan dan perbuatan mohon di maafkan

wallahul muwafiq ila aqwamit thoriq, Wassalamu'alaikum Wr.Wb.
TXT;

    public const DEFAULT_TEKS_BA = <<<'TXT'
BERITA ACARA
ASESMEN PRAKTEK PENGAMALAN IBADAH (PPI)
SISWA KELAS VI
{{NAMA_MADRASAH}}
TAHUN PELAJARAN {{TAHUN_AJARAN}}

Dengan mengucap Bismillahirrahmanirrahim

Pada hari {{HARI}} tanggal {{TANGGAL}}
pukul {{JAM}} WIB. telah terlaksana Asesmen Praktek Pengamalan Ibadah (PPI)
atas nama {{NAMA_SISWA}}
bin/binti {{NAMA_AYAH}}

dengan Tim Penguji yang terdiri dari :

Penguji I  : {{NAMA_PENGUJI_1}}
Penguji II : {{NAMA_PENGUJI_2}}
Penguji III: {{NAMA_PENGUJI_3}}

Dari hasil beberapa pertanyaan dari tim penguji ananda
memperoleh sejumlah nilai sebagai berikut :

Penguji I   nilai rata-rata yang diperoleh  {{RATA_P1}}
Penguji II  nilai rata-rata yang diperoleh  {{RATA_P2}}
Penguji III nilai rata-rata yang diperoleh  {{RATA_P3}}

Dari ketiga penguji ditambah nilai hafalan surah-surah Yasin, Waqi'ah
dan surah-surah pendek sebelumnya (dihitung sesuai bobot masing-masing).
Maka ananda memperoleh nilai rata-rata akhir adalah {{NILAI_AKHIR}}
dan di nyatakan {{STATUS_LULUS}}
pada sidang Asesmen PPI ini dengan predikat {{PREDIKAT}}
dan dengan deskripsi {{DESKRIPSI}}

Di tetapkan di {{KOTA}} pada tanggal {{TANGGAL}}

{{TANDA_TANGAN}}
TXT;

    public const DAYS_ID = [
        1 => 'Senin',
        2 => 'Selasa',
        3 => 'Rabu',
        4 => 'Kamis',
        5 => 'Jumat',
        6 => 'Sabtu',
        7 => 'Minggu',
    ];

    /**
     * Struktur aspek penilaian default (§6 Data Default — docs/UJIAN-PPI-PLAN.md).
     * [kode, nama, penguji_urutan, urutan, [items]]
     */
    public const DEFAULT_ASPECTS = [
        ['1', 'Wudhu', 1, 1, ['Niat Wudhu', 'Praktik Wudhu', "Do'a Sesudah Wudhu", 'Niat Tayamum']],
        ['2', 'Praktik Shalat', 1, 2, [
            'Lafaz azan', 'Lafaz iqamah', "Do'a sesudah azan", "Do'a sesudah iqamah",
            'Niat shalat subuh', 'Niat shalat zuhur', 'Niat shalat asar', 'Niat shalat magrib', 'Niat shalat isya',
            "Do'a iftitah", 'Al-fatihah', "Bacaan ruku'", "Bacaan i'tidal", "Do'a Qunut",
            'Bacaan sujud', 'Bacaan duduk antara 2 sujud', 'Bacaan tahiyat awal', 'Bacaan tahiyat akhir',
            'Salam', "Do'a sebelum salam", 'Wirid / Dzikir Pendek bada shalat', "Do'a selamat",
        ]],
        ['3', "Tilawatil Qur'an", 2, 3, ['Makhorijul huruf', 'Hukum Bacaan', 'Kelancaran']],
        ['4', 'Shalat Jenazah', 2, 4, [
            'Niat salat Jenazah untuk laki-laki Dewasa', 'Niat salat Jenazah untuk Perempuan Dewasa',
            'Niat Salat Jenazah untuk Anak laki-laki', 'Niat Salat Jenazah Untuk Anak Perempuan',
            'Bacaan Takbir Pertama', 'Bacaan Takbir Kedua', 'Bacaan Takbir Ketiga', 'Bacaan Takbir Keempat',
        ]],
        ['5', 'Hafalan Hadis', 2, 5, ['Hadis tentang amal Shaleh', 'Hadis tentang keutamaan memberi']],
        ['6', "Do'a-Do'a Harian", 3, 6, [
            "Do'a Senandung Al-Qur'an", "Do'a mau Belajar", "Do'a Mau makan", "Do'a sesudah makan",
            "Do'a masuk WC", "Do'a keluar WC", "Do'a Masuk rumah", "Do'a Keluar rumah",
            "Do'a Mau tidur", "Do'a bangun tidur", "Do'a masuk mesjid", "Do'a Keluar mesjid",
            "Do'a untuk Kedua Orang Tua", 'Niat Puasa Ramadhan', "Do'a Berbuka Puasa", "Do'a bercermin",
            "Do'a Naik Kendaraan Darat", "Do'a Naik Kendaraan Air",
        ]],
        ['7', 'Pengetahuan Agama', 3, 7, ['Rukun islam', 'Rukun iman', 'Rukun wudhu', 'Rukun shalat', 'Shalat Sunnah']],
    ];

    /**
     * Materi setoran hafalan default (§6 Data Default — docs/UJIAN-PPI-PLAN.md).
     */
    public const DEFAULT_HAFALAN = [
        'Yaasin', "Al-Waqi'ah", 'Ad-Dhuha', 'Al-Insyirah', 'At-Tiin', 'Al-`Alaq', 'Al-Qadar', 'Al-Bayyinah',
        'Al-Zalzalah', 'Al-`Adiyat', "Al-Qari'ah", 'At-Takasur', 'Al-`Ashr', 'Al-Humazah', 'Al-Fiil',
        'Al-Quraisy', 'Al-Ma`un', 'Al-Kausar', 'Al-Kafirun', 'An-Nasr', 'Al-Lahab', 'Al-Ikhlas', 'Al-Falaq', 'An-Naas',
    ];

    /**
     * Isi struktur aspek & materi default ke periode baru.
     * Idempotent — tidak duplikat jika data sudah ada.
     */
    public function seedDefaults(PpiExamPeriod $period): void
    {
        if ($period->categories()->exists() || $period->hafalanMateri()->exists()) {
            return;
        }

        foreach (self::DEFAULT_ASPECTS as [$kode, $nama, $penguji, $urutan, $items]) {
            $category = $period->categories()->create([
                'kode' => $kode,
                'nama' => $nama,
                'penguji_urutan' => $penguji,
                'urutan' => $urutan,
            ]);

            foreach ($items as $i => $itemName) {
                $category->aspects()->create([
                    'kode' => (string) ($i + 1),
                    'nama' => $itemName,
                    'urutan' => $i + 1,
                ]);
            }
        }

        foreach (self::DEFAULT_HAFALAN as $i => $nama) {
            $period->hafalanMateri()->create([
                'nama' => $nama,
                'urutan' => $i + 1,
            ]);
        }
    }

    /**
     * Employee milik user yang sedang login (akun guru dari Data Guru/Kepegawaian).
     */
    public function employeeOfUser(User $user): ?Employee
    {
        return Employee::where('user_id', $user->id)->first();
    }

    public function isAdmin(User $user): bool
    {
        return in_array($user->role, self::ADMIN_ROLES, true);
    }

    /**
     * Ruang ujian yang bisa diakses user (admin = semua, guru = ruang tempat jadi penguji).
     */
    public function examinerRooms(User $user, PpiExamPeriod $period): Collection
    {
        if ($this->isAdmin($user)) {
            return $period->rooms()->with('examiners.employee.person')->get();
        }

        $employee = $this->employeeOfUser($user);
        if (! $employee) {
            return collect();
        }

        $roomIds = $period->examiners()->where('employee_id', $employee->id)->pluck('exam_room_id');

        return PpiExamRoom::whereIn('id', $roomIds)
            ->with('examiners.employee.person')
            ->get();
    }

    /**
     * Grup setoran yang bisa diakses user (admin = semua, guru = grup tempat jadi pembimbing).
     */
    public function pembimbingGroups(User $user, PpiExamPeriod $period): Collection
    {
        if ($this->isAdmin($user)) {
            return $period->groups()->with('pembimbing.person')->get();
        }

        $employee = $this->employeeOfUser($user);
        if (! $employee) {
            return collect();
        }

        return PpiExamGroup::where('exam_period_id', $period->id)
            ->where('pembimbing_employee_id', $employee->id)
            ->with('pembimbing.person')
            ->get();
    }

    /**
     * Nomor penguji (1/2/3) dari user guru pada periode tertentu, atau null.
     */
    public function examinerUrutan(User $user, PpiExamPeriod $period): ?int
    {
        $employee = $this->employeeOfUser($user);
        if (! $employee) {
            return null;
        }

        $examiner = $period->examiners()->where('employee_id', $employee->id)->first();

        return $examiner?->urutan;
    }

    /**
     * Konfigurasi periode terkunci otomatis saat berlangsung; hanya Super Admin
     * yang bisa membuka kunci eksplisit (config_locked_at = null).
     */
    public function assertConfigEditable(PpiExamPeriod $period, ?User $user = null): void
    {
        $user ??= Auth::user();
        $editableStatus = in_array($period->status, [PpiExamPeriod::DRAFT, PpiExamPeriod::SETUP], true);

        if ($editableStatus) {
            return;
        }

        $superAdminUnlocked = $user && $user->role === 'super_admin' && ! $period->isLocked();

        if (! $superAdminUnlocked) {
            throw new HttpException(403, 'Konfigurasi periode ini terkunci. Ubah status ke Setup/Draft, atau buka kunci oleh Super Admin.');
        }
    }

    /**
     * Input nilai guru (setoran/ujian) hanya saat periode berlangsung.
     */
    public function assertInputOpen(PpiExamPeriod $period): void
    {
        if ($period->status !== PpiExamPeriod::BERLANGSUNG) {
            throw new HttpException(403, 'Input nilai hanya bisa dilakukan saat periode berstatus Berlangsung.');
        }
    }

    /**
     * Siswa kelas VI pada tahun ajaran (enrollment aktif).
     */
    public function studentsKelasVI(AcademicYear $year): Collection
    {
        return Student::query()
            ->whereHas('enrollments', function ($q) use ($year) {
                $q->where('academic_year_id', $year->id)
                    ->where('status', 'aktif')
                    ->whereHas('classGroup', fn ($c) => $c->where('grade_level', 'VI'));
            })
            ->with(['enrollments' => function ($q) use ($year) {
                $q->where('academic_year_id', $year->id)
                    ->where('status', 'aktif')
                    ->with('classGroup');
            }])
            ->orderBy('name')
            ->get();
    }

    /**
     * Daftar guru (employee) aktif untuk pilihan penguji/pembimbing.
     */
    public function employees(): Collection
    {
        return Employee::query()
            ->where('status', 'aktif')
            ->with('person')
            ->leftJoin('people', 'people.id', '=', 'employees.person_id')
            ->orderBy('people.name')
            ->select('employees.*')
            ->get();
    }

    /**
     * Nama lengkap employee dari data person.
     */
    public function employeeName(Employee $employee): string
    {
        return $employee->person?->name ?? 'Guru #'.$employee->id;
    }

    /**
     * Format angka ratarata (mis. 87.5).
     */
    public static function fmt(float|int|null $nilai): string
    {
        if ($nilai === null) {
            return '–';
        }

        return rtrim(rtrim(number_format((float) $nilai, 2, '.', ''), '0'), '.');
    }

    /**
     * Render template teks dengan placeholder {{KEY}}.
     */
    public function renderTemplate(?string $template, array $vars): string
    {
        $body = $template ?: '';

        foreach ($vars as $key => $value) {
            $body = str_replace('{{'.$key.'}}', (string) ($value ?? ''), $body);
        }

        return $body;
    }

    /**
     * Nama ayah siswa: dari PPDB (father_name) → guardian pertama → '—'.
     */
    public function fatherName(Student $student): string
    {
        $nik = $student->person?->nik;

        if ($nik) {
            $registration = PpdbRegistration::where('nik', $nik)->first();
            if ($registration?->father_name) {
                return $registration->father_name;
            }
        }

        $guardian = $student->guardians()->first();

        return $guardian?->name ?? '—';
    }

    /**
     * NISN best-effort dari PPDB (cocok NIK person), fallback '—'.
     */
    public function nisnOf(Student $student): string
    {
        $nik = $student->person?->nik;

        if ($nik) {
            $registration = PpdbRegistration::where('nik', $nik)->first();
            if ($registration?->nisn) {
                return $registration->nisn;
            }
        }

        return '—';
    }

    public function kop(): array
    {
        return app(PembiasaanService::class)->kop();
    }

    /**
     * Data placeholder untuk teks MC & berita acara per peserta.
     */
    public function dokumenVars(PpiExamParticipant $participant, ?int $penutup = null): array
    {
        $period = $participant->period->load('academicYear');
        $examiners = $participant->room?->examiners()->with('employee.person')->get() ?? collect();
        $nama = fn (int $urutan) => $examiners->firstWhere('urutan', $urutan)?->employee?->person?->name ?? '—';

        $penutup = in_array($penutup, [1, 2, 3], true) ? $penutup : 3;

        $now = now(); // gunakan tanggal hari ini, bukan tanggal_ujian statis

        // Tabel rapi untuk nama penguji (3 kolom fixed-width, kompatibel DomPDF)
        $pengujiTable = '<table width="100%" style="margin-top:12px;border-collapse:collapse;text-align:center;font-size:12px;">'
            .'<tr>'
            .collect([[1, 'Penguji I'], [2, 'Penguji II'], [3, 'Penguji III']])
                ->map(fn ($p) => '<td style="width:33%;padding:0 8px;"><strong>'.$p[1].'</strong><br><br><br><br><br><br><span style="border-top:1px solid #111;display:block;padding-top:4px;">'.$nama($p[0]).'</span></td>')
                ->implode('')
            .'</tr></table>';

        return [
            'NAMA_MADRASAH' => Setting::get('madrasah_name', 'MADRASAH IBTIDAIYAH'),
            'TAHUN_AJARAN' => (string) ($period->academicYear?->name ?? ''),
            'HARI' => self::DAYS_ID[$now->dayOfWeekIso] ?? '',
            'TANGGAL' => $now->translatedFormat('d-m-Y'),
            'JAM' => Setting::get('ppi_ujian_jam', '08.00'),
            'KOTA' => Setting::get('madrasah_kabupaten', '—'),
            'NAMA_SISWA' => $participant->student?->name ?? '—',
            'NAMA_AYAH' => $participant->student ? $this->fatherName($participant->student) : '—',
            'TANDA_TANGAN' => $pengujiTable,
            'NAMA_PENGUJI_1' => $nama(1),
            'NAMA_PENGUJI_2' => $nama(2),
            'NAMA_PENGUJI_3' => $nama(3),
            'NAMA_PENGUJI_PENUTUP' => $nama($penutup),
            'RATA_P1' => self::fmt($participant->rata_p1),
            'RATA_P2' => self::fmt($participant->rata_p2),
            'RATA_P3' => self::fmt($participant->rata_p3),
            'NILAI_AKHIR' => self::fmt($participant->nilai_akhir),
            'STATUS_LULUS' => $participant->status_lulus === null ? '—' : ($participant->status_lulus ? 'LULUS' : 'TIDAK LULUS'),
            'PREDIKAT' => $participant->predicateScale?->predikat ?? '—',
            'DESKRIPSI' => $participant->predicateScale?->deskripsi ?? '—',
        ];
    }
}
