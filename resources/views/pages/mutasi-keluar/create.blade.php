@php
    $action = $editing ? route('mutasi-keluar.update', $mutation) : route('mutasi-keluar.store');
    $method = $editing ? 'PUT' : 'POST';
    $old = fn ($key) => old($key, $editing ? $mutation->{$key} : null);
    $studentsByClass = $students->groupBy(fn ($s) => $s->enrollments->first()?->classGroup?->name ?? 'Tanpa rombel');
@endphp
<x-layouts.page
    :title="$editing ? 'Ubah Mutasi Keluar' : 'Catat Mutasi Keluar'"
    :roleLabel="$roleLabel"
    :breadcrumb="$breadcrumb"
    active-route="{{ $editing ? 'mutasi-keluar.edit' : 'mutasi-keluar.create' }}">

    <div class="mx-auto max-w-3xl">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">{{ $editing ? 'Ubah Mutasi Keluar' : 'Catat Mutasi Keluar' }}</h1>
                <p class="mt-1.5 max-w-prose text-sm leading-relaxed text-ink-soft">
                    Pilih siswa yang pindah keluar lalu isi detail tujuan &amp; alasan.
                    @unless ($editing)
                        Mencatat akan melepas siswa dari rombel aktif tahun berjalan.
                    @endunless
                </p>
            </div>
        </div>

        @if ($errors->any())
            <div class="mt-6"><x-ui.alert variant="danger" dismissible>@foreach ($errors->all() as $error) {{ $error }} @endforeach</x-ui.alert></div>
        @endif

        <form method="POST" action="{{ $action }}" class="mt-6 space-y-6">
            @csrf
            @method($method)

            <x-ui.sheet title="Siswa" pinned ruled>
                @if ($editing)
                    <input type="hidden" name="student_id" value="{{ $mutation->student_id }}">
                    <div class="flex items-center gap-3 rounded-[var(--radius-control)] bg-paper-deep px-3.5 py-2.5 ring-1 ring-inset ring-rule-strong">
                        <span class="flex size-8 shrink-0 items-center justify-center rounded-full bg-primary-soft text-[11px] font-extrabold text-primary-strong">{{ mb_substr($mutation->student->displayName(), 0, 1) }}</span>
                        <span class="text-sm font-semibold text-ink">{{ $mutation->student->displayName() }} <span class="tabular font-mono text-xs font-semibold text-ink-faint">({{ $mutation->student->nis ?? 'tanpa NIS' }})</span></span>
                    </div>
                @else
                    <label class="block text-xs font-bold text-ink">Siswa <span class="text-danger" aria-hidden="true">*</span></label>
                    <div class="mt-1.5" x-data="studentPicker({{ json_encode(old('student_id')) }})">
                        <div class="relative" x-show="!pickedId">
                            <x-svg-magnifying-glass class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-ink-faint" aria-hidden="true" />
                            <input type="text" x-model="query" placeholder="Ketik nama atau NIS untuk mencari…"
                                class="w-full rounded-[var(--radius-control)] bg-sheet py-2.5 pl-9 pr-3 text-sm text-ink placeholder:text-ink-faint ring-1 ring-inset ring-rule-strong transition focus:outline-none focus:ring-2 focus:ring-primary"
                                x-ref="searchInput" x-init="$nextTick(() => $refs.searchInput.focus())">
                        </div>

                        <div class="mt-1.5 max-h-52 overflow-y-auto rounded-[var(--radius-control)] ring-1 ring-inset ring-rule/70"
                            x-show="pool.length > 0 && !pickedId && query.trim() !== ''">
                            <template x-for="s in pool" :key="s.id">
                                <button type="button" x-on:click="pickedId = s.id; query = ''"
                                    class="flex w-full items-center justify-between gap-3 px-3 py-2.5 text-left text-[13px] text-ink transition hover:bg-primary-soft"
                                    :class="pickedId === s.id ? 'bg-primary-soft font-semibold' : ''">
                                    <div class="flex items-center gap-2.5">
                                        <span class="flex size-7 shrink-0 items-center justify-center rounded-full bg-primary-soft text-[10px] font-extrabold text-primary-strong" x-text="s.label.charAt(0)"></span>
                                        <span class="font-semibold text-ink" x-text="s.label"></span>
                                    </div>
                                    <span class="shrink-0 text-[11px] tabular text-ink-faint" x-text="s.nis + ' · ' + s.kelas"></span>
                                </button>
                            </template>
                        </div>

                        <p class="mt-1.5 rounded-[var(--radius-control)] bg-paper-deep px-3 py-2 text-xs text-ink-faint"
                            x-show="pool.length === 0 && !pickedId" x-cloak
                            x-text="query.trim() ? 'Tidak ada hasil untuk \"' + query + '\".' : 'Ketik nama atau NIS untuk mulai mencari…'"></p>

                        <input type="hidden" name="student_id" :value="pickedId ?? ''">

                        <p class="mt-2 flex items-center gap-1.5 text-xs text-ink-faint" x-show="picked" x-cloak>
                            Terpilih:
                            <span class="font-semibold text-ink" x-text="picked?.label"></span>
                            <span class="tabular" x-text="picked?.nis + ' · ' + picked?.kelas"></span>
                            <button type="button" x-on:click="pickedId = null; query = ''"
                                class="ml-1 text-danger hover:underline" title="Batalkan pilihan">✕</button>
                        </p>
                        @if ($errors->first('student_id'))
                            <p class="mt-2 flex items-center gap-1 text-xs font-medium text-danger">
                                <x-svg-exclamation-circle class="size-3.5" aria-hidden="true" />
                                {{ $errors->first('student_id') }}
                            </p>
                        @endif
                    </div>
                @endif
            </x-ui.sheet>

            <x-ui.sheet title="Detail Mutasi" pinned ruled>
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <x-ui.field label="Tanggal Mutasi" :required="true" :error="$errors->first('tanggal_mutasi')">
                        <input type="date" name="tanggal_mutasi" value="{{ $old('tanggal_mutasi')?->format('Y-m-d') ?? old('tanggal_mutasi') }}" required
                            class="w-full rounded-[var(--radius-control)] bg-sheet px-3.5 py-2.5 text-sm text-ink ring-1 ring-inset ring-rule-strong focus:outline-none focus:ring-2 focus:ring-primary">
                    </x-ui.field>
                    <x-ui.field label="Nomor Surat" :hint="'Opsional — nomor surat keterangan pindah.'" :error="$errors->first('no_surat')">
                        <input type="text" name="no_surat" value="{{ $old('no_surat') }}" maxlength="100"
                            class="w-full rounded-[var(--radius-control)] bg-sheet px-3.5 py-2.5 text-sm text-ink ring-1 ring-inset ring-rule-strong focus:outline-none focus:ring-2 focus:ring-primary" placeholder="Contoh: 421.2/001/MTs">
                    </x-ui.field>
                    <x-ui.field label="Sekolah Tujuan" :required="true" :error="$errors->first('sekolah_tujuan')">
                        <input type="text" name="sekolah_tujuan" value="{{ $old('sekolah_tujuan') }}" maxlength="100" required
                            class="w-full rounded-[var(--radius-control)] bg-sheet px-3.5 py-2.5 text-sm text-ink ring-1 ring-inset ring-rule-strong focus:outline-none focus:ring-2 focus:ring-primary" placeholder="Nama madrasah / sekolah tujuan">
                    </x-ui.field>
                    <x-ui.field label="Alasan Pindah" :required="true" :error="$errors->first('alasan_pindah')">
                        <x-ui.select name="alasan_pindah"
                            :options="['pindah_ortu' => 'Mengikuti orang tua', 'pindah_alamat' => 'Pindah alamat / domisili', 'keluarga' => 'Alasan keluarga', 'lainnya' => 'Lainnya']"
                            :selected="old('alasan_pindah', $editing ? $mutation->alasan_pindah : null)" />
                    </x-ui.field>
                    <x-ui.field label="NSM Tujuan" :hint="'Opsional'" :error="$errors->first('tujuan_nsm')">
                        <input type="text" name="tujuan_nsm" value="{{ $old('tujuan_nsm') }}" maxlength="12"
                            class="w-full rounded-[var(--radius-control)] bg-sheet px-3.5 py-2.5 text-sm text-ink ring-1 ring-inset ring-rule-strong focus:outline-none focus:ring-2 focus:ring-primary">
                    </x-ui.field>
                    <x-ui.field label="NPSN Tujuan" :hint="'Opsional'" :error="$errors->first('tujuan_npsn')">
                        <input type="text" name="tujuan_npsn" value="{{ $old('tujuan_npsn') }}" maxlength="8"
                            class="w-full rounded-[var(--radius-control)] bg-sheet px-3.5 py-2.5 text-sm text-ink ring-1 ring-inset ring-rule-strong focus:outline-none focus:ring-2 focus:ring-primary">
                    </x-ui.field>
                    <div class="sm:col-span-2">
                        <x-ui.field label="Keterangan" :hint="'Opsional — catatan tambahan.'" :error="$errors->first('keterangan')">
                            <textarea name="keterangan" rows="3" maxlength="1000"
                                class="w-full rounded-[var(--radius-control)] bg-sheet px-3.5 py-2.5 text-sm text-ink ring-1 ring-inset ring-rule-strong focus:outline-none focus:ring-2 focus:ring-primary">{{ $old('keterangan') }}</textarea>
                        </x-ui.field>
                    </div>
                </div>
            </x-ui.sheet>

            <div class="flex items-center justify-end gap-2">
                <x-ui.button variant="ghost" size="md" href="{{ route('mutasi-keluar.index') }}">Batal</x-ui.button>
                <x-ui.button type="submit" variant="primary" size="md" icon="check">
                    {{ $editing ? 'Simpan Perubahan' : 'Catat Mutasi Keluar' }}
                </x-ui.button>
            </div>
        </form>
    </div>
    @unless ($editing)
    <script>
        function studentPicker(initialId) {
            return {
                query: '',
                pickedId: initialId ? Number(initialId) : null,
                students: @json($studentPool),
                get pool() {
                    const needle = this.query.trim().toLowerCase();
                    return needle
                        ? this.students.filter(s => s.label.toLowerCase().includes(needle) || s.nis.toLowerCase().includes(needle))
                        : [];
                },
                get picked() {
                    return this.students.find(s => s.id === this.pickedId);
                },
            };
        }
    </script>
    @endunless
</x-layouts.page>