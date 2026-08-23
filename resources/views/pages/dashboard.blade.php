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
                    Ringkasan kondisi madrasah hari ini — sematkan persetujuan untuk menyelesaikan tindakan yang menunggu.
                </p>
            </div>
            <div class="flex items-center gap-2">
                <x-ui.button variant="secondary" icon="calendar-days" href="{{ route('dashboard') }}">Kamis, 27 Agu 2026</x-ui.button>
                <x-ui.button variant="primary" icon="plus">Buat Pengumuman</x-ui.button>
            </div>
        </div>

        <!-- Lembar KPI (digit-bank) -->
        <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <x-ui.kpi label="Siswa Aktif" value="482" suffix="siswa" icon="academic-cap" trend="+12" trend-up="true" hint="vs. 2025/2026" />
            <x-ui.kpi label="Guru & Tenaga Kependidikan" value="34" suffix="orang" icon="user-group" trend="+2" trend-up="true" hint="vs. 2025/2026" />
            <x-ui.kpi label="Tagihan Terkumpul (Agu)" prefix="Rp" value="118.450.000" icon="banknotes" trend="+8,4%" trend-up="true" hint="dari target" />
            <x-ui.kpi label="Kehadiran Hari Ini" value="94,6" suffix="%" icon="clipboard-document-check" trend="−1,2%" trend-up="false" hint="dari 516 hadir" />
        </div>

        <div class="mt-6 grid grid-cols-1 gap-6 xl:grid-cols-3">
            <!-- Papan Perlu Tindakan -->
            <div class="xl:col-span-2">
                <x-ui.sheet title="Perlu Tindakan" subtitle="Persetujuan dan verifikasi yang menunggu Anda" pinned ruled
                    :actions="view('components.ui.pagination', ['current' => 1, 'last' => 1])->render()">
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
                                    <x-ui.button size="sm" variant="primary" icon="check" x-on:click="done = true; $store.toasts.push('Lembar ' + '{{ $item['id'] }}' + ' disetujui dan disematkan ke papan.')">Sematkan &amp; Setujui</x-ui.button>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </x-ui.sheet>
            </div>

            <!-- Agenda & Pengumuman -->
            <div class="space-y-6">
                <x-ui.sheet title="Agenda & Pengumuman" pinned :padding="false" ruled>
                    <ul class="divide-y divide-rule/70">
                        @foreach ($pengumuman as $item)
                            <li class="flex items-start gap-3 px-5 py-3.5">
                                <span class="mt-0.5 flex flex-col items-center">
                                    <span class="pin-dot size-2 {{ $item['jenis'] === 'agenda' ? 'bg-info' : 'bg-warning' }}" aria-hidden="true"></span>
                                </span>
                                <div class="min-w-0">
                                    <p class="text-[13px] font-semibold leading-snug text-ink">{{ $item['label'] }}</p>
                                    <p class="mt-0.5 text-xs text-ink-faint">{{ $item['tanggal'] }}</p>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                    <div class="border-t border-rule/70 p-3">
                        <x-ui.button variant="ghost" size="sm" icon="calendar-days" href="{{ route('dashboard') }}" class="w-full justify-center">Lihat kalender pendidikan</x-ui.button>
                    </div>
                </x-ui.sheet>
            </div>
        </div>

        <!-- Kas: lembar tagihan -->
        <div class="mt-6">
            <x-ui.sheet title="Tagihan & Pembayaran Terbaru" subtitle="Ringkasan SPP bulan berjalan — posisi keuangan madrasah" pinned
                :actions="'<a href=&quot;' . e(route('dashboard')) . '&quot; class=&quot;inline-flex items-center gap-1.5 text-xs font-bold text-primary transition hover:text-primary-strong&quot;>Lihat semua <svg class=&quot;size-3.5&quot; viewBox=&quot;0 0 24 24&quot; fill=&quot;none&quot; stroke=&quot;currentColor&quot; stroke-width=&quot;2&quot; aria-hidden=&quot;true&quot;><path stroke-linecap=&quot;round&quot; stroke-linejoin=&quot;round&quot; d=&quot;M17.25 8.25 21 12m0 0-3.75 3.75M21 12H3&quot;/></svg></a>'">
                <x-ui.table :headers="['Siswa', 'Jenis Tagihan', 'Tanggal', 'Nominal', 'Status', '']">
                    <x-slot name="emptySlot">Tidak ada tagihan bulan ini.</x-slot>
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
                                    @php
                                        $sv = match ($t['status']) {
                                            'lunas' => 'success',
                                            'cicilan' => 'warning',
                                            default => 'danger',
                                        };
                                        $sl = match ($t['status']) {
                                            'lunas' => 'Lunas',
                                            'cicilan' => 'Cicilan',
                                            default => 'Belum',
                                        };
                                    @endphp
                                    <x-ui.badge :variant="$sv">{{ $sl }}</x-ui.badge>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <x-ui.button size="sm" variant="ghost" icon="eye" href="{{ route('dashboard') }}">Detail</x-ui.button>
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
            </x-ui.sheet>
        </div>
    </div>
</x-layouts.page>
