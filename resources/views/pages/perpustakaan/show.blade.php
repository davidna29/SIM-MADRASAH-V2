<x-layouts.page
    :title="$book->title"
    :roleLabel="$roleLabel"
    :breadcrumb="$breadcrumb"
    active-route="perpustakaan.show">

    <div class="mx-auto max-w-4xl">
        @if (session('status'))
            <div class="mb-6">
                <x-ui.alert variant="success" dismissible>{{ session('status') }}</x-ui.alert>
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6">
                <x-ui.alert variant="danger" dismissible>
                    <strong class="font-bold">Periksa kembali:</strong>
                    @foreach ($errors->all() as $error) {{ $error }} @endforeach
                </x-ui.alert>
            </div>
        @endif

        <!-- Detail Buku -->
        <x-ui.sheet title="Detail Buku" :subtitle="$book->code">
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div class="space-y-3">
                    <div>
                        <p class="text-xs font-bold text-ink-faint">Judul</p>
                        <p class="text-sm font-semibold text-ink">{{ $book->title }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-ink-faint">Pengarang</p>
                        <p class="text-sm text-ink">{{ $book->author }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-ink-faint">Penerbit</p>
                        <p class="text-sm text-ink">{{ $book->publisher ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-ink-faint">Tahun / ISBN</p>
                        <p class="text-sm text-ink">{{ $book->year ?? '—' }} @if ($book->isbn) · {{ $book->isbn }} @endif</p>
                    </div>
                </div>
                <div class="space-y-3">
                    <div>
                        <p class="text-xs font-bold text-ink-faint">Kategori</p>
                        <p class="text-sm text-ink">{{ $book->category?->name ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-ink-faint">Stok (Tersedia / Total)</p>
                        <p class="text-sm font-semibold text-ink">{{ $book->available_qty }} / {{ $book->total_qty }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-ink-faint">Lokasi</p>
                        <p class="text-sm text-ink">{{ $book->location ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-ink-faint">Status</p>
                        <x-ui.badge :variant="$book->status === 'tersedia' ? 'success' : 'neutral'">{{ ucwords($book->status) }}</x-ui.badge>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-ink-faint">Ebook</p>
                        @if ($book->is_ebook && $book->ebook_url)
                            <a href="{{ $book->ebook_url }}" target="_blank" rel="noopener" class="text-sm text-primary underline">Buka Ebook ↗</a>
                        @elseif ($book->is_ebook)
                            <x-ui.badge variant="info">Ebook (URL belum diisi)</x-ui.badge>
                        @else
                            <span class="text-sm text-ink-faint">Tidak</span>
                        @endif
                    </div>
                </div>
            </div>
            @if ($book->description)
                <div class="mt-4 border-t border-rule/50 pt-4">
                    <p class="text-xs font-bold text-ink-faint">Deskripsi</p>
                    <p class="mt-1 text-sm leading-relaxed text-ink-soft">{{ $book->description }}</p>
                </div>
            @endif

            <div class="mt-6 flex items-center gap-3 border-t border-rule/50 pt-4">
                @can('update', $book)
                    <x-ui.button size="sm" variant="secondary" icon="pencil" href="{{ route('perpustakaan.edit', $book) }}">Ubah</x-ui.button>
                @endcan
                @can('delete', $book)
                    <form method="POST" action="{{ route('perpustakaan.destroy', $book) }}" onsubmit="return confirm('Hapus buku ini dari katalog?');">
                        @csrf
                        @method('DELETE')
                        <x-ui.button type="submit" size="sm" variant="ghost" icon="trash">Hapus</x-ui.button>
                    </form>
                @endcan
            </div>
        </x-ui.sheet>

        @if ($canManage && $book->status === 'tersedia')
        <!-- Form Peminjaman -->
        <div class="mt-6">
            <x-ui.sheet title="Catat Peminjaman">
                <form method="POST" action="{{ route('perpustakaan.loan.store', $book) }}" class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    @csrf
                    <x-ui.field label="Anggota" required :error="$errors->first('member_id')">
                        <x-ui.select name="member_id" class="w-full"
                            :options="$members->pluck('name', 'id')"
                            placeholder="Pilih anggota…" />
                    </x-ui.field>
                    <div></div>
                    <x-ui.field label="Tanggal Pinjam" required :error="$errors->first('loan_date')">
                        <x-ui.input type="date" name="loan_date" value="{{ old('loan_date', now()->format('Y-m-d')) }}" />
                    </x-ui.field>
                    <x-ui.field label="Tanggal Kembali" required :error="$errors->first('due_date')">
                        <x-ui.input type="date" name="due_date" value="{{ old('due_date', now()->addDays(7)->format('Y-m-d')) }}" />
                    </x-ui.field>
                    <div class="sm:col-span-2">
                        <x-ui.field label="Catatan" :error="$errors->first('note')">
                            <x-ui.input name="note" :value="old('note')" placeholder="Opsional…" />
                        </x-ui.field>
                    </div>
                    <div class="sm:col-span-2">
                        <x-ui.button type="submit" variant="primary" icon="plus">Catat Pinjam</x-ui.button>
                    </div>
                </form>
            </x-ui.sheet>
        </div>
        @endif

        <!-- Riwayat Peminjaman -->
        <div class="mt-6">
            <x-ui.sheet title="Riwayat Peminjaman" :subtitle="$loans->total() . ' transaksi'" pinned :padding="false">
                <x-ui.table :headers="['Anggota', 'Pinjam', 'Jatuh Tempo', 'Kembali', 'Status', '']">
                    <x-slot name="emptySlot">Belum ada riwayat peminjaman.</x-slot>
                    <x-slot>
                        @foreach ($loans as $loan)
                            <tr class="transition hover:bg-paper/60">
                                <td class="whitespace-nowrap px-4 py-3 text-sm text-ink">{{ $loan->member?->name ?? '—' }}</td>
                                <td class="whitespace-nowrap px-4 py-3 text-xs text-ink-soft">{{ $loan->loan_date->format('d/m/Y') }}</td>
                                <td class="whitespace-nowrap px-4 py-3 text-xs text-ink-soft">{{ $loan->due_date->format('d/m/Y') }}</td>
                                <td class="whitespace-nowrap px-4 py-3 text-xs text-ink-soft">{{ $loan->return_date?->format('d/m/Y') ?? '—' }}</td>
                                <td class="whitespace-nowrap px-4 py-3">
                                    <x-ui.badge :variant="match($loan->status) { 'dipinjam' => 'warning', 'terlambat' => 'danger', default => 'success' }">{{ ucwords(str_replace('_', ' ', $loan->status)) }}</x-ui.badge>
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 text-right">
                                    @if ($loan->status === 'dipinjam' && $canManage)
                                        <form method="POST" action="{{ route('perpustakaan.loan.return', [$book, $loan]) }}" onsubmit="return confirm('Kembalikan buku ini?');">
                                            @csrf
                                            <x-ui.button type="submit" size="sm" variant="secondary" icon="arrow-uturn-left">Kembali</x-ui.button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </x-slot>
                </x-ui.table>
                <div class="border-t border-rule/70 px-5 py-3">
                    <x-ui.pagination :current="$loans->currentPage()" :last="$loans->lastPage()" />
                </div>
            </x-ui.sheet>
        </div>

        <div class="mt-6">
            <x-ui.button variant="ghost" icon="arrow-left" href="{{ route('perpustakaan.index') }}">Kembali ke Katalog</x-ui.button>
        </div>
    </div>
</x-layouts.page>
