<x-layouts.page
    :title="$room->name"
    :roleLabel="$roleLabel"
    :breadcrumb="$breadcrumb"
    active-route="ruangan.show">

    <div class="mx-auto max-w-4xl">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">{{ $room->name }}</h1>
                <p class="mt-1.5 max-w-prose text-sm leading-relaxed text-ink-soft">
                    {{ \App\Models\Room::TYPES[$room->type] ?? 'Ruangan' }} · {{ $room->code }}
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                @can('update', $room)
                    <x-ui.button variant="secondary" size="sm" icon="pencil-square" href="{{ route('ruangan.edit', $room) }}">Ubah</x-ui.button>
                @endcan
                @can('delete', $room)
                    <form method="POST" action="{{ route('ruangan.destroy', $room) }}" class="inline"
                        x-data="{ open: false }" @submit.prevent="open = true">
                        @csrf @method('DELETE')
                        <x-ui.modal title="Hapus Ruangan?" description="Apakah Anda yakin ingin menghapus ruangan {{ $room->name }}? Tindakan ini tidak dapat dibatalkan." x-show="open" @close="open = false">
                            <x-slot:actions>
                                <x-ui.button variant="ghost" @click="open = false">Batal</x-ui.button>
                                <x-ui.button variant="danger" type="submit" icon="trash">Hapus</x-ui.button>
                            </x-slot:actions>
                        </x-ui.modal>
                        <x-ui.button variant="danger" size="sm" icon="trash" type="submit">Hapus</x-ui.button>
                    </form>
                @endcan
            </div>
        </div>

        @if (session('status'))
            <div class="mt-6">
                <x-ui.alert variant="success" dismissible>{{ session('status') }}</x-ui.alert>
            </div>
        @endif

        <!-- Detail Card -->
        <div class="mt-6">
            <x-ui.sheet title="Detail Ruangan">
                <div class="grid grid-cols-1 gap-px overflow-hidden rounded-[var(--radius-control)] bg-rule-strong ring-1 ring-inset ring-rule-strong sm:grid-cols-2">
                    <div class="bg-paper px-4 py-3">
                        <p class="text-[10px] font-bold uppercase tracking-[0.08em] text-ink-faint">Kode</p>
                        <p class="tabular mt-0.5 font-mono text-sm font-bold text-ink">{{ $room->code }}</p>
                    </div>
                    <div class="bg-paper px-4 py-3">
                        <p class="text-[10px] font-bold uppercase tracking-[0.08em] text-ink-faint">Jenis</p>
                        <p class="mt-0.5 text-sm font-bold text-ink">{{ \App\Models\Room::TYPES[$room->type] ?? $room->type }}</p>
                    </div>
                    <div class="bg-paper px-4 py-3">
                        <p class="text-[10px] font-bold uppercase tracking-[0.08em] text-ink-faint">Gedung</p>
                        <p class="mt-0.5 text-sm font-bold text-ink">{{ $room->building ?? '—' }}</p>
                    </div>
                    <div class="bg-paper px-4 py-3">
                        <p class="text-[10px] font-bold uppercase tracking-[0.08em] text-ink-faint">Lantai</p>
                        <p class="mt-0.5 text-sm font-bold text-ink">{{ $room->floor ?? '—' }}</p>
                    </div>
                    <div class="bg-paper px-4 py-3">
                        <p class="text-[10px] font-bold uppercase tracking-[0.08em] text-ink-faint">Kapasitas</p>
                        <p class="tabular mt-0.5 font-mono text-sm font-bold text-ink">{{ $room->capacity }} orang</p>
                    </div>
                    <div class="bg-paper px-4 py-3">
                        <p class="text-[10px] font-bold uppercase tracking-[0.08em] text-ink-faint">Penanggung Jawab</p>
                        <p class="mt-0.5 text-sm font-bold text-ink">{{ $room->penanggJawab?->person?->name ?? '—' }}</p>
                    </div>
                    <div class="bg-paper px-4 py-3">
                        <p class="text-[10px] font-bold uppercase tracking-[0.08em] text-ink-faint">Kondisi</p>
                        @php
                            $condColor = match($room->condition) {
                                'baik' => 'success',
                                'rusak_ringan' => 'warning',
                                'rusak_berat' => 'danger',
                                'dalam_perbaikan' => 'info',
                                default => 'neutral',
                            };
                        @endphp
                        <p class="mt-0.5"><x-ui.badge :variant="$condColor">{{ $room->conditionLabel() }}</x-ui.badge></p>
                    </div>
                    <div class="bg-paper px-4 py-3">
                        <p class="text-[10px] font-bold uppercase tracking-[0.08em] text-ink-faint">Dibuat Oleh</p>
                        <p class="mt-0.5 text-sm font-bold text-ink">{{ $room->creator?->name ?? '—' }}</p>
                    </div>
                </div>

                @if ($room->description)
                    <div class="mt-4">
                        <p class="text-[10px] font-bold uppercase tracking-[0.08em] text-ink-faint">Keterangan</p>
                        <p class="mt-1 text-sm leading-relaxed text-ink">{{ $room->description }}</p>
                    </div>
                @endif
            </x-ui.sheet>
        </div>
    </div>
</x-layouts.page>
