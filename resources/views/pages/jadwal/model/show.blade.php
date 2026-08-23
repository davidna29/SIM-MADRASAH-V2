<x-layouts.page
    :title="'Detail Model Jadwal'"
    :roleLabel="$roleLabel"
    :breadcrumb="$breadcrumb">

    <div class="mx-auto max-w-4xl">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">{{ $model->name }}</h1>
                <p class="mt-1.5 max-w-prose text-sm leading-relaxed text-ink-soft">
                    {{ $model->academicYear->name }} · jam mulai {{ $model->start_time->format('H:i') }} ·
                    maks {{ $model->max_hours_per_day }} jam/hari.
                </p>
            </div>
            <div class="flex items-center gap-2">
                <x-ui.button variant="secondary" icon="pencil-square" href="{{ route('jadwal.model.edit', $model) }}">Ubah</x-ui.button>
            </div>
        </div>

        @if (session('status'))
            <div class="mt-6">
                <x-ui.alert variant="success" dismissible>{{ session('status') }}</x-ui.alert>
            </div>
        @endif

        <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
            <x-ui.sheet title="Tingkatan yang Dicakup" pinned>
                <div class="flex flex-wrap gap-2">
                    @foreach ($model->gradeLevelRows as $row)
                        <x-ui.badge variant="info" :dot="false">Tingkat {{ $row->grade_level }}</x-ui.badge>
                    @endforeach
                    @if ($model->is_active)
                        <x-ui.badge variant="success">Aktif</x-ui.badge>
                    @else
                        <x-ui.badge variant="neutral">Nonaktif</x-ui.badge>
                    @endif
                </div>
                <div class="mt-4">
                    <p class="text-xs font-bold uppercase tracking-wide text-ink-faint">Rombel terkait</p>
                    <div class="mt-2 flex flex-wrap gap-1.5">
                        @foreach ($classes as $class)
                            <x-ui.badge variant="neutral" :dot="false">{{ $class->name }}</x-ui.badge>
                        @endforeach
                    </div>
                </div>
            </x-ui.sheet>

            <x-ui.sheet title="Slot Template" subtitle="Jam ke- yang disusun dalam tabel master" pinned :padding="false">
                <x-ui.table :headers="['Jam ke-', 'Mulai', 'Selesai', 'Jenis']">
                    <x-slot name="emptySlot">Belum ada slot.</x-slot>
                    <x-slot>
                        @foreach ($model->slots as $slot)
                            <tr class="transition hover:bg-paper/60">
                                <td class="tabular px-4 py-3 font-mono text-xs font-semibold text-ink-faint">{{ $slot->period_no }}</td>
                                <td class="tabular px-4 py-3 font-mono text-xs text-ink">{{ $slot->start_time->format('H:i') }}</td>
                                <td class="tabular px-4 py-3 font-mono text-xs text-ink">{{ $slot->end_time->format('H:i') }}</td>
                                <td class="px-4 py-3">
                                    @if ($slot->is_break)
                                        <x-ui.badge variant="warning">Non-KBM · {{ $slot->label }}</x-ui.badge>
                                    @else
                                        <x-ui.badge variant="success" :dot="false">KBM</x-ui.badge>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </x-slot>
                </x-ui.table>
            </x-ui.sheet>
        </div>

        <div class="mt-6 flex items-center justify-between border-t border-rule-strong/60 pt-5">
            <x-ui.button variant="ghost" icon="arrow-left" href="{{ route('jadwal.model.index') }}">Kembali</x-ui.button>
            <form method="POST" action="{{ route('jadwal.model.destroy', $model) }}" onsubmit="return confirm('Hapus model jadwal ini?');">
                @csrf
                @method('DELETE')
                <x-ui.button type="submit" variant="danger" icon="trash">Hapus</x-ui.button>
            </form>
        </div>
    </div>
</x-layouts.page>
