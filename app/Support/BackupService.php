<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;

class BackupService
{
    private string $backupDisk = 'backups';

    public function __construct()
    {
        $this->ensureDirectories();
    }

    /**
     * Create a database backup using mysqldump.
     */
    public function createDatabaseBackup(): string
    {
        $filename = 'db/'.now()->format('Y-m-d_His').'.sql';
        $path = $this->getFullPath($filename);

        $host = config('database.connections.mysql.host', '127.0.0.1');
        $port = config('database.connections.mysql.port', '3306');
        $database = config('database.connections.mysql.database');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');

        $command = sprintf(
            'mysqldump -h %s -P %s -u %s %s > %s 2>&1',
            escapeshellarg($host),
            escapeshellarg($port),
            escapeshellarg($username),
            escapeshellarg($database),
            escapeshellarg($path),
        );

        // Set password via env for mysqldump
        $env = ['MYSQL_PWD' => $password];

        $result = Process::env($env)->run($command);

        if ($result->failed()) {
            throw new \RuntimeException('Gagal membuat backup database: '.$result->errorOutput());
        }

        // Verify file was created and is not empty
        if (! File::exists($path) || File::size($path) === 0) {
            throw new \RuntimeException('File backup database kosong atau tidak ditemukan.');
        }

        return $filename;
    }

    /**
     * Create a file storage backup (zip of public + private storage).
     */
    public function createFileBackup(): string
    {
        $filename = 'files/'.now()->format('Y-m-d_His').'.zip';
        $path = $this->getFullPath($filename);

        $publicPath = storage_path('app/public');
        $privatePath = storage_path('app/private');

        $tempZip = tempnam(sys_get_temp_dir(), 'backup_');
        $zip = new \ZipArchive;

        if ($zip->open($tempZip, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException('Gagal membuat file zip.');
        }

        // Add public files
        if (File::isDirectory($publicPath)) {
            $this->addDirectoryToZip($zip, $publicPath, 'public');
        }

        // Add private files
        if (File::isDirectory($privatePath)) {
            $this->addDirectoryToZip($zip, $privatePath, 'private');
        }

        $zip->close();

        // Move temp file to backup location
        File::move($tempZip, $path);

        return $filename;
    }

    /**
     * List all backups with metadata.
     */
    public function listBackups(): Collection
    {
        $backups = collect();

        $directories = ['db', 'files', 'uploads'];

        foreach ($directories as $dir) {
            $dirPath = $this->getFullPath($dir);
            if (! File::isDirectory($dirPath)) {
                continue;
            }

            $files = File::files($dirPath);
            foreach ($files as $file) {
                $backups->push([
                    'name' => $dir.'/'.$file->getFilename(),
                    'type' => match ($dir) {
                        'db' => 'Database',
                        'files' => 'File Storage',
                        'uploads' => 'Upload',
                        default => 'Lainnya',
                    },
                    'size' => $file->getSize(),
                    'size_human' => $this->formatBytes($file->getSize()),
                    'date' => $file->getMTime(),
                    'date_human' => date('d M Y, H:i', $file->getMTime()),
                    'extension' => $file->getExtension(),
                ]);
            }
        }

        return $backups->sortByDesc('date')->values();
    }

    /**
     * Get the full path for a backup file.
     */
    public function getFullPath(string $filename): string
    {
        return storage_path('app/backups/'.$filename);
    }

    /**
     * Validate that a backup file exists and is safe to access.
     */
    public function validateBackup(string $filename): string
    {
        // Prevent directory traversal
        if (str_contains($filename, '..') || str_contains($filename, '/./') || str_starts_with($filename, '/')) {
            throw new \InvalidArgumentException('Nama file backup tidak valid.');
        }

        $path = $this->getFullPath($filename);

        if (! File::exists($path)) {
            throw new \RuntimeException("File backup '{$filename}' tidak ditemukan.");
        }

        return $path;
    }

    /**
     * Delete a backup file.
     */
    public function deleteBackup(string $filename): void
    {
        $path = $this->validateBackup($filename);
        File::delete($path);
    }

    /**
     * Restore database from a backup file.
     */
    public function restoreDatabase(string $filename): void
    {
        $path = $this->validateBackup($filename);

        if (pathinfo($path, PATHINFO_EXTENSION) !== 'sql') {
            throw new \RuntimeException('Hanya file .sql yang bisa di-restore ke database.');
        }

        $sql = file_get_contents($path);

        if (empty($sql)) {
            throw new \RuntimeException('File backup kosong.');
        }

        // Disable foreign key checks and drop all tables (except migrations)
        DB::unprepared('SET FOREIGN_KEY_CHECKS = 0');

        $tables = DB::select('SHOW TABLES');
        $dbName = DB::getDatabaseName();

        foreach ($tables as $table) {
            $tableName = $table->{'Tables_in_'.$dbName};
            if ($tableName !== 'migrations') {
                DB::unprepared("DROP TABLE IF EXISTS `{$tableName}`");
            }
        }

        DB::unprepared('SET FOREIGN_KEY_CHECKS = 1');

        // Execute the SQL dump
        DB::unprepared($sql);

        // Re-run migrations to ensure structure is up to date
        Artisan::call('migrate:force', ['--no-interaction' => true]);
    }

    /**
     * Upload a backup file.
     */
    public function uploadBackup(UploadedFile $file): string
    {
        $originalName = $file->getClientOriginalName();
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

        if (! in_array($extension, ['sql', 'zip'])) {
            throw new \InvalidArgumentException('Hanya file .sql atau .zip yang diperbolehkan.');
        }

        $filename = 'uploads/'.now()->format('Y-m-d_His').'_'.$originalName;
        $path = $this->getFullPath($filename);

        $file->move(dirname($path), basename($path));

        return $filename;
    }

    /**
     * Calculate total size of all backups.
     */
    public function getTotalSize(): string
    {
        $totalBytes = 0;
        $backups = $this->listBackups();

        foreach ($backups as $backup) {
            $totalBytes += $backup['size'];
        }

        return $this->formatBytes($totalBytes);
    }

    /**
     * Get total number of backups.
     */
    public function getCount(): int
    {
        return $this->listBackups()->count();
    }

    /**
     * Recursively add a directory to a zip archive.
     */
    private function addDirectoryToZip(\ZipArchive $zip, string $directory, string $prefix): void
    {
        $files = File::allFiles($directory);

        foreach ($files as $file) {
            $relativePath = $prefix.'/'.ltrim(str_replace($directory, '', $file->getPathname()), DIRECTORY_SEPARATOR);
            $zip->addFile($file->getPathname(), $relativePath);
        }
    }

    /**
     * Format bytes to human readable.
     */
    private function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= pow(1024, $pow);

        return round($bytes, $precision).' '.$units[$pow];
    }

    /**
     * Ensure backup directories exist.
     */
    private function ensureDirectories(): void
    {
        $base = storage_path('app/backups');

        foreach (['db', 'files', 'uploads'] as $dir) {
            $path = $base.'/'.$dir;
            if (! File::isDirectory($path)) {
                File::makeDirectory($path, 0755, true);
            }
        }
    }
}
