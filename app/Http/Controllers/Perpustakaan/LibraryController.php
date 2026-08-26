<?php

namespace App\Http\Controllers\Perpustakaan;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\ClassGroup;
use App\Models\Employee;
use App\Models\LibraryBook;
use App\Models\LibraryCategory;
use App\Models\LibraryLoan;
use App\Models\LibraryMember;
use App\Models\Student;
use App\Models\StudentEnrollment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class LibraryController extends Controller
{
    // ──────────────────────────────────────────────
    //  BUKU
    // ──────────────────────────────────────────────

    public function index(Request $request): View
    {
        $this->authorize('viewAny', LibraryBook::class);

        $books = LibraryBook::with('category')
            ->when($request->filled('category_id'), fn ($q) => $q->where('category_id', $request->input('category_id')))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->input('status')))
            ->when($request->has('is_ebook'), fn ($q) => $q->where('is_ebook', $request->input('is_ebook') === '1'))
            ->when($request->filled('q'), fn ($q) => $q->where(function ($w) use ($request) {
                $s = $request->input('q');
                $w->where('title', 'like', "%{$s}%")
                    ->orWhere('author', 'like', "%{$s}%")
                    ->orWhere('code', 'like', "%{$s}%")
                    ->orWhere('isbn', 'like', "%{$s}%");
            }))
            ->orderBy('title')
            ->paginate(15)
            ->withQueryString();

        return view('pages.perpustakaan.index', [
            'roleLabel' => 'Perpustakaan',
            'breadcrumb' => [
                ['label' => 'Sarpras & Perpustakaan', 'href' => route('dashboard')],
                ['label' => 'Katalog Buku'],
            ],
            'books' => $books,
            'categories' => LibraryCategory::orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', LibraryBook::class);

        return view('pages.perpustakaan.form', [
            'roleLabel' => 'Perpustakaan',
            'breadcrumb' => [
                ['label' => 'Sarpras & Perpustakaan', 'href' => route('dashboard')],
                ['label' => 'Katalog Buku', 'href' => route('perpustakaan.index')],
                ['label' => 'Tambah Buku'],
            ],
            'editing' => false,
            'categories' => LibraryCategory::orderBy('name')->pluck('name', 'id'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', LibraryBook::class);

        $validated = $request->validate($this->bookRules());

        $validated['available_qty'] = $validated['total_qty'] ?? 1;
        $validated['code'] = $this->nextBookCode();
        $validated['created_by'] = auth()->id();

        $book = LibraryBook::create($validated);

        activity('perpustakaan')->performedOn($book)->log('buku_ditambah');

        return redirect()->route('perpustakaan.show', $book)->with('status', 'Buku ditambahkan ke katalog.');
    }

    public function show(LibraryBook $book): View
    {
        $this->authorize('view', $book);

        $book->load('category', 'creator');
        $loans = $book->loans()->with('member', 'recorder')->latest('loan_date')->paginate(10);

        return view('pages.perpustakaan.show', [
            'roleLabel' => 'Perpustakaan',
            'breadcrumb' => [
                ['label' => 'Sarpras & Perpustakaan', 'href' => route('dashboard')],
                ['label' => 'Katalog Buku', 'href' => route('perpustakaan.index')],
                ['label' => $book->title],
            ],
            'book' => $book,
            'loans' => $loans,
            'members' => LibraryMember::where('status', 'aktif')->orderBy('name')->get(),
            'canManage' => auth()->user()->can('update', $book),
        ]);
    }

    public function edit(LibraryBook $book): View
    {
        $this->authorize('update', $book);

        return view('pages.perpustakaan.form', [
            'roleLabel' => 'Perpustakaan',
            'breadcrumb' => [
                ['label' => 'Sarpras & Perpustakaan', 'href' => route('dashboard')],
                ['label' => 'Katalog Buku', 'href' => route('perpustakaan.index')],
                ['label' => 'Ubah '.$book->title],
            ],
            'editing' => true,
            'book' => $book,
            'categories' => LibraryCategory::orderBy('name')->pluck('name', 'id'),
        ]);
    }

    public function update(Request $request, LibraryBook $book): RedirectResponse
    {
        $this->authorize('update', $book);

        $validated = $request->validate($this->bookRules($book->id));

        // Menambah stok menambah jumlah tersedia; mengurangi stok tidak boleh
        // melewati jumlah yang sedang dipinjam.
        $oldTotal = $book->total_qty;
        $newTotal = (int) $validated['total_qty'];
        $newAvailable = $book->available_qty + ($newTotal - $oldTotal);

        if ($newAvailable < 0) {
            $borrowed = $oldTotal - $book->available_qty;

            return back()->withErrors(['total_qty' => "Jumlah stok tidak boleh kurang dari jumlah buku yang sedang dipinjam ({$borrowed})."]);
        }

        $validated['available_qty'] = $newAvailable;
        $book->update($validated);

        activity('perpustakaan')->performedOn($book)->log('buku_diubah');

        return redirect()->route('perpustakaan.show', $book)->with('status', 'Data buku diperbarui.');
    }

    public function destroy(LibraryBook $book): RedirectResponse
    {
        $this->authorize('delete', $book);

        if ($book->loans()->where('status', 'dipinjam')->exists()) {
            return back()->withErrors(['book' => 'Buku masih memiliki peminjaman aktif — tidak dapat dihapus.']);
        }

        $book->delete();

        activity('perpustakaan')->log('buku_dihapus');

        return redirect()->route('perpustakaan.index')->with('status', 'Buku dihapus dari katalog.');
    }

    // ──────────────────────────────────────────────
    //  PINJAM / KEMBALIKAN
    // ──────────────────────────────────────────────

    public function loanStore(Request $request, LibraryBook $book): RedirectResponse
    {
        $this->authorize('loan', $book);

        if ($book->status === 'tidak_aktif') {
            return back()->withErrors(['loan' => 'Buku tidak aktif — tidak dapat dipinjamkan.']);
        }

        if ($book->available_qty < 1) {
            return back()->withErrors(['loan' => 'Stok buku habis — tidak tersedia untuk dipinjam.']);
        }

        $activeForSameMember = LibraryLoan::where('book_id', $book->id)
            ->where('member_id', $request->input('member_id'))
            ->where('status', 'dipinjam')
            ->exists();

        if ($activeForSameMember) {
            return back()->withErrors(['loan' => 'Anggota ini masih meminjam buku yang sama — kembalikan dahulu sebelum meminjam lagi.']);
        }

        $validated = $request->validate([
            'member_id' => ['required', 'exists:library_members,id'],
            'loan_date' => ['required', 'date'],
            'due_date' => ['required', 'date', 'after:loan_date'],
            'note' => ['nullable', 'string', 'max:500'],
        ]);

        $validated['book_id'] = $book->id;
        $validated['status'] = 'dipinjam';
        $validated['recorded_by'] = auth()->id();

        DB::transaction(function () use ($book, $validated) {
            LibraryLoan::create($validated);
            $book->decrement('available_qty');
        });

        activity('perpustakaan')->performedOn($book)->log('buku_dipinjam');

        return back()->with('status', 'Peminjaman dicatat.');
    }

    public function loanReturn(LibraryBook $book, LibraryLoan $loan): RedirectResponse
    {
        $this->authorize('returnBook', $book);

        if ($loan->status !== 'dipinjam') {
            return back()->withErrors(['loan' => 'Peminjaman ini sudah diproses.']);
        }

        $returnDate = now()->toDateString();
        $newStatus = $returnDate > $loan->due_date->format('Y-m-d') ? 'terlambat' : 'dikembalikan';

        DB::transaction(function () use ($book, $loan, $returnDate, $newStatus) {
            $loan->update([
                'return_date' => $returnDate,
                'status' => $newStatus,
            ]);
            $book->increment('available_qty');
        });

        $label = $newStatus === 'terlambat' ? 'Pengembalian (terlambat)' : 'Pengembalian';
        activity('perpustakaan')->performedOn($book)->log('buku_dikembalikan');

        return back()->with('status', $label.' dicatat.');
    }

    // ──────────────────────────────────────────────
    //  ANGGOTA
    // ──────────────────────────────────────────────

    public function memberIndex(): View
    {
        $this->authorize('viewAny', LibraryMember::class);

        $members = LibraryMember::with('student', 'employee')
            ->when(request('type'), fn ($q, $v) => $q->where('member_type', $v))
            ->when(request('status'), fn ($q, $v) => $q->where('status', $v))
            ->when(request('q'), fn ($q, $s) => $q->where(function ($w) use ($s) {
                $w->where('name', 'like', "%{$s}%")
                    ->orWhere('member_no', 'like', "%{$s}%");
            }))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        // Kandidat anggota: siswa aktif (dikelompokkan per rombel TA berjalan) & pegawai aktif.
        $tahun = AcademicYear::active();

        $enrollments = StudentEnrollment::query()
            ->where('academic_year_id', $tahun->id)
            ->where('status', 'aktif')
            ->with('student.person')
            ->get();

        $rombelNames = ClassGroup::whereIn('id', $enrollments->pluck('class_group_id')->unique())
            ->orderBy('name')
            ->pluck('name', 'id');

        $studentPool = $enrollments
            ->filter(fn ($enrollment) => $enrollment->student !== null)
            ->map(fn ($enrollment) => [
                'id' => (int) $enrollment->student_id,
                'label' => $enrollment->student->displayName(),
                'nis' => $enrollment->student->nis,
                'rombel_id' => (int) $enrollment->class_group_id,
                'rombel' => $rombelNames[$enrollment->class_group_id] ?? '—',
            ])
            ->unique('id')
            ->sortBy('label')
            ->values();

        $employeePool = Employee::query()
            ->where('status', 'aktif')
            ->with('person')
            ->get()
            ->map(fn ($employee) => [
                'id' => $employee->id,
                'label' => $employee->person?->name ?? '—',
                'nip' => $employee->nip,
            ])
            ->sortBy('label')
            ->values();

        return view('pages.perpustakaan.anggota', [
            'roleLabel' => 'Perpustakaan',
            'breadcrumb' => [
                ['label' => 'Sarpras & Perpustakaan', 'href' => route('dashboard')],
                ['label' => 'Katalog Buku', 'href' => route('perpustakaan.index')],
                ['label' => 'Anggota'],
            ],
            'members' => $members,
            'rombels' => $rombelNames,
            'studentPool' => $studentPool,
            'employeePool' => $employeePool,
        ]);
    }

    public function memberStore(Request $request): RedirectResponse
    {
        $this->authorize('create', LibraryMember::class);

        $validated = $request->validate([
            'member_type' => ['required', Rule::in(LibraryMember::TYPES)],
            'student_id' => ['required_if:member_type,siswa', 'nullable', 'exists:students,id'],
            'employee_id' => ['required_if:member_type,pegawai', 'nullable', 'exists:employees,id'],
        ]);

        // Validate that one ID is provided based on type
        if ($validated['member_type'] === 'siswa' && empty($validated['student_id'])) {
            return back()->withErrors(['student_id' => 'Siswa wajib dipilih untuk anggota jenis siswa.']);
        }
        if ($validated['member_type'] === 'pegawai' && empty($validated['employee_id'])) {
            return back()->withErrors(['employee_id' => 'Pegawai wajib dipilih untuk anggota jenis pegawai.']);
        }

        // Resolve name from the linked entity
        $name = match ($validated['member_type']) {
            'siswa' => Student::find($validated['student_id'])?->displayName() ?? '—',
            'pegawai' => Employee::find($validated['employee_id'])?->person?->name ?? '—',
        };

        $validated['member_no'] = $this->nextMemberNo();
        $validated['name'] = $name;
        $validated['status'] = 'aktif';
        $validated['joined_at'] = now()->toDateString();

        $member = LibraryMember::create($validated);

        activity('perpustakaan')->performedOn($member)->log('anggota_perpustakaan_ditambah');

        return back()->with('status', 'Anggota perpustakaan ditambahkan.');
    }

    public function memberDestroy(LibraryMember $member): RedirectResponse
    {
        $this->authorize('delete', $member);

        if ($member->activeLoans()->exists()) {
            return back()->withErrors(['member' => 'Anggota masih memiliki peminjaman aktif — tidak dapat dihapus.']);
        }

        $member->delete();

        activity('perpustakaan')->performedOn($member)->log('anggota_perpustakaan_dihapus');

        return back()->with('status', 'Anggota dihapus.');
    }

    // ──────────────────────────────────────────────
    //  KATEGORI
    // ──────────────────────────────────────────────

    public function categoryIndex(): View
    {
        $this->authorize('viewAny', LibraryCategory::class);

        $categories = LibraryCategory::withCount('books')->orderBy('name')->paginate(15);

        return view('pages.perpustakaan.kategori', [
            'roleLabel' => 'Perpustakaan',
            'breadcrumb' => [
                ['label' => 'Sarpras & Perpustakaan', 'href' => route('dashboard')],
                ['label' => 'Katalog Buku', 'href' => route('perpustakaan.index')],
                ['label' => 'Kategori'],
            ],
            'categories' => $categories,
        ]);
    }

    public function categoryStore(Request $request): RedirectResponse
    {
        $this->authorize('create', LibraryCategory::class);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:60', 'unique:library_categories,name'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        LibraryCategory::create($validated);

        activity('perpustakaan')->log('kategori_perpustakaan_dibuat');

        return back()->with('status', 'Kategori ditambahkan.');
    }

    public function categoryUpdate(Request $request, LibraryCategory $category): RedirectResponse
    {
        $this->authorize('update', $category);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:60', Rule::unique('library_categories', 'name')->ignore($category->id)],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $category->update($validated);

        activity('perpustakaan')->performedOn($category)->log('kategori_perpustakaan_diubah');

        return back()->with('status', 'Kategori diperbarui.');
    }

    public function categoryDestroy(LibraryCategory $category): RedirectResponse
    {
        $this->authorize('delete', $category);

        if ($category->books()->exists()) {
            return back()->withErrors(['category' => 'Kategori masih berisi buku — tidak dapat dihapus.']);
        }

        $category->delete();

        activity('perpustakaan')->performedOn($category)->log('kategori_perpustakaan_dihapus');

        return back()->with('status', 'Kategori dihapus.');
    }

    // ──────────────────────────────────────────────
    //  HELPER
    // ──────────────────────────────────────────────

    protected function bookRules(?int $ignoreId = null): array
    {
        return [
            'title' => ['required', 'string', 'max:200'],
            'author' => ['required', 'string', 'max:120'],
            'publisher' => ['nullable', 'string', 'max:120'],
            'year' => ['nullable', 'integer', 'min:1900', 'max:'.(date('Y') + 1)],
            'category_id' => ['nullable', 'exists:library_categories,id'],
            'isbn' => ['nullable', 'string', 'max:30'],
            'total_qty' => ['required', 'integer', 'min:1'],
            'location' => ['nullable', 'string', 'max:100'],
            'is_ebook' => ['boolean'],
            'ebook_url' => ['nullable', 'url', 'max:500'],
            'description' => ['nullable', 'string', 'max:2000'],
            'status' => ['required', Rule::in(LibraryBook::STATUSES)],
        ];
    }

    protected function nextBookCode(): string
    {
        $prefix = 'BUK-'.now()->format('Ym').'-';
        $last = LibraryBook::where('code', 'like', $prefix.'%')
            ->orderByDesc('code')
            ->value('code');

        $seq = $last ? ((int) substr($last, -3)) + 1 : 1;

        return $prefix.str_pad((string) $seq, 3, '0', STR_PAD_LEFT);
    }

    protected function nextMemberNo(): string
    {
        $prefix = 'ANG-'.now()->format('Y').'-';
        $last = LibraryMember::where('member_no', 'like', $prefix.'%')
            ->orderByDesc('member_no')
            ->value('member_no');

        $seq = $last ? ((int) substr($last, -3)) + 1 : 1;

        return $prefix.str_pad((string) $seq, 3, '0', STR_PAD_LEFT);
    }
}
