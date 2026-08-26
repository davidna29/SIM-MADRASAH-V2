<x-layouts.page
    :title="'Tentukan Kelas'"
    :roleLabel="$roleLabel"
    :breadcrumb="$breadcrumb"
    active-route="ppdb.assign-class-page">

    <div class="mx-auto max-w-6xl">
        @include('pages.ppdb.partials.steps', [
            'active' => 'ppdb.assign-class-page',
            'note' => 'Kelas/rombel wajib sudah dibuat di menu Kelas & Penempatan. Belum ada batas kapasitas otomatis — pastikan distribusi merata.',
        ])

        <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">Tentukan Kelas / Rombel</h1>
        <p class="mt-1.5 text-sm text-ink-soft">Distribusikan siswa diterima ke tiap rombel yang sudah dibuat di menu Kelas &amp; Penempatan.</p>

        @if (session('status'))
            <div class="mt-6"><x-ui.alert variant="success" dismissible>{{ session('status') }}</x-ui.alert></div>
        @endif

        @if ($errors->any())
            <div class="mt-6"><x-ui.alert variant="danger" dismissible>{{ $errors->first() }}</x-ui.alert></div>
        @endif

        {{-- Peringatan jika belum ada kelas --}}
        @if ($classes->isEmpty())
            <div class="mt-6">
                <x-ui.alert variant="warning" :dismissible="false">
                    Belum ada kelas/rombel. Silakan buat kelas terlebih dahulu di menu
                    <a href="{{ route('kelas.index') }}" class="font-bold underline">Kelas &amp; Penempatan</a>
                    (mis. I-A, II-B, dst.) sebelum menentukan kelas siswa.
                </x-ui.alert>
            </div>
        @endif

        {{-- Class Distribution Summary --}}
        @if ($classCounts->isNotEmpty())
            <div class="mt-6">
                <x-ui.sheet title="Distribusi per Kelas" pinned ruled>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($classes as $class)
                            @php $count = $classCounts->get($class->name, 0); @endphp
                            <x-ui.badge variant="{{ $count >= 20 ? 'danger' : 'primary' }}" :dot="false">
                                {{ $class->name }}: {{ $count }} siswa
                            </x-ui.badge>
                        @endforeach
                    </div>
                </x-ui.sheet>
            </div>
        @endif

        {{-- Penetapan Massal & Sebar Rata (satu scope Alpine untuk checkbox) --}}
        @if (! $accepted->isEmpty())
            <div class="mt-6" x-data="{ selected: [] }" @change="selected = Array.from($root.querySelectorAll('input[name=\'ids[]\']:checked')).map(i => i.value)">
                <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
                    {{-- Bulk: pilih banyak siswa + 1 kelas --}}
                    <x-ui.sheet title="Tetapkan Kelas Terpilih" subtitle="Centang beberapa siswa, lalu pilih satu kelas" pinned ruled>
                        <form method="POST" action="{{ route('ppdb.assign-class-bulk') }}"
                            @submit="if(selected.length === 0) { event.preventDefault(); alert('Pilih minimal satu siswa.'); }">
                            @csrf
                            <input type="hidden" name="registration_ids[]" :value="selected" />
                            <div class="flex flex-wrap items-end gap-3">
                                <x-ui.field label="Kelas / Rombel" :error="$errors->first('class_name')">
                                    <x-ui.select name="class_name" :options="$classOptions" placeholder="-- Pilih kelas --" :disabled="$classes->isEmpty()" />
                                </x-ui.field>
                                <x-ui.button type="submit" variant="primary" size="md" :disabled="$classes->isEmpty()">Tetapkan</x-ui.button>
                                <p class="text-xs text-ink-faint" x-text="selected.length + ' siswa dipilih'"></p>
                            </div>
                            <p class="mt-2 text-xs text-ink-faint">Gunakan kotak centang pada tabel di bawah untuk memilih siswa.</p>
                        </form>
                    </x-ui.sheet>

                    {{-- Sebar Rata per Tingkat --}}
                    <x-ui.sheet title="Sebar Rata per Tingkat" subtitle="Bagi rata siswa terpilih ke semua rombel satu tingkat" pinned ruled>
                        <form method="POST" action="{{ route('ppdb.assign-class-distribute') }}"
                            @submit="if(selected.length === 0) { event.preventDefault(); alert('Pilih minimal satu siswa.'); }">
                            @csrf
                            <input type="hidden" name="registration_ids[]" :value="selected" />
                            <div class="flex flex-wrap items-end gap-3">
                                <x-ui.field label="Tingkat" :error="$errors->first('grade_level')">
                                    <x-ui.select name="grade_level" :options="['I' => 'I', 'II' => 'II', 'III' => 'III', 'IV' => 'IV', 'V' => 'V', 'VI' => 'VI']" placeholder="-- Pilih tingkat --" />
                                </x-ui.field>
                                <x-ui.button type="submit" variant="secondary" size="md" icon="arrows-pointing-out">Sebar Rata</x-ui.button>
                                <p class="text-xs text-ink-faint" x-text="selected.length + ' siswa disebar'"></p>
                            </div>
                            <p class="mt-2 text-xs text-ink-faint">Membagi rata siswa terpilih (yang belum dapat kelas) ke rombel tingkat tersebut secara bergiliran.</p>
                        </form>
                    </x-ui.sheet>
                </div>

                {{-- Siswa Belum Dikelompokkan --}}
                <div class="mt-6">
                    <x-ui.sheet title="Siswa Belum Dikelompokkan" :subtitle="$accepted->count() . ' siswa'" :padding="false" pinned ruled>
                        <x-ui.table :headers="['', 'No. Daftar', 'Nama', 'NIS', 'Kelas', 'Aksi']">
                            @foreach ($accepted as $reg)
                                <tr class="hover:bg-paper-deep/50">
                                    <td class="w-8 px-4 py-3 text-center">
                                        <input type="checkbox" name="ids[]" value="{{ $reg->id }}"
                                            class="size-4 rounded border-rule-strong text-primary focus:ring-primary" />
                                    </td>
                                    <td class="px-4 py-3 font-mono text-xs">{{ $reg->registration_no }}</td>
                                    <td class="px-4 py-3 text-sm font-bold">{{ strtoupper($reg->name) }}</td>
                                    <td class="px-4 py-3 font-mono text-xs text-primary">{{ $reg->nis_nism ?? '—' }}</td>
                                    <td class="px-4 py-3">{{ $reg->rombel ?? '—' }}</td>
                                    <td class="px-4 py-3 text-right">
                                        <form method="POST" action="{{ route('ppdb.assign-class', $reg) }}" class="inline-flex items-center gap-2">
                                            @csrf
                                            <select name="class_name" required
                                                class="h-8 rounded-[var(--radius-control)] bg-sheet px-2 text-xs ring-1 ring-inset ring-rule-strong focus:ring-2 focus:ring-primary">
                                                <option value="">-- Pilih kelas --</option>
                                                @foreach ($classOptions as $name => $label)
                                                    <option value="{{ $name }}" {{ $reg->rombel === $name ? 'selected' : '' }}>{{ $label }}</option>
                                                @endforeach
                                            </select>
                                            <x-ui.button type="submit" variant="primary" size="sm" :disabled="$classes->isEmpty()">Simpan</x-ui.button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </x-ui.table>
                        <div class="border-t border-rule/70 px-4 py-3 text-xs text-ink-faint">
                            Centang beberapa baris untuk penetapan massal atau sebar rata (panel di atas).
                        </div>
                    </x-ui.sheet>
                </div>
            </div>
        @endif
    </div>
</x-layouts.page>
