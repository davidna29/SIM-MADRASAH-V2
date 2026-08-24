<x-layouts.page
    :title="'SPP Bulanan'"
    :roleLabel="$roleLabel"
    :breadcrumb="$breadcrumb"
    active-route="spp.index">

    <div class="mx-auto max-w-7xl" x-data="{
        open: false,
        f: {},
        openPay(data) {
            this.f = {
                id: data.id,
                bulan: data.bulan,
                nominal: data.nominal,
                tanggal: data.tanggal ?? '',
                metode: data.metode ?? '',
                catatan: data.catatan ?? '',
                label: data.label,
            };
            this.open = true;
        },
    }">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">SPP Bulanan</h1>
                <p class="mt-1.5 max-w-prose text-sm leading-relaxed text-ink-soft">
                    Catat pembayaran SPP per siswa per bulan pada Tahun Ajaran {{ $tahun->name }}.
                    Klik sel bulan untuk mencatat atau mengoreksi pembayaran.
                </p>
            </div>
            <div class="flex items-center gap-2">
                <x-ui.badge variant="info" icon="calendar-days">{{ $tahun->name }} · Semester {{ ucfirst($tahun->semester) }}</x-ui.badge>
                <x-ui.button variant="secondary" icon="printer" onclick="window.print()">Cetak</x-ui.button>
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

        <!-- Filter -->
        <form method="GET" action="{{ route('spp.index') }}" class="mt-6 rounded-sheet bg-sheet shadow-sheet ring-1 ring-inset ring-rule/60 p-4 print:hidden">
            <div class="flex flex-wrap items-end gap-3">
                <div>
                    <label for="class_group_id" class="block pb-1.5 text-xs font-bold text-ink">Kelas / Rombel</label>
                    <x-ui.select name="class_group_id" :full="false" class="w-44" :options="$classes->pluck('name', 'id')" :selected="$classGroup?->id" />
                </div>
                <x-ui.button type="submit" variant="secondary" size="md">Tampilkan</x-ui.button>
            </div>
        </form>

        @if ($classGroup)
            <div class="mt-6">
                <x-ui.sheet
                    title="Rekap SPP"
                    :subtitle="$classGroup->name . ' · ' . $tahun->name . ' · Semester ' . ucfirst($tahun->semester)"
                    pinned
                    :padding="false">

                    <!-- Kepala formulir -->
                    <div class="border-b border-rule/70 px-5 py-4 sm:px-6">
                        <div class="grid grid-cols-1 gap-px overflow-hidden rounded-[var(--radius-control)] bg-rule-strong ring-1 ring-inset ring-rule-strong sm:grid-cols-2 lg:grid-cols-4">
                            <div class="bg-paper px-4 py-3">
                                <p class="text-[10px] font-bold uppercase tracking-[0.08em] text-ink-faint">Tahun Pelajaran</p>
                                <p class="mt-0.5 text-sm font-bold text-ink">{{ $tahun->name }}</p>
                            </div>
                            <div class="bg-paper px-4 py-3">
                                <p class="text-[10px] font-bold uppercase tracking-[0.08em] text-ink-faint">Semester</p>
                                <p class="mt-0.5 text-sm font-bold text-ink">{{ ucfirst($tahun->semester) }}</p>
                            </div>
                            <div class="bg-paper px-4 py-3">
                                <p class="text-[10px] font-bold uppercase tracking-[0.08em] text-ink-faint">Kelas</p>
                                <p class="mt-0.5 text-sm font-bold text-ink">{{ $classGroup->name }}</p>
                            </div>
                            <div class="bg-paper px-4 py-3">
                                <p class="text-[10px] font-bold uppercase tracking-[0.08em] text-ink-faint">Nominal Default</p>
                                <p class="tabular mt-0.5 font-mono text-sm font-bold text-ink">
                                    {{ $defaultNominal !== null ? 'Rp '.number_format($defaultNominal, 0, ',', '.') : 'Belum diatur' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Tabel rekap -->
                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse text-xs">
                            <thead>
                                <tr class="border-b-2 border-rule-strong bg-paper-deep/60">
                                    <th scope="col" class="w-10 px-3 py-2.5 text-left font-bold uppercase tracking-wide text-ink-soft">No</th>
                                    <th scope="col" class="min-w-[150px] px-3 py-2.5 text-left font-bold uppercase tracking-wide text-ink-soft">Nama Siswa</th>
                                    <th scope="col" class="w-28 px-3 py-2.5 text-right font-bold uppercase tracking-wide text-ink-soft">Nominal</th>
                                    @foreach ($months as $bulan)
                                        <th scope="col" class="w-20 px-2 py-2.5 text-center font-bold uppercase tracking-wide text-ink-soft">{{ $monthsLabel[$bulan] }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-rule/70">
                                @forelse ($rows as $row)
                                    <tr class="transition hover:bg-paper/60">
                                        <td class="tabular px-3 py-2 text-center font-mono text-ink-faint">{{ $loop->iteration }}</td>
                                        <td class="whitespace-nowrap px-3 py-2">
                                            <span class="font-semibold text-ink">{{ $row['student']->displayName() }}</span>
                                            @if ($row['override'])
                                                <x-ui.badge variant="warning" :dot="false">Khusus</x-ui.badge>
                                            @endif
                                        </td>
                                        <td class="tabular whitespace-nowrap px-3 py-2 text-right font-mono font-semibold text-ink">
                                            {{ $row['nominal'] !== null ? 'Rp '.number_format($row['nominal'], 0, ',', '.') : '—' }}
                                        </td>
                                        @foreach ($months as $bulan)
                                            @php
                                                $payment = $row['cells'][$bulan];
                                                $lunas = $payment && $payment->isLunas();
                                                $payData = [
                                                    'id' => $row['enrollment']->id,
                                                    'bulan' => $bulan,
                                                    'nominal' => $payment?->nominal ?? $row['nominal'] ?? 0,
                                                    'tanggal' => $payment?->tanggal_bayar?->format('Y-m-d'),
                                                    'metode' => $payment?->metode,
                                                    'catatan' => $payment?->catatan,
                                                    'label' => $monthsLabel[$bulan],
                                                ];
                                            @endphp
                                            <td class="px-2 py-2 text-center">
                                                <button type="button"
                                                    @click="openPay({{ json_encode($payData) }})"
                                                    title="{{ $lunas ? 'Lunas — klik untuk koreksi' : 'Belum bayar — klik untuk mencatat' }}"
                                                    class="inline-flex min-w-[64px] items-center justify-center gap-1 rounded-[var(--radius-control)] px-2 py-1.5 text-xs font-semibold ring-1 ring-inset transition duration-150 hover:brightness-95 active:scale-[0.97] {{ $lunas ? 'bg-success-soft text-success ring-success/30' : 'bg-warning-soft text-warning ring-warning/30' }}">
                                                    @if ($lunas)
                                                        <span class="size-1.5 rounded-full bg-success" aria-hidden="true"></span>
                                                        Lunas
                                                    @else
                                                        <span class="size-1.5 rounded-full bg-warning" aria-hidden="true"></span>
                                                        Belum
                                                    @endif
                                                </button>
                                            </td>
                                        @endforeach
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ 3 + count($months) }}" class="px-4 py-8 text-center text-xs text-ink-faint">Tidak ada siswa aktif di kelas ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="border-t border-rule/70 px-5 py-3 text-xs text-ink-faint">
                        Klik sel bulan untuk mencatat/mengoreksi pembayaran. Nominal mengikuti keringanan siswa jika ada, selain itu nominal default.
                    </div>
                </x-ui.sheet>
            </div>
        @else
            <div class="mt-6 rounded-sheet bg-sheet shadow-sheet ring-1 ring-inset ring-rule/60 px-5 py-10 text-center">
                <p class="text-sm font-semibold text-ink">Belum ada kelas terdaftar.</p>
                <p class="mt-1 text-xs text-ink-faint">Buat kelas pada modul Kelas & Penempatan terlebih dahulu.</p>
            </div>
        @endif

        <!-- Modal pembayaran -->
        <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-end justify-center p-4 sm:items-center"
            x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-100" x-transition:leave-end="opacity-0"
            @keydown.escape.window="open = false" role="dialog" aria-modal="true" aria-labelledby="spp-pay-title">
            <div class="absolute inset-0 bg-board-deep/60 backdrop-blur-[2px]" @click="open = false"></div>
            <div class="relative w-full max-w-md rounded-sheet bg-sheet shadow-sheet-raised ring-1 ring-inset ring-rule"
                x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 translate-y-3 scale-[0.98]" x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                x-transition:leave="transition ease-in duration-100" x-transition:leave-end="opacity-0 translate-y-3 scale-[0.98]">
                <form method="POST" action="{{ route('spp.pay') }}">
                    @csrf
                    <header class="flex items-center justify-between border-b border-rule/70 px-5 py-4">
                        <h3 id="spp-pay-title" class="text-sm font-bold tracking-tight text-ink">
                            Pembayaran SPP — <span x-text="f.label"></span>
                        </h3>
                        <button type="button" @click="open = false" class="rounded-md p-1 text-ink-faint transition hover:bg-paper-deep hover:text-ink" aria-label="Tutup">
                            <x-svg-x-mark class="size-5" aria-hidden="true" />
                        </button>
                    </header>

                    <div class="space-y-4 px-5 py-5">
                        <input type="hidden" name="student_enrollment_id" :value="f.id">
                        <input type="hidden" name="bulan" :value="f.bulan">

                        <x-ui.field label="Nominal (Rp)" required :error="$errors->first('nominal')">
                            <input type="number" name="nominal" x-model.number="f.nominal" min="0" step="1" inputmode="numeric" required
                                class="tabular w-full rounded-[var(--radius-control)] bg-sheet px-3.5 py-2.5 font-mono text-sm font-semibold text-ink ring-1 ring-inset ring-rule-strong transition focus:outline-none focus:ring-2 focus:ring-primary">
                        </x-ui.field>

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <x-ui.field label="Tanggal Bayar" :error="$errors->first('tanggal_bayar')">
                                <input type="date" name="tanggal_bayar" x-model="f.tanggal" max="{{ now()->format('Y-m-d') }}"
                                    class="w-full rounded-[var(--radius-control)] bg-sheet px-3.5 py-2.5 text-sm text-ink ring-1 ring-inset ring-rule-strong transition focus:outline-none focus:ring-2 focus:ring-primary">
                            </x-ui.field>
                            <x-ui.field label="Metode" :error="$errors->first('metode')">
                                <select name="metode" x-model="f.metode"
                                    class="w-full appearance-none rounded-[var(--radius-control)] bg-sheet px-3.5 py-2.5 text-sm text-ink ring-1 ring-inset ring-rule-strong transition focus:outline-none focus:ring-2 focus:ring-primary">
                                    <option value="">—</option>
                                    <option value="tunai">Tunai</option>
                                    <option value="transfer">Transfer</option>
                                </select>
                            </x-ui.field>
                        </div>

                        <x-ui.field label="Catatan" :error="$errors->first('catatan')">
                            <input type="text" name="catatan" x-model="f.catatan" maxlength="255"
                                class="w-full rounded-[var(--radius-control)] bg-sheet px-3.5 py-2.5 text-sm text-ink placeholder:text-ink-faint ring-1 ring-inset ring-rule-strong transition focus:outline-none focus:ring-2 focus:ring-primary"
                                placeholder="Opsional">
                        </x-ui.field>

                        <p class="text-xs text-ink-faint">
                            Isi <strong>Tanggal Bayar</strong> untuk menandai <strong>Lunas</strong>; tanpa tanggal, tersimpan sebagai <strong>Belum Bayar</strong>.
                        </p>
                    </div>

                    <footer class="flex items-center justify-end gap-2 border-t border-rule/70 px-5 py-4">
                        <x-ui.button type="button" variant="ghost" size="sm" @click="open = false">Batal</x-ui.button>
                        <x-ui.button type="submit" variant="primary" size="sm" icon="check">Simpan Pembayaran</x-ui.button>
                    </footer>
                </form>
            </div>
        </div>
    </div>
</x-layouts.page>
