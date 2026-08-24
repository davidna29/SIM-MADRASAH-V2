<x-layouts.page
    :title="'Dashboard'"
    :roleLabel="$roleLabel"
    :breadcrumb="$breadcrumb"
    active-route="dashboard">

    <div class="mx-auto max-w-7xl">
        <!-- Kepala halaman -->
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">Papan Pengawasan</h1>
                <p class="mt-1.5 max-w-prose text-sm leading-relaxed text-ink-soft">
                    Ringkasan kondisi madrasah hari ini — data sungguhan dari seluruh modul.
                </p>
            </div>
            <div class="flex items-center gap-2">
                <x-ui.button variant="secondary" icon="calendar-days" href="{{ route('dashboard') }}">{{ now()->locale('id')->isoFormat('dddd, D MMM YYYY') }}</x-ui.button>
            </div>
        </div>

        <!-- Lembar KPI (digit-bank) -->
        <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <x-ui.kpi label="Siswa Aktif" :value="$kpis['siswa_aktif']" suffix="siswa" icon="academic-cap" :hint="'Tahun ' . $tahun->name" />
            <x-ui.kpi label="Guru & Pegawai Aktif" :value="$kpis['guru_pegawai']" suffix="orang" icon="user-group" hint="Data kepegawaian" />
            <x-ui.kpi label="SPP Terkumpul" prefix="Rp" :value="number_format($kpis['spp_terkumpul'], 0, ',', '.')" icon="banknotes" :hint="'Semester ' . ucfirst($tahun->semester)" />
            <x-ui.kpi label="Kehadiran Hari Ini" :value="$kpis['kehadiran_persen'] ?? '—'" suffix="%" icon="clipboard-document-check" :hint="$kpis['kehadiran_hadir'] > 0 ? 'dari ' . $kpis['kehadiran_hadir'] . ' hadir tercatat' : 'belum ada catatan'" />
        </div>

        <div class="mt-6 grid grid-cols-1 gap-6 xl:grid-cols-3">
            <!-- Papan Perlu Tindakan -->
            <div class="xl:col-span-2">
                <x-ui.sheet title="Perlu Tindakan" subtitle="Kondisi yang menunggu perhatian hari ini" pinned ruled>
                    @if ($perluTindakan->isEmpty())
                        <div class="py-8 text-center">
                            <p class="text-sm font-semibold text-ink">Tidak ada tindakan yang menunggu.</p>
                            <p class="mt-1 text-xs text-ink-faint">Seluruh kehadiran, SPP, dan rapor sudah terkini.</p>
                        </div>
                    @else
                        <ul class="divide-y divide-rule/70">
                            @foreach ($perluTindakan as $item)
                                <li x-data="{ done: false }" :class="done ? 'opacity-55' : ''"
                                    class="group flex items-start gap-3 px-1 py-3.5 transition duration-300">
                                    <span data-pin x-cloak x-show="done"
                                        x-transition:enter="transition ease-out duration-200" x-transition:enter-start="scale-0" x-transition:enter-end="scale-100"
                                        class="pin-dot mt-1 size-2.5 shrink-0 bg-success" aria-hidden="true"></span>
                                    <span :class="done ? 'opacity-0 scale-0' : 'scale-100'"
                                        class="mt-1 size-2.5 shrink-0 rounded-[3px] border border-rule-strong transition duration-300" aria-hidden="true"></span>
                                    <div class="min-w-0 flex-1">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <span class="tabular font-mono text-[11px] font-semibold text-ink-faint">{{ $item['id'] }}</span>
                                            @php
                                                $var = match ($item['urgensi']) {
                                                    'tinggi' => 'danger',
                                                    'sedang' => 'warning',
                                                    default => 'neutral',
                                                };
                                                $label = match ($item['urgensi']) {
                                                    'tinggi' => 'Penting',
                                                    'sedang' => 'Sedang',
                                                    default => 'Rutin',
                                                };
                                            @endphp
                                            <x-ui.badge :variant="$var">{{ $label }}</x-ui.badge>
                                            <x-ui.badge variant="neutral" :dot="false">{{ $item['jenis'] }}</x-ui.badge>
                                        </div>
                                        <p :class="done ? 'line-through decoration-rule-strong' : ''"
                                            class="mt-1 text-[13px] font-semibold leading-snug text-ink transition">{{ $item['label'] }}</p>
                                        <p class="mt-0.5 text-xs text-ink-faint">{{ $item['waktu'] }}</p>
                                    </div>
                                    <div class="flex shrink-0 items-center gap-1.5">
                                        <x-ui.button size="sm" variant="secondary" x-on:click="done = false">Buka</x-ui.button>
                                        <x-ui.button size="sm" variant="primary" icon="check" x-on:click="done = true; $store.toasts.push('Tindakan ' + '{{ $item['id'] }}' + ' diselesaikan.')">Selesaikan</x-ui.button>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </x-ui.sheet>
            </div>

            <!-- Kehadiran Hari Ini per Rombel -->
            <div>
                <x-ui.sheet title="Kehadiran Hari Ini per Rombel" subtitle="Status review & jumlah hadir tercatat" pinned :padding="false" ruled>
                    <ul class="divide-y divide-rule/70">
                        @forelse ($kehadiranRombel as $row)
                            <li class="flex items-center justify-between gap-3 px-5 py-3">
                                <div class="min-w-0">
                                    <p class="text-[13px] font-semibold text-ink">{{ $row['class']->name }}</p>
                                    <p class="tabular mt-0.5 font-mono text-xs text-ink-faint">{{ $row['hadir'] }} hadir / {{ $row['total'] }} catatan</p>
                                </div>
                                @if ($row['reviewed'])
                                    <x-ui.badge variant="success" icon="check">Sudah review</x-ui.badge>
                                @else
                                    <x-ui.badge variant="warning" icon="clock">Belum</x-ui.badge>
                                @endif
                            </li>
                        @empty
                            <li class="px-5 py-8 text-center">
                                <p class="text-sm font-semibold text-ink">Belum ada rombel aktif.</p>
                            </li>
                        @endforelse
                    </ul>
                </x-ui.sheet>
            </div>
        </div>

        <!-- Kas: lembar tagihan -->
        <div class="mt-6">
            <x-ui.sheet title="Tagihan & Pembayaran Terbaru" subtitle="Pembayaran SPP lunas terakhir — posisi keuangan madrasah" pinned :padding="false">
                <x-ui.table :headers="['Siswa', 'Jenis Tagihan', 'Tanggal', 'Nominal', 'Status']" :empty="$tagihan->isEmpty()">
                    <x-slot name="emptySlot">Belum ada pembayaran tercatat.</x-slot>
                    <x-slot>
                        @foreach ($tagihan as $t)
                            <tr class="transition hover:bg-paper/60">
                                <td class="px-4 py-3">
                                    <p class="font-semibold text-ink">{{ $t['nama'] }}</p>
                                    <p class="tabular font-mono text-xs text-ink-faint">NIS {{ $t['nis'] }}</p>
                                </td>
                                <td class="px-4 py-3 text-ink-soft">{{ $t['jenis'] }}</td>
                                <td class="tabular px-4 py-3 text-ink-soft">{{ \Carbon\Carbon::parse($t['tanggal'])->isoFormat('D MMM YYYY') }}</td>
                                <td class="tabular px-4 py-3 text-right font-mono font-semibold text-ink">Rp{{ number_format($t['nominal'], 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-right">
                                    <x-ui.badge variant="success" icon="check">Lunas</x-ui.badge>
                                </td>
                            </tr>
                        @endforeach
                    </x-slot>
                </x-ui.table>
            </x-ui.sheet>
        </div>

        <!-- Jejak aktivitas -->
        <div class="mt-6">
            <x-ui.sheet title="Jejak Aktivitas" subtitle="Activity log — siapa mengubah apa, kapan" pinned ruled>
                @if ($aktivitas->isEmpty())
                    <p class="py-6 text-center text-sm text-ink-faint">Belum ada aktivitas tercatat.</p>
                @else
                    <ol class="space-y-0">
                        @foreach ($aktivitas as $a)
                            <li class="flex items-start gap-3 py-2.5">
                                <span class="mt-1.5 size-1.5 shrink-0 rounded-full bg-primary/60" aria-hidden="true"></span>
                                <div class="min-w-0 flex-1 text-[13px] leading-relaxed text-ink">
                                    <span class="font-bold">{{ $a['nama'] }}</span>
                                    <span class="text-ink-soft"> {{ $a['aksi'] }}</span>
                                </div>
                                <time class="shrink-0 text-xs text-ink-faint">{{ $a['waktu'] }}</time>
                            </li>
                        @endforeach
                    </ol>
                @endif
            </x-ui.sheet>
        </div>
    </div>
</x-layouts.page>
