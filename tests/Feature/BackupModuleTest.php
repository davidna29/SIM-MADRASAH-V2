<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class BackupModuleTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $guru;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'super_admin']);
        $this->guru = User::factory()->create(['role' => 'guru']);

        // Ensure backup directories exist and are clean
        $base = storage_path('app/backups');
        foreach (['db', 'files', 'uploads'] as $dir) {
            $path = $base.'/'.$dir;
            if (File::isDirectory($path)) {
                File::cleanDirectory($path);
            } else {
                File::makeDirectory($path, 0755, true);
            }
        }
    }

    public function test_admin_can_access_index(): void
    {
        $this->actingAs($this->admin);
        $response = $this->get(route('backup.index'));
        $response->assertOk();
        $response->assertSee('Backup & Restore');
    }

    public function test_guru_cannot_access(): void
    {
        $this->actingAs($this->guru);
        $response = $this->get(route('backup.index'));
        $response->assertForbidden();
    }

    public function test_unauthenticated_cannot_access(): void
    {
        $response = $this->get(route('backup.index'));
        $response->assertRedirect('/login');
    }

    public function test_can_list_backups(): void
    {
        $this->actingAs($this->admin);

        // Create a test backup file
        $backupDir = storage_path('app/backups/db');
        File::put($backupDir.'/2026-01-01_120000.sql', '-- test backup');

        $response = $this->get(route('backup.index'));
        $response->assertOk();
        $response->assertSee('2026-01-01_120000.sql');
    }

    public function test_empty_state_shows_message(): void
    {
        $this->actingAs($this->admin);
        $response = $this->get(route('backup.index'));
        $response->assertOk();
        $response->assertSee('Belum ada backup');
    }

    public function test_can_download_backup(): void
    {
        $this->actingAs($this->admin);

        // Create a test backup file
        $backupDir = storage_path('app/backups/db');
        File::put($backupDir.'/test-backup.sql', 'CREATE TABLE test (id INT);');

        $response = $this->get(route('backup.download', 'db/test-backup.sql'));
        $response->assertOk();
        $response->assertHeader('Content-Disposition');
    }

    public function test_download_nonexistent_returns_error(): void
    {
        $this->actingAs($this->admin);
        $response = $this->get(route('backup.download', 'db/nonexistent.sql'));
        $response->assertRedirect();
    }

    public function test_can_delete_backup(): void
    {
        $this->actingAs($this->admin);

        // Create a test backup file
        $backupDir = storage_path('app/backups/db');
        File::put($backupDir.'/to-delete.sql', 'test');

        $response = $this->delete(route('backup.destroy', 'db/to-delete.sql'));
        $response->assertRedirect();
        $this->assertFileDoesNotExist($backupDir.'/to-delete.sql');
    }

    public function test_can_upload_backup(): void
    {
        $this->actingAs($this->admin);

        $file = UploadedFile::fake()->createWithContent('test-backup.sql', 'CREATE TABLE test (id INT);');

        $response = $this->post(route('backup.upload'), [
            'backup_file' => $file,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('status');
    }

    public function test_upload_rejects_invalid_extension(): void
    {
        $this->actingAs($this->admin);

        $file = UploadedFile::fake()->createWithContent('malicious.php', '<?php echo "hacked"; ?>');

        $response = $this->post(route('backup.upload'), [
            'backup_file' => $file,
        ]);

        $response->assertSessionHasErrors('backup_file');
    }

    public function test_restore_requires_confirmation(): void
    {
        $this->actingAs($this->admin);

        // Create a test backup file with valid SQL
        $backupDir = storage_path('app/backups/db');
        File::put($backupDir.'/restore-test.sql', '-- empty backup');

        $response = $this->post(route('backup.restore'), [
            'filename' => 'db/restore-test.sql',
            'confirmation' => 'WRONG',
        ]);

        $response->assertSessionHasErrors('confirmation');
    }

    public function test_restore_rejects_nonexistent_file(): void
    {
        $this->actingAs($this->admin);

        $response = $this->post(route('backup.restore'), [
            'filename' => 'db/nonexistent.sql',
            'confirmation' => 'RESTORE',
        ]);

        $response->assertSessionHasErrors('restore');
    }

    public function test_guru_cannot_create_backup(): void
    {
        $this->actingAs($this->guru);
        $response = $this->post(route('backup.store-db'));
        $response->assertForbidden();
    }

    public function test_guru_cannot_delete_backup(): void
    {
        $this->actingAs($this->guru);
        $response = $this->delete(route('backup.destroy', 'db/test.sql'));
        $response->assertForbidden();
    }

    public function test_guru_cannot_upload_backup(): void
    {
        $this->actingAs($this->guru);

        $file = UploadedFile::fake()->createWithContent('test.sql', 'test');

        $response = $this->post(route('backup.upload'), [
            'backup_file' => $file,
        ]);

        $response->assertForbidden();
    }

    public function test_guru_cannot_download_backup(): void
    {
        $this->actingAs($this->guru);
        $response = $this->get(route('backup.download', 'db/test.sql'));
        $response->assertForbidden();
    }
}
