<?php

namespace App\Console\Commands;

use App\Models\AcademicYear;
use App\Support\Misnu\StudentImporter;
use App\Support\Misnu\StudentWorkbookReader;
use Illuminate\Console\Command;
use Throwable;

class MisnuImportStudents extends Command
{
    protected $signature = 'misnu:import-siswa
        {--file= : Path workbook EMIS (.xlsx); default memakai berkas sumber MIS NU}
        {--rombel=VI-A,VI-B : Daftar rombel dipisah koma, atau "all"}
        {--dry-run : Validasi & hitung saja tanpa menulis database}
        {--report= : Path laporan CSV (default storage/app/reports)}
        {--limit= : Batasi jumlah baris yang diproses}';

    protected $description = 'Impor data siswa dari workbook EMIS MIS NU ke people/students/enrollments/guardians.';

    private const DEFAULT_WORKBOOK = '/Users/admin/Downloads/TENTANG MIS Nahdlatul Ulama Palangka Raya/DATABASE SISWA EMIS MIS NU (2026-2027).xlsx';

    public function handle(): int
    {
        $file = (string) ($this->option('file') ?: env('MISNU_XLSX', self::DEFAULT_WORKBOOK));

        if (! is_file($file)) {
            $this->error("Workbook tidak ditemukan: {$file}");

            return self::FAILURE;
        }

        $tahun = AcademicYear::where('is_active', true)->first();

        if (! $tahun) {
            $this->error('Tahun ajaran aktif belum ada. Jalankan seeder akademik terlebih dahulu.');

            return self::FAILURE;
        }

        $rombelOption = trim((string) $this->option('rombel'));
        $rombelFilter = $rombelOption === '' || strtolower($rombelOption) === 'all'
            ? []
            : array_values(array_filter(array_map(fn ($value) => strtoupper(trim($value)), explode(',', $rombelOption))));

        $reportPath = $this->option('report') ?: storage_path('app/reports/misnu-siswa-'.now()->format('Ymd-His').'.csv');
        $limit = $this->option('limit') !== null ? (int) $this->option('limit') : null;

        $this->info("Membaca workbook: {$file}");

        try {
            $rows = (new StudentWorkbookReader)->rows($file);
        } catch (Throwable $exception) {
            $this->error('Gagal membaca workbook: '.$exception->getMessage());

            return self::FAILURE;
        }

        $this->line('Baris data ditemukan: '.count($rows));
        $this->line('Rombel: '.($rombelFilter === [] ? 'semua' : implode(', ', $rombelFilter)));
        $this->line('Tahun ajaran: '.$tahun->name);

        if ($this->option('dry-run')) {
            $this->warn('Mode dry-run — tidak ada perubahan database.');
        }

        $importer = new StudentImporter($tahun->id);
        $result = $importer->import($rows, $rombelFilter, (bool) $this->option('dry-run'), $reportPath, $limit);

        $this->newLine();
        $this->table(['Metrik', 'Nilai'], [
            ['Total baris workbook', $result['summary']['total_baris']],
            ['Diproses', $result['summary']['diproses']],
            ['Dibuat', $result['summary']['dibuat']],
            ['Diperbarui', $result['summary']['diperbarui']],
            ['Dilewati (di luar filter/kosong)', $result['summary']['dilewati']],
            ['Wali dibuat', $result['summary']['wali_dibuat']],
            ['Wali ditautkan', $result['summary']['wali_ditautkan']],
            ['Masalah dicatat', $result['summary']['masalah']],
        ]);

        if ($result['per_class'] !== []) {
            $this->newLine();
            $this->table(
                ['Rombel', 'Siswa'],
                collect($result['per_class'])->sortKeys()->map(fn ($count, $class) => [$class, $count])->values()->all()
            );
        }

        if ($result['issues'] !== []) {
            $this->newLine();
            $this->warn('Contoh masalah (20 pertama):');
            $this->table(
                ['Baris', 'Rombel', 'Nama', 'Level', 'Pesan'],
                collect($result['issues'])->take(20)->map(fn ($issue) => [
                    $issue['row'], $issue['rombel'], $issue['nama'], $issue['level'], $issue['pesan'],
                ])->all()
            );
        }

        if ($result['report']) {
            $this->newLine();
            $this->info('Laporan lengkap: '.$result['report']);
        }

        return self::SUCCESS;
    }
}
