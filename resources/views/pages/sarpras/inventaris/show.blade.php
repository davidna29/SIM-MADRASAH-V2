<x-layouts.page
    :title="'Detail Barang'"
    :roleLabel="$roleLabel"
    :breadcrumb="$breadcrumb"
    active-route="inventaris.show">

    <div class="mx-auto max-w-6xl">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">{{ $item->name }}</h1>
                <p class="tabular mt-1.5 text-xs text-ink-soft">
                    <span class="font-mono font-semibold">{{ $item->code }}</span>
                    · {{ $item->category?->name ?? 'Tanpa kategori' }}
                    @if ($item->brand) · {{ $item->brand }} @endif
                    @if ($item->location) · {{ $item->location }} @endif
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                @can('update', $item)
                    <x-ui.button variant="secondary" size="sm" icon="pencil-square" href="{{ route('inventaris.edit', $item) }}">Ubah</x-ui.button>
                @endcan
                <x-ui.badge :variant="$item->status === 'tersedia' ? 'success' : ($item->status === 'dalam_perawatan' ? 'warning' : 'neutral')">{{ ucwords(str_replace('_', ' ', $item->status)) }}</x-ui.badge>
            </div>
        </div>

        @if (session('status'))
            <div class="mt-6">
                <x-ui.alert variant="success" dismissible>{{ session('status') }}</x-ui.alert>
            </div>
        @endif

        @if ($errors->any())
            <div class="mt-6">
                <x-ui.alert variant="danger" dismissible>
                    <strong class="font-bold">Periksa kembali:</strong>
                    @foreach ($errors->all() as $error) {{ $error }} @endforeach
                </x-ui.alert>
            </div>
        @endif

        <!-- Detail -->
        <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-3">
            <div class="lg:col-span-2">
                <x-ui.sheet title="Informasi Barang" pinned>
                    <dl class="grid grid-cols-2 gap-x-6 gap-y-5 sm:grid-cols-3">
                        <div>
                            <dt class="text-xs font-bold text-ink-faint">Kategori</dt>
                            <dd class="mt-1 text-sm font-semibold text-ink">{{ $item->category?->name ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-bold text-ink-faint">Kondisi</dt>
                            <dd class="mt-1 text-sm font-semibold text-ink">{{ ucwords(str_replace('_', ' ', $item->condition)) }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-bold text-ink-faint">Status</dt>
                            <dd class="mt-1 text-sm font-semibold text-ink">{{ ucwords(str_replace('_', ' ', $item->status)) }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-bold text-ink-faint">Jumlah</dt>
                            <dd class="tabular mt-1 font-mono text-sm font-semibold text-ink">{{ $item->quantity }} {{ $item->unit }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-bold text-ink-faint">Lokasi</dt>
                            <dd class="mt-1 text-sm font-semibold text-ink">{{ $item->location ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-bold text-ink-faint">Merek / Model</dt>
                            <dd class="mt-1 text-sm font-semibold text-ink">{{ trim(($item->brand ?? '').' '.($item->model ?? '')) ?: '—' }}</dd>
                        </div>
                        @if ($item->serial_number)
                            <div>
                                <dt class="text-xs font-bold text-ink-faint">No. Seri</dt>
                                <dd class="tabular mt-1 font-mono text-sm font-semibold text-ink">{{ $item->serial_number }}</dd>
                            </div>
                        @endif
                        @if ($item->purchase_date)
                            <div>
                                <dt class="text-xs font-bold text-ink-faint">Tanggal Beli</dt>
                                <dd class="mt-1 text-sm font-semibold text-ink">{{ $item->purchase_date->isoFormat('D MMMM YYYY') }}</dd>
                            </div>
                        @endif
                        @if ($item->purchase_price !== null)
                            <div>
                                <dt class="text-xs font-bold text-ink-faint">Harga Beli</dt>
                                <dd class="tabular mt-1 font-mono text-sm font-semibold text-ink">Rp {{ number_format($item->purchase_price, 0, ',', '.') }}</dd>
                            </div>
                        @endif
                    </dl>
                    @if ($item->notes)
                        <div class="mt-6 border-t border-rule/70 pt-4">
                            <dt class="text-xs font-bold text-ink-faint">Catatan</dt>
                            <p class="mt-1 text-sm leading-relaxed text-ink-soft">{{ $item->notes }}</p>
                        </div>
                    @endif
                </x-ui.sheet>
            </div>

            <div>
                <x-ui.sheet title="Ringkasan">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-ink-faint">Total Mutasi</span>
                            <span class="tabular font-mono text-sm font-semibold text-ink">{{ $item->mutations->count() }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-ink-faint">Catatan Pemeliharaan</span>
                            <span class="tabular font-mono text-sm font-semibold text-ink">{{ $item->maintenances->count() }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-ink-faint">Total Nilai Aset</span>
                            <span class="tabular font-mono text-sm font-semibold text-ink">
                                Rp {{ number_format(($item->purchase_price ?? 0) * $item->quantity, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                </x-ui.sheet>
            </div>
        </div>

        @can('update', $item)
            <!-- Form Mutasi & Pemeliharaan -->
            <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
                <form method="POST" action="{{ route('inventaris.mutasi.store', $item) }}" class="rounded-sheet bg-sheet shadow-sheet ring-1 ring-inset ring-rule/60 p-5">
                    @csrf
                    <p class="text-sm font-bold text-ink">Ajukan Mutasi</p>
                    <p class="mt-1 text-xs text-ink-soft">Memindahkan barang ke lokasi lain. Disetujui oleh Wakamad Sarpras / Super Admin.</p>
                    <div class="mt-4 space-y-4">
                        <div class="grid grid-cols-2 gap-3">
                            <x-ui.field label="Ke Lokasi" required :error="$errors->first('to_location')">
                                <x-ui.input name="to_location" value="{{ old('to_location') }}" placeholder="Mis. Ruang Kelas V-A" />
                            </x-ui.field>
                            <x-ui.field label="Jumlah" required :error="$errors->first('quantity')">
                                <x-ui.input type="number" name="quantity" min="1" :max="$item->quantity" :value="old('quantity', 1)" />
                            </x-ui.field>
                        </div>
                        <x-ui.field label="Tanggal" required :error="$errors->first('mutation_date')">
                            <x-ui.input type="date" name="mutation_date" :value="old('mutation_date', now()->format('Y-m-d'))" />
                        </x-ui.field>
                        <x-ui.field label="Alasan" :error="$errors->first('reason')">
                            <x-ui.input name="reason" :value="old('reason')" placeholder="Kebutuhan pengajaran…" />
                        </x-ui.field>
                    </div>
                    <div class="mt-4 flex justify-end">
                        <x-ui.button type="submit" size="sm" variant="primary" icon="arrow-path">Ajukan Mutasi</x-ui.button>
                    </div>
                </form>

                <form method="POST" action="{{ route('inventaris.perawatan.store', $item) }}" class="rounded-sheet bg-sheet shadow-sheet ring-1 ring-inset ring-rule/60 p-5">
                    @csrf
                    <p class="text-sm font-bold text-ink">Catat Pemeliharaan / Perbaikan</p>
                    <div class="mt-4 space-y-4">
                        <x-ui.field label="Jenis" required :error="$errors->first('type')">
                            <x-ui.select name="type" :options="['perawatan' => 'Perawatan', 'perbaikan' => 'Perbaikan']" :selected="old('type', 'perawatan')" />
                        </x-ui.field>
                        <x-ui.field label="Deskripsi" required :error="$errors->first('description')">
                            <x-ui.input name="description" :value="old('description')" placeholder="Uraian pekerjaan…" />
                        </x-ui.field>
                        <div class="grid grid-cols-2 gap-3">
                            <x-ui.field label="Biaya (Rp)" :error="$errors->first('cost')">
                                <x-ui.input type="number" name="cost" min="0" :value="old('cost')" placeholder="0" />
                            </x-ui.field>
                            <x-ui.field label="Teknisi / Vendor" :error="$errors->first('technician')">
                                <x-ui.input name="technician" :value="old('technician')" placeholder="Nama / toko" />
                            </x-ui.field>
                        </div>
                        <x-ui.field label="Tanggal Mulai" required :error="$errors->first('start_date')">
                            <x-ui.input type="date" name="start_date" :value="old('start_date', now()->format('Y-m-d'))" />
                        </x-ui.field>
                    </div>
                    <div class="mt-4 flex justify-end">
                        <x-ui.button type="submit" size="sm" variant="primary" icon="wrench-screwdriver">Catat</x-ui.button>
                    </div>
                </form>
            </div>
        @endcan

        <!-- Riwayat Mutasi -->
        <div class="mt-6">
            <x-ui.sheet title="Riwayat Mutasi" :subtitle="$item->mutations->count() . ' catatan'" :padding="false">
                @if ($item->mutations->isEmpty())
                    <p class="px-5 py-8 text-center text-sm text-ink-faint">Belum ada mutasi.</p>
                @else
                    <x-ui.table :headers="['Tanggal', 'Dari', 'Ke', 'Jumlah', 'Status', 'Aksi']">
                        <x-slot>
                            @foreach ($item->mutations as $m)
                                <tr class="transition hover:bg-paper/60">
                                    <td class="tabular whitespace-nowrap px-4 py-3 font-mono text-xs text-ink-soft">{{ $m->mutation_date->isoFormat('D MMM YYYY') }}</td>
                                    <td class="px-4 py-3 text-xs text-ink-soft">{{ $m->from_location ?? '—' }}</td>
                                    <td class="px-4 py-3 text-xs font-semibold text-ink">{{ $m->to_location }}</td>
                                    <td class="tabular px-4 py-3 text-center font-mono text-xs text-ink">{{ $m->quantity }}</td>
                                    <td class="whitespace-nowrap px-4 py-3">
                                        <x-ui.badge :variant="$m->status === 'disetujui' ? 'success' : ($m->status === 'ditolak' ? 'danger' : 'warning')">{{ ucfirst($m->status) }}</x-ui.badge>
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3 text-right">
                                        @if ($m->status === 'pending')
                                            <div class="flex items-center justify-end gap-1.5">
                                                @can('approve', \App\Models\InventoryItem::class)
                                                    <form method="POST" action="{{ route('inventaris.mutasi.approve', [$item, $m]) }}">
                                                        @csrf
                                                        <x-ui.button type="submit" size="sm" variant="success" icon="check">Setujui</x-ui.button>
                                                    </form>
                                                    <form method="POST" action="{{ route('inventaris.mutasi.reject', [$item, $m]) }}" onsubmit="return confirm('Tolak pengajuan mutasi ini?');">
                                                        @csrf
                                                        <x-ui.button type="submit" size="sm" variant="ghost" icon="x-mark">Tolak</x-ui.button>
                                                    </form>
                                                @else
                                                    <form method="POST" action="{{ route('inventaris.mutasi.destroy', [$item, $m]) }}" onsubmit="return confirm('Batalkan pengajuan mutasi ini?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <x-ui.button type="submit" size="sm" variant="ghost" icon="trash">Batal</x-ui.button>
                                                    </form>
                                                @endcan
                                            </div>
                                        @else
                                            <span class="text-xs text-ink-faint">{{ $m->approver?->name ?? '—' }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </x-slot>
                    </x-ui.table>
                @endif
            </x-ui.sheet>
        </div>

        <!-- Riwayat Pemeliharaan -->
        <div class="mt-6">
            <x-ui.sheet title="Riwayat Pemeliharaan" :subtitle="$item->maintenances->count() . ' catatan'" :padding="false">
                @if ($item->maintenances->isEmpty())
                    <p class="px-5 py-8 text-center text-sm text-ink-faint">Belum ada pemeliharaan.</p>
                @else
                    <x-ui.table :headers="['Jenis', 'Deskripsi', 'Biaya', 'Periode', 'Status', 'Aksi']">
                        <x-slot>
                            @foreach ($item->maintenances as $mt)
                                <tr class="transition hover:bg-paper/60">
                                    <td class="whitespace-nowrap px-4 py-3 text-xs font-semibold text-ink">{{ ucfirst($mt->type) }}</td>
                                    <td class="px-4 py-3">
                                        <p class="text-xs text-ink">{{ $mt->description }}</p>
                                        @if ($mt->technician)
                                            <p class="mt-0.5 text-[11px] text-ink-faint">{{ $mt->technician }}</p>
                                        @endif
                                    </td>
                                    <td class="tabular whitespace-nowrap px-4 py-3 font-mono text-xs text-ink">{{ $mt->cost !== null ? 'Rp '.number_format($mt->cost, 0, ',', '.') : '—' }}</td>
                                    <td class="tabular whitespace-nowrap px-4 py-3 font-mono text-xs text-ink-soft">
                                        {{ $mt->start_date->isoFormat('D MMM YYYY') }}{{ $mt->end_date ? ' → '.$mt->end_date->isoFormat('D MMM YYYY') : '' }}
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3">
                                        <x-ui.badge :variant="$mt->status === 'selesai' ? 'success' : 'warning'">{{ ucfirst($mt->status) }}</x-ui.badge>
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3 text-right">
                                        @if ($mt->status === 'berlangsung')
                                            <form method="POST" action="{{ route('inventaris.perawatan.selesai', [$item, $mt]) }}" class="inline">
                                                @csrf
                                                <x-ui.button type="submit" size="sm" variant="secondary" icon="check">Selesai</x-ui.button>
                                            </form>
                                        @endif
                                        <form method="POST" action="{{ route('inventaris.perawatan.destroy', [$item, $mt]) }}" class="inline" onsubmit="return confirm('Hapus catatan pemeliharaan ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <x-ui.button type="submit" size="sm" variant="ghost" icon="trash">Hapus</x-ui.button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </x-slot>
                    </x-ui.table>
                @endif
            </x-ui.sheet>
        </div>
    </div>
</x-layouts.page>
