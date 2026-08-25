<x-layouts.page title="Edit Surat" :roleLabel="$roleLabel" :breadcrumb="$breadcrumb">
    {{-- Flash messages --}}
    @if($errors->any())
        <x-ui.alert variant="danger" dismissible>
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </x-ui.alert>
    @endif

    <form method="POST" action="{{ route('surat.update', $letter) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- Data Surat --}}
        <x-ui.sheet title="Data Surat" :pinned="true" class="mb-6">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                {{-- Nomor Surat --}}
                <x-ui.field label="Nomor Surat" :hint="$letter->isKeluar() ? 'Otomatis diisi jika kosong' : 'Opsional untuk surat masuk'">
                    <x-ui.input name="number" value="{{ old('number', $letter->number) }}" placeholder="{{ $letter->isKeluar() ? 'Otomatis' : 'Nomor surat (opsional)' }}" />
                </x-ui.field>

                {{-- Tanggal Surat --}}
                <x-ui.field label="Tanggal Surat" required :error="$errors->first('date')">
                    <x-ui.input type="date" name="date" :value="old('date', $letter->date->format('Y-m-d'))" required />
                </x-ui.field>

                {{-- Dari/Ke --}}
                <x-ui.field label="{{ $letter->isMasuk() ? 'Pengirim' : 'Penerima' }}" required :error="$errors->first('from_to')">
                    <x-ui.input name="from_to" :value="old('from_to', $letter->from_to)" placeholder="{{ $letter->isMasuk() ? 'Nama pengirim/instansi' : 'Nama penerima/instansi' }}" required />
                </x-ui.field>

                {{-- Kategori --}}
                <x-ui.field label="Kategori" :error="$errors->first('category')">
                    <x-ui.select name="category" :options="$categories->pluck('name', 'name')" :selected="old('category', $letter->category)" placeholder="Pilih kategori…" />
                </x-ui.field>

                {{-- Perihal --}}
                <div class="sm:col-span-2">
                    <x-ui.field label="Perihal" required :error="$errors->first('subject')">
                        <x-ui.input name="subject" :value="old('subject', $letter->subject)" placeholder="Perihal surat" required />
                    </x-ui.field>
                </div>

                {{-- Keterangan --}}
                <div class="sm:col-span-2">
                    <x-ui.field label="Keterangan" :error="$errors->first('description')">
                        <textarea name="description" rows="3" class="w-full rounded-[var(--radius-control)] bg-sheet px-3.5 py-2.5 text-sm text-ink placeholder:text-ink-faint ring-1 ring-inset ring-rule-strong transition duration-150 hover:ring-ink-faint/60 focus:outline-none focus:ring-2 focus:ring-primary" placeholder="Keterangan tambahan (opsional)">{{ old('description', $letter->description) }}</textarea>
                    </x-ui.field>
                </div>
            </div>
        </x-ui.sheet>

        {{-- Status & Prioritas --}}
        <x-ui.sheet title="Status & Prioritas" :pinned="true" class="mb-6">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                {{-- Status --}}
                <x-ui.field label="Status" required :error="$errors->first('status')">
                    <x-ui.select name="status" :options="collect($statuses)->mapWithKeys(fn($s) => [$s => ucwords(str_replace('_', ' ', $s))])" :selected="old('status', $letter->status)" required />
                </x-ui.field>

                {{-- Prioritas --}}
                <x-ui.field label="Prioritas" required :error="$errors->first('priority')">
                    <x-ui.select name="priority" :options="collect($priorities)->mapWithKeys(fn($p) => [$p => ucfirst($p)])" :selected="old('priority', $letter->priority)" required />
                </x-ui.field>
            </div>
        </x-ui.sheet>

        {{-- Disposisi --}}
        <x-ui.sheet title="Disposisi (Opsional)" subtitle="Catatan disposisi untuk tindak lanjut" class="mb-6">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                {{-- Disposisi Ke --}}
                <x-ui.field label="Disposisi Ke" :error="$errors->first('disposition_to')">
                    <x-ui.input name="disposition_to" :value="old('disposition_to', $letter->disposition_to)" placeholder="Nama/jabatan penerima disposisi" />
                </x-ui.field>

                {{-- Catatan Disposisi --}}
                <div class="sm:col-span-2">
                    <x-ui.field label="Catatan Disposisi" :error="$errors->first('disposition_note')">
                        <textarea name="disposition_note" rows="2" class="w-full rounded-[var(--radius-control)] bg-sheet px-3.5 py-2.5 text-sm text-ink placeholder:text-ink-faint ring-1 ring-inset ring-rule-strong transition duration-150 hover:ring-ink-faint/60 focus:outline-none focus:ring-2 focus:ring-primary" placeholder="Catatan untuk penerima disposisi (opsional)">{{ old('disposition_note', $letter->disposition_note) }}</textarea>
                    </x-ui.field>
                </div>
            </div>
        </x-ui.sheet>

        {{-- Lampiran --}}
        <x-ui.sheet title="Lampiran PDF" subtitle="Link ke file PDF (opsional)" class="mb-6">
            @if($letter->file_url)
                <div class="mb-4 flex items-center gap-3 rounded-[var(--radius-control)] bg-paper-deep p-3">
                    <x-svg-document-text class="size-6 text-ink-faint" />
                    <div>
                        <a href="{{ $letter->file_url }}" target="_blank" class="text-sm font-medium text-primary hover:underline">{{ $letter->file_url }}</a>
                        <p class="text-xs text-ink-soft">Link PDF saat ini</p>
                    </div>
                </div>
            @endif
            <x-ui.field label="URL File PDF" hint="Masukkan link ke dokumen PDF (Google Drive, dll)" :error="$errors->first('file_url')">
                <x-ui.input name="file_url" type="url" value="{{ old('file_url', $letter->file_url) }}" placeholder="https://drive.google.com/file/d/..." />
            </x-ui.field>
        </x-ui.sheet>

        {{-- Tombol Aksi --}}
        <div class="flex items-center justify-end gap-3">
            <x-ui.button variant="ghost" icon="arrow-left" href="{{ route('surat.show', $letter) }}">Batal</x-ui.button>
            <x-ui.button type="submit" variant="primary" icon="check">Simpan Perubahan</x-ui.button>
        </div>
    </form>
</x-layouts.page>
