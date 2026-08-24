@php
    $editing = isset($journal) && $journal !== null;
    $periods = collect(range(1, 10))->mapWithKeys(fn ($p) => [$p => 'Jam ke-'.$p])->all();
@endphp

<form method="POST"
    action="{{ $editing ? route('guru.jurnal.update', [$assignment, $journal]) : route('guru.jurnal.store', $assignment) }}"
    enctype="multipart/form-data"
    class="space-y-6">
    @csrf
    @if ($editing)
        @method('PUT')
    @endif

    <x-ui.form-section title="Catatan Pembelajaran" description="Materi wajib diisi; catatan lain membantu wakamad kurikulum memantau pelaksanaan.">
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
            <x-ui.field label="Tanggal Pembelajaran" required :error="$errors->first('journal_date')">
                <x-ui.input type="date" name="journal_date"
                    :value="old('journal_date', $editing ? $journal->journal_date->format('Y-m-d') : now()->format('Y-m-d'))"
                    max="{{ now()->format('Y-m-d') }}" />
            </x-ui.field>
            <x-ui.field label="Jam Pelajaran ke" hint="Opsional — kosongkan bila tidak tercatat di jadwal." :error="$errors->first('period_no')">
                <x-ui.select name="period_no" :options="$periods" :selected="old('period_no', $editing ? $journal->period_no : null)" placeholder="—" />
            </x-ui.field>

            <div class="sm:col-span-2">
                <x-ui.field label="Materi Pembelajaran" required :error="$errors->first('materi')">
                    <textarea name="materi" rows="3" required placeholder="Materi yang dibahas hari ini…"
                        class="w-full rounded-[var(--radius-control)] bg-sheet px-3.5 py-2.5 text-sm text-ink placeholder:text-ink-faint ring-1 ring-inset transition duration-150 focus:outline-none focus:ring-2 focus:ring-primary {{ $errors->has('materi') ? 'ring-danger/60 focus:ring-danger' : 'ring-rule-strong hover:ring-ink-faint/60 focus:ring-primary' }}">{{ old('materi', $editing ? $journal->materi : '') }}</textarea>
                </x-ui.field>
            </div>

            <div class="sm:col-span-2">
                <x-ui.field label="Tujuan Pembelajaran" :error="$errors->first('tujuan')">
                    <textarea name="tujuan" rows="2" placeholder="Capaian yang diharapkan setelah pembelajaran…"
                        class="w-full rounded-[var(--radius-control)] bg-sheet px-3.5 py-2.5 text-sm text-ink placeholder:text-ink-faint ring-1 ring-inset transition duration-150 focus:outline-none focus:ring-2 focus:ring-primary {{ $errors->has('tujuan') ? 'ring-danger/60 focus:ring-danger' : 'ring-rule-strong hover:ring-ink-faint/60 focus:ring-primary' }}">{{ old('tujuan', $editing ? $journal->tujuan : '') }}</textarea>
                </x-ui.field>
            </div>

            <x-ui.field label="Metode" :error="$errors->first('metode')">
                <x-ui.input name="metode" :value="old('metode', $editing ? $journal->metode : '')" placeholder="Mis. ceramah, diskusi, permainan" />
            </x-ui.field>

            <x-ui.field label="Tindak Lanjut" :error="$errors->first('tindak_lanjut')">
                <x-ui.input name="tindak_lanjut" :value="old('tindak_lanjut', $editing ? $journal->tindak_lanjut : '')" placeholder="Remedial, latihan rumah, dll." />
            </x-ui.field>

            <div class="sm:col-span-2">
                <x-ui.field label="Catatan Kegiatan" :error="$errors->first('catatan')">
                    <textarea name="catatan" rows="2" placeholder="Kondisi kelas, siswa yang perlu perhatian, hambatan, dll."
                        class="w-full rounded-[var(--radius-control)] bg-sheet px-3.5 py-2.5 text-sm text-ink placeholder:text-ink-faint ring-1 ring-inset transition duration-150 focus:outline-none focus:ring-2 focus:ring-primary {{ $errors->has('catatan') ? 'ring-danger/60 focus:ring-danger' : 'ring-rule-strong hover:ring-ink-faint/60 focus:ring-primary' }}">{{ old('catatan', $editing ? $journal->catatan : '') }}</textarea>
                </x-ui.field>
            </div>

            <div class="sm:col-span-2">
                <x-ui.field label="Lampiran" hint="Opsional — PDF, dokumen, atau gambar, maks. 5 MB." :error="$errors->first('lampiran')">
                    <input type="file" name="lampiran"
                        class="w-full rounded-[var(--radius-control)] bg-sheet px-3.5 py-2.5 text-sm text-ink file:mr-3 file:rounded file:border-0 file:bg-primary-soft file:px-3 file:py-1.5 file:text-xs file:font-bold file:text-primary-strong ring-1 ring-inset ring-rule-strong transition duration-150 focus:outline-none focus:ring-2 focus:ring-primary" />
                    @if ($editing && $journal->lampiran)
                        <p class="mt-2 flex flex-wrap items-center gap-2 text-xs text-ink-faint">
                            Lampiran saat ini:
                            <a href="{{ route('guru.jurnal.lampiran', [$assignment, $journal]) }}"
                                class="inline-flex items-center gap-1 font-semibold text-primary underline-offset-2 hover:underline">
                                <x-svg-paper-clip class="size-3.5" aria-hidden="true" />
                                {{ basename($journal->lampiran) }}
                            </a>
                        </p>
                        <label class="mt-1.5 inline-flex items-center gap-2 text-xs font-medium text-ink-soft">
                            <input type="checkbox" name="hapus_lampiran" value="1" class="size-4 border-rule-strong text-primary focus:ring-primary">
                            Hapus lampiran saat ini
                        </label>
                    @endif
                </x-ui.field>
            </div>
        </div>
    </x-ui.form-section>

    <div class="flex flex-col-reverse items-center justify-between gap-3 border-t border-rule-strong/60 pt-5 sm:flex-row">
        <x-ui.button variant="ghost" icon="arrow-left" href="{{ route('guru.jurnal.show', $assignment) }}">Kembali</x-ui.button>
        <div class="flex flex-wrap items-center gap-2">
            <x-ui.button type="submit" name="status" value="draft" variant="secondary" icon="document-text">Simpan Draf</x-ui.button>
            <x-ui.button type="submit" name="status" value="terisi" variant="primary" icon="check">
                {{ $editing ? 'Sematkan Perubahan' : 'Sematkan & Tandai Terisi' }}
            </x-ui.button>
        </div>
    </div>
</form>
