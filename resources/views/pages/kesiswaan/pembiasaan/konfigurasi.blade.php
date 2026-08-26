<x-layouts.page
    :title="$title"
    :roleLabel="$roleLabel"
    :breadcrumb="[['label' => 'Kesiswaan', 'href' => route('dashboard')], ['label' => 'Konfigurasi Materi '.$label]]"
    active-route="{{ $modul }}.konfigurasi">

    @php
        $grade = [1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI'];
    @endphp

    <div class="mx-auto max-w-full">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">{{ $title }}</h1>
                <p class="mt-1.5 max-w-prose text-sm leading-relaxed text-ink-soft">
                    Aktif/nonaktifkan materi per Kelas &times; Semester. Perubahan otomatis mengunci/membuka
                    sel pada form input nilai {{ $label }}.
                </p>
            </div>
        </div>

        @if (session('status'))
            <div class="mt-6">
                <x-ui.alert variant="success" dismissible>{{ session('status') }}</x-ui.alert>
            </div>
        @endif

        <form method="POST" action="{{ route($modul.'.konfigurasi.update') }}" class="mt-6">
            @csrf
            <div class="overflow-x-auto rounded-sheet bg-sheet shadow-sheet ring-1 ring-inset ring-rule/60">
                <table class="w-full min-w-[1100px] border-collapse text-sm">
                    <thead>
                        <tr class="border-b border-rule-strong">
                            <th class="px-3 py-2 text-left text-xs font-bold text-ink-soft">No</th>
                            <th class="px-3 py-2 text-left text-xs font-bold text-ink-soft">Materi</th>
                            @foreach ($pairs as $pair)
                                <th class="px-2 py-2 text-center text-xs font-bold text-ink-soft">{{ $grade[$pair[0]] }}.{{ $pair[1] }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-rule/70">
                        @foreach ($materials as $m)
                            <tr>
                                <td class="px-3 py-2 text-ink-soft">{{ $m->no_urut }}</td>
                                <td class="px-3 py-2">
                                    {{ $m->nama_materi }}
                                    @if ($m->jenis)
                                        <span class="text-xs text-ink-faint">({{ $m->jenis }})</span>
                                    @endif
                                </td>
                                @foreach ($pairs as $pair)
                                    @php
                                        $per = $m->periodes->first(fn ($p) => $p->kelas === $pair[0] && $p->semester === $pair[1]);
                                    @endphp
                                    <td class="px-2 py-1 text-center">
                                        <input type="checkbox" name="periode[{{ $m->id }}-{{ $pair[0] }}-{{ $pair[1] }}]" value="1"
                                            {{ ($per && $per->aktif) ? 'checked' : '' }}
                                            class="h-4 w-4 rounded border-rule-strong text-primary focus:ring-primary">
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4 flex items-center justify-between">
                <x-ui.button variant="ghost" icon="arrow-left" href="{{ route($modul.'.index') }}">Kembali</x-ui.button>
                <x-ui.button type="submit" variant="primary" icon="check">Simpan Konfigurasi</x-ui.button>
            </div>
        </form>
    </div>
</x-layouts.page>
