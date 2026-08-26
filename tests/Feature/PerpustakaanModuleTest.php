<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\ClassGroup;
use App\Models\Employee;
use App\Models\LibraryBook;
use App\Models\LibraryCategory;
use App\Models\LibraryLoan;
use App\Models\LibraryMember;
use App\Models\Person;
use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PerpustakaanModuleTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $pustakawan;

    protected User $kepala;

    protected User $guru;

    protected LibraryCategory $kategori;

    protected function setUp(): void
    {
        parent::setUp();

        AcademicYear::create(['name' => '2026/2027', 'semester' => 'ganjil', 'is_active' => true]);

        $this->admin = User::factory()->create(['role' => 'super_admin']);
        $this->pustakawan = User::factory()->create(['role' => 'pustakawan']);
        $this->kepala = User::factory()->create(['role' => 'kepala_madrasah']);
        $this->guru = User::factory()->create(['role' => 'guru']);

        $this->kategori = LibraryCategory::create(['name' => 'Fiksi']);
    }

    protected function makeBook(array $overrides = []): LibraryBook
    {
        static $i = 0;
        $i++;

        return LibraryBook::create(array_merge([
            'code' => 'BUK-'.now()->format('Ym').'-'.str_pad((string) $i, 3, '0', STR_PAD_LEFT),
            'title' => 'Laskar Pelangi',
            'author' => 'Andrea Hirata',
            'publisher' => 'Bentang Pustaka',
            'year' => 2005,
            'category_id' => $this->kategori->id,
            'isbn' => '978-979-3062-79-4',
            'total_qty' => 3,
            'available_qty' => 3,
            'location' => 'Rak F-1',
            'status' => 'tersedia',
            'created_by' => $this->admin->id,
        ], $overrides));
    }

    protected function makeMember(array $overrides = []): LibraryMember
    {
        static $i = 0;
        $i++;

        return LibraryMember::create(array_merge([
            'member_type' => 'siswa',
            'member_no' => 'ANG-'.now()->format('Y').'-'.str_pad((string) $i, 3, '0', STR_PAD_LEFT),
            'name' => 'Aisyah Nur Azizah',
            'status' => 'aktif',
            'joined_at' => now()->toDateString(),
        ], $overrides));
    }

    public function test_pustakawan_can_create_book_with_auto_code(): void
    {
        $response = $this->actingAs($this->pustakawan)->post(route('perpustakaan.store'), [
            'title' => 'Matematika Kelas VI',
            'author' => 'Tim Penulis',
            'total_qty' => 5,
            'status' => 'tersedia',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('library_books', [
            'title' => 'Matematika Kelas VI',
            'code' => 'BUK-'.now()->format('Ym').'-001',
        ]);
    }

    public function test_admin_can_update_book(): void
    {
        $book = $this->makeBook();

        $this->actingAs($this->admin)->put(route('perpustakaan.update', $book), [
            'title' => 'Matematika Kelas VI Edisi Baru',
            'author' => 'Tim Penulis',
            'total_qty' => 5,
            'status' => 'tersedia',
        ])->assertRedirect();

        $this->assertDatabaseHas('library_books', ['id' => $book->id, 'title' => 'Matematika Kelas VI Edisi Baru']);
    }

    public function test_update_book_stock_increases_available_qty(): void
    {
        $book = $this->makeBook(['total_qty' => 3, 'available_qty' => 3]);

        $this->actingAs($this->admin)->put(route('perpustakaan.update', $book), [
            'title' => $book->title,
            'author' => $book->author,
            'total_qty' => 5,
            'status' => 'tersedia',
        ])->assertRedirect();

        $book->refresh();
        $this->assertSame(5, $book->total_qty);
        $this->assertSame(5, $book->available_qty);
    }

    public function test_update_book_reducing_stock_lowers_available_qty(): void
    {
        $book = $this->makeBook(['total_qty' => 3, 'available_qty' => 2]); // 1 dipinjam
        $member = $this->makeMember();

        $this->actingAs($this->admin)->put(route('perpustakaan.update', $book), [
            'title' => $book->title,
            'author' => $book->author,
            'total_qty' => 2,
            'status' => 'tersedia',
        ])->assertRedirect();

        $book->refresh();
        $this->assertSame(2, $book->total_qty);
        $this->assertSame(1, $book->available_qty);
    }

    public function test_cannot_reduce_stock_below_loaned_count(): void
    {
        $book = $this->makeBook(['total_qty' => 2, 'available_qty' => 0]); // 2 sedang dipinjam

        $this->actingAs($this->admin)->put(route('perpustakaan.update', $book), [
            'title' => $book->title,
            'author' => $book->author,
            'total_qty' => 1,
            'status' => 'tersedia',
        ])->assertSessionHasErrors('total_qty');

        $book->refresh();
        $this->assertSame(2, $book->total_qty);
        $this->assertSame(0, $book->available_qty);
    }

    public function test_guru_cannot_access_library(): void
    {
        $book = $this->makeBook();

        $this->actingAs($this->guru)->get(route('perpustakaan.index'))->assertForbidden();
        $this->actingAs($this->guru)->post(route('perpustakaan.store'), [
            'title' => 'Test',
            'author' => 'Test',
            'total_qty' => 1,
            'status' => 'tersedia',
        ])->assertForbidden();
        $this->actingAs($this->guru)->get(route('perpustakaan.show', $book))->assertForbidden();
    }

    public function test_kepala_read_only_cannot_mutate(): void
    {
        $book = $this->makeBook();

        $this->actingAs($this->kepala)->get(route('perpustakaan.index'))->assertOk();
        $this->actingAs($this->kepala)->get(route('perpustakaan.show', $book))->assertOk();

        $this->actingAs($this->kepala)->post(route('perpustakaan.store'), [
            'title' => 'Test',
            'author' => 'Test',
            'total_qty' => 1,
            'status' => 'tersedia',
        ])->assertForbidden();
    }

    public function test_loan_decrements_available_qty_and_return_increments(): void
    {
        $book = $this->makeBook(['total_qty' => 2, 'available_qty' => 2]);
        $member = $this->makeMember();

        // Pinjam
        $this->actingAs($this->pustakawan)->post(route('perpustakaan.loan.store', $book), [
            'member_id' => $member->id,
            'loan_date' => now()->toDateString(),
            'due_date' => now()->addDays(7)->toDateString(),
        ])->assertRedirect();

        $book->refresh();
        $this->assertSame(1, $book->available_qty);

        $loan = LibraryLoan::first();
        $this->assertSame('dipinjam', $loan->status);

        // Kembalikan
        $this->actingAs($this->pustakawan)->post(route('perpustakaan.loan.return', [$book, $loan]))->assertRedirect();

        $book->refresh();
        $this->assertSame(2, $book->available_qty);

        $loan->refresh();
        $this->assertSame('dikembalikan', $loan->status);
        $this->assertNotNull($loan->return_date);
    }

    public function test_cannot_loan_when_stock_empty(): void
    {
        $book = $this->makeBook(['available_qty' => 0]);
        $member = $this->makeMember();

        $this->actingAs($this->pustakawan)->post(route('perpustakaan.loan.store', $book), [
            'member_id' => $member->id,
            'loan_date' => now()->toDateString(),
            'due_date' => now()->addDays(7)->toDateString(),
        ])->assertSessionHasErrors('loan');
    }

    public function test_cannot_loan_same_book_twice_by_same_member(): void
    {
        $book = $this->makeBook(['total_qty' => 2, 'available_qty' => 2]);
        $member = $this->makeMember();

        // Pinjaman pertama berhasil, stok turun.
        $this->actingAs($this->pustakawan)->post(route('perpustakaan.loan.store', $book), [
            'member_id' => $member->id,
            'loan_date' => now()->toDateString(),
            'due_date' => now()->addDays(7)->toDateString(),
        ])->assertRedirect();

        $book->refresh();
        $this->assertSame(1, $book->available_qty);

        // Meminjam buku yang sama oleh anggota yang sama ditolak — stok tidak berkurang ganda.
        $this->actingAs($this->pustakawan)->post(route('perpustakaan.loan.store', $book), [
            'member_id' => $member->id,
            'loan_date' => now()->toDateString(),
            'due_date' => now()->addDays(7)->toDateString(),
        ])->assertSessionHasErrors('loan');

        $book->refresh();
        $this->assertSame(1, $book->available_qty);
    }

    public function test_member_can_loan_again_after_return(): void
    {
        $book = $this->makeBook(['total_qty' => 2, 'available_qty' => 2]);
        $member = $this->makeMember();

        // Pinjam → kembali.
        $this->actingAs($this->pustakawan)->post(route('perpustakaan.loan.store', $book), [
            'member_id' => $member->id,
            'loan_date' => now()->toDateString(),
            'due_date' => now()->addDays(7)->toDateString(),
        ])->assertRedirect();

        $loan = LibraryLoan::where('book_id', $book->id)->where('member_id', $member->id)->first();
        $this->actingAs($this->pustakawan)->post(route('perpustakaan.loan.return', [$book, $loan]))->assertRedirect();

        $book->refresh();
        $this->assertSame(2, $book->available_qty);

        // Setelah dikembalikan, anggota yang sama boleh meminjam lagi.
        $this->actingAs($this->pustakawan)->post(route('perpustakaan.loan.store', $book), [
            'member_id' => $member->id,
            'loan_date' => now()->toDateString(),
            'due_date' => now()->addDays(7)->toDateString(),
        ])->assertRedirect();

        $book->refresh();
        $this->assertSame(1, $book->available_qty);
    }

    public function test_return_marks_late_when_overdue(): void
    {
        $book = $this->makeBook();
        $member = $this->makeMember();

        $loan = LibraryLoan::create([
            'book_id' => $book->id,
            'member_id' => $member->id,
            'loan_date' => now()->subDays(14)->toDateString(),
            'due_date' => now()->subWeek()->toDateString(),
            'status' => 'dipinjam',
            'recorded_by' => $this->pustakawan->id,
        ]);

        $this->actingAs($this->pustakawan)->post(route('perpustakaan.loan.return', [$book, $loan]))->assertRedirect();

        $loan->refresh();
        $this->assertSame('terlambat', $loan->status);
    }

    public function test_category_management(): void
    {
        $this->actingAs($this->pustakawan)->post(route('perpustakaan.kategori.store'), [
            'name' => 'Agama',
        ])->assertRedirect();

        $this->assertDatabaseHas('library_categories', ['name' => 'Agama']);

        // Kategori berisi buku tidak bisa dihapus
        $this->makeBook();
        $this->actingAs($this->pustakawan)->delete(route('perpustakaan.kategori.destroy', $this->kategori))
            ->assertSessionHasErrors('category');

        // Kategori kosong bisa dihapus
        $kosong = LibraryCategory::create(['name' => 'Kosong']);
        $this->actingAs($this->pustakawan)->delete(route('perpustakaan.kategori.destroy', $kosong))->assertRedirect();
        $this->assertDatabaseMissing('library_categories', ['id' => $kosong->id]);
    }

    public function test_member_management(): void
    {
        $siswa = Student::create(['nis' => '990101', 'name' => 'Test Siswa', 'gender' => 'L']);

        $this->actingAs($this->pustakawan)->post(route('perpustakaan.anggota.store'), [
            'member_type' => 'siswa',
            'student_id' => $siswa->id,
        ])->assertRedirect();

        $member = LibraryMember::first();
        $this->assertSame('siswa', $member->member_type);
        $this->assertSame($siswa->id, $member->student_id);
        $this->assertStringStartsWith('ANG-', $member->member_no);
    }

    public function test_show_renders_details_and_loan_form(): void
    {
        $book = $this->makeBook();

        $response = $this->actingAs($this->pustakawan)->get(route('perpustakaan.show', $book));

        $response->assertOk();
        $response->assertSee('Laskar Pelangi');
        $response->assertSee('Catat Peminjam');
    }

    public function test_index_filters(): void
    {
        LibraryBook::create([
            'code' => 'BUK-FILTER-001',
            'title' => 'Laskar Pelangi',
            'author' => 'Andrea Hirata',
            'total_qty' => 1,
            'available_qty' => 1,
            'status' => 'tersedia',
            'created_by' => $this->admin->id,
        ]);
        LibraryBook::create([
            'code' => 'BUK-FILTER-002',
            'title' => 'Ensiklopedia Sains',
            'author' => 'Khairul Amri',
            'total_qty' => 1,
            'available_qty' => 1,
            'status' => 'tersedia',
            'created_by' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->pustakawan)->get(route('perpustakaan.index', ['q' => 'Laskar']));

        $response->assertOk();
        $response->assertSee('Laskar Pelangi');
        $response->assertDontSee('Ensiklopedia Sains');
    }

    public function test_index_category_filter_returns_only_that_category(): void
    {
        $kategoriLain = LibraryCategory::create(['name' => 'Agama']);
        LibraryBook::create([
            'code' => 'BUK-CAT-001', 'title' => 'Novel Remaja', 'author' => 'Penulis A',
            'category_id' => $this->kategori->id,
            'total_qty' => 1, 'available_qty' => 1, 'status' => 'tersedia',
            'created_by' => $this->admin->id,
        ]);
        LibraryBook::create([
            'code' => 'BUK-CAT-002', 'title' => 'Fiqih Praktis', 'author' => 'Penulis B',
            'category_id' => $kategoriLain->id,
            'total_qty' => 1, 'available_qty' => 1, 'status' => 'tersedia',
            'created_by' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->pustakawan)->get(route('perpustakaan.index', ['category_id' => $this->kategori->id]));

        $response->assertOk();
        $response->assertSee('Novel Remaja');
        $response->assertDontSee('Fiqih Praktis');
    }

    public function test_index_status_filter_returns_only_that_status(): void
    {
        LibraryBook::create([
            'code' => 'BUK-STAT-001', 'title' => 'Buku Tersedia', 'author' => 'Penulis A',
            'total_qty' => 1, 'available_qty' => 1, 'status' => 'tersedia',
            'created_by' => $this->admin->id,
        ]);
        LibraryBook::create([
            'code' => 'BUK-STAT-002', 'title' => 'Buku Nonaktif', 'author' => 'Penulis B',
            'total_qty' => 1, 'available_qty' => 1, 'status' => 'tidak_aktif',
            'created_by' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->pustakawan)->get(route('perpustakaan.index', ['status' => 'tidak_aktif']));

        $response->assertOk();
        $response->assertSee('Buku Nonaktif');
        $response->assertDontSee('Buku Tersedia');
    }

    public function test_index_ebook_filter_physical_returns_fisik_only(): void
    {
        LibraryBook::create([
            'code' => 'BUK-EB-001', 'title' => 'Buku Fisik', 'author' => 'Penulis A',
            'total_qty' => 1, 'available_qty' => 1, 'status' => 'tersedia',
            'is_ebook' => false, 'created_by' => $this->admin->id,
        ]);
        LibraryBook::create([
            'code' => 'BUK-EB-002', 'title' => 'Buku Ebook', 'author' => 'Penulis B',
            'total_qty' => 1, 'available_qty' => 1, 'status' => 'tersedia',
            'is_ebook' => true, 'created_by' => $this->admin->id,
        ]);

        // is_ebook=0 (Fisik) — must still apply the filter despite PHP treating '0' as empty
        $response = $this->actingAs($this->pustakawan)->get(route('perpustakaan.index', ['is_ebook' => '0']));

        $response->assertOk();
        $response->assertSee('Buku Fisik');
        $response->assertDontSee('Buku Ebook');
    }

    public function test_index_ebook_filter_ebook_returns_ebook_only(): void
    {
        LibraryBook::create([
            'code' => 'BUK-EB-003', 'title' => 'Buku Fisik', 'author' => 'Penulis A',
            'total_qty' => 1, 'available_qty' => 1, 'status' => 'tersedia',
            'is_ebook' => false, 'created_by' => $this->admin->id,
        ]);
        LibraryBook::create([
            'code' => 'BUK-EB-004', 'title' => 'Buku Ebook', 'author' => 'Penulis B',
            'total_qty' => 1, 'available_qty' => 1, 'status' => 'tersedia',
            'is_ebook' => true, 'created_by' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->pustakawan)->get(route('perpustakaan.index', ['is_ebook' => '1']));

        $response->assertOk();
        $response->assertSee('Buku Ebook');
        $response->assertDontSee('Buku Fisik');
    }

    public function test_pustakawan_member_index_filters(): void
    {
        $this->makeMember(['name' => 'Aisyah', 'member_type' => 'siswa']);
        $this->makeMember(['name' => 'Budi', 'member_type' => 'pegawai', 'member_no' => 'ANG-'.now()->format('Y').'-099']);

        $response = $this->actingAs($this->pustakawan)->get(route('perpustakaan.anggota.index', ['type' => 'siswa']));

        $response->assertOk();
        $response->assertSee('Aisyah');
        $response->assertDontSee('Budi');
    }

    public function test_member_index_loads_student_and_employee_candidates_for_modal(): void
    {
        $kelas = ClassGroup::create(['name' => 'VI-A', 'grade_level' => 'VI']);
        $person = Person::create(['nik' => '3500000000000001', 'name' => 'Citra Lestari', 'gender' => 'P', 'religion' => 'Islam']);
        $student = Student::create(['person_id' => $person->id, 'nis' => '2026001', 'name' => 'Citra Lestari', 'gender' => 'P']);

        StudentEnrollment::create([
            'academic_year_id' => AcademicYear::active()->id,
            'class_group_id' => $kelas->id,
            'student_id' => $student->id,
            'status' => 'aktif',
        ]);

        $employee = Employee::create([
            'person_id' => Person::create(['nik' => '3500000000000002', 'name' => 'Ustadz Ahmad', 'gender' => 'L', 'religion' => 'Islam'])->id,
            'nip' => '198001012005011001',
            'status' => 'aktif',
        ]);

        // Nonaktif tidak boleh masuk kandidat.
        Employee::create([
            'person_id' => Person::create(['nik' => '3500000000000003', 'name' => 'Pegawai Nonaktif', 'gender' => 'L', 'religion' => 'Islam'])->id,
            'nip' => '198001012005011002',
            'status' => 'nonaktif',
        ]);

        $response = $this->actingAs($this->pustakawan)->get(route('perpustakaan.anggota.index'));

        $response->assertOk();
        // Kandidat siswa (dengan rombel) & pegawai aktif dikirim ke modal picker.
        $response->assertSee('Citra Lestari');
        $response->assertSee('VI-A');
        $response->assertSee('Ustadz Ahmad');
        $response->assertDontSee('Pegawai Nonaktif');

        // Simpan via picker tetap bekerja: pilih siswa dari daftar rombel.
        $store = $this->actingAs($this->pustakawan)->post(route('perpustakaan.anggota.store'), [
            'member_type' => 'siswa',
            'student_id' => $student->id,
        ]);
        $store->assertRedirect();
        $this->assertDatabaseHas('library_members', [
            'member_type' => 'siswa',
            'student_id' => $student->id,
            'status' => 'aktif',
        ]);

        // Pegawai juga dapat didaftarkan lewat picker.
        $storePegawai = $this->actingAs($this->pustakawan)->post(route('perpustakaan.anggota.store'), [
            'member_type' => 'pegawai',
            'employee_id' => $employee->id,
        ]);
        $storePegawai->assertRedirect();
        $this->assertDatabaseHas('library_members', [
            'member_type' => 'pegawai',
            'employee_id' => $employee->id,
        ]);
    }

    public function test_cannot_delete_book_with_active_loans(): void
    {
        $book = $this->makeBook();
        $member = $this->makeMember();

        LibraryLoan::create([
            'book_id' => $book->id,
            'member_id' => $member->id,
            'loan_date' => now()->toDateString(),
            'due_date' => now()->addDays(7)->toDateString(),
            'status' => 'dipinjam',
            'recorded_by' => $this->pustakawan->id,
        ]);

        $this->actingAs($this->pustakawan)->delete(route('perpustakaan.destroy', $book))
            ->assertSessionHasErrors('book');
    }
}
