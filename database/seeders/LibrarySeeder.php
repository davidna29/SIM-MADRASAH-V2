<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\LibraryBook;
use App\Models\LibraryCategory;
use App\Models\LibraryLoan;
use App\Models\LibraryMember;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;

class LibrarySeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('username', 'admin')->first();
        $userId = $admin?->id;

        // Kategori
        $kategori = [
            'Fiksi' => 'Novel, cerpen, dan karya fiksi lainnya.',
            'Agama' => 'Buku-buku keagamaan dan keislaman.',
            'Pelajaran' => 'Buku pelajaran kurikulum madrasah.',
            'Referensi' => 'Ensiklopedia, kamus, dan buku rujukan.',
            'Umum' => 'Buku umum, motivasi, dan keterampilan hidup.',
        ];

        $cats = [];
        foreach ($kategori as $name => $desc) {
            $cats[$name] = LibraryCategory::firstOrCreate(['name' => $name], ['description' => $desc]);
        }

        // Buku
        $books = [
            ['Laskar Pelangi', 'Andrea Hirata', 'Bentang Pustaka', 2005, 'Fiksi', '978-979-3062-79-4', 3, 'Rak F-1', false],
            ['Tenggelamnya Kapal Van Der Wijck', 'Hamka', 'Republika', 2016, 'Fiksi', '978-602-291-124-9', 2, 'Rak F-2', false],
            ['Matematika Kelas VI', 'Tim Penulis', 'Kementerian Agama', 2024, 'Pelajaran', '978-602-XXX-001-X', 5, 'Rak P-1', false],
            ['Bahasa Arab Kelas V', 'Tim Penulis', 'Kementerian Agama', 2024, 'Pelajaran', null, 4, 'Rak P-2', false],
            ['Ensiklopedia Sains', 'Khairul Amri', 'Erlangga', 2020, 'Referensi', '978-602-291-200-X', 2, 'Rak R-1', false],
            ['Buku Doa Harian', 'Abdul Somad', 'Gema Insani', 2022, 'Agama', null, 3, 'Rak A-1', false],
            ['Fiqih Islam', 'Hasan Bashari', 'Bumi Aksara', 2021, 'Agama', null, 2, 'Rak A-2', false],
            ['Mengenal Al-Qur\'an untuk Anak', 'Khalid Muhammad', 'Republika', 2023, 'Agama', null, 3, 'Rak A-1', true],
        ];

        $bookModels = [];
        foreach ($books as $idx => [$title, $author, $publisher, $year, $catName, $isbn, $qty, $loc, $isEbook]) {
            $prefix = 'BUK-'.now()->format('Ym').'-';
            $bookModels[] = LibraryBook::firstOrCreate(
                ['code' => $prefix.str_pad((string) ($idx + 1), 3, '0', STR_PAD_LEFT)],
                [
                    'title' => $title,
                    'author' => $author,
                    'publisher' => $publisher,
                    'year' => $year,
                    'category_id' => $cats[$catName]?->id,
                    'isbn' => $isbn,
                    'total_qty' => $qty,
                    'available_qty' => $qty,
                    'location' => $loc,
                    'is_ebook' => $isEbook,
                    'ebook_url' => $isEbook ? 'https://drive.google.com/file/d/example-ebook' : null,
                    'status' => 'tersedia',
                    'created_by' => $userId,
                ]
            );
        }

        // Anggota
        $siswa = Student::where('nis', '240101')->first();
        $pegawai = Employee::first();

        $members = [];
        if ($siswa) {
            $members[] = LibraryMember::firstOrCreate(
                ['member_no' => 'ANG-'.now()->format('Y').'-001'],
                [
                    'member_type' => 'siswa',
                    'student_id' => $siswa->id,
                    'name' => $siswa->name,
                    'status' => 'aktif',
                    'joined_at' => now()->subMonths(3)->toDateString(),
                ]
            );
        }
        if ($pegawai) {
            $members[] = LibraryMember::firstOrCreate(
                ['member_no' => 'ANG-'.now()->format('Y').'-002'],
                [
                    'member_type' => 'pegawai',
                    'employee_id' => $pegawai->id,
                    'name' => $pegawai->person?->name ?? 'Pegawai',
                    'status' => 'aktif',
                    'joined_at' => now()->subMonths(2)->toDateString(),
                ]
            );
        }

        // Contoh peminjaman: 1 aktif + 1 dikembalikan
        if (isset($bookModels[0]) && isset($members[0])) {
            LibraryLoan::firstOrCreate(
                [
                    'book_id' => $bookModels[0]->id,
                    'member_id' => $members[0]->id,
                    'loan_date' => now()->subWeek()->toDateString(),
                ],
                [
                    'due_date' => now()->addDays(7)->toDateString(),
                    'status' => 'dipinjam',
                    'note' => 'Peminjaman contoh',
                    'recorded_by' => $userId,
                ]
            );
            $bookModels[0]->decrement('available_qty');
        }

        if (isset($bookModels[2]) && isset($members[0])) {
            LibraryLoan::firstOrCreate(
                [
                    'book_id' => $bookModels[2]->id,
                    'member_id' => $members[0]->id,
                    'loan_date' => now()->subMonth()->toDateString(),
                ],
                [
                    'due_date' => now()->subWeeks(2)->toDateString(),
                    'return_date' => now()->subWeek()->toDateString(),
                    'status' => 'dikembalikan',
                    'recorded_by' => $userId,
                ]
            );
        }
    }
}
