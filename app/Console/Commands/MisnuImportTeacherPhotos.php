<?php

namespace App\Console\Commands;

use App\Models\Employee;
use App\Support\Images\ImageOptimizer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MisnuImportTeacherPhotos extends Command
{
    protected $signature = 'misnu:import-foto-guru
        {--source= : Folder foto guru (default: folder "foto guru" MIS NU)}
        {--dry-run : Tampilkan pemetaan tanpa menulis berkas/database}';

    protected $description = 'Memetakan foto guru 1:1 ke data pegawai dan menyimpannya di storage publik.';

    private const DEFAULT_SOURCE = '/Users/admin/Downloads/TENTANG MIS Nahdlatul Ulama Palangka Raya/foto guru';

    /** @var array<string, string> */
    private const ALIASES = [
        'erna' => 'ERNA',
        'esti' => 'ESTI MUNIARTINI',
        'sri haryati' => 'SRI HARYATI',
        'anwari anas' => 'ANWARI ANAS',
        'ibrahim' => 'IBRAHIM',
        'mely astuti' => 'MELY ASTUTI',
        'saidah' => 'SAIDAH',
        'mahmudah' => 'MAHMUDAH',
        'suwarni' => 'SUWARNI',
        'nida' => 'NIDA RAHMAWATI',
        'siti istikharoh' => 'SITI ISTIKHAROH',
        'abdul sani' => 'ABDUL SANI',
        'mahlan' => 'H. MUHAMMAD MAHLAN',
        'rahman' => 'RAHMAN, S.Pd.I',
        'rushana' => 'RUSHANA SULISTIANI',
        'baihaki' => 'AHMAD BAIHAKI',
        'meliya ayu' => 'MELIA AYU LINDASARI',
        'wiwin' => 'WIWIN ELPIRA',
        'salamat' => 'SALAMAT',
        'felia' => 'FELIA DESINTIAWATI',
        'rasidah' => 'RASIDAH',
        'nurul azizah' => 'NURUL AZIZAH',
        'ahmadi maulana' => 'AHMADI MAULANA',
        'alwafa' => 'ALWAFA AMRULLAH',
        'm nor rahman' => 'MUHAMMAD NOOR RAHMAN',
        'fitri' => 'FITRIANI',
        'hulaifi' => 'AKHMAD HULAIFI',
        'arsyad' => 'MUHAMMAD ARSYAD',
        'dede maulana' => 'M. DEDE MAULANA',
        'yulia amelia' => 'YULIA AMELIA',
        'zahra' => 'ZAHRATUNNISA',
    ];

    public function handle(): int
    {
        $source = (string) ($this->option('source') ?: self::DEFAULT_SOURCE);
        $dryRun = (bool) $this->option('dry-run');

        if (! is_dir($source)) {
            $this->error("Folder tidak ditemukan: {$source}");

            return self::FAILURE;
        }

        $files = collect(File::files($source))->merge(
            is_dir($source.'/tambahan') ? File::files($source.'/tambahan') : []
        );

        $rows = [];
        $matched = 0;
        $unmatched = [];

        foreach ($files as $file) {
            if (! ImageOptimizer::isImage($file->getPathname())) {
                continue;
            }

            $key = trim(Str::lower($file->getFilenameWithoutExtension()));
            $needle = self::ALIASES[$key] ?? null;

            if (! $needle) {
                $unmatched[] = $file->getFilename();

                continue;
            }

            $employee = Employee::query()
                ->whereHas('person', fn ($query) => $query->where('name', 'like', $needle.'%'))
                ->first();

            if (! $employee) {
                $unmatched[] = $file->getFilename().' (pegawai "'.$needle.'" tidak ditemukan)';

                continue;
            }

            $destination = 'pegawai/'.$employee->id.'.jpg';
            $absolute = storage_path('app/public/'.$destination);

            if (! $dryRun) {
                ImageOptimizer::saveSquare($file->getPathname(), $absolute, 700, 82);
                $employee->update(['photo_path' => $destination]);
            }

            $matched++;
            $rows[] = [$employee->person?->name, $file->getFilename(), $destination];
        }

        $this->table(['Pegawai', 'Berkas sumber', 'Disimpan'], $rows);
        $this->info("Cocok: {$matched} pegawai".($dryRun ? ' (dry-run)' : ''));

        $withoutPhoto = Employee::query()
            ->whereNull('photo_path')
            ->whereNull('deleted_at')
            ->with('person:id,name')
            ->get()
            ->pluck('person.name')
            ->filter()
            ->values()
            ->all();

        if ($withoutPhoto !== []) {
            $this->warn('Belum punya foto: '.implode(', ', $withoutPhoto));
        }

        if ($unmatched !== []) {
            $this->warn('Tidak dikenali: '.implode(', ', $unmatched));
        }

        return self::SUCCESS;
    }
}
