<x-layouts.page :title="$letter->subject" :roleLabel="$roleLabel" :breadcrumb="$breadcrumb">
    {{-- Flash messages --}}
    @if(session('status'))
        <x-ui.alert variant="success" dismissible>{{ session('status') }}</x-ui.alert>
    @endif

    {{-- Action buttons --}}
    <x-slot:actions>
        <x-ui.button variant="secondary" icon="pencil" href="{{ route('surat.edit', $letter) }}">Ubah</x-ui.button>
        <x-ui.button variant="secondary" icon="printer" x-on:click="window.print()">Cetak</x-ui.button>
        <form method="POST" action="{{ route('surat.destroy', $letter) }}" onsubmit="return confirm('Yakin ingin menghapus surat ini?')" class="inline">
            @csrf
            @method('DELETE')
            <x-ui.button type="submit" variant="danger" icon="trash">Hapus</x-ui.button>
        </form>
    </x-slot:actions>

    {{-- Detail Surat --}}
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        {{-- Informasi Utama --}}
        <div class="lg:col-span-2 space-y-6">
            <x-ui.sheet title="Detail Surat" :pinned="true">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    {{-- Nomor Surat --}}
                    <div>
                        <p class="text-xs font-bold text-ink-soft">Nomor Surat</p>
                        <p class="mt-1 font-mono text-sm text-ink">{{ $letter->number ?? '–' }}</p>
                    </div>

                    {{-- Tanggal --}}
                    <div>
                        <p class="text-xs font-bold text-ink-soft">Tanggal</p>
                        <p class="mt-1 text-sm text-ink">{{ $letter->date->format('d F Y') }}</p>
                    </div>

                    {{-- Dari/Ke --}}
                    <div>
                        <p class="text-xs font-bold text-ink-soft">{{ $letter->isMasuk() ? 'Pengirim' : 'Penerima' }}</p>
                        <p class="mt-1 text-sm text-ink">{{ $letter->from_to }}</p>
                    </div>

                    {{-- Kategori --}}
                    <div>
                        <p class="text-xs font-bold text-ink-soft">Kategori</p>
                        <p class="mt-1 text-sm text-ink">{{ $letter->category ?? '–' }}</p>
                    </div>

                    {{-- Perihal --}}
                    <div class="sm:col-span-2">
                        <p class="text-xs font-bold text-ink-soft">Perihal</p>
                        <p class="mt-1 text-sm font-semibold text-ink">{{ $letter->subject }}</p>
                    </div>

                    {{-- Keterangan --}}
                    @if($letter->description)
                        <div class="sm:col-span-2">
                            <p class="text-xs font-bold text-ink-soft">Keterangan</p>
                            <p class="mt-1 text-sm text-ink whitespace-pre-line">{{ $letter->description }}</p>
                        </div>
                    @endif
                </div>
            </x-ui.sheet>

            {{-- Disposisi --}}
            @if($letter->disposition_to || $letter->disposition_note)
                <x-ui.sheet title="Disposisi">
                    <div class="space-y-3">
                        @if($letter->disposition_to)
                            <div>
                                <p class="text-xs font-bold text-ink-soft">Disposisi Ke</p>
                                <p class="mt-1 text-sm text-ink">{{ $letter->disposition_to }}</p>
                            </div>
                        @endif
                        @if($letter->disposition_note)
                            <div>
                                <p class="text-xs font-bold text-ink-soft">Catatan</p>
                                <p class="mt-1 text-sm text-ink whitespace-pre-line">{{ $letter->disposition_note }}</p>
                            </div>
                        @endif
                    </div>
                </x-ui.sheet>
            @endif

            {{-- Lampiran --}}
            @if($letter->file_path)
                <x-ui.sheet title="Lampiran">
                    <div class="flex items-center gap-3">
                        <x-svg-document-text class="size-8 text-ink-faint" />
                        <div>
                            <p class="text-sm font-medium text-ink">{{ basename($letter->file_path) }}</p>
                            <a href="{{ route('surat.download', $letter) }}" class="text-xs font-semibold text-primary hover:underline">Unduh File</a>
                        </div>
                    </div>
                </x-ui.sheet>
            @endif
        </div>

        {{-- Sidebar Info --}}
        <div class="space-y-6">
            {{-- Status --}}
            <x-ui.sheet title="Status">
                <div class="space-y-4">
                    {{-- Status --}}
                    <div>
                        <p class="text-xs font-bold text-ink-soft">Status Surat</p>
                        <div class="mt-2">
                            <x-ui.badge :variant="$letter->statusBadgeVariant()" :dot="true">{{ $letter->statusLabel() }}</x-ui.badge>
                        </div>
                    </div>

                    {{-- Prioritas --}}
                    <div>
                        <p class="text-xs font-bold text-ink-soft">Prioritas</p>
                        <div class="mt-2">
                            <x-ui.badge :variant="$letter->priorityBadgeVariant()" :dot="true">{{ $letter->priorityLabel() }}</x-ui.badge>
                        </div>
                    </div>

                    {{-- Tipe --}}
                    <div>
                        <p class="text-xs font-bold text-ink-soft">Tipe Surat</p>
                        <div class="mt-2">
                            <x-ui.badge variant="primary" :dot="false">{{ $letter->isMasuk() ? 'Surat Masuk' : 'Surat Keluar' }}</x-ui.badge>
                        </div>
                    </div>
                </div>
            </x-ui.sheet>

            {{-- Meta --}}
            <x-ui.sheet title="Informasi Sistem">
                <div class="space-y-3">
                    <div>
                        <p class="text-xs font-bold text-ink-soft">Dicatat Oleh</p>
                        <p class="mt-1 text-sm text-ink">{{ $letter->recorder?->name ?? '–' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-ink-soft">Tahun Ajaran</p>
                        <p class="mt-1 text-sm text-ink">{{ $letter->academicYear?->name ?? '–' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-ink-soft">Terakhir Diubah</p>
                        <p class="mt-1 text-sm text-ink">{{ $letter->updated_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
            </x-ui.sheet>

            {{-- Disposisi (Super Admin Only) --}}
            @can('disposition', $letter)
                <x-ui.sheet title="Disposisi Surat">
                    <form method="POST" action="{{ route('surat.disposition', $letter) }}">
                        @csrf
                        @method('PATCH')

                        <x-ui.field label="Disposisi Ke" required :error="$errors->first('disposition_to')">
                            <x-ui.input name="disposition_to" :value="old('disposition_to', $letter->disposition_to)" placeholder="Nama/jabatan" required />
                        </x-ui.field>

                        <x-ui.field label="Catatan" :error="$errors->first('disposition_note')">
                            <textarea name="disposition_note" rows="2" class="w-full rounded-[var(--radius-control)] bg-sheet px-3.5 py-2.5 text-sm text-ink placeholder:text-ink-faint ring-1 ring-inset ring-rule-strong transition duration-150 hover:ring-ink-faint/60 focus:outline-none focus:ring-2 focus:ring-primary" placeholder="Catatan disposisi">{{ old('disposition_note', $letter->disposition_note) }}</textarea>
                        </x-ui.field>

                        <x-ui.field label="Update Status" required :error="$errors->first('status')">
                            <x-ui.select name="status" :options="collect($statuses ?? ['diterima', 'diproses', 'selesai', 'arsip'])->mapWithKeys(fn($s) => [$s => ucwords(str_replace('_', ' ', $s))])" :selected="old('status', $letter->status)" required />
                        </x-ui.field>

                        <div class="mt-4">
                            <x-ui.button type="submit" variant="primary" icon="check">Simpan Disposisi</x-ui.button>
                        </div>
                    </form>
                </x-ui.sheet>
            @endcan
        </div>
    </div>

    {{-- Tombol Kembali --}}
    <div class="mt-6">
        <x-ui.button variant="ghost" icon="arrow-left" href="{{ route('surat.index', ['type' => $letter->type]) }}">Kembali ke Daftar</x-ui.button>
    </div>
</x-layouts.page>
