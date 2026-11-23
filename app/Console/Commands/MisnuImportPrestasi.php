<?php

namespace App\Console\Commands;

use App\Models\Achievement;
use App\Models\Employee;
use App\Models\Student;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MisnuImportPrestasi extends Command
{
    protected $signature = 'misnu:import-prestasi
        {--file= : CSV hasil ekstraksi rekapitulasi prestasi}
        {--dry-run : Hitung dan cocokkan tanpa menulis database}';

    protected $description = 'Memetakan riwayat prestasi MIS NU ke siswa aktif berdasarkan nama.';

    private const DEFAULT_FILE = 'storage/app/reports/prestasi-misnu.csv';

    private int $matched = 0;

    private int $created = 0;

    private int $updated = 0;

    /** @var list<array{nama: string, tahun: string, kegiatan: string, alasan: string}> */
    private array $unmatched = [];

    public function handle(): int
    {
        $file = (string) $this->option('file');
        $file = $file !== '' ? $file : base_path(self::DEFAULT_FILE);

        if (! is_file($file)) {
            $this->error("CSV tidak ditemukan: {$file}");

            return self::FAILURE;
        }

        $handle = fopen($file, 'rb');
        $header = fgetcsv($handle);
        $header[0] = preg_replace('/^\xEF\xBB\xBF/', '', (string) ($header[0] ?? ''));
        $header = array_map(fn ($value) => strtolower(trim((string) $value)), $header);

        $students = Student::with('person')
            ->whereHas('enrollments', fn ($query) => $query->where('status', 'aktif'))
            ->get()
            ->keyBy(fn (Student $student) => $this->normalizeName($student->displayName()));

        $employeeNames = Employee::with('person')->get()
            ->map(fn (Employee $employee) => $this->normalizeName($employee->person?->name ?? ''))
            ->filter()
            ->flip();

        $adminId = User::where('username', 'admin')->value('id');
        $dryRun = (bool) $this->option('dry-run');
        $rows = [];

        while (($line = fgetcsv($handle)) !== false) {
            if (count($line) === 1 && trim((string) $line[0]) === '') {
                continue;
            }

            $data = array_combine($header, array_pad($line, count($header), null)) ?: [];
            $rows[] = $data;
        }

        fclose($handle);

        $callback = function () use ($rows, $students, $employeeNames, $adminId, $dryRun) {
            foreach ($rows as $data) {
                $rawName = trim((string) ($data['nama_siswa'] ?? ''));
                $year = $this->year((string) ($data['tahun'] ?? ''));
                $activity = trim((string) ($data['kegiatan'] ?? ''));

                if ($rawName === '' || $activity === '') {
                    $this->unmatched[] = ['nama' => $rawName, 'tahun' => (string) $year, 'kegiatan' => $activity, 'alasan' => 'nama/kegiatan kosong'];

                    continue;
                }

                $name = $this->normalizeName($rawName);

                if ($this->isNotPerson($name) || $employeeNames->has($name)) {
                    $this->unmatched[] = ['nama' => $rawName, 'tahun' => (string) $year, 'kegiatan' => $activity, 'alasan' => 'bukan nama siswa (kelompok/guru)'];

                    continue;
                }

                $student = $students->get($name);

                if (! $student) {
                    $this->unmatched[] = ['nama' => $rawName, 'tahun' => (string) $year, 'kegiatan' => $activity, 'alasan' => 'siswa tidak ada di data aktif'];

                    continue;
                }

                $this->matched++;

                $attributes = [
                    'student_id' => $student->id,
                    'jenis' => $this->jenis($activity),
                    'nama_kegiatan' => Str::limit($activity, 150, ''),
                    'tingkat' => $this->tingkat((string) ($data['tingkat'] ?? '')),
                    'penyelenggara' => Str::limit(trim((string) ($data['penyelenggara'] ?? '')), 100, '') ?: null,
                    'tanggal' => null,
                    'peringkat' => Str::limit(trim((string) ($data['peringkat'] ?? '')), 50, '') ?: null,
                    'pembimbing' => null,
                    'status_verifikasi' => 'terverifikasi',
                    'status_publikasi' => 'internal',
                    'created_by' => $adminId,
                ];

                if ($dryRun) {
                    $this->created++;

                    continue;
                }

                $achievement = Achievement::firstOrNew([
                    'student_id' => $student->id,
                    'nama_kegiatan' => $attributes['nama_kegiatan'],
                ]);

                if ($achievement->exists) {
                    $this->updated++;
                } else {
                    $this->created++;
                }

                $achievement->fill($attributes);
                $achievement->save();
            }
        };

        if ($dryRun) {
            $callback();
        } else {
            DB::transaction($callback);
        }

        $this->table(['Metrik', 'Nilai'], [
            ['Baris CSV', count($rows)],
            ['Cocok ke siswa aktif', $this->matched],
            ['Dibuat', $this->created],
            ['Diperbarui', $this->updated],
            ['Tidak cocok', count($this->unmatched)],
        ]);

        if ($this->unmatched !== []) {
            $report = storage_path('app/reports/prestasi-unmatched.csv');
            $handle = fopen($report, 'wb');
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, ['nama', 'tahun', 'kegiatan', 'alasan']);

            foreach ($this->unmatched as $row) {
                fputcsv($handle, [$row['nama'], $row['tahun'], $row['kegiatan'], $row['alasan']]);
            }

            fclose($handle);
            $this->info('Daftar tidak cocok: '.$report);
        }

        return self::SUCCESS;
    }

    private function normalizeName(string $name): string
    {
        $name = preg_replace('/\([^)]*\)/', '', $name) ?? $name;
        $name = preg_replace('/[^A-Za-z0-9 ]/', ' ', $name) ?? $name;
        $name = preg_replace('/\s+/', ' ', $name) ?? $name;

        return Str::lower(trim($name));
    }

    private function isNotPerson(string $normalizedName): bool
    {
        foreach (['kelompok', 'group', 'tim ', 'humas', 'guru ', 'siswa misnu', 'misnu', 'pa', 'timp'] as $keyword) {
            if (str_contains($normalizedName, $keyword)) {
                return true;
            }
        }

        return $normalizedName === '';
    }

    private function year(string $value): ?string
    {
        if (preg_match('/(\d{4})/', $value, $matches) === 1) {
            return $matches[1];
        }

        return null;
    }

    private function jenis(string $activity): string
    {
        $text = Str::lower($activity);

        foreach (['olimpiade', 'sains', 'matematika', 'ipa', 'cerdas cermat', 'ksm', 'osn', 'akademik'] as $keyword) {
            if (str_contains($text, $keyword)) {
                return 'akademik';
            }
        }

        return 'nonakademik';
    }

    private function tingkat(string $value): string
    {
        $text = Str::lower($value);

        return match (true) {
            str_contains($text, 'internasional') => 'internasional',
            str_contains($text, 'nasional') => 'nasional',
            str_contains($text, 'provinsi') => 'provinsi',
            str_contains($text, 'kota'), str_contains($text, 'kabupaten'), str_contains($text, 'kab.') => 'kabupaten',
            str_contains($text, 'kecamatan') => 'kecamatan',
            default => 'sekolah',
        };
    }
}
